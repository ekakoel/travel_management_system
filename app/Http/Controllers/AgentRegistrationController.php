<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Agent;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Mail\AgentRegistered;
use App\Mail\AgentConfirmation;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use App\Notifications\NewAgentRegistered;

class AgentRegistrationController extends Controller
{
    public function __construct()
    {
        $this->middleware('registration.open')->only(['showForm', 'submitForm']);
    }

    public function showForm()
    {
        return view('frontend.home.agents.register');
    }

    public function test_view_email(Request $request)
    {
        $agent = Agent::find(30);
        return view('emails.agents.registered', compact('agent'));
    }

    public function submitForm(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'pic_name' => 'required|string|max:255',
            'email' => 'required|email|unique:agents,email',
            'phone' => 'required|string|max:20',
            'country' => 'required|string',
            'company_address' => 'required|string|max:500',
            'website' => 'nullable|url',
            'agree_terms' => 'accepted',

            'business_license' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'tax_document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'company_letter' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'translation_documents.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $folder = 'agents/' . Str::slug($request->company_name) . '-' . time();

        $fileFields = [
            'business_license' => 'business_license',
            'tax_document' => 'tax_document',
            'company_letter' => 'company_letter',
        ];

        foreach ($fileFields as $field => $filename) {
            $validated[$field] = $request->hasFile($field)
                ? $request->file($field)->storeAs($folder, $filename . '.' . $request->file($field)->getClientOriginalExtension())
                : null;
        }

        // Handle translation documents
        $translationPaths = [];
        if ($request->hasFile('translation_documents')) {
            foreach ($request->file('translation_documents') as $i => $file) {
                $name = 'translation_' . ($i + 1) . '.' . $file->getClientOriginalExtension();
                $translationPaths[] = $file->storeAs($folder . '/translations', $name);
            }
        }

        $validated['translation_documents'] = $translationPaths;
        $status = 'pending';

        try {
            $agent = Agent::create($validated);
            $admins = User::where('position','developer')->get();
            $agent_id = $agent->id;

            Mail::to(config('app.administrator_mail'))->send(new AgentConfirmation($agent_id));
            Mail::to(config('app.administrator_mail'))->send(new AgentRegistered($agent_id));
            foreach ($admins as $admin) {
                $admin->notify(new NewAgentRegistered($agent_id));
            }
            return redirect()->route('agent.register')->with('success', __('messages.Thank you for registering. Your documents are under review.'));
        } catch (\Exception $e) {
            Log::error('Agent registration failed: ' . $e->getMessage());

            return redirect()->route('agent.register')->with('error', 'There was a problem processing your request. Please try again.');
        }
    }

}
