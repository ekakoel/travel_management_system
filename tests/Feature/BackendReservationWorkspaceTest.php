<?php

namespace Tests\Feature;

use App\Http\Controllers\ReservationController;
use App\Http\Requests\Backend\Operations\Reservations\StoreManualReservationRequest;
use App\Models\Reservation;
use App\Models\User;
use App\Services\Reservations\ReservationAdminService;
use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class BackendReservationWorkspaceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('password')->nullable();
            $table->string('type');
            $table->string('position')->nullable();
            $table->string('status');
            $table->string('code')->nullable();
            $table->string('office')->nullable();
            $table->timestamps();
        });
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->string('rsv_no');
            $table->string('service');
            $table->unsignedBigInteger('agn_id');
            $table->unsignedBigInteger('adm_id');
            $table->string('checkin')->nullable();
            $table->string('checkout')->nullable();
            $table->text('additional_info')->nullable();
            $table->string('status');
            $table->timestamp('deleted_at')->nullable();
            $table->timestamps();
        });
        Schema::create('invoice_admins', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('rsv_id');
            $table->string('inv_no')->nullable();
            $table->string('due_date')->nullable();
            $table->timestamps();
        });
        Schema::create('guests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('rsv_id')->nullable();
            $table->timestamps();
        });
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('rsv_id')->nullable();
            $table->timestamps();
        });
        Schema::create('spks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('reservation_id')->nullable();
            $table->timestamps();
        });
        Schema::create('log_data', function (Blueprint $table) {
            $table->id();
            $table->string('service')->nullable();
            $table->string('action')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamps();
        });
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_index_data_only_contains_active_reservations_assigned_to_the_admin(): void
    {
        Carbon::setTestNow('2026-08-10 10:00:00');
        [$admin, $otherAdmin, $agent] = $this->users();
        $assigned = $this->reservation($admin, $agent, 'RSV-A', 'Active');
        $assigned->update(['additional_info' => '<p>Meet the agent at the hotel lobby.</p>']);
        $this->reservation($admin, $agent, 'RSV-PENDING', 'Pending');
        $this->reservation($admin, $agent, 'RSV-DRAFT', 'Draft');
        $this->reservation($otherAdmin, $agent, 'RSV-B', 'Active');
        DB::table('invoice_admins')->insert([
            'rsv_id' => $assigned->id,
            'inv_no' => 'INV-001',
            'due_date' => '2026-08-09',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $data = app(ReservationAdminService::class)->indexData($admin);

        $this->assertCount(1, $data['reservationRows']);
        $this->assertSame('RSV-A', $data['reservationRows']->first()['number']);
        $this->assertSame('INV-001', $data['reservationRows']->first()['invoice']);
        $this->assertTrue($data['reservationRows']->first()['is_overdue']);
        $this->assertSame('Active', $data['reservationRows']->first()['status']);
        $this->assertSame(1, $data['reservationStats'][0]['value']);
        $this->assertCount(1, $data['reservationCalendarEvents']);
        $this->assertSame('2026-09-01', $data['reservationCalendarEvents']->first()['start']);
        $this->assertSame('2026-09-04', $data['reservationCalendarEvents']->first()['end']);
        $this->assertSame('Meet the agent at the hotel lobby.', $data['reservationCalendarEvents']->first()['note']);
        $this->assertSame(route('view.reservation.detail', $assigned), $data['reservationCalendarEvents']->first()['detailUrl']);
        $this->assertSame(__('reservations.calendar_today'), $data['reservationCalendarSettings']['today']);
        $this->assertCount(12, $data['reservationCalendarSettings']['monthNames']);
    }

    public function test_pending_reservation_detail_redirects_before_rendering_operational_view(): void
    {
        [$admin, , $agent] = $this->users();
        $pending = $this->reservation($admin, $agent, 'RSV-PENDING', 'Pending');

        $response = app(ReservationController::class)->view_detail_reservation($pending->id);

        $this->assertSame(route('view.reservation'), $response->getTargetUrl());
        $this->assertSame(
            __('reservations.active_only_detail'),
            session('invalid')
        );
    }

    public function test_manual_number_generation_continues_after_z_and_uses_server_actor(): void
    {
        Carbon::setTestNow('2026-08-10 10:00:00');
        [$admin, , $agent] = $this->users();
        $prefix = 'AGT260810';

        foreach (range('A', 'Z') as $suffix) {
            $this->reservation($admin, $agent, $prefix.$suffix, 'Draft');
        }

        $reservation = app(ReservationAdminService::class)->createManual([
            'agn_id' => $agent->id,
            'checkin' => '2026-09-01',
            'checkout' => '2026-09-03',
        ], $admin);

        $this->assertSame($prefix.'AA', $reservation->rsv_no);
        $this->assertSame($admin->id, $reservation->adm_id);
        $this->assertSame('Reservation', $reservation->service);
        $this->assertDatabaseHas('log_data', [
            'action' => 'Create Reservation',
            'user_id' => $admin->id,
        ]);
    }

    public function test_only_empty_manual_draft_can_be_removed(): void
    {
        [$admin, , $agent] = $this->users();
        $draft = $this->reservation($admin, $agent, 'DRAFT-1', 'Draft');
        $active = $this->reservation($admin, $agent, 'ACTIVE-1', 'Active');
        $service = app(ReservationAdminService::class);

        $service->deleteManualDraft($draft, $admin);

        $this->assertDatabaseMissing('reservations', ['id' => $draft->id]);

        $this->expectException(ValidationException::class);
        $service->deleteManualDraft($active, $admin);
    }

    public function test_manual_request_authorizes_operations_roles_and_rejects_invalid_agent_or_dates(): void
    {
        [$admin, , $agent] = $this->users();
        $request = new StoreManualReservationRequest();
        $request->setUserResolver(fn () => $admin);

        $this->assertTrue($request->authorize());

        $validator = Validator::make([
            'agn_id' => $admin->id,
            'checkin' => '2026-09-03',
            'checkout' => '2026-09-01',
        ], $request->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('agn_id', $validator->errors()->toArray());
        $this->assertArrayHasKey('checkout', $validator->errors()->toArray());

        $valid = Validator::make([
            'agn_id' => $agent->id,
            'checkin' => '2026-09-01',
            'checkout' => '2026-09-03',
        ], $request->rules());

        $this->assertFalse($valid->fails());
    }

    public function test_workspace_view_and_routes_use_backend_contracts(): void
    {
        $view = file_get_contents(resource_path('views/backend/operations/reservations/index.blade.php'));
        $script = file_get_contents(resource_path('backend/js/operations/reservations/index.js'));
        $routes = file_get_contents(base_path('routes/web.php'));

        $this->assertStringContainsString('<x-backend.page-hero', $view);
        $this->assertStringContainsString('backend-kpi-grid', $view);
        $this->assertStringContainsString('backend-filter-panel', $view);
        $this->assertStringContainsString('backend-table-card-list', $view);
        $this->assertStringContainsString("route('view.reservation.detail'", $view);
        $this->assertStringNotContainsString('data-reservation-filter="status"', $view);
        $this->assertStringContainsString('data-reservation-calendar', $view);
        $this->assertStringContainsString('data-reservation-calendar-events', $view);
        $this->assertStringContainsString("defaultView: compact ? 'listMonth' : 'month'", $script);
        $this->assertStringContainsString('editable: false', $script);
        $this->assertStringContainsString('eventLimit: 3', $script);
        $this->assertStringContainsString("name('admin.reservations.manual.store')", $routes);
        $this->assertStringContainsString("name('admin.reservations.destroy')", $routes);
        $this->assertStringNotContainsString('/reservation-{{', $view);
    }

    private function users(): array
    {
        $admin = User::forceCreate(['name' => 'Developer', 'type' => 'admin', 'position' => 'developer', 'status' => 'Active']);
        $otherAdmin = User::forceCreate(['name' => 'Reservation Staff', 'type' => 'admin', 'position' => 'reservation', 'status' => 'Active']);
        $agent = User::forceCreate(['name' => 'Agent One', 'type' => 'user', 'position' => 'agent', 'status' => 'Active', 'code' => 'AGT', 'office' => 'Taipei']);

        return [$admin, $otherAdmin, $agent];
    }

    private function reservation(User $admin, User $agent, string $number, string $status): Reservation
    {
        return Reservation::create([
            'rsv_no' => $number,
            'service' => 'Reservation',
            'agn_id' => $agent->id,
            'adm_id' => $admin->id,
            'checkin' => '2026-09-01',
            'checkout' => '2026-09-03',
            'status' => $status,
        ]);
    }
}
