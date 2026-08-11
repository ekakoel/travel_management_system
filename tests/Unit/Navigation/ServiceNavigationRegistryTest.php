<?php

namespace Tests\Unit\Navigation;

use App\Models\Services;
use App\Services\Navigation\ServiceNavigationRegistry;
use Tests\TestCase;

class ServiceNavigationRegistryTest extends TestCase
{
    public function test_tour_package_canonical_and_legacy_slugs_resolve_to_the_same_routes(): void
    {
        $registry = app(ServiceNavigationRegistry::class);

        foreach (['tour-packages', 'tours'] as $slug) {
            $item = $registry->item(new Services([
                'name' => 'Tour Packages',
                'nicname' => $slug,
                'icon' => '<i class="icon-copy fa fa-briefcase" aria-hidden="true"></i>',
                'status' => 'Active',
            ]));

            $this->assertSame('tour-packages', $item['canonical_slug']);
            $this->assertSame('Tour Packages', $item['label']);
            $this->assertSame('view.tour-packages-service', $item['public_route']);
            $this->assertSame('admin.tour-packages.index', $item['admin_route']);
            $this->assertSame('Tours', $item['content_key']);
            $this->assertSame('icon-copy fa fa-briefcase', $item['icon']);
            $this->assertTrue($item['navigation_ready']);
        }
    }

    public function test_unknown_service_does_not_publish_broken_navigation_routes(): void
    {
        $item = app(ServiceNavigationRegistry::class)->item(new Services([
            'name' => 'Future Service',
            'nicname' => 'future-service',
            'icon' => 'fa fa-star',
            'status' => 'Active',
        ]));

        $this->assertNull($item['public_route']);
        $this->assertNull($item['admin_route']);
        $this->assertSame('Future Service', $item['label']);
        $this->assertFalse($item['navigation_ready']);
    }
}
