<?php

namespace Tests\Feature;

use App\Models\Orders;
use App\Services\AccommodationOrderLifecycleService;
use App\Services\ActivityOrderLifecycleService;
use App\Services\TourOrderLifecycleService;
use App\Services\TransportOrderLifecycleService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class PublicOrderLifecycleGroupingTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (config('database.default') !== 'sqlite'
            || config('database.connections.sqlite.database') !== ':memory:') {
            $this->markTestSkipped(
                'Public lifecycle grouping tests require sqlite :memory: to avoid touching active data.'
            );
        }

        Schema::dropIfExists('orders');
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('service');
            $table->string('status');
            $table->dateTime('checkin')->nullable();
            $table->dateTime('checkout')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->unsignedBigInteger('completed_by')->nullable();
            $table->timestamps();
        });
    }

    #[DataProvider('publicServiceGroupingProvider')]
    public function test_public_service_current_and_history_grouping_is_consistent(
        string $service,
        string $otherService,
        string $lifecycleClass,
        string $currentMethod,
        string $historyMethod
    ): void {
        $currentIds = [
            $this->insertOrder($service, 'Draft'),
            $this->insertOrder($service, 'Pending'),
            $this->insertOrder($service, 'Approved'),
            $this->insertOrder($service, 'Paid'),
        ];

        $paidCompletedId = $this->insertOrder($service, 'Paid', '2026-07-28 12:00:00');
        $terminalIds = [];

        foreach (['Canceled', 'Rejected', 'Invalid', 'Deleted'] as $status) {
            $terminalIds[$status] = $this->insertOrder($service, $status);
        }

        $legacyCompletedId = $this->insertOrder($service, 'Completed');
        $invalidCompletedMarkerId = $this->insertOrder(
            $service,
            'Approved',
            '2026-07-28 12:00:00'
        );

        $otherServiceIds = [
            $this->insertOrder($otherService, 'Paid'),
            $this->insertOrder($otherService, 'Paid', '2026-07-28 12:00:00'),
            $this->insertOrder($otherService, 'Invalid'),
        ];

        $lifecycle = app($lifecycleClass);
        $actualCurrentIds = $lifecycle->{$currentMethod}(Orders::query(), now())
            ->pluck('id')
            ->all();
        $actualHistoryIds = $lifecycle->{$historyMethod}(Orders::query(), now())
            ->pluck('id')
            ->all();

        sort($currentIds);
        sort($actualCurrentIds);

        $expectedHistoryIds = array_merge(
            [$paidCompletedId],
            array_values($terminalIds),
            [$legacyCompletedId]
        );
        sort($expectedHistoryIds);
        sort($actualHistoryIds);

        $this->assertSame($currentIds, $actualCurrentIds);
        $this->assertSame($expectedHistoryIds, $actualHistoryIds);
        $this->assertNotContains($terminalIds['Invalid'], $actualCurrentIds);
        $this->assertContains($terminalIds['Invalid'], $actualHistoryIds);
        $this->assertContains($paidCompletedId, $actualHistoryIds);
        $this->assertNotContains($invalidCompletedMarkerId, $actualCurrentIds);
        $this->assertNotContains($invalidCompletedMarkerId, $actualHistoryIds);

        foreach ($terminalIds as $terminalId) {
            $this->assertContains($terminalId, $actualHistoryIds);
        }

        foreach ($otherServiceIds as $otherServiceId) {
            $this->assertNotContains($otherServiceId, $actualCurrentIds);
            $this->assertNotContains($otherServiceId, $actualHistoryIds);
        }
    }

    public static function publicServiceGroupingProvider(): array
    {
        return [
            'Accommodation' => [
                'Hotel',
                Orders::PUBLIC_TRANSPORT_SERVICE,
                AccommodationOrderLifecycleService::class,
                'applyAccommodationCurrentScope',
                'applyAccommodationHistoryScope',
            ],
            'Public Transport' => [
                Orders::PUBLIC_TRANSPORT_SERVICE,
                'Hotel',
                TransportOrderLifecycleService::class,
                'applyTransportCurrentScope',
                'applyTransportHistoryScope',
            ],
            'Tour Package' => [
                Orders::PUBLIC_TOUR_SERVICE,
                Orders::PUBLIC_ACTIVITY_SERVICE,
                TourOrderLifecycleService::class,
                'applyTourCurrentScope',
                'applyTourHistoryScope',
            ],
            'Activity' => [
                Orders::PUBLIC_ACTIVITY_SERVICE,
                Orders::PUBLIC_TOUR_SERVICE,
                ActivityOrderLifecycleService::class,
                'applyActivityCurrentScope',
                'applyActivityHistoryScope',
            ],
        ];
    }

    private function insertOrder(
        string $service,
        string $status,
        ?string $completedAt = null
    ): int {
        return DB::table('orders')->insertGetId([
            'service' => $service,
            'status' => $status,
            'checkin' => '2020-01-01 09:00:00',
            'checkout' => '2020-01-01 11:00:00',
            'completed_at' => $completedAt,
            'completed_by' => null,
            'created_at' => '2026-07-28 08:00:00',
            'updated_at' => '2026-07-28 08:00:00',
        ]);
    }
}
