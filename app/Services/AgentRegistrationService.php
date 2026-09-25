<?php

namespace App\Services;

use App\Models\Agent;
use App\Models\AgentDocument;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AgentRegistrationService
{
    private const PRIVATE_DISK = 'private';
    private const DOCUMENT_ROOT = 'agent-applications';

    public function register(array $data): Agent
    {
        $storedPaths = [];

        try {
            return DB::transaction(function () use ($data, &$storedPaths): Agent {
                $this->ensureNoActiveApplication($data['contact_email']);

                $user = $this->createInactiveAgentUser($data);
                $documents = $this->storeDocuments($data, $storedPaths);

                $agent = Agent::create([
                    'user_id' => $user->id,
                    'name' => $data['company_name'],
                    'company_name' => $data['company_name'],
                    'company_type' => $data['company_type'],
                    'pic_name' => $data['contact_name'],
                    'contact_name' => $data['contact_name'],
                    'contact_email' => $data['contact_email'],
                    // Legacy email stays an internal unique identifier; contact_email is the correspondence address.
                    'email' => $this->generatedAgentEmail(),
                    'phone' => $data['phone'],
                    'country' => $data['country'],
                    'company_address' => $data['company_address'],
                    'website' => $data['website'] ?? null,
                    'business_license_number' => $data['business_license_number'] ?? null,
                    'position' => $data['position'] ?? null,
                    'preferred_contact' => $data['preferred_contact'] ?? null,
                    'main_market' => $data['main_market'] ?? null,
                    'monthly_bali_clients' => $data['monthly_bali_clients'] ?? null,
                    'interested_services' => $data['interested_services'] ?? null,
                    'agreed_to_terms_at' => now(),
                    'agreed_to_contact_at' => !empty($data['agree_to_contact']) ? now() : null,
                    'business_license' => $documents['business_license']['path'],
                    'company_letter' => $documents['company_letter']['path'],
                    'tax_document' => $documents['tax_document']['path'] ?? null,
                    'translation_documents' => collect($documents['supporting_document'] ?? [])
                        ->pluck('path')
                        ->all(),
                    'status' => 'pending',
                ]);

                foreach ($documents as $type => $documentSet) {
                    foreach ((isset($documentSet['path']) ? [$documentSet] : $documentSet) as $document) {
                        $agent->documents()->create([
                            'document_type' => $type,
                            'storage_path' => $document['path'],
                            'original_filename' => $document['original_filename'],
                            'mime_type' => $document['mime_type'],
                            'size' => $document['size'],
                        ]);
                    }
                }

                return $agent;
            });
        } catch (\Throwable $exception) {
            Storage::disk(self::PRIVATE_DISK)->delete($storedPaths);

            throw $exception;
        }
    }

    private function ensureNoActiveApplication(string $contactEmail): void
    {
        $exists = Agent::query()
            ->whereIn('status', ['pending', 'approved', 'verified'])
            ->where(function ($query) use ($contactEmail): void {
                $query->where('contact_email', $contactEmail)
                    ->orWhere(function ($legacyQuery) use ($contactEmail): void {
                        $legacyQuery->whereNull('contact_email')->where('email', $contactEmail);
                    });
            })
            ->lockForUpdate()
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'contact_email' => __('agent-registration.validation.existing_application'),
            ]);
        }
    }

    private function createInactiveAgentUser(array $data): User
    {
        $user = User::create([
            'name' => $data['contact_name'],
            'username' => $this->generatedUsername(),
            'email' => $data['contact_email'],
            'password' => Hash::make(Str::random(64)),
            'type' => 'agent',
            'position' => 'agent',
            'office' => $data['company_name'],
            'job_title' => $data['position'],
            'phone' => $data['phone'],
            'address' => $data['company_address'],
            'country' => $data['country'],
            'website' => $data['website'],
            'company_registration_number' => $data['business_license_number'],
            'status' => 'Inactive',
            'is_approved' => false,
            'is_subscribed' => false,
            'subscriber' => false,
        ]);

        $role = Role::query()->whereRaw('LOWER(name) = ?', ['agent'])->first();
        if ($role) {
            $user->assignRole($role);
        }

        return $user;
    }

    private function storeDocuments(array $data, array &$storedPaths): array
    {
        $root = self::DOCUMENT_ROOT.'/'.$data['submission_token'];
        $documents = [];

        foreach (['business_license', 'company_letter', 'tax_document'] as $type) {
            if (!isset($data[$type]) || !$data[$type] instanceof UploadedFile) {
                continue;
            }

            $documents[$type] = $this->storeDocument($data[$type], $root, $storedPaths);
        }

        foreach ($data['supporting_documents'] ?? [] as $file) {
            if ($file instanceof UploadedFile) {
                $documents['supporting_document'][] = $this->storeDocument($file, $root, $storedPaths);
            }
        }

        return $documents;
    }

    private function storeDocument(UploadedFile $file, string $root, array &$storedPaths): array
    {
        $path = $file->store($root, self::PRIVATE_DISK);
        $storedPaths[] = $path;

        return [
            'path' => $path,
            'original_filename' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType() ?: 'application/octet-stream',
            'size' => $file->getSize(),
        ];
    }

    private function generatedUsername(): string
    {
        return 'partner_'.Str::lower(Str::random(16));
    }

    private function generatedUserEmail(): string
    {
        return 'partner-'.Str::lower(Str::uuid()).'@pending.balikamitour.invalid';
    }

    private function generatedAgentEmail(): string
    {
        return 'agent-'.Str::lower(Str::uuid()).'@pending.balikamitour.invalid';
    }
}
