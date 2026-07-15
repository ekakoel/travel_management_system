<?php

namespace Tests\Feature;

use App\Models\FooterLink;
use App\Models\FooterSetting;
use App\Services\FooterContentService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class FooterContentLocalizationTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        app(FooterContentService::class)->forget();
    }

    protected function tearDown(): void
    {
        app(FooterContentService::class)->forget();
        app()->setLocale('en');

        parent::tearDown();
    }

    public function test_footer_content_uses_localized_fallback_when_database_translation_is_empty(): void
    {
        app()->setLocale('zh-CN');

        FooterSetting::updateOrCreate(
            ['key' => 'contact_title'],
            [
                'value' => 'Get In Touch',
                'value_traditional' => null,
                'value_simplified' => null,
                'status' => true,
            ]
        );

        FooterLink::updateOrCreate(
            [
                'group' => 'services',
                'label' => 'Accommodations',
            ],
            [
                'label_traditional' => null,
                'label_simplified' => null,
                'route_name' => 'view.accommodation-service',
                'sort_order' => 10,
                'open_new_tab' => false,
                'status' => true,
            ]
        );

        $footerData = app(FooterContentService::class)->data();

        $this->assertSame('联系我们', $footerData['contact']['title']);
        $this->assertSame('电子邮件', $footerData['newsletter']['email_label']);
        $servicesSection = collect($footerData['link_sections'])->firstWhere('title', '我们的服务');

        $this->assertNotNull($servicesSection);
        $this->assertTrue(collect($servicesSection['links'])->contains('label', '住宿安排'));
    }

    public function test_footer_content_prefers_database_localized_value_when_available(): void
    {
        app()->setLocale('zh');

        FooterSetting::updateOrCreate(
            ['key' => 'contact_title'],
            [
                'value' => 'Get In Touch',
                'value_traditional' => '自訂聯絡標題',
                'value_simplified' => null,
                'status' => true,
            ]
        );

        $footerData = app(FooterContentService::class)->data();

        $this->assertSame('自訂聯絡標題', $footerData['contact']['title']);
    }
}
