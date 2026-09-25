<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\InteractsWithFormSubmissions;
use App\Http\Requests\StoreAgentApplicationRequest;
use App\Mail\AgentConfirmation;
use App\Mail\AgentRegistered;
use App\Models\Agent;
use App\Models\User;
use App\Notifications\NewAgentRegistered;
use App\Services\AgentRegistrationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;

class AgentRegistrationController extends Controller
{
    use InteractsWithFormSubmissions;

    public function __construct()
    {
        $this->middleware('registration.open')->only(['showForm', 'submitForm', 'pending']);
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

    public function submitForm(StoreAgentApplicationRequest $request, AgentRegistrationService $registrationService)
    {
        $token = $request->validated('submission_token');

        if ($this->findProcessedFormSubmission('agent-application', $token)) {
            return redirect()->route('partner.application.pending');
        }

        $agent = $registrationService->register($request->validated());
        $this->rememberProcessedFormSubmission('agent-application', $token, $agent->id);

        try {
            $admins = User::query()
                ->whereIn('position', ['developer', 'administrator', 'author'])
                ->get();

            Notification::send($admins, new NewAgentRegistered($agent));
            Mail::to($agent->contact_email)->send(new AgentConfirmation($agent));

            if (filled(config('app.administrator_mail'))) {
                Mail::to(config('app.administrator_mail'))->send(new AgentRegistered($agent));
            }
        } catch (\Throwable $exception) {
            Log::error('Agent registration follow-up failed.', [
                'agent_id' => $agent->id,
                'exception' => $exception,
            ]);
        }

        return redirect()->route('partner.application.pending');
    }

    public function pending()
    {
        return view('frontend.home.agents.pending');
    }
}
