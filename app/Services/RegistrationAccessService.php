<?php

namespace App\Services;

use App\Models\SystemSetting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class RegistrationAccessService
{
    public const SETTING_KEY = 'registration_access';
    protected const CACHE_KEY = 'system.registration_access.enabled';

    public function enabled(): bool
    {
        if (!Schema::hasTable('system_settings')) {
            return true;
        }

        return Cache::rememberForever(self::CACHE_KEY, function () {
            $setting = SystemSetting::query()
                ->where('key', self::SETTING_KEY)
                ->first();

            return $setting ? (bool) $setting->status : true;
        });
    }

    public function setting(): SystemSetting
    {
        if (!Schema::hasTable('system_settings')) {
            return new SystemSetting([
                'key' => self::SETTING_KEY,
                'value' => 'Public registration access',
                'status' => true,
            ]);
        }

        return SystemSetting::query()->firstOrCreate(
            ['key' => self::SETTING_KEY],
            [
                'value' => 'Public registration access',
                'status' => true,
            ]
        );
    }

    public function update(bool $enabled): SystemSetting
    {
        $setting = $this->setting();
        $setting->forceFill(['status' => $enabled])->save();
        Cache::forget(self::CACHE_KEY);

        return $setting->refresh();
    }
}
