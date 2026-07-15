<?php

namespace App\Services;

use App\Models\FooterLink;
use App\Models\FooterSetting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class FooterContentService
{
    private const FOOTER_SETTING_FALLBACKS = [
        'en' => [
            'logo_aria' => 'Bali Kami Tour homepage',
            'highlights_aria' => 'Bali Kami Tour partner highlights',
            'highlight_worldwide_agents' => 'Worldwide Travel Agents',
            'highlight_indonesia_supply' => 'Indonesia Expert Supply',
            'highlight_global_access' => 'Global Access',
            'contact_title' => 'Get In Touch',
            'newsletter_title' => 'Newsletter',
            'newsletter_copy' => 'Stay informed with partner updates, curated offers, and service announcements.',
            'newsletter_placeholder' => 'Enter your email',
            'newsletter_button' => 'Subscribe',
            'newsletter_email_label' => 'Email',
            'social_title' => 'Follow Us',
            'services_title' => 'Our Services',
            'services_aria' => 'Footer services',
            'quick_links_title' => 'Quick Links',
            'quick_links_aria' => 'Footer quick links',
            'policies_title' => 'Policies',
            'policies_aria' => 'Footer policies',
            'platform_title' => 'Platform',
            'platform_copy' => 'Built for professional B2B travel partners to discover, reserve, and manage premium Indonesia services.',
            'copyright_suffix' => 'All Right Reserved.',
            'phone_fallback' => 'Business Phone',
            'website_label_fallback' => 'B2B Travel Agent Services',
        ],
        'zh' => [
            'logo_aria' => 'Bali Kami Tour 首頁',
            'highlights_aria' => 'Bali Kami Tour 合作夥伴亮點',
            'highlight_worldwide_agents' => '全球旅行社',
            'highlight_indonesia_supply' => '印尼專業供應',
            'highlight_global_access' => '全球 B2B 合作',
            'contact_title' => '聯絡我們',
            'newsletter_title' => '電子報',
            'newsletter_copy' => '接收合作夥伴更新、精選優惠與服務公告。',
            'newsletter_placeholder' => '輸入您的電子郵件',
            'newsletter_button' => '訂閱',
            'newsletter_email_label' => '電子郵件',
            'social_title' => '關注我們',
            'services_title' => '我們的服務',
            'services_aria' => '頁尾服務',
            'quick_links_title' => '快速連結',
            'quick_links_aria' => '頁尾快速連結',
            'policies_title' => '政策',
            'policies_aria' => '頁尾政策',
            'platform_title' => '平台',
            'platform_copy' => '為全球旅行夥伴提供國際標準的預訂支援、清晰服務存取與值得信賴的在地專業。',
            'copyright_suffix' => '版權所有，保留一切權利。',
            'phone_fallback' => '公司電話',
            'website_label_fallback' => 'B2B 旅行夥伴服務',
        ],
        'zh-CN' => [
            'logo_aria' => 'Bali Kami Tour 首页',
            'highlights_aria' => 'Bali Kami Tour 合作伙伴亮点',
            'highlight_worldwide_agents' => '全球旅行社',
            'highlight_indonesia_supply' => '印尼专业供应',
            'highlight_global_access' => '全球 B2B 合作',
            'contact_title' => '联系我们',
            'newsletter_title' => '电子报',
            'newsletter_copy' => '接收合作伙伴更新、精选优惠与服务公告。',
            'newsletter_placeholder' => '输入您的电子邮件',
            'newsletter_button' => '订阅',
            'newsletter_email_label' => '电子邮件',
            'social_title' => '关注我们',
            'services_title' => '我们的服务',
            'services_aria' => '页脚服务',
            'quick_links_title' => '快速链接',
            'quick_links_aria' => '页脚快速链接',
            'policies_title' => '政策',
            'policies_aria' => '页脚政策',
            'platform_title' => '平台',
            'platform_copy' => '为全球旅行伙伴提供国际标准的预订支持、清晰服务访问与值得信赖的本地专业。',
            'copyright_suffix' => '版权所有，保留所有权利。',
            'phone_fallback' => '公司电话',
            'website_label_fallback' => 'B2B 旅行伙伴服务',
        ],
    ];

    private const FOOTER_LINK_LABEL_FALLBACKS = [
        'view.accommodation-service' => [
            'en' => 'Accommodations',
            'zh' => '住宿安排',
            'zh-CN' => '住宿安排',
        ],
        'view.transport-service' => [
            'en' => 'Transports',
            'zh' => '交通',
            'zh-CN' => '交通',
        ],
        'view.tour-package-services' => [
            'en' => 'Tour Packages',
            'zh' => '旅遊套餐',
            'zh-CN' => '旅游套餐',
        ],
        'about-us' => [
            'en' => 'About Us',
            'zh' => '關於我們',
            'zh-CN' => '关于我们',
        ],
        'contact-us' => [
            'en' => 'Contact Us',
            'zh' => '聯絡我們',
            'zh-CN' => '联系我们',
        ],
        'services' => [
            'en' => 'Our Services',
            'zh' => '我們的服務',
            'zh-CN' => '我们的服务',
        ],
        'terms-and-conditions' => [
            'en' => 'Terms & Conditions',
            'zh' => '條款與細則',
            'zh-CN' => '条款与细则',
        ],
        'privacy-policy' => [
            'en' => 'Privacy Policy',
            'zh' => '隱私政策',
            'zh-CN' => '隐私政策',
        ],
        'faq' => [
            'en' => 'FAQs',
            'zh' => '常見問題',
            'zh-CN' => '常见问题',
        ],
    ];

    protected BusinessProfileService $businessProfileService;

    public function __construct(BusinessProfileService $businessProfileService)
    {
        $this->businessProfileService = $businessProfileService;
    }

    public function data(): array
    {
        $locale = app()->getLocale();

        return Cache::remember("footer_content.$locale", 3600, function () use ($locale) {
            $businessProfile = $this->businessProfileService->primary();
            $settings = FooterSetting::query()
                ->where('status', true)
                ->get()
                ->keyBy('key');
            $links = FooterLink::query()
                ->where('status', true)
                ->orderBy('group')
                ->orderBy('sort_order')
                ->orderBy('label')
                ->get()
                ->groupBy('group');

            $setting = fn (string $key, string $fallback = '') => $this->localizedSetting($settings->get($key), $this->footerFallback($key, $fallback, $locale), $locale);
            $profile = fn (string $field, $fallback = null) => $this->profileValue($businessProfile, $field, $fallback);

            $phone = collect([
                $profile('phone'),
                $profile('phone_2'),
                $profile('phone_3'),
            ])->filter()->implode(' / ');
            $phone = $phone ?: $setting('phone_fallback', 'Business Phone');

            $website = $profile('website', $setting('website_label_fallback', 'B2B Travel Agent Services'));

            return [
                'brand' => [
                    'name' => $profile('nickname', $profile('name', config('app.business', config('app.name', 'Bali Kami Tour')))),
                    'logo_url' => $this->assetUrl($profile('logo_dark', $profile('logo', config('app.logo_img_white')))),
                    'logo_aria' => $setting('logo_aria', 'Bali Kami Tour homepage'),
                    'tagline' => $this->localizedProfileValue($businessProfile, 'public_tagline', __('home.footer.tagline'), $locale),
                    'description' => $this->localizedProfileValue($businessProfile, 'public_description', __('home.footer.description'), $locale),
                    'trust_aria' => $setting('highlights_aria', 'Bali Kami Tour partner highlights'),
                    'trust_items' => [
                        $setting('highlight_worldwide_agents', 'Worldwide Travel Agents'),
                        $setting('highlight_indonesia_supply', 'Indonesia Expert Supply'),
                        $setting('highlight_global_access', 'Global Access'),
                    ],
                ],
                'contact' => [
                    'title' => $setting('contact_title', 'Get In Touch'),
                    'address' => $profile('address', __('home.footer.address')),
                    'phone' => $phone,
                    'phone_href' => preg_replace('/[^0-9+]/', '', explode('/', $phone)[0] ?? $phone),
                    'email' => $profile('email', 'e-admin@balikamitour.com'),
                ],
                'newsletter' => [
                    'title' => $setting('newsletter_title', 'Newsletter'),
                    'copy' => $setting('newsletter_copy', 'Stay informed with partner updates, curated offers, and service announcements.'),
                    'placeholder' => $setting('newsletter_placeholder', 'Enter your email'),
                    'button' => $setting('newsletter_button', 'Subscribe'),
                    'email_label' => $setting('newsletter_email_label', 'Email'),
                ],
                'social' => [
                    'title' => $setting('social_title', 'Follow Us'),
                    'links' => $this->socialLinks($businessProfile),
                ],
                'link_sections' => [
                    [
                        'title' => $setting('services_title', 'Our Services'),
                        'aria' => $setting('services_aria', 'Footer services'),
                        'links' => $this->footerLinks($links->get('services', collect()), $locale),
                    ],
                    [
                        'title' => $setting('quick_links_title', 'Quick Links'),
                        'aria' => $setting('quick_links_aria', 'Footer quick links'),
                        'links' => $this->footerLinks($links->get('quick_links', collect()), $locale),
                    ],
                    [
                        'title' => $setting('policies_title', 'Policies'),
                        'aria' => $setting('policies_aria', 'Footer policies'),
                        'links' => $this->footerLinks($links->get('policies', collect()), $locale),
                    ],
                ],
                'platform' => [
                    'title' => $setting('platform_title', 'Platform'),
                    'copy' => $setting('platform_copy', 'Built for professional B2B travel partners to discover, reserve, and manage premium Indonesia services.'),
                ],
                'copyright' => [
                    'year' => now()->year,
                    'website_label' => $website,
                    'website_url' => Str::startsWith($website, ['http://', 'https://']) ? $website : 'https://' . $website,
                    'suffix' => $setting('copyright_suffix', 'All Right Reserved.'),
                ],
            ];
        });
    }

    public function forget(): void
    {
        foreach (['en', 'zh', 'zh-CN'] as $locale) {
            Cache::forget("footer_content.$locale");
        }
    }

    protected function profileValue($businessProfile, string $field, $fallback = null)
    {
        $value = trim((string) ($businessProfile->{$field} ?? ''));

        return $value !== '' && $value !== '-' ? $value : $fallback;
    }

    protected function localizedProfileValue($businessProfile, string $field, string $fallback, string $locale): string
    {
        $localizedField = match ($locale) {
            'zh' => "{$field}_traditional",
            'zh-CN' => "{$field}_simplified",
            default => $field,
        };

        return $this->profileValue($businessProfile, $localizedField, $this->profileValue($businessProfile, $field, $fallback));
    }

    protected function localizedSetting($setting, string $fallback, string $locale): string
    {
        if (!$setting) {
            return $fallback;
        }

        $field = match ($locale) {
            'zh' => 'value_traditional',
            'zh-CN' => 'value_simplified',
            default => 'value',
        };

        $value = trim((string) ($setting->{$field} ?? ''));

        if (($value === '' || $value === '-') && $locale !== 'en' && $fallback !== '') {
            return $fallback;
        }

        if ($value === '' || $value === '-') {
            $value = trim((string) ($setting->value ?? ''));
        }

        return $value !== '' && $value !== '-' ? $value : $fallback;
    }

    protected function footerFallback(string $key, string $fallback, string $locale): string
    {
        return self::FOOTER_SETTING_FALLBACKS[$locale][$key]
            ?? self::FOOTER_SETTING_FALLBACKS['en'][$key]
            ?? $fallback;
    }

    protected function assetUrl(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        return Str::startsWith($path, ['http://', 'https://', '/'])
            ? $path
            : asset('storage/logo/' . ltrim($path, '/'));
    }

    protected function socialLinks($businessProfile): array
    {
        return collect([
            ['url' => $this->profileValue($businessProfile, 'facebook'), 'label' => 'Facebook', 'icon' => 'fab fa-facebook-f'],
            ['url' => $this->profileValue($businessProfile, 'instagram'), 'label' => 'Instagram', 'icon' => 'fab fa-instagram'],
            ['url' => $this->profileValue($businessProfile, 'youtube'), 'label' => 'YouTube', 'icon' => 'fab fa-youtube'],
            ['url' => $this->profileValue($businessProfile, 'linkedin'), 'label' => 'LinkedIn', 'icon' => 'fab fa-linkedin-in'],
        ])->filter(fn ($link) => filled($link['url']))->values()->all();
    }

    protected function footerLinks($links, string $locale): array
    {
        return $links->map(function ($link) use ($locale) {
            $label = $this->localizedFooterLinkLabel($link, $locale);

            return [
                'label' => $label,
                'url' => $this->resolveLinkUrl($link),
                'target' => $link->open_new_tab ? '_blank' : null,
                'rel' => $link->open_new_tab ? 'noopener noreferrer' : null,
            ];
        })->filter(fn ($link) => filled($link['url']))->values()->all();
    }

    protected function resolveLinkUrl($link): ?string
    {
        if ($link->route_name && Route::has($link->route_name)) {
            return route($link->route_name);
        }

        return $link->url;
    }

    protected function localizedFooterLinkLabel($link, string $locale): string
    {
        $localizedLabel = match ($locale) {
            'zh' => $link->label_traditional,
            'zh-CN' => $link->label_simplified,
            default => $link->label,
        };

        $localizedLabel = trim((string) $localizedLabel);

        if ($localizedLabel !== '' && $localizedLabel !== '-') {
            return $localizedLabel;
        }

        if ($locale !== 'en' && $link->route_name) {
            return self::FOOTER_LINK_LABEL_FALLBACKS[$link->route_name][$locale]
                ?? self::FOOTER_LINK_LABEL_FALLBACKS[$link->route_name]['en']
                ?? $link->label;
        }

        return $link->label;
    }
}
