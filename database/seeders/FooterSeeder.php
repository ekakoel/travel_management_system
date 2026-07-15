<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class FooterSeeder extends Seeder
{
    public function run()
    {
        $now = now();

        $settings = [
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

        foreach ($settings as $key => $values) {
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

        $links = [
            ['group' => 'services', 'label' => 'Accommodations', 'label_traditional' => '住宿安排', 'label_simplified' => '住宿安排', 'route_name' => 'view.accommodation-service', 'sort_order' => 10],
            ['group' => 'services', 'label' => 'Transports', 'label_traditional' => '交通', 'label_simplified' => '交通', 'route_name' => 'view.transport-service', 'sort_order' => 20],
            ['group' => 'services', 'label' => 'Tour Packages', 'label_traditional' => '旅遊套餐', 'label_simplified' => '旅游套餐', 'route_name' => 'view.tour-package-services', 'sort_order' => 30],
            ['group' => 'quick_links', 'label' => 'About Us', 'label_traditional' => '關於我們', 'label_simplified' => '关于我们', 'route_name' => 'about-us', 'sort_order' => 10],
            ['group' => 'quick_links', 'label' => 'Contact Us', 'label_traditional' => '聯絡我們', 'label_simplified' => '联系我们', 'route_name' => 'contact-us', 'sort_order' => 20],
            ['group' => 'quick_links', 'label' => 'Our Services', 'label_traditional' => '我們的服務', 'label_simplified' => '我们的服务', 'route_name' => 'services', 'sort_order' => 30],
            ['group' => 'policies', 'label' => 'Terms & Conditions', 'label_traditional' => '條款與細則', 'label_simplified' => '条款与细则', 'route_name' => 'terms-and-conditions', 'sort_order' => 10],
            ['group' => 'policies', 'label' => 'Privacy Policy', 'label_traditional' => '隱私政策', 'label_simplified' => '隐私政策', 'route_name' => 'privacy-policy', 'sort_order' => 20],
            ['group' => 'policies', 'label' => 'FAQs', 'label_traditional' => '常見問題', 'label_simplified' => '常见问题', 'route_name' => 'faq', 'sort_order' => 30],
        ];

        foreach ($links as $link) {
            DB::table('footer_links')->updateOrInsert(
                [
                    'group' => $link['group'],
                    'label' => $link['label'],
                ],
                array_merge($link, [
                    'status' => true,
                    'updated_at' => $now,
                    'created_at' => $now,
                ])
            );
        }

        foreach (['en', 'zh', 'zh-CN'] as $locale) {
            Cache::forget("footer_content.$locale");
        }
    }
}
