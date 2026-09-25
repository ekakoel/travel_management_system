<?php

namespace App\Http\Controllers;

use App\Mail\AgentVerifiedMail;
use App\Models\Agent;
use App\Models\AgentDocument;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AgentController extends Controller
{
    /**
     * Agent Management
     */
    public function index()
    {
        $now = Carbon::now();
        $agents = Agent::query()
            ->with('user')
            ->latest()
            ->get();

        $totalAgents = $agents->count();

        $pendingAgents = $agents
            ->where('status', 'pending')
            ->count();

        $verifiedAgents = $agents
            ->where('status', 'verified')
            ->count();

        $rejectedAgents = $agents
            ->where('status', 'rejected')
            ->count();

        return view('admin.agents.index', compact(
            'agents',
            'totalAgents',
            'pendingAgents',
            'verifiedAgents',
            'rejectedAgents',
            'now'
        ));
    }

    /**
     * Agent Detail
     */
    public function show($id)
    {
        $now = Carbon::now();
        $agent = Agent::query()
            ->with('user')
            ->findOrFail($id);
        $companyType = match ($agent->company_type) {
            'travel_agency' => 'Travel Agent',
            'tour_operator', 'wholesaler' => 'Tour Operator',
            'corporate_travel' => 'Corporate Travel',
            default => null,
        };

        
        return view('admin.agents.show', compact('agent','now','companyType'));
    }

    /**
     * Agent Verification Page
     */
    public function verification($id)
    {
        $agent = Agent::query()
            ->with('user')
            ->findOrFail($id);

        $validationErrors = $this->validateAgentForVerification($agent);

        return view(
            'admin.agents.verification',
            compact('agent', 'validationErrors')
        );
    }

    /**
     * Verify Agent
     */
    public function verify(Request $request, $id)
    {
        $agent = Agent::query()
            ->with('user')
            ->lockForUpdate()
            ->findOrFail($id);

        if ($agent->status !== 'pending') {
            return redirect()
                ->route('admin.agents.show', $agent->id)
                ->with(
                    'error',
                    'This Agent is no longer pending verification.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Verification Checklist
        |--------------------------------------------------------------------------
        */

        $validated = $request->validate([
            'checks' => [
                'required',
                'array',
            ],
            'checks.*' => [
                'string',
            ],
        ]);

        $requiredChecks = [
            'company_information',
            'contact_information',
            'business_information',
            'submitted_documents',
            'ready_for_approval',
        ];

        foreach ($requiredChecks as $requiredCheck) {
            if (!in_array($requiredCheck, $validated['checks'], true)) {
                return redirect()
                    ->route('admin.agents.verification', $agent->id)
                    ->withInput()
                    ->with(
                        'error',
                        'Please complete all required verification checks.'
                    );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Server-side validation
        |--------------------------------------------------------------------------
        */

        $validationErrors = $this->validateAgentForVerification($agent);

        if (!empty($validationErrors)) {
            return redirect()
                ->route('admin.agents.verification', $agent->id)
                ->with(
                    'verification_errors',
                    $validationErrors
                );
        }

        /*
        |--------------------------------------------------------------------------
        | User Account
        |--------------------------------------------------------------------------
        */

        if (!$agent->user) {
            return redirect()
                ->route('admin.agents.verification', $agent->id)
                ->with(
                    'error',
                    'The Agent does not have a linked user account.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Generate New Login Password
        |--------------------------------------------------------------------------
        */

        $defaultPassword = Str::random(12);

        $result = DB::transaction(function () use (
            $agent,
            $defaultPassword
        ) {
            $agent->update([
                'status' => 'verified',
                'approved_at' => now(),
                'rejection_reason' => null,
            ]);

            $agent->user->update([
                'email' => $agent->contact_email,
                'password' => Hash::make($defaultPassword),
                'status' => 'Active',
                'is_approved' => true,
                'approved_at' => now(),
            ]);

            return [
                'agent' => $agent->fresh('user'),
                'email' => $agent->contact_email,
                'password' => $defaultPassword,
            ];
        });

        /*
        |--------------------------------------------------------------------------
        | Send Login Credentials
        |--------------------------------------------------------------------------
        */

        Mail::to($result['email'])->send(
            new AgentVerifiedMail(
                $result['agent'],
                $result['email'],
                $result['password']
            )
        );

        return redirect()
            ->route('admin.agents.show', $agent->id)
            ->with(
                'success',
                'Agent has been verified successfully. Login credentials have been sent to the Agent email.'
            );
    }

    /**
     * Reject Agent
     */
    public function reject(Request $request, $id)
    {
        $agent = Agent::query()->findOrFail($id);

        if ($agent->status !== 'pending') {
            return redirect()
                ->route('admin.agents.show', $agent->id)
                ->with(
                    'error',
                    'This Agent is no longer pending verification.'
                );
        }

        $validated = $request->validate([
            'rejection_reason' => [
                'required',
                'string',
                'max:2000',
            ],
        ]);

        $agent->update([
            'status' => 'rejected',
            'rejection_reason' => $validated['rejection_reason'],
        ]);

        return redirect()
            ->route('admin.agents.show', $agent->id)
            ->with(
                'success',
                'Agent application has been rejected.'
            );
    }

    /**
     * Agent Document
     */
    public function document($id, $type, $index = null)
    {
        $agent = Agent::query()->findOrFail($id);

        $query = AgentDocument::query()
            ->where('agent_id', $agent->id)
            ->where('document_type', $type);

        if ($type === 'translation_document' && $index !== null) {
            $document = $query
                ->orderBy('id')
                ->skip((int) $index)
                ->first();
        } else {
            $document = $query
                ->latest('id')
                ->first();
        }

        if (!$document || blank($document->storage_path)) {
            abort(404);
        }

        $path = ltrim($document->storage_path, '/');

        if (!Storage::disk('private')->exists($path)) {
            abort(404);
        }

        return Storage::disk('private')->response(
            $path,
            $document->original_filename,
            [
                'Content-Type' => $document->mime_type ?: 'application/octet-stream',
                'Content-Disposition' => 'inline; filename="' . addslashes($document->original_filename) . '"',
            ]
        );
    }

    /**
     * Validate Agent before verification.
     */
    protected function validateAgentForVerification(Agent $agent): array
    {
        $errors = [];

        /*
        |--------------------------------------------------------------------------
        | Company
        |--------------------------------------------------------------------------
        */

        if (blank($agent->company_name) && blank($agent->name)) {
            $errors[] = 'Company name is missing.';
        }

        if (blank($agent->company_type)) {
            $errors[] = 'Company type is missing.';
        }

        if (blank($agent->country)) {
            $errors[] = 'Country is missing.';
        }

        if (
            blank($agent->company_address) &&
            blank($agent->Address)
        ) {
            $errors[] = 'Company address is missing.';
        }

        /*
        |--------------------------------------------------------------------------
        | Contact
        |--------------------------------------------------------------------------
        */

        if (
            blank($agent->contact_name) &&
            blank($agent->pic_name)
        ) {
            $errors[] = 'Contact person is missing.';
        }

        if (blank($agent->contact_email)) {
            $errors[] = 'Contact email is missing.';
        } elseif (
            !filter_var(
                $agent->contact_email,
                FILTER_VALIDATE_EMAIL
            )
        ) {
            $errors[] = 'Contact email format is invalid.';
        }

        if (blank($agent->phone)) {
            $errors[] = 'Phone number is missing.';
        }

        /*
        |--------------------------------------------------------------------------
        | Business
        |--------------------------------------------------------------------------
        */

        if (blank($agent->business_license_number)) {
            $errors[] = 'Business registration/license number is missing.';
        }

        /*
        |--------------------------------------------------------------------------
        | Required Documents
        |--------------------------------------------------------------------------
        */

        if (blank($agent->business_license)) {
            $errors[] = 'Business license document is missing.';
        }

        if (blank($agent->company_letter)) {
            $errors[] = 'Company letter document is missing.';
        }

        /*
        |--------------------------------------------------------------------------
        | Linked User
        |--------------------------------------------------------------------------
        */

        if (!$agent->user) {
            $errors[] = 'Linked user account is missing.';
        }

        return $errors;
    }

}