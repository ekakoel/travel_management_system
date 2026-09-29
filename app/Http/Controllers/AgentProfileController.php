<?php

namespace App\Http\Controllers;

use App\Models\Agent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class AgentProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    /**
     * Show the current user's Agent/Agency profile.
     *
     * Only the Agent owner can access this page.
     */
    public function edit()
    {
        $user = Auth::user();

        $agent = Agent::query()
            ->where('user_id', $user->id)
            ->firstOrFail();

        return view('frontend.home.profile.agent-edit', [
            'agent' => $agent,
        ]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $agent = Agent::query()
            ->where('user_id', $user->id)
            ->firstOrFail();

        $validated = $request->validateWithBag('agentProfileUpdate', [
            'company_name' => [
                'required',
                'string',
                'max:255',
            ],

            'company_type' => [
                'required',
                Rule::in([
                    'travel_agency',
                    'tour_operator',
                    'wholesaler',
                    'corporate_travel',
                ]),
            ],

            'country' => [
                'required',
                'string',
                'max:120',
            ],

            'company_address' => [
                'required',
                'string',
                'max:500',
            ],

            'website' => [
                'nullable',
                'url',
                'max:255',
            ],

            'business_license_number' => [
                'nullable',
                'string',
                'max:120',
            ],

            'contact_name' => [
                'required',
                'string',
                'max:255',
            ],

            'contact_email' => [
                'required',
                'email',
                'max:255',
            ],

            'phone' => [
                'required',
                'string',
                'max:50',
            ],

            'position' => [
                'nullable',
                'string',
                'max:120',
            ],

            'preferred_contact' => [
                'nullable',
                'string',
                'max:120',
            ],

            'main_market' => [
                'nullable',
                'string',
                'max:255',
            ],

            'monthly_bali_clients' => [
                'nullable',
                'integer',
                'min:0',
            ],

            'interested_services' => [
                'nullable',
                'array',
            ],

            'interested_services.*' => [
                Rule::in([
                    'accommodation',
                    'transport',
                    'tour_packages',
                    'activities',
                ]),
            ],
        ]);

        $agent->update($validated);

        return redirect()
            ->route('profile')
            ->with(
                'success',
                __('messages.Company profile has been updated successfully.')
            );
    }
}