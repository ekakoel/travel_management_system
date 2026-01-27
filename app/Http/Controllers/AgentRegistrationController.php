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
    public function showForm()
    {
        return view('home.agents.register');
    }

    public function test_view_email(Request $request)
    {
        $agent = Agent::find(30);
        return view('emails.agents.registered', compact('agent'));
    }



    // public function submitForm(Request $request)
    // {
    //     $request->validate([
    //         'company_name' => 'required|string|max:255',
    //         'pic_name' => 'required|string|max:255',
    //         'email' => 'required|email|unique:agents,email',
    //         'phone' => 'required|string|max:20',
    //         'country' => 'required|string',
    //         'company_address' => 'required|string|max:500',
    //         'website' => 'nullable|url',
    //         'agree_terms' => 'accepted',

    //         'business_license' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
    //         'tax_document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
    //         'company_letter' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
    //         'translation_documents.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
    //     ]);

    //     $folder = 'agents/' . Str::slug($request->company_name) . '-' . time();

    //     $data = $request->only([
    //         'company_name', 'pic_name', 'email', 'phone',
    //         'country', 'company_address', 'website'
    //     ]);

    //     // Simpan dokumen
    //     $data['business_license'] = $request->file('business_license')
    //         ->storeAs($folder, 'business_license.' . $request->file('business_license')->getClientOriginalExtension());

    //     $data['tax_document'] = $request->hasFile('tax_document')
    //         ? $request->file('tax_document')->storeAs($folder, 'tax_document.' . $request->file('tax_document')->getClientOriginalExtension())
    //         : null;

    //     $data['company_letter'] = $request->file('company_letter')
    //         ->storeAs($folder, 'company_letter.' . $request->file('company_letter')->getClientOriginalExtension());

    //     // Terjemahan dokumen (optional dan bisa banyak)
    //     $translationFiles = [];
    //     if ($request->hasFile('translation_documents')) {
    //         foreach ($request->file('translation_documents') as $index => $file) {
    //             $fileName = 'translation_' . ($index + 1) . '.' . $file->getClientOriginalExtension();
    //             $translationFiles[] = $file->storeAs($folder . '/translations', $fileName);
    //         }
    //     }
    //     $data['translation_documents'] = $translationFiles;

    //     $data['status'] = 'pending';

    //     // Simpan ke database
    //     $agent = Agent::create($data);
    //     try {
    //         Mail::to($agent->email)->send(new AgentConfirmation($agent));
    //         Mail::to(config('app.administrator_mail'))->send(new AgentRegistered($agent));
    //     } catch (\Exception $e) {
    //         Log::error('Mail send failed: ' . $e->getMessage());
    //         return back()->with('error', 'Failed to send email. Please check mail configuration.');
    //     }
    //     return redirect()->route('agent.register')->with('success', __('messages.Thank you for registering. Your documents are under review.'));
    // }

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
            // Mail::to($agent->email)->send(new AgentConfirmation($agent));
            Mail::to(config('app.administrator_mail'))->send(new AgentRegistered($agent_id));
            foreach ($admins as $admin) {
                $admin->notify(new NewAgentRegistered($agent_id));
            }
            
            // dd($agent);
            return redirect()->route('agent.register')->with('success', __('messages.Thank you for registering. Your documents are under review.'));
        } catch (\Exception $e) {
            Log::error('Agent registration failed: ' . $e->getMessage());

            return redirect()->route('agent.register')->with('error', 'There was a problem processing your request. Please try again.');
        }
    }

}
