<?php

namespace App\Services\Navigation;

use App\Models\Services;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class ServiceNavigationRegistry
{
    private const DEFINITIONS = [
        'activities' => [
            'aliases' => ['activities', 'activity'],
            'public_route' => 'view.activities-service',
            'admin_route' => 'admin.activities.index',
            'admin_active' => ['admin.activities.*'],
            'content_key' => 'Activities',
        ],
        'hotels' => [
            'aliases' => ['hotels', 'hotel', 'accommodations', 'accommodation'],
            'public_route' => 'view.hotels-service',
            'admin_route' => 'admin.hotels.index',
            'admin_active' => ['admin.hotels.*'],
            'content_key' => 'Hotels',
        ],
        'tour-packages' => [
            'aliases' => ['tour-packages', 'tour-package', 'tours', 'tour'],
            'public_route' => 'view.tour-packages-service',
            'admin_route' => 'admin.tour-packages.index',
            'admin_active' => ['admin.tour-packages.*', 'admin.tours.*'],
            'content_key' => 'Tours',
        ],
        'transports' => [
            'aliases' => ['transports', 'transport', 'transportations', 'transportation'],
            'public_route' => 'view.transports-service',
            'admin_route' => 'admin.transports.index',
            'admin_active' => ['admin.transports.*'],
            'content_key' => 'Transports',
        ],
        'villas' => [
            'aliases' => ['villas', 'villa'],
            'public_route' => 'view.villas-service',
            'admin_route' => 'admin.villas.index',
            'admin_active' => ['admin.villas.*'],
            'content_key' => 'Villas',
        ],
        'weddings' => [
            'aliases' => ['weddings', 'wedding'],
            'public_route' => 'view.weddings',
            'admin_route' => 'weddings-admin.index',
            'admin_active' => ['weddings-admin.*'],
            'content_key' => 'Weddings',
        ],
    ];

    public function items(Collection $services): Collection
    {
        return $services
            ->map(fn (Services $service) => $this->item($service))
            ->values();
    }

    public function item(Services $service): array
    {
        $definition = $this->definition($service->nicname);
        $publicRoute = $this->availableRoute($definition['public_route']);
        $adminRoute = $this->availableRoute($definition['admin_route']);

        return [
            'id' => $service->getKey(),
            'name' => $service->name,
            'label' => $this->translatedName($service->name),
            'slug' => $service->nicname,
            'canonical_slug' => $definition['canonical_slug'],
            'icon' => $this->iconClass($service->icon),
            'public_route' => $publicRoute,
            'admin_route' => $adminRoute,
            'public_navigation_ready' => $publicRoute !== null,
            'admin_navigation_ready' => $adminRoute !== null,
            'admin_active' => $definition['admin_active'],
            'content_key' => $definition['content_key'],
            'navigation_ready' => $publicRoute !== null && $adminRoute !== null,
        ];
    }

    public function definition(?string $slug): array
    {
        $normalized = Str::slug((string) $slug);

        foreach (self::DEFINITIONS as $canonical => $definition) {
            if ($normalized === $canonical || in_array($normalized, $definition['aliases'], true)) {
                return ['canonical_slug' => $canonical, ...$definition];
            }
        }

        return [
            'canonical_slug' => $normalized,
            'aliases' => [$normalized],
            'public_route' => $normalized !== '' ? "view.{$normalized}-service" : null,
            'admin_route' => $normalized !== '' ? "admin.{$normalized}.index" : null,
            'admin_active' => $normalized !== '' ? ["admin.{$normalized}.*"] : [],
            'content_key' => null,
        ];
    }

    public function iconClass(?string $icon): string
    {
        $value = trim((string) $icon);

        if (preg_match('/class=["\']([^"\']+)["\']/i', $value, $matches)) {
            return trim($matches[1]);
        }

        return trim(strip_tags($value));
    }

    private function translatedName(?string $name): string
    {
        $value = trim((string) $name);
        $key = "messages.{$value}";
        $translation = __($key);

        return $translation === $key ? $value : $translation;
    }

    private function availableRoute(?string $route): ?string
    {
        return $route && Route::has($route) ? $route : null;
    }
}
