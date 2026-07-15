<?php

namespace Tests\Feature;

use App\Models\Activities;
use App\Models\BusinessProfile;
use App\Models\Hotels;
use App\Models\ManualBook;
use App\Models\OptionalRate;
use App\Models\Orders;
use App\Models\Services;
use App\Models\Tax;
use App\Models\Tours;
use App\Models\TourPrices;
use App\Models\TransportPrice;
use App\Models\Transports;
use App\Models\UsdRates;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ProjectStructureStandardTest extends TestCase
{
    use DatabaseTransactions;

    public function test_project_structure_documentation_and_migration_tracker_exist(): void
    {
        $this->assertFileExists(base_path('docs/project-structure-standard.md'));
        $this->assertFileExists(base_path('docs/project-structure-migration-todo.md'));

        $standard = file_get_contents(base_path('docs/project-structure-standard.md'));
        $tracker = file_get_contents(base_path('docs/project-structure-migration-todo.md'));

        $this->assertStringContainsString('frontend/landing-page', $standard);
        $this->assertStringContainsString('frontend/home', $standard);
        $this->assertStringContainsString('Guard Rule Untuk File Baru', $standard);
        $this->assertStringContainsString('Transport Domain Inventory', $tracker);
        $this->assertStringContainsString('Next Execution Plan', $tracker);
    }

    public function test_frontend_and_backend_target_directories_are_seeded(): void
    {
        $requiredPaths = [
            resource_path('views/frontend/landing-page/transports/.gitkeep'),
            resource_path('views/frontend/home/orders/.gitkeep'),
            resource_path('views/frontend/home/profile/.gitkeep'),
            resource_path('views/frontend/shared/.gitkeep'),
            resource_path('frontend/js/landing-page/transports/.gitkeep'),
            resource_path('frontend/js/home/orders/.gitkeep'),
            resource_path('frontend/js/home/profile/.gitkeep'),
            resource_path('frontend/js/shared/.gitkeep'),
            resource_path('frontend/scss/landing-page/transports/.gitkeep'),
            resource_path('frontend/scss/home/orders/.gitkeep'),
            resource_path('frontend/scss/home/profile/.gitkeep'),
            resource_path('frontend/scss/shared/.gitkeep'),
            resource_path('views/backend/operations/.gitkeep'),
            resource_path('views/backend/sales/.gitkeep'),
            resource_path('backend/js/admin/.gitkeep'),
            resource_path('backend/js/operations/.gitkeep'),
            resource_path('backend/js/sales/.gitkeep'),
            resource_path('backend/scss/admin/.gitkeep'),
            resource_path('backend/scss/operations/.gitkeep'),
            resource_path('backend/scss/sales/.gitkeep'),
        ];

        foreach ($requiredPaths as $path) {
            $this->assertFileExists($path);
        }
    }

    public function test_transport_public_views_are_routed_to_landing_page_structure(): void
    {
        $frontEndController = file_get_contents(app_path('Http/Controllers/FrontEndController.php'));
        $homeController = file_get_contents(app_path('Http/Controllers/HomeController.php'));

        $this->assertFileExists(resource_path('views/frontend/landing-page/transports/index.blade.php'));
        $this->assertFileExists(resource_path('views/frontend/landing-page/transports/detail.blade.php'));
        $this->assertStringContainsString("view('frontend.landing-page.transports.index'", $frontEndController);
        $this->assertStringContainsString("view('frontend.landing-page.transports.index'", $homeController);
        $this->assertStringContainsString("view('frontend.landing-page.transports.detail'", $homeController);
        $this->assertStringNotContainsString("view('home.landing-page.transport'", $frontEndController);
        $this->assertStringNotContainsString("view('home.transports.detail'", $homeController);
    }

    public function test_transport_public_assets_are_sourced_from_landing_page_structure(): void
    {
        $mix = file_get_contents(base_path('webpack.mix.js'));

        $this->assertFileExists(resource_path('frontend/js/landing-page/transports/index.js'));
        $this->assertFileExists(resource_path('frontend/js/landing-page/transports/detail.js'));
        $this->assertFileExists(resource_path('frontend/scss/landing-page/transports/index-entry.scss'));
        $this->assertFileExists(resource_path('frontend/scss/landing-page/transports/_index.scss'));
        $this->assertFileExists(resource_path('frontend/scss/landing-page/transports/detail-entry.scss'));
        $this->assertFileExists(resource_path('frontend/scss/landing-page/transports/_detail.scss'));
        $this->assertStringContainsString("resources/frontend/js/landing-page/transports/index.js", $mix);
        $this->assertStringContainsString("resources/frontend/js/landing-page/transports/detail.js", $mix);
        $this->assertStringContainsString("resources/frontend/scss/landing-page/transports/index-entry.scss", $mix);
        $this->assertStringContainsString("resources/frontend/scss/landing-page/transports/detail-entry.scss", $mix);
        $this->assertStringNotContainsString("resources/frontend/js/pages/transportations-index.js", $mix);
        $this->assertStringNotContainsString("resources/frontend/js/pages/transport-detail.js", $mix);
    }

    public function test_transport_directory_route_renders_landing_page_view(): void
    {
        $response = $this->get(route('view.transport-service'));

        $response->assertOk();
        $response->assertViewIs('frontend.landing-page.transports.index');
        $response->assertViewHasAll([
            'transports',
            'types',
            'brands',
            'directoryStats',
            'searchName',
            'searchType',
            'searchBrand',
            'minimumCapacity',
        ]);
    }

    public function test_transport_detail_route_renders_landing_page_view(): void
    {
        $transport = Transports::create([
            'name' => 'Route Test Transport',
            'code' => 'RTT-' . uniqid(),
            'type' => 'Daily Rent',
            'brand' => 'Route Brand',
            'description' => 'Route test transport description.',
            'include' => 'Driver and fuel',
            'additional_info' => 'Route test additional information.',
            'cancellation_policy' => 'Route test cancellation policy.',
            'capacity' => '5',
            'cover' => 'default.webp',
            'status' => 'Active',
            'author_id' => 1,
        ]);

        TransportPrice::create([
            'transports_id' => $transport->id,
            'type' => 'Daily Rent',
            'src' => 'Hotel',
            'dst' => 'Ubud',
            'duration' => 10,
            'contract_rate' => 500000,
            'markup' => 10,
            'extra_time' => 50000,
            'additional_info' => 'Route test price.',
            'author_id' => 1,
        ]);

        $response = $this->get(route('transport.show', $transport->id));

        $response->assertOk();
        $response->assertViewIs('frontend.landing-page.transports.detail');
        $response->assertViewHasAll([
            'transport',
            'prices',
            'priceGroups',
            'similarTransports',
            'orderNumber',
            'agents',
            'transportOrderNumbersByAgent',
        ]);
    }

    public function test_activity_public_views_are_routed_to_landing_page_structure(): void
    {
        $frontEndController = file_get_contents(app_path('Http/Controllers/FrontEndController.php'));

        $this->assertFileExists(resource_path('views/frontend/landing-page/activities/index.blade.php'));
        $this->assertFileExists(resource_path('views/frontend/landing-page/activities/detail.blade.php'));
        $this->assertStringContainsString("view('frontend.landing-page.activities.index'", $frontEndController);
        $this->assertStringContainsString("view('frontend.landing-page.activities.detail'", $frontEndController);
        $this->assertStringNotContainsString("view('frontend.activities.index'", $frontEndController);
        $this->assertStringNotContainsString("view('frontend.activities.detail'", $frontEndController);
    }

    public function test_activity_public_assets_are_sourced_from_landing_page_structure(): void
    {
        $mix = file_get_contents(base_path('webpack.mix.js'));

        $this->assertFileExists(resource_path('frontend/js/landing-page/activities/index.js'));
        $this->assertFileExists(resource_path('frontend/js/landing-page/activities/detail.js'));
        $this->assertFileExists(resource_path('frontend/scss/landing-page/activities/index-entry.scss'));
        $this->assertFileExists(resource_path('frontend/scss/landing-page/activities/_index.scss'));
        $this->assertFileExists(resource_path('frontend/scss/landing-page/activities/detail-entry.scss'));
        $this->assertFileExists(resource_path('frontend/scss/landing-page/activities/_detail.scss'));
        $this->assertStringContainsString("resources/frontend/js/landing-page/activities/index.js", $mix);
        $this->assertStringContainsString("resources/frontend/js/landing-page/activities/detail.js", $mix);
        $this->assertStringContainsString("resources/frontend/scss/landing-page/activities/index-entry.scss", $mix);
        $this->assertStringContainsString("resources/frontend/scss/landing-page/activities/detail-entry.scss", $mix);
        $this->assertStringNotContainsString("resources/frontend/js/pages/activities-index.js", $mix);
        $this->assertStringNotContainsString("resources/frontend/js/pages/activity-detail.js", $mix);
    }

    public function test_activity_directory_route_renders_landing_page_view(): void
    {
        $response = $this->get(route('view.activity-services'));

        $response->assertOk();
        $response->assertViewIs('frontend.landing-page.activities.index');
        $response->assertViewHasAll([
            'activities',
            'locationOptions',
            'typeOptions',
            'searchName',
            'searchLocation',
            'searchType',
            'featuredActivity',
            'directoryStats',
        ]);
    }

    public function test_activity_detail_route_renders_landing_page_view(): void
    {
        $activity = Activities::create([
            'name' => 'Route Test Activity',
            'code' => 'RTA-' . uniqid(),
            'type' => 'Adventure',
            'location' => 'Ubud',
            'map' => 'https://example.test/map',
            'description' => 'Route test activity description.',
            'itinerary' => 'Route test itinerary.',
            'duration' => '2 hours',
            'include' => 'Guide',
            'additional_info' => 'Route test additional information.',
            'cancellation_policy' => 'Route test cancellation policy.',
            'contract_rate' => 250000,
            'markup' => 10,
            'qty' => '10',
            'min_pax' => '1',
            'status' => 'Active',
            'validity' => now()->addMonth()->toDateString(),
            'author_id' => 1,
            'cover' => 'default.webp',
        ]);

        $response = $this->get(route('view.activity-public-detail', $activity->code));

        $response->assertOk();
        $response->assertViewIs('frontend.landing-page.activities.detail');
        $response->assertViewHasAll([
            'activity',
            'galleryImages',
            'activitySections',
            'summaryStats',
            'overviewFacts',
            'sidebarFacts',
            'nearActivities',
            'activityOrderForm',
        ]);
    }

    public function test_tour_public_views_are_routed_to_landing_page_structure(): void
    {
        $frontEndController = file_get_contents(app_path('Http/Controllers/FrontEndController.php'));
        $toursController = file_get_contents(app_path('Http/Controllers/ToursController.php'));

        $this->assertFileExists(resource_path('views/frontend/landing-page/tours/directory.blade.php'));
        $this->assertFileExists(resource_path('views/frontend/landing-page/tours/detail.blade.php'));
        $this->assertStringContainsString("view('frontend.landing-page.tours.directory'", $frontEndController);
        $this->assertStringContainsString("view('frontend.landing-page.tours.detail'", $toursController);
        $this->assertStringNotContainsString("view('frontend.tours.directory'", $frontEndController);
        $this->assertStringNotContainsString("view('frontend.tours.detail-modern'", $toursController);
    }

    public function test_tour_public_assets_are_sourced_from_landing_page_structure(): void
    {
        $mix = file_get_contents(base_path('webpack.mix.js'));

        $this->assertFileExists(resource_path('frontend/js/landing-page/tours/index.js'));
        $this->assertFileExists(resource_path('frontend/js/landing-page/tours/detail.js'));
        $this->assertFileExists(resource_path('frontend/scss/landing-page/tours/index-entry.scss'));
        $this->assertFileExists(resource_path('frontend/scss/landing-page/tours/_directory.scss'));
        $this->assertFileExists(resource_path('frontend/scss/landing-page/tours/detail-entry.scss'));
        $this->assertFileExists(resource_path('frontend/scss/landing-page/tours/_detail.scss'));
        $this->assertStringContainsString("resources/frontend/js/landing-page/tours/index.js", $mix);
        $this->assertStringContainsString("resources/frontend/js/landing-page/tours/detail.js", $mix);
        $this->assertStringContainsString("resources/frontend/scss/landing-page/tours/index-entry.scss", $mix);
        $this->assertStringContainsString("resources/frontend/scss/landing-page/tours/detail-entry.scss", $mix);
        $this->assertStringNotContainsString("resources/frontend/js/pages/tour-packages-index.js", $mix);
        $this->assertStringNotContainsString("resources/frontend/js/pages/tour-detail.js", $mix);
    }

    public function test_tour_directory_route_renders_landing_page_view(): void
    {
        $response = $this->get(route('view.tour-package-services'));

        $response->assertOk();
        $response->assertViewIs('frontend.landing-page.tours.directory');
        $response->assertViewHasAll([
            'tours',
            'areaOptions',
            'typeOptions',
            'searchName',
            'searchArea',
            'searchType',
            'featuredTour',
            'directoryStats',
        ]);
    }

    public function test_tour_detail_route_renders_landing_page_view(): void
    {
        $tourData = [
            'name' => 'Route Test Tour',
            'code' => 'RTT-' . uniqid(),
            'name_traditional' => 'Route Test Tour',
            'name_simplified' => 'Route Test Tour',
            'slug' => 'route-test-tour-' . uniqid(),
            'cover' => 'default.webp',
            'short_description' => 'Route test tour short description.',
            'description' => 'Route test tour description.',
            'package_highlights' => 'Route test tour highlights.',
            'duration_days' => 1,
            'duration_nights' => 0,
            'itinerary' => 'Route test itinerary.',
            'include' => 'Guide',
            'exclude' => 'Personal expenses',
            'additional_info' => 'Route test additional information.',
            'cancellation_policy' => 'Route test cancellation policy.',
            'status' => 'Active',
        ];

        foreach (['area', 'area_traditional', 'area_simplified'] as $areaColumn) {
            if (Schema::hasColumn('tours', $areaColumn)) {
                $tourData[$areaColumn] = 'Bali';
            }
        }

        $tour = Tours::create($tourData);

        $response = $this->get(route('view.tour-detail', $tour->slug));

        $response->assertOk();
        $response->assertViewIs('frontend.landing-page.tours.detail');
        $response->assertViewHasAll([
            'tour',
            'neartours',
            'prices',
            'tourGeneratedItinerary',
            'tourMapLocations',
            'canViewTourRates',
            'tourRateAccess',
        ]);
    }

    public function test_accommodation_public_views_are_routed_to_landing_page_structure(): void
    {
        $frontEndController = file_get_contents(app_path('Http/Controllers/FrontEndController.php'));

        $this->assertFileExists(resource_path('views/frontend/landing-page/accommodations/index.blade.php'));
        $this->assertFileExists(resource_path('views/frontend/landing-page/accommodations/detail.blade.php'));
        $this->assertStringContainsString("view('frontend.landing-page.accommodations.index'", $frontEndController);
        $this->assertStringContainsString("view('frontend.landing-page.accommodations.detail'", $frontEndController);
        $this->assertStringNotContainsString("view('frontend.accommodations.index'", $frontEndController);
        $this->assertStringNotContainsString("view('frontend.accommodations.detail'", $frontEndController);
    }

    public function test_accommodation_public_assets_are_sourced_from_landing_page_structure(): void
    {
        $mix = file_get_contents(base_path('webpack.mix.js'));

        $this->assertFileExists(resource_path('frontend/js/landing-page/accommodations/index.js'));
        $this->assertFileExists(resource_path('frontend/js/landing-page/accommodations/detail.js'));
        $this->assertFileExists(resource_path('frontend/scss/landing-page/accommodations/index-entry.scss'));
        $this->assertFileExists(resource_path('frontend/scss/landing-page/accommodations/_index.scss'));
        $this->assertFileExists(resource_path('frontend/scss/landing-page/accommodations/detail-entry.scss'));
        $this->assertFileExists(resource_path('frontend/scss/landing-page/accommodations/_detail.scss'));
        $this->assertStringContainsString("resources/frontend/js/landing-page/accommodations/index.js", $mix);
        $this->assertStringContainsString("resources/frontend/js/landing-page/accommodations/detail.js", $mix);
        $this->assertStringContainsString("resources/frontend/scss/landing-page/accommodations/index-entry.scss", $mix);
        $this->assertStringContainsString("resources/frontend/scss/landing-page/accommodations/detail-entry.scss", $mix);
        $this->assertStringNotContainsString("resources/frontend/js/pages/accommodations-index.js", $mix);
        $this->assertStringNotContainsString("resources/frontend/js/pages/accommodation-detail.js", $mix);
    }

    public function test_accommodation_directory_route_renders_landing_page_view(): void
    {
        $response = $this->get(route('view.accommodation-service'));

        $response->assertOk();
        $response->assertViewIs('frontend.landing-page.accommodations.index');
        $response->assertViewHasAll([
            'hotels',
            'regions',
            'searchName',
            'searchRegion',
            'promoAvailable',
            'featuredHotel',
            'directoryStats',
        ]);
    }

    public function test_accommodation_detail_route_renders_landing_page_view(): void
    {
        $hotel = Hotels::create([
            'name' => 'Route Test Hotel',
            'code' => 'RTH-' . uniqid(),
            'region' => 'Bali',
            'address' => 'Route Test Address',
            'airport_duration' => 1,
            'airport_distance' => 12,
            'contact_person' => 'Route Contact',
            'phone' => '08123456789',
            'description' => 'Route test hotel description.',
            'facility' => 'Pool',
            'additional_info' => 'Route test additional information.',
            'wedding_info' => 'Route test wedding information.',
            'entrance_fee' => '0',
            'wedding_cancellation_policy' => 'Route test wedding cancellation policy.',
            'status' => 'Active',
            'cover' => 'default.webp',
            'author_id' => 1,
            'min_stay' => '1',
            'max_stay' => '14',
            'check_in_time' => '14:00',
            'check_out_time' => '12:00',
            'map' => 'https://example.test/map',
            'benefits' => 'Route test benefits.',
            'optional_rate' => '0',
            'cancellation_policy' => 'Route test cancellation policy.',
        ]);

        $response = $this->get(route('view.accommodation-detail', $hotel->code));

        $response->assertOk();
        $response->assertViewIs('frontend.landing-page.accommodations.detail');
        $response->assertViewHasAll([
            'hotel',
            'canUseCheckPriceForm',
            'checkPriceCta',
        ]);
    }

    public function test_hotel_availability_view_is_routed_to_frontend_home_booking_structure(): void
    {
        $hotelsController = file_get_contents(app_path('Http/Controllers/HotelsController.php'));

        $this->assertFileExists(resource_path('views/frontend/home/booking/hotel-availability.blade.php'));
        $this->assertStringContainsString("view('frontend.home.booking.hotel-availability'", $hotelsController);
        $this->assertStringNotContainsString("view('main.hotelavailability'", $hotelsController);
    }

    public function test_hotel_availability_assets_are_sourced_from_frontend_home_booking_structure(): void
    {
        $mix = file_get_contents(base_path('webpack.mix.js'));

        $this->assertFileExists(resource_path('frontend/js/home/booking/hotel-availability.js'));
        $this->assertFileExists(resource_path('frontend/scss/home/booking/hotel-availability-entry.scss'));
        $this->assertFileExists(resource_path('frontend/scss/home/booking/_hotel-availability.scss'));
        $this->assertStringContainsString("resources/frontend/js/home/booking/hotel-availability.js", $mix);
        $this->assertStringContainsString("resources/frontend/scss/home/booking/hotel-availability-entry.scss", $mix);
        $this->assertStringNotContainsString("resources/frontend/js/pages/hotel-availability.js", $mix);
        $this->assertStringNotContainsString("resources/frontend/scss/pages/hotel-availability-entry.scss", $mix);
    }

    public function test_hotel_availability_route_renders_frontend_home_booking_view(): void
    {
        $hotel = Hotels::create([
            'name' => 'Route Availability Hotel',
            'code' => 'RAH-' . uniqid(),
            'region' => 'Bali',
            'address' => 'Route Availability Address',
            'airport_duration' => 1,
            'airport_distance' => 12,
            'contact_person' => 'Route Contact',
            'phone' => '08123456789',
            'description' => 'Route availability hotel description.',
            'facility' => 'Pool',
            'additional_info' => 'Route availability additional information.',
            'wedding_info' => 'Route availability wedding information.',
            'entrance_fee' => '0',
            'wedding_cancellation_policy' => 'Route availability wedding cancellation policy.',
            'status' => 'Active',
            'cover' => 'default.webp',
            'author_id' => 1,
            'min_stay' => '1',
            'max_stay' => '14',
            'check_in_time' => '14:00',
            'check_out_time' => '12:00',
            'map' => 'https://example.test/map',
            'benefits' => 'Route availability benefits.',
            'optional_rate' => '0',
            'cancellation_policy' => 'Route availability cancellation policy.',
        ]);

        $response = $this
            ->withoutMiddleware()
            ->get(route('view.hotel-prices.page', [
                'code' => $hotel->code,
                'checkin' => now()->addDays(7)->toDateString(),
                'checkout' => now()->addDays(9)->toDateString(),
            ]));

        $response->assertOk();
        $response->assertViewIs('frontend.home.booking.hotel-availability');
        $response->assertViewHasAll([
            'hotel',
            'nearhotels',
            'promotions',
            'duration',
            'checkin',
            'checkout',
            'rateSections',
            'hasAnyResults',
        ]);
    }

    public function test_static_landing_page_views_are_routed_to_landing_page_structure(): void
    {
        $homeController = file_get_contents(app_path('Http/Controllers/HomeController.php'));
        $appServiceProvider = file_get_contents(app_path('Providers/AppServiceProvider.php'));

        $this->assertFileExists(resource_path('views/frontend/landing-page/about/index.blade.php'));
        $this->assertFileExists(resource_path('views/frontend/landing-page/contact/index.blade.php'));
        $this->assertStringContainsString("view('frontend.landing-page.about.index'", $homeController);
        $this->assertStringContainsString("view('frontend.landing-page.contact.index'", $homeController);
        $this->assertStringContainsString("View::composer('frontend.landing-page.about.index'", $appServiceProvider);
        $this->assertStringNotContainsString("view('home.landing-page.about'", $homeController);
        $this->assertStringNotContainsString("view('home.landing-page.contact'", $homeController);
        $this->assertStringNotContainsString("View::composer('home.landing-page.about'", $appServiceProvider);
    }

    public function test_static_landing_page_assets_are_sourced_from_landing_page_structure(): void
    {
        $mix = file_get_contents(base_path('webpack.mix.js'));

        $this->assertFileExists(resource_path('frontend/scss/landing-page/about/index-entry.scss'));
        $this->assertFileExists(resource_path('frontend/scss/landing-page/about/_index.scss'));
        $this->assertFileExists(resource_path('frontend/scss/landing-page/contact/index-entry.scss'));
        $this->assertFileExists(resource_path('frontend/scss/landing-page/contact/_index.scss'));
        $this->assertStringContainsString("resources/frontend/scss/landing-page/about/index-entry.scss", $mix);
        $this->assertStringContainsString("resources/frontend/scss/landing-page/contact/index-entry.scss", $mix);
        $this->assertStringNotContainsString("resources/frontend/scss/pages/about-page-entry.scss", $mix);
        $this->assertStringNotContainsString("resources/frontend/scss/pages/contact-page-entry.scss", $mix);
    }

    public function test_about_route_renders_landing_page_view(): void
    {
        $response = $this->get(route('about-us'));

        $response->assertOk();
        $response->assertViewIs('frontend.landing-page.about.index');
        $response->assertViewHas('businessProfile');
    }

    public function test_contact_route_renders_landing_page_view(): void
    {
        $response = $this->get(route('contact-us'));

        $response->assertOk();
        $response->assertViewIs('frontend.landing-page.contact.index');
        $response->assertViewHasAll([
            'businessProfile',
            'contactData',
        ]);
    }

    public function test_public_policy_views_are_routed_to_landing_page_structure(): void
    {
        $controller = file_get_contents(app_path('Http/Controllers/TermAndConditionController.php'));

        $this->assertFileExists(resource_path('views/frontend/landing-page/policies/terms-and-conditions.blade.php'));
        $this->assertFileExists(resource_path('views/frontend/landing-page/policies/privacy-policy.blade.php'));
        $this->assertFileExists(resource_path('views/frontend/landing-page/policies/faq.blade.php'));
        $this->assertFileExists(resource_path('views/frontend/landing-page/policies/partials/public-policy-page.blade.php'));
        $this->assertStringContainsString("view('frontend.landing-page.policies.terms-and-conditions'", $controller);
        $this->assertStringContainsString("view('frontend.landing-page.policies.privacy-policy'", $controller);
        $this->assertStringContainsString("view('frontend.landing-page.policies.faq'", $controller);
        $this->assertStringNotContainsString("view('privacy-policy.terms-and-conditions'", $controller);
        $this->assertStringNotContainsString("view('privacy-policy.faq'", $controller);
    }

    public function test_public_policy_assets_are_sourced_from_landing_page_structure(): void
    {
        $mix = file_get_contents(base_path('webpack.mix.js'));

        $this->assertFileExists(resource_path('frontend/scss/landing-page/policies/index-entry.scss'));
        $this->assertFileExists(resource_path('frontend/scss/landing-page/policies/_index.scss'));
        $this->assertStringContainsString("resources/frontend/scss/landing-page/policies/index-entry.scss", $mix);
        $this->assertStringNotContainsString("resources/frontend/scss/pages/public-policy-entry.scss", $mix);
    }

    public function test_public_policy_routes_render_landing_page_views(): void
    {
        $termsResponse = $this->get(route('terms-and-conditions'));
        $privacyResponse = $this->get(route('privacy-policy'));
        $faqResponse = $this->get(route('faq'));
        $helpResponse = $this->get(route('help'));

        $termsResponse->assertOk();
        $termsResponse->assertViewIs('frontend.landing-page.policies.terms-and-conditions');
        $privacyResponse->assertOk();
        $privacyResponse->assertViewIs('frontend.landing-page.policies.privacy-policy');
        $faqResponse->assertOk();
        $faqResponse->assertViewIs('frontend.landing-page.policies.faq');
        $helpResponse->assertOk();
        $helpResponse->assertViewIs('frontend.landing-page.policies.faq');
    }

    public function test_footer_policy_links_include_faqs(): void
    {
        $footerSeeder = file_get_contents(database_path('seeders/FooterSeeder.php'));
        $footerFaqMigration = file_get_contents(database_path('migrations/2026_07_15_150000_add_faqs_to_footer_policy_links.php'));

        $this->assertStringContainsString("'group' => 'policies'", $footerSeeder);
        $this->assertStringContainsString("'label' => 'FAQs'", $footerSeeder);
        $this->assertStringContainsString("'route_name' => 'faq'", $footerSeeder);
        $this->assertStringContainsString("'group' => 'policies'", $footerFaqMigration);
        $this->assertStringContainsString("'label' => 'FAQs'", $footerFaqMigration);
        $this->assertStringContainsString("'route_name' => 'faq'", $footerFaqMigration);
    }

    public function test_profile_view_is_routed_to_frontend_home_structure(): void
    {
        $controller = file_get_contents(app_path('Http/Controllers/ProfileController.php'));

        $this->assertFileExists(resource_path('views/frontend/home/profile/index.blade.php'));
        $this->assertStringContainsString("view('frontend.home.profile.index'", $controller);
        $this->assertStringNotContainsString("view('main.profile'", $controller);
    }

    public function test_profile_assets_are_sourced_from_frontend_home_structure(): void
    {
        $mix = file_get_contents(base_path('webpack.mix.js'));

        $this->assertFileExists(resource_path('frontend/js/home/profile/index.js'));
        $this->assertFileExists(resource_path('frontend/scss/home/profile/index-entry.scss'));
        $this->assertFileExists(resource_path('frontend/scss/home/profile/_index.scss'));
        $this->assertStringContainsString("resources/frontend/js/home/profile/index.js", $mix);
        $this->assertStringContainsString("resources/frontend/scss/home/profile/index-entry.scss", $mix);
        $this->assertStringNotContainsString("resources/frontend/js/pages/profile.js", $mix);
        $this->assertStringNotContainsString("resources/frontend/scss/pages/profile-entry.scss", $mix);
    }

    public function test_profile_route_renders_frontend_home_view(): void
    {
        $user = User::forceCreate([
            'name' => 'Profile Structure User',
            'username' => 'profile-structure',
            'email' => 'profile-structure@example.test',
            'password' => 'secret',
            'type' => 'user',
            'status' => 'Active',
            'is_approved' => 1,
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($user)->get(route('profile'));

        $response->assertOk();
        $response->assertViewIs('frontend.home.profile.index');
        $response->assertViewHas('profileUser');
    }

    public function test_manual_book_view_is_routed_to_frontend_home_structure(): void
    {
        $manualBookController = file_get_contents(app_path('Http/Controllers/ManualBookController.php'));

        $this->assertFileExists(resource_path('views/frontend/home/manual-book/index.blade.php'));
        $this->assertFileDoesNotExist(resource_path('views/main/manual-book.blade.php'));
        $this->assertStringContainsString("view('frontend.home.manual-book.index'", $manualBookController);
        $this->assertStringNotContainsString("view('main.manual-book'", $manualBookController);
    }

    public function test_manual_book_assets_are_sourced_from_frontend_home_structure(): void
    {
        $mix = file_get_contents(base_path('webpack.mix.js'));

        $this->assertFileExists(resource_path('frontend/js/home/manual-book/index.js'));
        $this->assertFileExists(resource_path('frontend/scss/home/manual-book/index-entry.scss'));
        $this->assertFileExists(resource_path('frontend/scss/home/manual-book/_index.scss'));
        $this->assertStringContainsString("resources/frontend/js/home/manual-book/index.js", $mix);
        $this->assertStringContainsString("resources/frontend/scss/home/manual-book/index-entry.scss", $mix);
    }

    public function test_manual_book_route_renders_frontend_home_view(): void
    {
        $user = User::forceCreate([
            'name' => 'Manual Book Structure User',
            'username' => 'manual-book-structure',
            'email' => 'manual-book-structure@example.test',
            'password' => 'secret',
            'type' => 'user',
            'status' => 'Active',
            'is_approved' => 1,
            'email_verified_at' => now(),
        ]);

        ManualBook::create([
            'name' => 'Partner User Guide',
            'language' => 'en',
            'file_name' => 'partner-user-guide.pdf',
        ]);

        $response = $this->actingAs($user)->get(route('view.manual-book'));

        $response->assertOk();
        $response->assertViewIs('frontend.home.manual-book.index');
        $response->assertViewHas('manualBooks');
        $response->assertSee('Partner User Guide');
    }

    public function test_orders_dashboard_view_is_routed_to_frontend_home_structure(): void
    {
        $orderController = file_get_contents(app_path('Http/Controllers/OrderController.php'));
        $menuController = file_get_contents(app_path('Http/Controllers/MenuController.php'));

        $this->assertFileExists(resource_path('views/frontend/home/orders/index.blade.php'));
        $this->assertStringContainsString("view('frontend.home.orders.index'", $orderController);
        $this->assertStringContainsString("view('frontend.home.orders.index'", $menuController);
        $this->assertStringNotContainsString("view('main.order'", $orderController);
        $this->assertStringNotContainsString("view('main.order'", $menuController);
    }

    public function test_orders_dashboard_assets_are_sourced_from_frontend_home_structure(): void
    {
        $mix = file_get_contents(base_path('webpack.mix.js'));

        $this->assertFileExists(resource_path('frontend/js/home/orders/index.js'));
        $this->assertFileExists(resource_path('frontend/scss/home/orders/index-entry.scss'));
        $this->assertFileExists(resource_path('frontend/scss/home/orders/_index.scss'));
        $this->assertStringContainsString("resources/frontend/js/home/orders/index.js", $mix);
        $this->assertStringContainsString("resources/frontend/scss/home/orders/index-entry.scss", $mix);
        $this->assertStringNotContainsString("resources/frontend/js/pages/frontend-orders.js", $mix);
        $this->assertStringNotContainsString("resources/frontend/scss/pages/frontend-orders-entry.scss", $mix);
    }

    public function test_orders_dashboard_route_renders_frontend_home_view(): void
    {
        $user = User::forceCreate([
            'name' => 'Orders Structure User',
            'username' => 'orders-structure',
            'email' => 'orders-structure@example.test',
            'password' => 'secret',
            'type' => 'user',
            'status' => 'Active',
            'is_approved' => 1,
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($user)->get(route('view.orders'));

        $response->assertOk();
        $response->assertViewIs('frontend.home.orders.index');
        $response->assertViewHas('orders');
    }

    public function test_order_detail_view_is_routed_to_frontend_home_structure(): void
    {
        $orderController = file_get_contents(app_path('Http/Controllers/OrderController.php'));

        $this->assertFileExists(resource_path('views/frontend/home/orders/detail.blade.php'));
        $this->assertStringContainsString("view('frontend.home.orders.detail'", $orderController);
        $this->assertStringNotContainsString("view('main.orderdetail'", $orderController);
    }

    public function test_order_detail_assets_are_sourced_from_frontend_home_structure(): void
    {
        $mix = file_get_contents(base_path('webpack.mix.js'));

        $this->assertFileExists(resource_path('frontend/js/home/orders/detail.js'));
        $this->assertFileExists(resource_path('frontend/scss/home/orders/detail-entry.scss'));
        $this->assertFileExists(resource_path('frontend/scss/home/orders/_detail.scss'));
        $this->assertStringContainsString("resources/frontend/js/home/orders/detail.js", $mix);
        $this->assertStringContainsString("resources/frontend/scss/home/orders/detail-entry.scss", $mix);
        $this->assertStringNotContainsString("resources/frontend/js/pages/order-detail.js", $mix);
        $this->assertStringNotContainsString("resources/frontend/scss/pages/order-detail-entry.scss", $mix);
    }

    public function test_order_detail_route_renders_frontend_home_view(): void
    {
        $user = User::forceCreate([
            'name' => 'Order Detail Structure User',
            'username' => 'order-detail-structure',
            'email' => 'order-detail-structure@example.test',
            'password' => 'secret',
            'type' => 'user',
            'status' => 'Active',
            'is_approved' => 1,
            'email_verified_at' => now(),
        ]);

        $order = Orders::forceCreate([
            'user_id' => $user->id,
            'sales_agent' => $user->id,
            'orderno' => 'ODT260715A',
            'confirmation_order' => 'CONFIRM',
            'name' => $user->name,
            'email' => $user->email,
            'servicename' => 'Structure Test Service',
            'service' => 'Additional Service',
            'checkin' => now()->toDateString(),
            'checkout' => now()->addDay()->toDateString(),
            'number_of_guests' => 1,
            'guest_detail' => 'Structure Guest',
            'price_total' => 100,
            'final_price' => 100,
            'status' => 'Pending',
        ]);

        $response = $this->actingAs($user)->get(route('view.detail-order', ['id' => $order->id]));

        $response->assertOk();
        $response->assertViewIs('frontend.home.orders.detail');
        $response->assertViewHas('order');
    }

    public function test_orders_history_view_is_routed_to_frontend_home_structure(): void
    {
        $orderController = file_get_contents(app_path('Http/Controllers/OrderController.php'));

        $this->assertFileExists(resource_path('views/frontend/home/orders/history.blade.php'));
        $this->assertStringContainsString("view('frontend.home.orders.history'", $orderController);
        $this->assertStringNotContainsString("view('layouts.order-history'", $orderController);
    }

    public function test_orders_history_route_renders_frontend_home_view(): void
    {
        $user = User::forceCreate([
            'name' => 'Orders History Structure User',
            'username' => 'orders-history-structure',
            'email' => 'orders-history-structure@example.test',
            'password' => 'secret',
            'type' => 'user',
            'status' => 'Active',
            'is_approved' => 1,
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($user)->get(route('orders.history'));

        $response->assertOk();
        $response->assertViewIs('frontend.home.orders.history');
        $response->assertViewHas('historyItems');
    }

    public function test_tour_order_edit_view_is_routed_to_frontend_home_structure(): void
    {
        $orderController = file_get_contents(app_path('Http/Controllers/OrderController.php'));

        $this->assertFileExists(resource_path('views/frontend/home/orders/edit-tour.blade.php'));
        $this->assertStringContainsString("view('frontend.home.orders.edit-tour'", $orderController);
        $this->assertStringNotContainsString("view('frontend.orders.edit-order-tour'", $orderController);
    }

    public function test_tour_order_edit_assets_are_sourced_from_frontend_home_structure(): void
    {
        $mix = file_get_contents(base_path('webpack.mix.js'));

        $this->assertFileExists(resource_path('frontend/js/home/orders/edit.js'));
        $this->assertStringContainsString("resources/frontend/js/home/orders/edit.js", $mix);
        $this->assertStringNotContainsString("resources/frontend/js/pages/order-edit.js", $mix);
    }

    public function test_tour_order_edit_route_renders_frontend_home_view(): void
    {
        \Illuminate\Support\Facades\Cache::forget('tax_1');
        \Illuminate\Support\Facades\Cache::forget('usd_rate');

        $user = User::forceCreate([
            'name' => 'Tour Edit Structure User',
            'username' => 'tour-edit-structure',
            'email' => 'tour-edit-structure@example.test',
            'password' => 'secret',
            'type' => 'user',
            'status' => 'Active',
            'is_approved' => 1,
            'email_verified_at' => now(),
        ]);

        Tax::unguarded(function () {
            Tax::updateOrCreate(['id' => 1], ['name' => 'Structure Tax', 'tax' => 0]);
        });

        UsdRates::updateOrCreate(
            ['name' => 'USD'],
            ['rate' => 10000, 'sell' => 10000, 'buy' => 10000, 'difference' => 0]
        );

        $tour = Tours::forceCreate([
            'name' => 'Structure Tour Edit Package',
            'code' => 'STE',
            'name_traditional' => 'Structure Tour Edit Package',
            'name_simplified' => 'Structure Tour Edit Package',
            'slug' => 'structure-tour-edit-package',
            'short_description' => 'Structure test tour.',
            'description' => 'Structure test tour.',
            'include' => 'Guide',
            'exclude' => 'Personal expense',
            'additional_info' => 'Structure info',
            'cancellation_policy' => 'Structure cancellation',
            'status' => 'Active',
        ]);

        TourPrices::forceCreate([
            'tour_id' => $tour->id,
            'min_qty' => 2,
            'max_qty' => 20,
            'contract_rate' => 100000,
            'markup' => 10,
            'expired_date' => now()->addYear()->toDateString(),
            'status' => 'Active',
        ]);

        $order = Orders::forceCreate([
            'user_id' => $user->id,
            'sales_agent' => $user->id,
            'orderno' => 'TOE260715A',
            'confirmation_order' => 'CONFIRM',
            'name' => $user->name,
            'email' => $user->email,
            'servicename' => $tour->name,
            'service' => 'Tour Package',
            'service_id' => $tour->id,
            'checkin' => now()->addDays(7)->toDateString(),
            'checkout' => now()->addDays(8)->toDateString(),
            'travel_date' => now()->addDays(7),
            'number_of_guests' => 2,
            'guest_detail' => '[]',
            'pickup_location' => 'Hotel Lobby',
            'dropoff_location' => 'Hotel Lobby',
            'price_pax' => 20,
            'price_total' => 40,
            'final_price' => 40,
            'status' => 'Draft',
        ]);

        $response = $this->actingAs($user)->get(route('view.edit-order-tour', ['id' => $order->id]));

        $response->assertOk();
        $response->assertViewIs('frontend.home.orders.edit-tour');
        $response->assertViewHasAll(['order', 'tour', 'prices']);
    }

    public function test_legacy_order_edit_wrapper_is_routed_to_frontend_home_structure(): void
    {
        $orderController = file_get_contents(app_path('Http/Controllers/OrderController.php'));

        $this->assertFileExists(resource_path('views/frontend/home/orders/edit-legacy.blade.php'));
        $this->assertStringContainsString("view('frontend.home.orders.edit-legacy'", $orderController);
        $this->assertStringNotContainsString("view('order.user-edit-order'", $orderController);
    }

    public function test_transport_order_edit_partial_is_sourced_from_frontend_home_structure(): void
    {
        $wrapper = file_get_contents(resource_path('views/frontend/home/orders/edit-legacy.blade.php'));

        $this->assertFileExists(resource_path('views/frontend/home/orders/partials/edit-transport.blade.php'));
        $this->assertStringContainsString("@include('frontend.home.orders.partials.edit-transport')", $wrapper);
        $this->assertStringNotContainsString("@include('order.edit-order-transport')", $wrapper);
    }

    public function test_villa_order_edit_partial_is_sourced_from_frontend_home_structure(): void
    {
        $wrapper = file_get_contents(resource_path('views/frontend/home/orders/edit-legacy.blade.php'));

        $this->assertFileExists(resource_path('views/frontend/home/orders/partials/edit-villa.blade.php'));
        $this->assertStringContainsString("@include('frontend.home.orders.partials.edit-villa')", $wrapper);
        $this->assertStringNotContainsString("@include('order.edit-order-villa')", $wrapper);
    }

    public function test_hotel_order_edit_partial_is_sourced_from_frontend_home_structure(): void
    {
        $wrapper = file_get_contents(resource_path('views/frontend/home/orders/edit-legacy.blade.php'));

        $this->assertFileExists(resource_path('views/frontend/home/orders/partials/edit-hotel.blade.php'));
        $this->assertStringContainsString("@include('frontend.home.orders.partials.edit-hotel')", $wrapper);
        $this->assertStringNotContainsString("@include('order.edit-order-hotel')", $wrapper);
    }

    public function test_activity_order_edit_partial_is_sourced_from_frontend_home_structure(): void
    {
        $wrapper = file_get_contents(resource_path('views/frontend/home/orders/edit-legacy.blade.php'));

        $this->assertFileExists(resource_path('views/frontend/home/orders/partials/edit-activity.blade.php'));
        $this->assertStringContainsString("@include('frontend.home.orders.partials.edit-activity')", $wrapper);
        $this->assertStringNotContainsString("@include('order.edit-order-activity')", $wrapper);
    }

    public function test_additional_charge_order_edit_view_is_routed_to_frontend_home_structure(): void
    {
        $orderController = file_get_contents(app_path('Http/Controllers/OrderController.php'));

        $this->assertFileExists(resource_path('views/frontend/home/orders/edit-additional-charge.blade.php'));
        $this->assertStringContainsString("view('frontend.home.orders.edit-additional-charge'", $orderController);
        $this->assertStringNotContainsString("view('order.edit-order-additional-charge'", $orderController);
    }

    public function test_additional_charge_order_edit_route_renders_frontend_home_view(): void
    {
        \Illuminate\Support\Facades\Cache::forget('tax_1');
        \Illuminate\Support\Facades\Cache::forget('usd_rate');
        \Illuminate\Support\Facades\Cache::forget('business_profile');

        $user = User::forceCreate([
            'name' => 'Additional Charge Structure User',
            'username' => 'additional-charge-structure',
            'email' => 'additional-charge-structure@example.test',
            'password' => 'secret',
            'type' => 'user',
            'status' => 'Active',
            'is_approved' => 1,
            'email_verified_at' => now(),
        ]);

        Tax::unguarded(function () {
            Tax::updateOrCreate(['id' => 1], ['tax' => 0]);
        });

        UsdRates::updateOrCreate(
            ['name' => 'USD'],
            ['rate' => 10000, 'sell' => 10000, 'buy' => 10000, 'difference' => 0]
        );

        BusinessProfile::unguarded(function () {
            BusinessProfile::updateOrCreate(
                ['id' => 1],
                [
                    'profile_key' => 'primary',
                    'name' => 'Structure Business',
                    'caption' => 'Structure Caption',
                    'logo' => 'storage/logo.png',
                    'logo_dark' => 'storage/logo-dark.png',
                ]
            );
        });

        $hotel = Hotels::forceCreate([
            'name' => 'Structure Additional Charge Hotel',
            'code' => 'SACH',
            'region' => 'Bali',
            'address' => 'Structure Address',
            'contact_person' => 'Structure Contact',
            'phone' => '08123456789',
            'description' => 'Structure hotel description.',
            'facility' => 'Pool',
            'status' => 'Active',
            'cover' => 'storage/hotels/structure-cover.jpg',
            'author_id' => $user->id,
            'cancellation_policy' => 'Structure cancellation',
        ]);

        OptionalRate::forceCreate([
            'hotels_id' => $hotel->id,
            'name' => 'Structure Breakfast',
            'service' => 'Hotel',
            'service_id' => $hotel->id,
            'type' => 'Meals',
            'mandatory' => 0,
            'contract_rate' => 100000,
            'markup' => 10,
            'description' => 'Structure optional service.',
        ]);

        $order = Orders::forceCreate([
            'user_id' => $user->id,
            'sales_agent' => $user->id,
            'orderno' => 'ACE260715A',
            'confirmation_order' => 'CONFIRM',
            'name' => $user->name,
            'email' => $user->email,
            'servicename' => $hotel->name,
            'service' => 'Hotel',
            'service_id' => $hotel->id,
            'checkin' => now()->addDays(7)->toDateString(),
            'checkout' => now()->addDays(9)->toDateString(),
            'number_of_guests' => 2,
            'guest_detail' => '[]',
            'price_total' => 100,
            'final_price' => 100,
            'status' => 'Draft',
        ]);

        $response = $this->actingAs($user)->get(route('view.edit-order-additional-charge', ['id' => $order->id]));

        $response->assertOk();
        $response->assertViewIs('frontend.home.orders.edit-additional-charge');
        $response->assertViewHasAll(['order', 'optional_services', 'date_stay']);
    }

    public function test_legacy_optional_service_order_edit_view_is_retired(): void
    {
        $routeFile = file_get_contents(base_path('routes/web.php'));
        $orderController = file_get_contents(app_path('Http/Controllers/OrderController.php'));
        $views = collect(glob(resource_path('views/**/*.blade.php'), GLOB_BRACE))
            ->map(fn ($path) => str_replace('\\', '/', $path))
            ->reject(fn ($path) => str_ends_with($path, 'views/order/edit-order-optional-service.blade.php'))
            ->map(fn ($path) => file_get_contents($path))
            ->implode("\n");

        $this->assertFileDoesNotExist(resource_path('views/order/edit-order-optional-service.blade.php'));
        $this->assertStringNotContainsString('edit-order-optional-service', $routeFile);
        $this->assertStringNotContainsString("view('order.edit-order-optional-service'", $orderController);
        $this->assertStringNotContainsString("@include('order.edit-order-optional-service')", $views);
    }

    public function test_wedding_order_views_are_routed_to_frontend_home_structure(): void
    {
        $controller = file_get_contents(app_path('Http/Controllers/OrderWeddingController.php'));
        $legacyDetailWrapper = file_get_contents(resource_path('views/frontend/home/orders/details/legacy.blade.php'));

        $this->assertFileExists(resource_path('views/frontend/home/orders/weddings/edit.blade.php'));
        $this->assertFileExists(resource_path('views/frontend/home/orders/weddings/detail.blade.php'));
        $this->assertFileDoesNotExist(resource_path('views/order/edit-order-wedding.blade.php'));
        $this->assertFileDoesNotExist(resource_path('views/order/detail-order-wedding.blade.php'));
        $this->assertFileDoesNotExist(resource_path('views/order/edit-order-wedding.blade copy.php'));
        $this->assertFileDoesNotExist(resource_path('views/order/order-wedding-detail.blade.php'));
        $this->assertStringContainsString("view('frontend.home.orders.weddings.edit'", $controller);
        $this->assertStringContainsString("view('frontend.home.orders.weddings.detail'", $controller);
        $this->assertStringContainsString("@include('frontend.home.orders.weddings.detail')", $legacyDetailWrapper);
        $this->assertStringNotContainsString("view('order.edit-order-wedding'", $controller);
        $this->assertStringNotContainsString("view('order.detail-order-wedding'", $controller);
        $this->assertStringNotContainsString("@include('order.detail-order-wedding')", $legacyDetailWrapper);
    }

    public function test_wedding_order_legacy_partials_are_sourced_from_frontend_home_structure(): void
    {
        $backupForm = file_get_contents(resource_path('views/frontend/home/orders/weddings/legacy/order-weddings-backup.blade.php'));
        $partials = [
            'order_wedding_decoration',
            'order_wedding_dinner_venue',
            'order_wedding_documentation',
            'order_wedding_entertainment',
            'order_wedding_fixed_service',
            'order_wedding_makeup',
            'order_wedding_other',
            'order_wedding_room',
            'order_wedding_transport',
            'order_wedding_venues',
        ];

        foreach ($partials as $partial) {
            $this->assertFileExists(resource_path("views/frontend/home/orders/weddings/partials/{$partial}.blade.php"));
            $this->assertFileDoesNotExist(resource_path("views/order/{$partial}.blade.php"));
            $this->assertStringContainsString("@include('frontend.home.orders.weddings.partials.{$partial}')", $backupForm);
            $this->assertStringNotContainsString("@include('order.{$partial}')", $backupForm);
        }
    }

    public function test_service_order_detail_views_are_sourced_from_frontend_home_structure(): void
    {
        $controller = file_get_contents(app_path('Http/Controllers/OrderController.php'));
        $legacyWrapper = file_get_contents(resource_path('views/frontend/home/orders/details/legacy.blade.php'));
        $tourModern = file_get_contents(resource_path('views/frontend/home/orders/details/tour-modern.blade.php'));
        $transportModern = file_get_contents(resource_path('views/frontend/home/orders/details/transport-modern.blade.php'));
        $paymentStatus = file_get_contents(resource_path('views/partials/user-order-payment-status.blade.php'));

        $this->assertFileExists(resource_path('views/frontend/home/orders/details/legacy.blade.php'));
        $this->assertFileExists(resource_path('views/frontend/home/orders/details/activity.blade.php'));
        $this->assertFileExists(resource_path('views/frontend/home/orders/details/tour-modern.blade.php'));
        $this->assertFileExists(resource_path('views/frontend/home/orders/details/tour-legacy.blade.php'));
        $this->assertFileExists(resource_path('views/frontend/home/orders/details/villa.blade.php'));
        $this->assertFileExists(resource_path('views/frontend/home/orders/details/transport-modern.blade.php'));
        $this->assertFileExists(resource_path('views/frontend/home/orders/details/transport-legacy.blade.php'));
        $this->assertFileDoesNotExist(resource_path('views/frontend/orders/detail-order-tour-modern.blade.php'));
        $this->assertFileDoesNotExist(resource_path('views/frontend/orders/detail-order-tour.blade.php'));
        $this->assertFileDoesNotExist(resource_path('views/frontend/orders/detail-order-transport-modern.blade.php'));
        $this->assertFileDoesNotExist(resource_path('views/order/user-detail-order.blade.php'));
        $this->assertFileDoesNotExist(resource_path('views/order/detail-order-activity.blade.php'));
        $this->assertFileDoesNotExist(resource_path('views/order/detail-order-villa.blade.php'));
        $this->assertFileDoesNotExist(resource_path('views/order/detail-order-transport.blade.php'));
        $this->assertFileDoesNotExist(resource_path('views/order/detail-order-tour.blade.php'));
        $this->assertStringContainsString("view('frontend.home.orders.details.legacy'", $controller);
        $this->assertStringContainsString("view('frontend.home.orders.details.tour-modern'", $controller);
        $this->assertStringNotContainsString("view('order.user-detail-order'", $controller);
        $this->assertStringNotContainsString("view('frontend.orders.detail-order-tour-modern'", $controller);
        $this->assertStringContainsString("@include('frontend.home.orders.details.partials.hotel-detail-modern')", $legacyWrapper);
        $this->assertStringContainsString("@include('frontend.home.orders.details.villa')", $legacyWrapper);
        $this->assertStringContainsString("@include('frontend.home.orders.details.activity')", $legacyWrapper);
        $this->assertStringContainsString("@include('frontend.home.orders.details.tour-modern')", $legacyWrapper);
        $this->assertStringContainsString("@include('frontend.home.orders.details.transport-modern')", $legacyWrapper);
        $this->assertStringNotContainsString("@include('frontend.orders.detail-order-tour-modern')", $legacyWrapper);
        $this->assertStringNotContainsString("@include('frontend.orders.detail-order-transport-modern')", $legacyWrapper);
        $this->assertStringContainsString("@include('frontend.home.orders.details.partials.invoice-action-buttons'", $tourModern);
        $this->assertStringContainsString("@include('frontend.home.orders.details.partials.invoice-action-buttons'", $transportModern);
        $this->assertStringContainsString("@include('frontend.home.orders.details.partials.invoice-action-buttons'", $paymentStatus);
    }

    public function test_service_order_detail_shared_partials_are_sourced_from_frontend_home_structure(): void
    {
        $partials = [
            'hotel-detail-modern',
            'hotel-detail-modern-addons',
            'hotel-detail-modern-modals',
            'hotel-detail-modern-price',
            'hotel-detail-modern-sidebar',
            'invoice-action-buttons',
            'invoice-preview-modal',
            'invoice-preview-modal-compact',
            'legacy-order-payment-sidebar',
        ];

        foreach ($partials as $partial) {
            $this->assertFileExists(resource_path("views/frontend/home/orders/details/partials/{$partial}.blade.php"));
            $this->assertFileDoesNotExist(resource_path("views/order/partials/{$partial}.blade.php"));
        }

        $detailFiles = collect([
            resource_path('views/frontend/home/orders/details/activity.blade.php'),
            resource_path('views/frontend/home/orders/details/villa.blade.php'),
            resource_path('views/frontend/home/orders/details/transport-legacy.blade.php'),
            resource_path('views/frontend/home/orders/details/partials/hotel-detail-modern.blade.php'),
            resource_path('views/frontend/home/orders/details/partials/hotel-detail-modern-sidebar.blade.php'),
            resource_path('views/frontend/home/orders/details/partials/invoice-action-buttons.blade.php'),
            resource_path('views/frontend/home/orders/details/partials/legacy-order-payment-sidebar.blade.php'),
        ])->map(fn ($path) => file_get_contents($path))->implode("\n");

        $this->assertStringContainsString('frontend.home.orders.details.partials', $detailFiles);
        $this->assertStringNotContainsString('order.partials.', $detailFiles);
    }

    public function test_admin_order_helper_views_are_sourced_from_backend_operations_structure(): void
    {
        $controller = file_get_contents(app_path('Http/Controllers/OrdersAdminController.php'));

        $this->assertFileExists(resource_path('views/backend/operations/orders/actions/add-additional-services.blade.php'));
        $this->assertFileExists(resource_path('views/backend/operations/orders/actions/add-order-itinerary.blade.php'));
        $this->assertFileExists(resource_path('views/backend/operations/orders/actions/edit-airport-shuttle.blade.php'));
        $this->assertFileDoesNotExist(resource_path('views/order/add-additional-services.blade.php'));
        $this->assertFileDoesNotExist(resource_path('views/order/add-order-itinerary.blade.php'));
        $this->assertFileDoesNotExist(resource_path('views/order/edit-airport-shuttle.blade.php'));
        $this->assertStringContainsString("view('backend.operations.orders.actions.add-additional-services'", $controller);
        $this->assertStringContainsString("view('backend.operations.orders.actions.add-order-itinerary'", $controller);
        $this->assertStringContainsString("view('backend.operations.orders.actions.edit-airport-shuttle'", $controller);
        $this->assertStringNotContainsString("view('order.add-additional-services'", $controller);
        $this->assertStringNotContainsString("view('order.add-order-itinerary'", $controller);
        $this->assertStringNotContainsString("view('order.edit-airport-shuttle'", $controller);
    }

    public function test_reservation_helper_views_are_sourced_from_backend_operations_structure(): void
    {
        $controller = file_get_contents(app_path('Http/Controllers/ReservationController.php'));
        $actions = [
            'add-order' => 'add_rsv_order',
            'add-transport' => 'add_rsv_transport',
            'add-activity-tour' => 'add_rsv_activity_tour',
            'add-itinerary' => 'add_rsv_itinerary',
        ];

        foreach ($actions as $target => $legacy) {
            $this->assertFileExists(resource_path("views/backend/operations/reservations/actions/{$target}.blade.php"));
            $this->assertFileDoesNotExist(resource_path("views/form/{$legacy}.blade.php"));
        }

        $this->assertStringContainsString("view('backend.operations.reservations.actions.add-order'", $controller);
        $this->assertStringContainsString("view('backend.operations.reservations.actions.add-transport'", $controller);
        $this->assertStringContainsString("view('backend.operations.reservations.actions.add-activity-tour'", $controller);
        $this->assertStringContainsString("view('backend.operations.reservations.actions.add-itinerary'", $controller);
        $this->assertStringNotContainsString("view('form.add_rsv_order'", $controller);
        $this->assertStringNotContainsString("view('form.add_rsv_transport'", $controller);
        $this->assertStringNotContainsString("view('form.add_rsv_activity_tour'", $controller);
        $this->assertStringNotContainsString("view('form.add_rsv_itinerary'", $controller);
    }

    public function test_transport_admin_forms_are_sourced_from_backend_operations_structure(): void
    {
        $adminController = file_get_contents(app_path('Http/Controllers/TransportsAdminController.php'));
        $publicController = file_get_contents(app_path('Http/Controllers/TransportsController.php'));

        $this->assertFileExists(resource_path('views/backend/operations/transports/forms/create.blade.php'));
        $this->assertFileExists(resource_path('views/backend/operations/transports/forms/edit.blade.php'));
        $this->assertFileExists(resource_path('views/backend/operations/transports/forms/gallery-edit.blade.php'));
        $this->assertFileDoesNotExist(resource_path('views/form/transportadd.blade.php'));
        $this->assertFileDoesNotExist(resource_path('views/form/transportedit.blade.php'));
        $this->assertFileDoesNotExist(resource_path('views/form/transportgaleryedit.blade.php'));
        $this->assertStringContainsString("view('backend.operations.transports.forms.create'", $adminController);
        $this->assertStringContainsString("view('backend.operations.transports.forms.edit'", $adminController);
        $this->assertStringContainsString("view('backend.operations.transports.forms.gallery-edit'", $publicController);
        $this->assertStringNotContainsString("view('form.transportadd'", $adminController);
        $this->assertStringNotContainsString("view('form.transportedit'", $adminController);
        $this->assertStringNotContainsString("view('form.transportgaleryedit'", $publicController);
    }

    public function test_activity_admin_forms_are_sourced_from_backend_operations_structure(): void
    {
        $adminController = file_get_contents(app_path('Http/Controllers/ActivitiesAdminController.php'));
        $publicController = file_get_contents(app_path('Http/Controllers/ActivitiesController.php'));

        $this->assertFileExists(resource_path('views/backend/operations/activities/forms/create.blade.php'));
        $this->assertFileExists(resource_path('views/backend/operations/activities/forms/edit.blade.php'));
        $this->assertFileExists(resource_path('views/backend/operations/activities/forms/gallery-edit.blade.php'));
        $this->assertFileDoesNotExist(resource_path('views/form/activityadd.blade.php'));
        $this->assertFileDoesNotExist(resource_path('views/form/activityedit.blade.php'));
        $this->assertFileDoesNotExist(resource_path('views/form/activitygaleryedit.blade.php'));
        $this->assertStringContainsString("view('backend.operations.activities.forms.create'", $adminController);
        $this->assertStringContainsString("view('backend.operations.activities.forms.edit'", $adminController);
        $this->assertStringContainsString("view('backend.operations.activities.forms.gallery-edit'", $publicController);
        $this->assertStringNotContainsString("view('form.activityadd'", $adminController);
        $this->assertStringNotContainsString("view('form.activityedit'", $adminController);
        $this->assertStringNotContainsString("view('form.activitygaleryedit'", $publicController);
    }

    public function test_hotel_admin_forms_are_sourced_from_backend_operations_structure(): void
    {
        $controller = file_get_contents(app_path('Http/Controllers/HotelsAdminController.php'));
        $forms = [
            'create' => 'hoteladd',
            'edit' => 'hoteledit',
            'gallery-edit' => 'hotelgaleryedit',
            'add-normal-price' => 'hotel-add-normal-price',
            'add-promo' => 'hotelpromoadd',
            'room-create' => 'roomadd',
            'room-edit' => 'roomedit',
        ];

        foreach ($forms as $target => $legacy) {
            $this->assertFileExists(resource_path("views/backend/operations/hotels/forms/{$target}.blade.php"));
            $this->assertFileDoesNotExist(resource_path("views/form/{$legacy}.blade.php"));
        }

        $this->assertStringContainsString("view('backend.operations.hotels.forms.create'", $controller);
        $this->assertStringContainsString("view('backend.operations.hotels.forms.edit'", $controller);
        $this->assertStringContainsString("view('backend.operations.hotels.forms.gallery-edit'", $controller);
        $this->assertStringContainsString("view('backend.operations.hotels.forms.add-normal-price'", $controller);
        $this->assertStringContainsString("view('backend.operations.hotels.forms.add-promo'", $controller);
        $this->assertStringContainsString("view('backend.operations.hotels.forms.room-create'", $controller);
        $this->assertStringContainsString("view('backend.operations.hotels.forms.room-edit'", $controller);
        $this->assertStringNotContainsString("view('form.hoteladd'", $controller);
        $this->assertStringNotContainsString("view('form.hoteledit'", $controller);
        $this->assertStringNotContainsString("view('form.hotelgaleryedit'", $controller);
        $this->assertStringNotContainsString("view('form.hotel-add-normal-price'", $controller);
        $this->assertStringNotContainsString("view('form.hotelpromoadd'", $controller);
        $this->assertStringNotContainsString("view('form.roomadd'", $controller);
        $this->assertStringNotContainsString("view('form.roomedit'", $controller);
    }

    public function test_wedding_admin_forms_are_sourced_from_backend_operations_structure(): void
    {
        $controllers = collect([
            app_path('Http/Controllers/WeddingsController.php'),
            app_path('Http/Controllers/HotelsAdminController.php'),
            app_path('Http/Controllers/WeddingReceptionVenuesController.php'),
            app_path('Http/Controllers/WeddingLunchVenuesController.php'),
            app_path('Http/Controllers/WeddingDinnerVenuesController.php'),
            app_path('Http/Controllers/WeddingMenuController.php'),
        ])->map(fn ($path) => file_get_contents($path))->implode("\n");

        $forms = [
            'create' => 'weddingadd',
            'edit' => 'weddingedit',
            'venue-create' => 'wedding-venue-add',
            'venue-edit' => 'wedding-venue-edit',
            'reception-venue-edit' => 'wedding-reception-venue-edit',
            'lunch-venue-edit' => 'wedding-lunch-venue-edit',
            'dinner-venue-create' => 'wedding-dinner-venue-add',
            'dinner-venue-edit' => 'wedding-dinner-venue-edit',
            'dinner-package-create' => 'wedding-dinner-package-add',
            'dinner-package-edit' => 'wedding-dinner-package-edit',
            'food-and-beverage-create' => 'wedding-add-food-and-beverage',
        ];

        foreach ($forms as $target => $legacy) {
            $this->assertFileExists(resource_path("views/backend/operations/weddings/forms/{$target}.blade.php"));
            $this->assertFileDoesNotExist(resource_path("views/form/{$legacy}.blade.php"));
        }

        $this->assertStringContainsString("view('backend.operations.weddings.forms.create'", $controllers);
        $this->assertStringContainsString("view('backend.operations.weddings.forms.edit'", $controllers);
        $this->assertStringContainsString("view('backend.operations.weddings.forms.venue-create'", $controllers);
        $this->assertStringContainsString("view('backend.operations.weddings.forms.venue-edit'", $controllers);
        $this->assertStringContainsString("view('backend.operations.weddings.forms.reception-venue-edit'", $controllers);
        $this->assertStringContainsString("view('backend.operations.weddings.forms.lunch-venue-edit'", $controllers);
        $this->assertStringContainsString("view('backend.operations.weddings.forms.dinner-venue-create'", $controllers);
        $this->assertStringContainsString("view('backend.operations.weddings.forms.dinner-venue-edit'", $controllers);
        $this->assertStringContainsString("view('backend.operations.weddings.forms.dinner-package-create'", $controllers);
        $this->assertStringContainsString("view('backend.operations.weddings.forms.dinner-package-edit'", $controllers);
        $this->assertStringContainsString("view('backend.operations.weddings.forms.food-and-beverage-create'", $controllers);
        $this->assertStringNotContainsString("view('form.weddingadd'", $controllers);
        $this->assertStringNotContainsString("view('form.weddingedit'", $controllers);
        $this->assertStringNotContainsString("view('form.wedding-venue-add'", $controllers);
        $this->assertStringNotContainsString("view('form.wedding-venue-edit'", $controllers);
        $this->assertStringNotContainsString("view('form.wedding-reception-venue-edit'", $controllers);
        $this->assertStringNotContainsString("view('form.wedding-lunch-venue-edit'", $controllers);
        $this->assertStringNotContainsString("view('form.wedding-dinner-venue-add'", $controllers);
        $this->assertStringNotContainsString("view('form.wedding-dinner-venue-edit'", $controllers);
        $this->assertStringNotContainsString("view('form.wedding-dinner-package-add'", $controllers);
        $this->assertStringNotContainsString("view('form.wedding-dinner-package-edit'", $controllers);
        $this->assertStringNotContainsString("view('form.wedding-add-food-and-beverage'", $controllers);
    }

    public function test_partner_service_forms_are_sourced_from_backend_operations_structure(): void
    {
        $controller = file_get_contents(app_path('Http/Controllers/PartnersController.php'));

        $this->assertFileExists(resource_path('views/backend/operations/partners/forms/add-activity.blade.php'));
        $this->assertFileExists(resource_path('views/backend/operations/partners/forms/add-tour.blade.php'));
        $this->assertFileDoesNotExist(resource_path('views/form/partner-add-activity.blade.php'));
        $this->assertFileDoesNotExist(resource_path('views/form/partner-add-tour.blade.php'));
        $this->assertStringContainsString("view('backend.operations.partners.forms.add-activity'", $controller);
        $this->assertStringContainsString("view('backend.operations.partners.forms.add-tour'", $controller);
        $this->assertStringNotContainsString("view('form.partner-add-activity'", $controller);
        $this->assertStringNotContainsString("view('form.partner-add-tour'", $controller);
    }

    public function test_frontend_order_booking_forms_are_sourced_from_frontend_home_structure(): void
    {
        $controller = file_get_contents(app_path('Http/Controllers/OrderController.php'));
        $forms = [
            'hotel-normal' => 'order-hotel-normal',
            'hotel-package' => 'order-hotel-package',
            'hotel-promo' => 'order-hotel-promo',
            'transport' => 'order-transport',
        ];

        foreach ($forms as $target => $legacy) {
            $this->assertFileExists(resource_path("views/frontend/home/booking/orders/{$target}.blade.php"));
            $this->assertFileDoesNotExist(resource_path("views/form/{$legacy}.blade.php"));
        }

        $this->assertStringContainsString("view('frontend.home.booking.orders.hotel-normal'", $controller);
        $this->assertStringContainsString("view('frontend.home.booking.orders.hotel-package'", $controller);
        $this->assertStringContainsString("view('frontend.home.booking.orders.hotel-promo'", $controller);
        $this->assertStringContainsString("view('frontend.home.booking.orders.transport'", $controller);
        $this->assertStringNotContainsString("view('form.order-hotel-normal'", $controller);
        $this->assertStringNotContainsString("view('form.order-hotel-package'", $controller);
        $this->assertStringNotContainsString("view('form.order-hotel-promo'", $controller);
        $this->assertStringNotContainsString("view('form.order-transport'", $controller);
    }

    public function test_legacy_form_view_namespace_has_no_active_files(): void
    {
        $legacyFormPath = resource_path('views/form');
        $legacyFiles = [];

        if (is_dir($legacyFormPath)) {
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($legacyFormPath, \FilesystemIterator::SKIP_DOTS)
            );

            foreach ($iterator as $file) {
                if ($file->isFile()) {
                    $legacyFiles[] = str_replace(base_path() . DIRECTORY_SEPARATOR, '', $file->getPathname());
                }
            }
        }

        $this->assertFileExists(resource_path('views/frontend/home/orders/weddings/legacy/order-weddings.blade.php'));
        $this->assertFileExists(resource_path('views/frontend/home/orders/weddings/legacy/order-weddings-backup.blade.php'));
        $this->assertSame([], $legacyFiles);
    }

    public function test_review_views_are_sourced_from_frontend_home_structure(): void
    {
        $controller = file_get_contents(app_path('Http/Controllers/ReviewController.php'));
        $reviewFiles = [
            'index',
            'wedding-index',
            'print-reviews',
            'print-wedding-reviews',
            'create',
            'create-review',
            'create-wedding-review',
            'review_link_form',
            'wedding_review_link_form',
            'layouts/app',
            'partials/review_card',
            'partials/review_modal',
        ];

        foreach ($reviewFiles as $reviewFile) {
            $this->assertFileExists(resource_path("views/frontend/home/reviews/{$reviewFile}.blade.php"));
            $this->assertFileDoesNotExist(resource_path("views/home/reviews/{$reviewFile}.blade.php"));
        }

        $this->assertStringContainsString("view('frontend.home.reviews.index'", $controller);
        $this->assertStringContainsString("view('frontend.home.reviews.wedding-index'", $controller);
        $this->assertStringContainsString("view('frontend.home.reviews.print-reviews'", $controller);
        $this->assertStringContainsString("view('frontend.home.reviews.print-wedding-reviews'", $controller);
        $this->assertStringContainsString("view('frontend.home.reviews.create'", $controller);
        $this->assertStringContainsString("view('frontend.home.reviews.review_link_form'", $controller);
        $this->assertStringContainsString("view('frontend.home.reviews.wedding_review_link_form'", $controller);
        $this->assertStringNotContainsString("view('home.reviews.", $controller);

        $reviewFormTemplates = collect([
            resource_path('views/frontend/home/reviews/create.blade.php'),
            resource_path('views/frontend/home/reviews/create-review.blade.php'),
            resource_path('views/frontend/home/reviews/create-wedding-review.blade.php'),
        ])->map(fn ($path) => file_get_contents($path))->implode("\n");

        $this->assertStringContainsString("@extends('frontend.home.reviews.layouts.app')", $reviewFormTemplates);
        $this->assertStringNotContainsString("@extends('home.reviews.layouts.app')", $reviewFormTemplates);
    }

    public function test_home_public_legacy_routes_are_sourced_from_frontend_landing_page_structure(): void
    {
        $controller = file_get_contents(app_path('Http/Controllers/HomeController.php'));

        $this->assertFileExists(resource_path('views/frontend/landing-page/services/index.blade.php'));
        $this->assertFileDoesNotExist(resource_path('views/home/landing-page/services.blade.php'));
        $this->assertFileDoesNotExist(resource_path('views/home/landing-page/accommodation.blade.php'));
        $this->assertFileDoesNotExist(resource_path('views/home/landing-page/tour-package.blade.php'));
        $this->assertFileDoesNotExist(resource_path('views/home/hotels/detail.blade.php'));
        $this->assertFileDoesNotExist(resource_path('views/home/tour-packages/detail.blade.php'));
        $this->assertStringContainsString("app(FrontEndController::class)->accommodation_service", $controller);
        $this->assertStringContainsString("view('frontend.landing-page.services.index'", $controller);
        $this->assertStringContainsString("app(FrontEndController::class)->tour_package_services", $controller);
        $this->assertStringContainsString("redirect()->route('view.accommodation-detail'", $controller);
        $this->assertStringContainsString("redirect()->route('view.tour-detail'", $controller);
        $this->assertStringNotContainsString("view('home.landing-page.accommodation'", $controller);
        $this->assertStringNotContainsString("view('home.landing-page.services'", $controller);
        $this->assertStringNotContainsString("view('home.landing-page.tour-package'", $controller);
        $this->assertStringNotContainsString("view('home.hotels.detail'", $controller);
        $this->assertStringNotContainsString("view('home.tour-packages.detail'", $controller);

        $servicesResponse = $this->get(route('services'));
        $servicesResponse->assertOk();
        $servicesResponse->assertViewIs('frontend.landing-page.services.index');
        $servicesResponse->assertViewHasAll(['serviceCards', 'servicePreviews']);

        $legacyTourDirectoryResponse = $this->get(route('tour-package-service'));
        $legacyTourDirectoryResponse->assertOk();
        $legacyTourDirectoryResponse->assertViewIs('frontend.landing-page.tours.directory');
    }

    public function test_home_agent_and_shared_partials_are_sourced_from_frontend_structure(): void
    {
        $agentController = file_get_contents(app_path('Http/Controllers/AgentRegistrationController.php'));
        $accommodationDetail = file_get_contents(resource_path('views/frontend/landing-page/accommodations/detail.blade.php'));
        $legacyLayout = file_get_contents(resource_path('views/layouts/app.blade.php'));

        $this->assertFileExists(resource_path('views/frontend/home/agents/register.blade.php'));
        $this->assertFileExists(resource_path('views/frontend/shared/room-modal.blade.php'));
        $this->assertFileExists(resource_path('views/frontend/shared/footer-legacy.blade.php'));
        $this->assertFileDoesNotExist(resource_path('views/home/agents/register.blade.php'));
        $this->assertFileDoesNotExist(resource_path('views/home/partials/room-modal.blade.php'));
        $this->assertFileDoesNotExist(resource_path('views/home/partials/footer.blade.php'));
        $this->assertStringContainsString("view('frontend.home.agents.register'", $agentController);
        $this->assertStringNotContainsString("view('home.agents.register'", $agentController);
        $this->assertStringContainsString("@include('frontend.shared.room-modal')", $accommodationDetail);
        $this->assertStringNotContainsString("@include('home.partials.room-modal')", $accommodationDetail);
        $this->assertStringContainsString("@include('frontend.shared.footer-legacy')", $legacyLayout);
        $this->assertStringNotContainsString("@include('home.partials.footer')", $legacyLayout);
    }

    public function test_legacy_home_view_namespace_has_no_active_blade_files(): void
    {
        $legacyHomePath = resource_path('views/home');
        $legacyFiles = [];

        if (is_dir($legacyHomePath)) {
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($legacyHomePath, \FilesystemIterator::SKIP_DOTS)
            );

            foreach ($iterator as $file) {
                if ($file->isFile() && str_ends_with($file->getFilename(), '.blade.php')) {
                    $legacyFiles[] = str_replace(base_path() . DIRECTORY_SEPARATOR, '', $file->getPathname());
                }
            }
        }

        $this->assertSame([], $legacyFiles);
    }

    public function test_known_orphan_main_legacy_files_are_removed(): void
    {
        $orphanFiles = [
            'bookingcode-promotion.blade.php',
            'createdata.sql',
            'dashboard.blade.php',
            'download-data-hotel.blade.php',
            'error-500.blade.php',
            'error-msg.blade.php',
            'loading-page.blade.php',
            'test-input.blade.php',
            'wedding-planner-detail.blade.php',
            'weddingdetail.blade.php',
            'weddingsearch.blade.php',
        ];

        foreach ($orphanFiles as $orphanFile) {
            $this->assertFileDoesNotExist(resource_path("views/main/{$orphanFile}"));
        }
    }

    public function test_backend_admin_user_views_are_sourced_from_backend_admin_structure(): void
    {
        $controller = file_get_contents(app_path('Http/Controllers/UsersController.php'));

        $this->assertFileExists(resource_path('views/backend/admin/users/index.blade.php'));
        $this->assertFileExists(resource_path('views/backend/admin/users/show.blade.php'));
        $this->assertFileExists(resource_path('views/backend/admin/users/manager.blade.php'));
        $this->assertFileDoesNotExist(resource_path('views/admin/users.blade.php'));
        $this->assertFileDoesNotExist(resource_path('views/admin/userdetail.blade.php'));
        $this->assertFileDoesNotExist(resource_path('views/admin/user-manager.blade.php'));
        $this->assertStringContainsString("view('backend.admin.users.index'", $controller);
        $this->assertStringContainsString("view('backend.admin.users.show'", $controller);
        $this->assertStringContainsString("view('backend.admin.users.manager'", $controller);
        $this->assertStringNotContainsString("view('admin.users'", $controller);
        $this->assertStringNotContainsString("view('admin.userdetail'", $controller);
        $this->assertStringNotContainsString("view('admin.user-manager'", $controller);
    }

    public function test_backend_finance_and_report_views_are_sourced_from_backend_structure(): void
    {
        $invoiceController = file_get_contents(app_path('Http/Controllers/InvoiceAdminController.php'));
        $downloadController = file_get_contents(app_path('Http/Controllers/DownloadDataHotelController.php'));
        $reportViews = [
            'index',
            'hotel',
            'hotel-test',
            'hotel-package',
            'hotel-promo',
            'tour',
        ];

        $this->assertFileExists(resource_path('views/backend/finance/invoices/index.blade.php'));
        $this->assertFileExists(resource_path('views/backend/finance/invoices/detail.blade.php'));
        $this->assertFileDoesNotExist(resource_path('views/admin/invoice.blade.php'));
        $this->assertFileDoesNotExist(resource_path('views/admin/invoice-detail.blade.php'));
        $this->assertStringContainsString("view('backend.finance.invoices.index'", $invoiceController);
        $this->assertStringContainsString("view('backend.finance.invoices.detail'", $invoiceController);
        $this->assertStringNotContainsString("view('admin.invoice'", $invoiceController);
        $this->assertStringNotContainsString("view('admin.invoice-detail'", $invoiceController);

        foreach ($reportViews as $reportView) {
            $this->assertFileExists(resource_path("views/backend/reports/downloads/{$reportView}.blade.php"));
        }

        $this->assertFileDoesNotExist(resource_path('views/main/download.blade.php'));
        $this->assertFileDoesNotExist(resource_path('views/main/downloadhotel.blade.php'));
        $this->assertFileDoesNotExist(resource_path('views/main/download-test.blade.php'));
        $this->assertFileDoesNotExist(resource_path('views/main/download-hotel-package.blade.php'));
        $this->assertFileDoesNotExist(resource_path('views/main/download-hotel-promo.blade.php'));
        $this->assertFileDoesNotExist(resource_path('views/main/download-data-tour.blade.php'));
        $this->assertStringContainsString("view('backend.reports.downloads.index'", $downloadController);
        $this->assertStringContainsString("view('backend.reports.downloads.hotel'", $downloadController);
        $this->assertStringContainsString("view('backend.reports.downloads.hotel-test'", $downloadController);
        $this->assertStringContainsString("view('backend.reports.downloads.hotel-package'", $downloadController);
        $this->assertStringContainsString("view('backend.reports.downloads.hotel-promo'", $downloadController);
        $this->assertStringContainsString("view('backend.reports.downloads.tour'", $downloadController);
        $this->assertStringContainsString("PDF::loadView('backend.reports.downloads.hotel'", $downloadController);
        $this->assertStringNotContainsString("view('main.download", $downloadController);
        $this->assertStringNotContainsString("PDF::loadView('main.downloadhotel'", $downloadController);
    }

    public function test_remaining_user_order_edit_legacy_views_are_cleaned_up(): void
    {
        $controller = file_get_contents(app_path('Http/Controllers/OrderController.php'));
        $legacyWrapper = file_get_contents(resource_path('views/frontend/home/orders/edit-legacy.blade.php'));

        $this->assertFileExists(resource_path('views/frontend/home/orders/edit-room.blade.php'));
        $this->assertFileDoesNotExist(resource_path('views/order/edit-room.blade.php'));
        $this->assertFileDoesNotExist(resource_path('views/order/edit-order-tour.blade.php'));
        $this->assertFileDoesNotExist(resource_path('views/order/edit-order-hotel-promo.blade.php'));
        $this->assertFileDoesNotExist(resource_path('views/order/view-order-hotel-promo.blade.php'));
        $this->assertFileDoesNotExist(resource_path('views/order/add-order-wedding.blade.php'));
        $this->assertStringContainsString("view('frontend.home.orders.edit-room'", $controller);
        $this->assertStringNotContainsString("view('order.edit-room'", $controller);
        $this->assertStringNotContainsString("@include('order.edit-order-tour')", $legacyWrapper);
    }

    public function test_legacy_order_view_namespace_has_no_active_blade_files(): void
    {
        $legacyOrderPath = resource_path('views/order');
        $legacyFiles = [];

        if (is_dir($legacyOrderPath)) {
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($legacyOrderPath, \FilesystemIterator::SKIP_DOTS)
            );

            foreach ($iterator as $file) {
                if ($file->isFile() && str_ends_with($file->getFilename(), '.blade.php')) {
                    $legacyFiles[] = str_replace(base_path() . DIRECTORY_SEPARATOR, '', $file->getPathname());
                }
            }
        }

        $this->assertSame([], $legacyFiles);
    }

    public function test_wedding_order_package_sections_are_sourced_from_frontend_home_structure(): void
    {
        $weddingEdit = file_get_contents(resource_path('views/frontend/home/orders/weddings/edit.blade.php'));
        $sections = [
            'accommodation',
            'additional-services',
            'bride',
            'ceremony-and-decoration-venue',
            'flight',
            'include-services',
            'invitations',
            'reception-and-decoration-venue',
            'suite-and-villa-brides',
            'suite-and-villa-invitations',
            'transports',
            'wedding-detail',
            'wedding-dinner-venue',
            'wedding-lunch-venue',
            'wedding-venue',
        ];

        foreach ($sections as $section) {
            $this->assertFileExists(resource_path("views/frontend/home/orders/weddings/sections/{$section}.blade.php"));
            $this->assertFileDoesNotExist(resource_path("views/order-wedding-package/{$section}.blade.php"));
        }

        $this->assertStringContainsString("@include('frontend.home.orders.weddings.sections.bride')", $weddingEdit);
        $this->assertStringContainsString("@include('frontend.home.orders.weddings.sections.accommodation')", $weddingEdit);
        $this->assertStringContainsString("@include('frontend.home.orders.weddings.sections.transports')", $weddingEdit);
        $this->assertStringNotContainsString("@include('order-wedding-package.", $weddingEdit);
    }

    public function test_admin_panel_view_is_sourced_from_backend_structure(): void
    {
        $controller = file_get_contents(app_path('Http/Controllers/AdminPanelController.php'));
        $view = file_get_contents(resource_path('views/backend/developer/index.blade.php'));

        $this->assertFileExists(resource_path('views/backend/developer/index.blade.php'));
        $this->assertFileDoesNotExist(resource_path('views/admin/adminpanel.blade.php'));
        $this->assertFileDoesNotExist(resource_path('views/admin/panels/index.blade.php'));
        $this->assertStringContainsString("view('backend.developer.index'", $controller);
        $this->assertStringNotContainsString("view('admin.adminpanel'", $controller);
        $this->assertStringNotContainsString("view('admin.panels.index'", $controller);
        $this->assertStringNotContainsString('main-card-box', $view);
        $this->assertStringNotContainsString('Contract Rate Trend', $view);
        $this->assertStringNotContainsString('cdn.jsdelivr.net/npm/chart.js', $view);
        $this->assertStringNotContainsString('adminPanelPriceChart', $view);
        $this->assertStringNotContainsString('Upcoming Pipeline', $view);
        $this->assertStringNotContainsString('Latest Orders', $view);
        $this->assertStringNotContainsString('$orderPipeline', $view);
        $this->assertStringContainsString('Platform Health Checks', $view);
        $this->assertStringNotContainsString('UI Configuration Snapshot', $view);
        $this->assertStringNotContainsString('UI Config', $view);
        $this->assertStringNotContainsString("@include('backend.developer.partials.", $view);
        $this->assertStringContainsString("mix('build/backend/css/admin/panel/index.css')", $view);
        $this->assertStringContainsString("mix('build/backend/js/admin/panel/index.js')", $view);
    }

    public function test_admin_panel_assets_are_sourced_from_backend_structure(): void
    {
        $mix = file_get_contents(base_path('webpack.mix.js'));

        $this->assertFileExists(resource_path('backend/js/admin/panel/index.js'));
        $this->assertFileExists(resource_path('backend/scss/admin/panel/index-entry.scss'));
        $this->assertFileExists(resource_path('backend/scss/admin/panel/_index.scss'));
        $this->assertStringContainsString("resources/backend/js/admin/panel/index.js", $mix);
        $this->assertStringContainsString("resources/backend/scss/admin/panel/index-entry.scss", $mix);
    }

    public function test_admin_panel_controller_returns_backend_dashboard_view_data(): void
    {
        Services::forceCreate([
            'name' => 'Hotels',
            'nicname' => 'hotels',
            'icon' => 'fa fa-hotel',
            'status' => 'Active',
        ]);

        $view = app(\App\Http\Controllers\AdminPanelController::class)->index();

        $this->assertInstanceOf(\Illuminate\View\View::class, $view);
        $this->assertSame('backend.developer.index', $view->name());
        $this->assertArrayHasKey('dashboardStats', $view->getData());
        $this->assertArrayHasKey('services', $view->getData());
        $this->assertArrayHasKey('currencyRates', $view->getData());
        $this->assertArrayHasKey('expectedCurrencies', $view->getData());
        $this->assertArrayHasKey('missingCurrencyRates', $view->getData());
        $this->assertArrayHasKey('developerHealthChecks', $view->getData());
        $this->assertArrayNotHasKey('configs', $view->getData());
        $this->assertArrayNotHasKey('uiConfigSummary', $view->getData());
        $this->assertArrayNotHasKey('orderPipeline', $view->getData());
        $this->assertArrayNotHasKey('recentOrders', $view->getData());
        $this->assertArrayNotHasKey('validOrderRevenue', $view->getData());
        $this->assertEqualsCanonicalizing([
            'dashboardStats',
            'services',
            'currencyRates',
            'expectedCurrencies',
            'missingCurrencyRates',
            'developerHealthChecks',
        ], array_intersect(array_keys($view->getData()), [
            'dashboardStats',
            'services',
            'currencyRates',
            'expectedCurrencies',
            'missingCurrencyRates',
            'developerHealthChecks',
        ]));
    }

    public function test_ui_config_feature_is_removed_from_project_runtime(): void
    {
        $routeFile = file_get_contents(base_path('routes/web.php'));
        $kernel = file_get_contents(app_path('Http/Kernel.php'));
        $helpers = file_get_contents(app_path('Helpers/helpers.php'));
        $provider = file_get_contents(app_path('Providers/AppServiceProvider.php'));
        $footJs = file_get_contents(resource_path('views/layouts/footjs.blade.php'));
        $loginView = file_get_contents(resource_path('views/auth/login.blade.php'));
        $registerView = file_get_contents(resource_path('views/auth/register.blade.php'));
        $welcomeView = file_get_contents(resource_path('views/welcome.blade.php'));

        $this->assertFileDoesNotExist(app_path('Http/Controllers/UiConfigController.php'));
        $this->assertFileDoesNotExist(app_path('Models/UiConfig.php'));
        $this->assertFileDoesNotExist(app_path('Http/Middleware/CheckPageAccess.php'));
        $this->assertFileDoesNotExist(app_path('Policies/UiConfigPolicy.php'));
        $this->assertFileDoesNotExist(app_path('Http/Requests/StoreUiConfigRequest.php'));
        $this->assertFileDoesNotExist(app_path('Http/Requests/UpdateUiConfigRequest.php'));
        $this->assertFileDoesNotExist(database_path('factories/UiConfigFactory.php'));
        $this->assertFileDoesNotExist(database_path('seeders/UiConfigSeeder.php'));
        $this->assertFileDoesNotExist(database_path('migrations/2025_03_06_090758_create_ui_configs_table.php'));
        $this->assertFileDoesNotExist(resource_path('views/admin/ui-config.blade.php'));
        $this->assertStringNotContainsString('UiConfigController', $routeFile);
        $this->assertStringNotContainsString('/ui-config', $routeFile);
        $this->assertStringNotContainsString('admin.ui-config', $routeFile);
        $this->assertStringNotContainsString('page.access', $routeFile);
        $this->assertStringNotContainsString('page.access', $kernel);
        $this->assertStringNotContainsString('CheckPageAccess', $kernel);
        $this->assertStringNotContainsString('ui_config', $helpers);
        $this->assertStringNotContainsString('UiConfig', $helpers);
        $this->assertStringNotContainsString('uiEnabled', $provider);
        $this->assertStringNotContainsString('/ui-config/toggle', $footJs);
        $this->assertStringNotContainsString('@uiEnabled', $loginView);
        $this->assertStringNotContainsString('@uiEnabled', $registerView);
        $this->assertStringNotContainsString('@uiEnabled', $welcomeView);
        $this->assertStringNotContainsString('@elseUiEnabled', $registerView);
        $this->assertStringNotContainsString('@endUiEnabled', $loginView . $registerView . $welcomeView);
    }
}
