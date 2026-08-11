<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $settings = [
        'logo_aria' => ['en' => 'Bali Kami Tour homepage', 'zh' => 'Bali Kami Tour 首頁', 'zh-CN' => 'Bali Kami Tour 首页'],
        'highlights_aria' => ['en' => 'Bali Kami Tour partner highlights', 'zh' => 'Bali Kami Tour 合作夥伴亮點', 'zh-CN' => 'Bali Kami Tour 合作伙伴亮点'],
        'highlight_worldwide_agents' => ['en' => 'Worldwide Travel Agents', 'zh' => '全球旅行社', 'zh-CN' => '全球旅行社'],
        'highlight_indonesia_supply' => ['en' => 'Indonesia Expert Supply', 'zh' => '印尼專業供應', 'zh-CN' => '印尼专业供应'],
        'highlight_global_access' => ['en' => 'Global Access', 'zh' => '全球 B2B 合作', 'zh-CN' => '全球 B2B 合作'],
        'contact_title' => ['en' => 'Get In Touch', 'zh' => '聯絡我們', 'zh-CN' => '联系我们'],
        'newsletter_title' => ['en' => 'Newsletter', 'zh' => '電子報', 'zh-CN' => '电子报'],
        'newsletter_copy' => ['en' => 'Stay informed with partner updates, curated offers, and service announcements.', 'zh' => '接收合作夥伴更新、精選優惠與服務公告。', 'zh-CN' => '接收合作伙伴更新、精选优惠与服务公告。'],
        'newsletter_placeholder' => ['en' => 'Enter your email', 'zh' => '輸入您的電子郵件', 'zh-CN' => '输入您的电子邮件'],
        'newsletter_button' => ['en' => 'Subscribe', 'zh' => '訂閱', 'zh-CN' => '订阅'],
        'newsletter_email_label' => ['en' => 'Email', 'zh' => '電子郵件', 'zh-CN' => '电子邮件'],
        'social_title' => ['en' => 'Follow Us', 'zh' => '關注我們', 'zh-CN' => '关注我们'],
        'services_title' => ['en' => 'Our Services', 'zh' => '我們的服務', 'zh-CN' => '我们的服务'],
        'services_aria' => ['en' => 'Footer services', 'zh' => '頁尾服務', 'zh-CN' => '页脚服务'],
        'quick_links_title' => ['en' => 'Quick Links', 'zh' => '快速連結', 'zh-CN' => '快速链接'],
        'quick_links_aria' => ['en' => 'Footer quick links', 'zh' => '頁尾快速連結', 'zh-CN' => '页脚快速链接'],
        'policies_title' => ['en' => 'Policies', 'zh' => '政策', 'zh-CN' => '政策'],
        'policies_aria' => ['en' => 'Footer policies', 'zh' => '頁尾政策', 'zh-CN' => '页脚政策'],
        'platform_title' => ['en' => 'Platform', 'zh' => '平台', 'zh-CN' => '平台'],
        'platform_copy' => ['en' => 'Built for professional B2B travel partners to discover, reserve, and manage premium Indonesia services.', 'zh' => '為全球旅行夥伴提供國際標準的預訂支援、清晰服務存取與值得信賴的在地專業。', 'zh-CN' => '为全球旅行伙伴提供国际标准的预订支持、清晰服务访问与值得信赖的本地专业。'],
        'copyright_suffix' => ['en' => 'All Right Reserved.', 'zh' => '版權所有，保留一切權利。', 'zh-CN' => '版权所有，保留所有权利。'],
    ];

    private array $links = [
        'view.hotels-service' => ['zh' => '住宿安排', 'zh-CN' => '住宿安排'],
        'view.transports-service' => ['zh' => '交通', 'zh-CN' => '交通'],
        'view.tour-packages-service' => ['zh' => '旅遊套餐', 'zh-CN' => '旅游套餐'],
        'about-us' => ['zh' => '關於我們', 'zh-CN' => '关于我们'],
        'contact-us' => ['zh' => '聯絡我們', 'zh-CN' => '联系我们'],
        'services' => ['zh' => '我們的服務', 'zh-CN' => '我们的服务'],
        'terms-and-conditions' => ['zh' => '條款與細則', 'zh-CN' => '条款与细则'],
        'privacy-policy' => ['zh' => '隱私政策', 'zh-CN' => '隐私政策'],
        'faq' => ['zh' => '常見問題', 'zh-CN' => '常见问题'],
    ];

    public function up(): void
    {
        $now = now();

        if (Schema::hasTable('footer_settings')) {
            foreach ($this->settings as $key => $values) {
                DB::table('footer_settings')->updateOrInsert(
                    ['key' => $key],
                    [
                        'value' => $values['en'],
                        'value_traditional' => $values['zh'],
                        'value_simplified' => $values['zh-CN'],
                        'status' => true,
                        'updated_at' => $now,
                        'created_at' => $now,
                    ]
                );
            }
        }

        if (Schema::hasTable('footer_links')) {
            foreach ($this->links as $routeName => $values) {
                DB::table('footer_links')
                    ->where('route_name', $routeName)
                    ->update([
                        'label_traditional' => $values['zh'],
                        'label_simplified' => $values['zh-CN'],
                        'updated_at' => $now,
                    ]);
            }
        }

        $this->clearFooterCache();
    }

    public function down(): void
    {
        if (Schema::hasTable('footer_settings')) {
            DB::table('footer_settings')
                ->whereIn('key', array_keys($this->settings))
                ->update([
                    'value_traditional' => null,
                    'value_simplified' => null,
                    'updated_at' => now(),
                ]);
        }

        if (Schema::hasTable('footer_links')) {
            DB::table('footer_links')
                ->whereIn('route_name', array_keys($this->links))
                ->update([
                    'label_traditional' => null,
                    'label_simplified' => null,
                    'updated_at' => now(),
                ]);
        }

        $this->clearFooterCache();
    }

    private function clearFooterCache(): void
    {
        foreach (['en', 'zh', 'zh-CN'] as $locale) {
            Cache::forget("footer_content.$locale");
        }
    }
};
