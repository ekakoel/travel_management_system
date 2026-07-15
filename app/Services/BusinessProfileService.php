<?php

namespace App\Services;

use App\Models\BusinessProfile;
use Illuminate\Support\Facades\Cache;

class BusinessProfileService
{
    public function primary(): BusinessProfile
    {
        return Cache::remember('business_profile.primary', 3600, function () {
            return BusinessProfile::query()
                ->where('profile_key', 'primary')
                ->first()
                ?: BusinessProfile::query()->orderBy('id')->first()
                ?: new BusinessProfile([
                    'profile_key' => 'primary',
                    'name' => config('app.business', config('app.name', 'Bali Kami Tour')),
                    'nickname' => config('app.business', config('app.name', 'Bali Kami Tour')),
                    'type' => 'B2B Travel Agent',
                    'address' => config('app.bali_contact_office', 'Bali'),
                    'phone' => config('app.bali_contact_office_phone'),
                    'email' => config('app.administrator_mail'),
                    'website' => config('app.app_url'),
                    'logo' => config('app.logo_img_color'),
                    'logo_dark' => config('app.logo_img_white'),
                    'youtube' => 'https://www.youtube.com/@balikamichannel',
                    'linkedin' => 'https://id.linkedin.com/company/bali-kami-group',
                ]);
        });
    }

    public function forget(): void
    {
        Cache::forget('business_profile.primary');
        Cache::forget('business_profile');
    }
}
