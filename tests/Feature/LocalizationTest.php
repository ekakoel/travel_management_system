<?php

namespace Tests\Feature;

use Tests\TestCase;

class LocalizationTest extends TestCase
{
    public function test_language_switch_redirects_to_safe_frontend_url_and_stores_locale()
    {
        $targetUrl = '/hotel-price-demo-hotel?checkin=2026-07-30&checkout=2026-08-02';

        $response = $this->get(route('language.switch', ['locale' => 'zh']) . '?redirect=' . urlencode($targetUrl));

        $response->assertRedirect($targetUrl);
        $response->assertSessionHas('locale', 'zh');
    }

    public function test_language_switch_rejects_external_redirects_and_falls_back_to_home()
    {
        $response = $this
            ->from('/accommodation/test-hotel')
            ->get(route('language.switch', ['locale' => 'zh-CN']) . '?redirect=' . urlencode('https://example.com/evil'));

        $response->assertRedirect('/accommodation/test-hotel');
        $response->assertSessionHas('locale', 'zh-CN');
    }
}
