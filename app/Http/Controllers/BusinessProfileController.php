<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateBusinessProfileRequest;
use App\Models\BusinessProfile;
use App\Services\BusinessProfileService;
use App\Services\FooterContentService;
use Illuminate\Support\Str;

class BusinessProfileController extends Controller
{
    protected BusinessProfileService $businessProfileService;

    public function __construct()
    {
        $this->middleware(['auth','verified','type:admin']);
        $this->businessProfileService = app(BusinessProfileService::class);
    }

    public function index()
    {
        return redirect()->route('admin.company-profile.edit');
    }

    public function edit()
    {
        $businessProfile = BusinessProfile::query()->firstOrCreate(
            ['profile_key' => 'primary'],
            [
                'name' => config('app.business', config('app.name', 'Bali Kami Tour')),
                'nickname' => config('app.business', config('app.name', 'Bali Kami Tour')),
                'type' => 'B2B Travel Agent',
            ]
        );

        return view('backend.admin.company-profile.edit', [
            'businessProfile' => $businessProfile,
            'summary' => $this->profileSummary($businessProfile),
            'logoUrl' => $this->logoUrl($businessProfile->logo),
            'logoDarkUrl' => $this->logoUrl($businessProfile->logo_dark),
        ]);
    }

    public function update(UpdateBusinessProfileRequest $request)
    {
        $businessProfile = BusinessProfile::query()->firstOrCreate(
            ['profile_key' => 'primary'],
            ['name' => config('app.business', config('app.name', 'Bali Kami Tour'))]
        );

        $data = $request->safe()->except(['logo', 'logo_dark']);

        $this->storeLogoUpload($request, $data, 'logo', 'light');
        $this->storeLogoUpload($request, $data, 'logo_dark', 'dark');

        $businessProfile->fill($data);
        $businessProfile->save();
        $this->businessProfileService->forget();
        app(FooterContentService::class)->forget();

        return redirect()
            ->route('admin.company-profile.edit')
            ->with('success', 'Company profile has been updated successfully.');
    }

    protected function storeLogoUpload(UpdateBusinessProfileRequest $request, array &$data, string $field, string $variant): void
    {
        if (!$request->hasFile($field)) {
            return;
        }

        $logo = $request->file($field);
        $filename = 'company-profile-'
            .Str::slug($request->input('name', 'logo'))
            .'-'.$variant
            .'-'.time()
            .'.'.$logo->getClientOriginalExtension();

        $logo->storeAs('public/logo', $filename);
        $data[$field] = $filename;
    }

    protected function profileSummary(BusinessProfile $businessProfile): array
    {
        return [
            'brand' => $businessProfile->nickname ?: $businessProfile->name,
            'type' => $businessProfile->type ?: 'Not configured',
            'contact' => $businessProfile->email ?: $businessProfile->phone ?: 'Not configured',
            'public' => $businessProfile->public_tagline ? 'Ready' : 'Needs tagline',
        ];
    }

    protected function logoUrl(?string $logo): ?string
    {
        if (!$logo) {
            return null;
        }

        return Str::startsWith($logo, ['http://', 'https://', '/'])
            ? $logo
            : asset('storage/public/logo/'.ltrim($logo, '/'));
    }
}
