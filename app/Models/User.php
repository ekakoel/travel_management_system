<?php

namespace App\Models;

use App\Models\Role;
use App\Models\Spks;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, HasApiTokens;

    public const CONTACT_CHANNEL_PLATFORM_WHATSAPP = 'whatsapp';
    public const CONTACT_CHANNEL_PLATFORM_WECHAT = 'wechat';
    public const CONTACT_CHANNEL_PLATFORM_LINE = 'line';
    public const CONTACT_CHANNEL_PLATFORM_TELEGRAM = 'telegram';
    public const CONTACT_CHANNEL_PLATFORM_SKYPE = 'skype';
    public const CONTACT_CHANNEL_PLATFORM_FACEBOOK = 'facebook';
    public const CONTACT_CHANNEL_PLATFORM_INSTAGRAM = 'instagram';
    public const CONTACT_CHANNEL_PLATFORM_LINKEDIN = 'linkedin';
    public const CONTACT_CHANNEL_PLATFORM_OTHER = 'other';

    protected $fillable = [
        'name',
        'email',
        'password',
        'type',
        'username',
        'code',
        'profileimg',
        'phone',
        'whatsapp',
        'wechat',
        'line',
        'telegram',
        'chat_account',
        'contact_channels',
        'address',
        'city',
        'state_region',
        'postal_code',
        'country',
        'office',
        'company_legal_name',
        'job_title',
        'website',
        'timezone',
        'company_registration_number',
        'position',
        'status',
        'is_approved',
        'approved_at',
        'comment',
        'session_id',
        'email_verified_at',
        'remember_token',
        'subscriber',
        'is_subscribed',
        'unsubscribe_reason',
        'preferred_language'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'contact_channels' => 'array',
    ];

    public static function supportedContactChannelPlatforms(): array
    {
        return [
            self::CONTACT_CHANNEL_PLATFORM_WHATSAPP,
            self::CONTACT_CHANNEL_PLATFORM_WECHAT,
            self::CONTACT_CHANNEL_PLATFORM_LINE,
            self::CONTACT_CHANNEL_PLATFORM_TELEGRAM,
            self::CONTACT_CHANNEL_PLATFORM_SKYPE,
            self::CONTACT_CHANNEL_PLATFORM_FACEBOOK,
            self::CONTACT_CHANNEL_PLATFORM_INSTAGRAM,
            self::CONTACT_CHANNEL_PLATFORM_LINKEDIN,
            self::CONTACT_CHANNEL_PLATFORM_OTHER,
        ];
    }

    public static function sanitizeContactChannels(?array $channels): array
    {
        return collect($channels ?? [])
            ->map(function ($channel) {
                if (!is_array($channel)) {
                    return null;
                }

                $platform = self::normalizeContactChannelPlatform($channel['platform'] ?? null);
                $value = trim((string) ($channel['value'] ?? ''));

                if ($platform === null && $value === '') {
                    return null;
                }

                return [
                    'platform' => $platform,
                    'value' => $value,
                ];
            })
            ->filter(fn ($channel) => is_array($channel))
            ->filter(fn ($channel) => filled($channel['platform']) && filled($channel['value']))
            ->unique(fn ($channel) => $channel['platform'] . '|' . mb_strtolower($channel['value']))
            ->values()
            ->all();
    }

    public static function syncLegacyContactChannelAttributes(?array $channels): array
    {
        $channels = self::sanitizeContactChannels($channels);

        $attributes = [
            'contact_channels' => $channels,
            'whatsapp' => null,
            'wechat' => null,
            'line' => null,
            'telegram' => null,
            'chat_account' => null,
        ];

        foreach ($channels as $channel) {
            if ($channel['platform'] === self::CONTACT_CHANNEL_PLATFORM_WHATSAPP && blank($attributes['whatsapp'])) {
                $attributes['whatsapp'] = $channel['value'];
            }

            if ($channel['platform'] === self::CONTACT_CHANNEL_PLATFORM_WECHAT && blank($attributes['wechat'])) {
                $attributes['wechat'] = $channel['value'];
            }

            if ($channel['platform'] === self::CONTACT_CHANNEL_PLATFORM_LINE && blank($attributes['line'])) {
                $attributes['line'] = $channel['value'];
            }

            if ($channel['platform'] === self::CONTACT_CHANNEL_PLATFORM_TELEGRAM && blank($attributes['telegram'])) {
                $attributes['telegram'] = $channel['value'];
            }

            if ($channel['platform'] === self::CONTACT_CHANNEL_PLATFORM_OTHER && blank($attributes['chat_account'])) {
                $attributes['chat_account'] = $channel['value'];
            }
        }

        return $attributes;
    }

    public function getNormalizedContactChannelsAttribute(): array
    {
        $legacyChannels = [
            ['platform' => self::CONTACT_CHANNEL_PLATFORM_WHATSAPP, 'value' => $this->whatsapp],
            ['platform' => self::CONTACT_CHANNEL_PLATFORM_WECHAT, 'value' => $this->wechat],
            ['platform' => self::CONTACT_CHANNEL_PLATFORM_LINE, 'value' => $this->line],
            ['platform' => self::CONTACT_CHANNEL_PLATFORM_TELEGRAM, 'value' => $this->telegram],
            ['platform' => self::CONTACT_CHANNEL_PLATFORM_OTHER, 'value' => $this->chat_account],
        ];

        return self::sanitizeContactChannels(array_merge($this->contact_channels ?? [], $legacyChannels));
    }

    protected static function normalizeContactChannelPlatform(?string $platform): ?string
    {
        $platform = trim((string) $platform);

        if ($platform === 'chat_account') {
            $platform = self::CONTACT_CHANNEL_PLATFORM_OTHER;
        }

        return in_array($platform, self::supportedContactChannelPlatforms(), true) ? $platform : null;
    }
    public function spks(){
        return $this->hasMany(Spks::class,'operator_id'); //good
    }

    public function getPhotoAttribute()
    {
        return 'https://www.gravatar.com/avatar/' . md5(strtolower($this->email)) . '.jpg?s=200&d=mm';
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    public function assignRole(Role $role)
    {
        return $this->roles()->save($role);
    }


    public function isAdmin()
    {
        return $this->roles()->where('name', 'Admin')->exists();
    }

    public function isUser()
    {
        return $this->roles()->where('name', 'User')->exists();
    }
    public function isRsv()
    {
        return $this->roles()->where('name', 'Reservation')->exists();
    }
    public function isAuthor()
    {
        return $this->roles()->where('name', 'Author')->exists();
    }
    public function isDev()
    {
        return $this->roles()->where('name', 'Developer')->exists();
    }

    public function isAdm()
    {
        return $this->roles()->where('name', 'Administrator')->exists();
    }

    public function isAgentUser(): bool
    {
        return strtolower((string) $this->position) === 'agent';
    }

    public function canAccessAdminDashboard(): bool
    {
        return ! $this->isAgentUser();
    }
    
    public function messages()
    {
    return $this->hasMany(Message::class);
    }

}
