<?php

namespace Tests\Unit;

use App\Http\Requests\Hotels\StoreAccommodationOrderRequest;
use Illuminate\Contracts\Validation\ValidatesWhenResolved;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

class StoreAccommodationOrderRequestTest extends TestCase
{
    public function test_guest_manifest_partial_compiles_and_contains_complete_old_state_json(): void
    {
        $compiled = Blade::compileString(file_get_contents(
            resource_path('views/partials/hotel-booking-guest-manifest.blade.php')
        ));

        $this->assertStringContainsString('data-booking-old-state', $compiled);
        $this->assertStringContainsString("'room_adults' => old('room_adults', [])", $compiled);
        $this->assertStringContainsString("'extra_bed_id' => old('extra_bed_id', [])", $compiled);
        $this->assertStringContainsString('JSON_HEX_QUOT', $compiled);
    }

    public function test_structured_room_and_guest_manifest_is_normalized_for_legacy_order_columns(): void
    {
        $request = $this->makeRequest([
            'hotel_booking_version' => 2,
            'terms_accepted' => '1',
            'room_adults' => [2],
            'room_children' => [1],
            'room_child_ages' => [[8]],
            'guest_name' => ['Adult One', 'Adult Two', 'Child One'],
            'guest_room' => [1, 1, 1],
            'guest_category' => ['Adult', 'Adult', 'Child'],
            'guest_phone' => ['', '+62 812 0000', ''],
            'guest_sex' => ['Male', 'Female', 'Female'],
        ]);

        $request->validateResolved();

        $this->assertSame([3], $request->input('number_of_guests'));
    }

    public function test_guest_manifest_must_match_room_occupancy_and_child_ages(): void
    {
        $request = $this->makeRequest([
            'hotel_booking_version' => 2,
            'terms_accepted' => '1',
            'room_adults' => [1],
            'room_children' => [1],
            'room_child_ages' => [[]],
            'guest_name' => ['Only Adult'],
            'guest_room' => [1],
            'guest_category' => ['Adult'],
            'guest_phone' => [''],
            'guest_sex' => ['Male'],
        ]);

        try {
            $request->validateResolved();
            $this->fail('Expected the occupancy validation to fail.');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('guest_name', $exception->errors());
            $this->assertArrayHasKey('room_child_ages.0', $exception->errors());
        }
    }

    private function makeRequest(array $payload): StoreAccommodationOrderRequest
    {
        /** @var StoreAccommodationOrderRequest&ValidatesWhenResolved $request */
        $request = StoreAccommodationOrderRequest::create('/hotel-order', 'POST', $payload);
        $request->setContainer($this->app);
        $request->setRedirector($this->app->make('redirect'));
        $request->setUserResolver(fn () => (object) ['id' => 1]);

        return $request;
    }
}
