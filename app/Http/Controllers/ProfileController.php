<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','verified']);
    }

    public function profile()
    {
        return $this->renderProfileView(Auth::user(), 'Profile');
    }

    public function users($email)
    {
        $duser = User::find($email);
        return $this->renderProfileView($duser ?: Auth::user(), 'Users');
    }

    protected function renderProfileView(User $user, string $title)
    {
        $coreRequiredFields = [
            'email' => __('messages.Email'),
        ];

        $businessRecommendedFields = [
            'phone' => __('messages.Phone'),
            'job_title' => __('messages.Job Title'),
            'office' => __('messages.Office'),
            'city' => __('messages.City'),
            'address' => __('messages.Address'),
            'country' => __('messages.Country'),
            'preferred_language' => __('messages.Preferred Language'),
            'company_legal_name' => __('messages.Legal Company Name'),
            'website' => __('messages.Website'),
            'state_region' => __('messages.State / Region'),
            'postal_code' => __('messages.Postal Code'),
            'timezone' => __('messages.Time Zone'),
            'company_registration_number' => __('messages.Company Registration Number'),
        ];

        $missingFields = collect($coreRequiredFields)
            ->filter(fn ($label, $field) => blank($user->{$field} ?? null))
            ->values();

        $recommendedMissingFields = collect($businessRecommendedFields)
            ->filter(fn ($label, $field) => blank($user->{$field} ?? null))
            ->values();

        $completedFields = count($coreRequiredFields) - $missingFields->count();
        $completionRate = (int) round(($completedFields / max(count($coreRequiredFields), 1)) * 100);
        $recommendedCompletedFields = count($businessRecommendedFields) - $recommendedMissingFields->count();
        $businessCompletionRate = (int) round(($recommendedCompletedFields / max(count($businessRecommendedFields), 1)) * 100);

        $isBlocked = $user->status === 'Block';
        $isApproved = (int) $user->is_approved === 1;
        $accountStatus = $isBlocked
            ? __('messages.Blocked')
            : ($isApproved ? __('messages.Approved') : __('messages.Pending Approval'));
        $accountStatusClass = $isBlocked
            ? 'is-danger'
            : ($isApproved ? 'is-success' : 'is-warning');

        $countryOptions = collect($this->countries())
            ->merge([$user->country])
            ->filter()
            ->unique()
            ->sort()
            ->values()
            ->all();

        $languageOptions = [
            'en' => __('messages.English'),
            'zh' => __('messages.Chinese Traditional'),
            'zh-CN' => __('messages.Chinese Simplified'),
        ];

        $timezoneOptions = collect($this->timezones())
            ->merge([$user->timezone])
            ->filter()
            ->unique()
            ->sort()
            ->values()
            ->all();

        $contactChannelPlatformOptions = $this->contactChannelPlatformOptions();
        $contactChannels = collect($user->normalized_contact_channels)
            ->map(function (array $channel) use ($contactChannelPlatformOptions) {
                $platform = $contactChannelPlatformOptions[$channel['platform']] ?? null;

                if ($platform === null) {
                    return null;
                }

                return [
                    'platform' => $channel['platform'],
                    'label' => $platform['label'],
                    'icon' => $platform['icon'],
                    'value' => $channel['value'],
                    'href' => $this->contactChannelHref($channel['platform'], $channel['value']),
                ];
            })
            ->filter()
            ->values()
            ->all();

        $avatarUrl = filled($user->profileimg)
            ? asset('storage/user/profile/' . $user->profileimg)
            : asset('storage/user/profile/default_user_img.png');

        $displayTimezone = $user->timezone ?: __('messages.Pending Update');
        $displayWebsite = $user->website ?: __('messages.Pending Update');

        $heroStats = [
            [
                'label' => __('messages.Account Status'),
                'value' => $accountStatus,
            ],
            [
                'label' => __('messages.Profile Completion'),
                'value' => $completionRate . '%',
            ],
            [
                'label' => __('messages.Partner Code'),
                'value' => $user->code ?: __('messages.Not Assigned'),
            ],
            [
                'label' => __('messages.Company / Office'),
                'value' => $user->office ?: __('messages.Pending Update'),
            ],
        ];

        $verificationItems = [
            [
                'label' => __('messages.Email Verification'),
                'value' => $user->email_verified_at
                    ? __('messages.Verified on :date', ['date' => $user->email_verified_at->format('Y-m-d')])
                    : __('messages.Pending Verification'),
                'state' => $user->email_verified_at ? 'success' : 'warning',
            ],
            [
                'label' => __('messages.Account Approval'),
                'value' => $isApproved
                    ? __('messages.Approved')
                    : ($isBlocked ? __('messages.Blocked') : __('messages.Pending Approval')),
                'state' => $isApproved ? 'success' : ($isBlocked ? 'danger' : 'warning'),
            ],
            [
                'label' => __('messages.Profile Completion'),
                'value' => $completionRate . '%',
                'state' => $completionRate >= 100 ? 'success' : 'warning',
            ],
        ];

        $companyItems = [
            ['label' => __('messages.Company / Office'), 'value' => $user->office ?: '-'],
            ['label' => __('messages.Legal Company Name'), 'value' => $user->company_legal_name ?: '-'],
            ['label' => __('messages.Job Title'), 'value' => $user->job_title ?: '-'],
            ['label' => __('messages.Company Registration Number'), 'value' => $user->company_registration_number ?: '-'],
        ];

        $locationItems = [
            ['label' => __('messages.Address'), 'value' => $user->address ?: '-'],
            ['label' => __('messages.City'), 'value' => $user->city ?: '-'],
            ['label' => __('messages.State / Region'), 'value' => $user->state_region ?: '-'],
            ['label' => __('messages.Postal Code'), 'value' => $user->postal_code ?: '-'],
            ['label' => __('messages.Country'), 'value' => $user->country ?: '-'],
            ['label' => __('messages.Time Zone'), 'value' => $displayTimezone],
        ];

        $preferenceItems = [
            ['label' => __('messages.Preferred Language'), 'value' => $languageOptions[$user->preferred_language ?? 'en'] ?? Str::upper((string) $user->preferred_language)],
            ['label' => __('messages.Marketing Updates'), 'value' => $user->is_subscribed ? __('messages.Subscribed') : __('messages.Unsubscribed')],
            ['label' => __('messages.Account Status'), 'value' => $accountStatus],
            ['label' => __('messages.Partner Code'), 'value' => $user->code ?: __('messages.Not Assigned')],
        ];

        return view('frontend.home.profile.index', [
            'title' => $title,
            'profileUser' => $user,
            'countries' => $countryOptions,
            'languageOptions' => $languageOptions,
            'timezoneOptions' => $timezoneOptions,
            'requiredFields' => $coreRequiredFields,
            'businessRecommendedFields' => $businessRecommendedFields,
            'missingFields' => $missingFields,
            'recommendedMissingFields' => $recommendedMissingFields,
            'completionRate' => $completionRate,
            'businessCompletionRate' => $businessCompletionRate,
            'accountStatus' => $accountStatus,
            'accountStatusClass' => $accountStatusClass,
            'avatarUrl' => $avatarUrl,
            'heroStats' => $heroStats,
            'verificationItems' => $verificationItems,
            'contactChannels' => $contactChannels,
            'contactChannelPlatformOptions' => array_values($contactChannelPlatformOptions),
            'companyItems' => $companyItems,
            'locationItems' => $locationItems,
            'preferenceItems' => $preferenceItems,
        ]);
    }

    protected function contactChannelPlatformOptions(): array
    {
        return [
            User::CONTACT_CHANNEL_PLATFORM_WHATSAPP => [
                'value' => User::CONTACT_CHANNEL_PLATFORM_WHATSAPP,
                'label' => __('messages.WhatsApp'),
                'icon' => 'whatsapp',
                'placeholder' => __('messages.Example: +628123456789 or https://wa.me/628123456789'),
            ],
            User::CONTACT_CHANNEL_PLATFORM_WECHAT => [
                'value' => User::CONTACT_CHANNEL_PLATFORM_WECHAT,
                'label' => __('messages.WeChat'),
                'icon' => 'wechat',
                'placeholder' => __('messages.Example: WeChat ID or profile link'),
            ],
            User::CONTACT_CHANNEL_PLATFORM_LINE => [
                'value' => User::CONTACT_CHANNEL_PLATFORM_LINE,
                'label' => __('messages.LINE'),
                'icon' => 'line',
                'placeholder' => __('messages.Example: LINE ID or profile link'),
            ],
            User::CONTACT_CHANNEL_PLATFORM_TELEGRAM => [
                'value' => User::CONTACT_CHANNEL_PLATFORM_TELEGRAM,
                'label' => __('messages.Telegram'),
                'icon' => 'telegram',
                'placeholder' => __('messages.Example: @username or https://t.me/username'),
            ],
            User::CONTACT_CHANNEL_PLATFORM_SKYPE => [
                'value' => User::CONTACT_CHANNEL_PLATFORM_SKYPE,
                'label' => __('messages.Skype'),
                'icon' => 'skype',
                'placeholder' => __('messages.Example: skype.username'),
            ],
            User::CONTACT_CHANNEL_PLATFORM_FACEBOOK => [
                'value' => User::CONTACT_CHANNEL_PLATFORM_FACEBOOK,
                'label' => __('messages.Facebook'),
                'icon' => 'facebook',
                'placeholder' => __('messages.Example: https://facebook.com/yourprofile'),
            ],
            User::CONTACT_CHANNEL_PLATFORM_INSTAGRAM => [
                'value' => User::CONTACT_CHANNEL_PLATFORM_INSTAGRAM,
                'label' => __('messages.Instagram'),
                'icon' => 'instagram',
                'placeholder' => __('messages.Example: https://instagram.com/yourprofile'),
            ],
            User::CONTACT_CHANNEL_PLATFORM_LINKEDIN => [
                'value' => User::CONTACT_CHANNEL_PLATFORM_LINKEDIN,
                'label' => __('messages.LinkedIn'),
                'icon' => 'linkedin',
                'placeholder' => __('messages.Example: https://linkedin.com/in/yourprofile'),
            ],
            User::CONTACT_CHANNEL_PLATFORM_OTHER => [
                'value' => User::CONTACT_CHANNEL_PLATFORM_OTHER,
                'label' => __('messages.Other Chat Account'),
                'icon' => 'chat',
                'placeholder' => __('messages.Example: profile link, username, or direct number'),
            ],
        ];
    }

    protected function contactChannelHref(string $platform, string $value): ?string
    {
        if (blank($value)) {
            return null;
        }

        $trimmed = trim($value);

        if (str_starts_with($trimmed, 'http://') || str_starts_with($trimmed, 'https://')) {
            return $trimmed;
        }

        if ($platform === User::CONTACT_CHANNEL_PLATFORM_WHATSAPP) {
            $digits = preg_replace('/[^0-9]/', '', $trimmed);

            return $digits ? 'https://wa.me/' . $digits : null;
        }

        if ($platform === User::CONTACT_CHANNEL_PLATFORM_TELEGRAM) {
            $username = ltrim($trimmed, '@');

            return $username !== '' ? 'https://t.me/' . $username : null;
        }

        return null;
    }

    protected function countries(): array
    {
        return [
            'Australia',
            'Cambodia',
            'Canada',
            'China',
            'France',
            'Germany',
            'Hong Kong',
            'India',
            'Indonesia',
            'Italy',
            'Japan',
            'Malaysia',
            'Netherlands',
            'New Zealand',
            'Philippines',
            'Singapore',
            'South Korea',
            'Taiwan',
            'Thailand',
            'United Arab Emirates',
            'United Kingdom',
            'United States of America',
            'Vietnam',
        ];
    }

    protected function timezones(): array
    {
        return [
            'Asia/Singapore',
            'Asia/Jakarta',
            'Asia/Hong_Kong',
            'Asia/Shanghai',
            'Asia/Tokyo',
            'Asia/Seoul',
            'Asia/Bangkok',
            'Australia/Sydney',
            'Europe/London',
            'Europe/Paris',
            'America/New_York',
            'America/Los_Angeles',
            'America/Toronto',
            'Pacific/Auckland',
        ];
    }
}
