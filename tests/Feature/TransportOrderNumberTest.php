<?php

namespace Tests\Feature;

use App\Http\Controllers\OrderController;
use App\Models\Orders;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use ReflectionMethod;
use Tests\TestCase;

class TransportOrderNumberTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        if (config('database.default') === 'sqlite' && config('database.connections.sqlite.database') === ':memory:') {
            $this->prepareSqliteSchema();
        }
    }

    private function prepareSqliteSchema(): void
    {
        if (!Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->id();
                $table->string('username')->nullable();
                $table->string('name')->nullable();
                $table->string('type')->nullable();
                $table->string('code')->nullable();
                $table->string('email')->nullable();
                $table->string('position')->nullable();
                $table->string('phone')->nullable();
                $table->string('office')->nullable();
                $table->string('address')->nullable();
                $table->string('country')->nullable();
                $table->string('status')->nullable();
                $table->boolean('is_approved')->default(false);
                $table->timestamp('email_verified_at')->nullable();
                $table->string('password')->nullable();
                $table->boolean('is_subscribed')->default(false);
                $table->boolean('subscriber')->default(false);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('orders')) {
            Schema::create('orders', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->unsignedBigInteger('sales_agent')->nullable();
                $table->string('orderno')->nullable();
                $table->string('name')->nullable();
                $table->string('email')->nullable();
                $table->string('servicename')->nullable();
                $table->string('service')->nullable();
                $table->string('status')->nullable();
                $table->string('confirmation_order')->nullable();
                $table->timestamps();
            });
        }
    }

    private function makeAgent(string $code = 'ABC'): User
    {
        return User::create([
            'username' => 'transport-agent-' . uniqid(),
            'name' => 'Transport Agent',
            'type' => 'partner',
            'code' => $code,
            'email' => 'transport-agent-' . uniqid() . '@example.test',
            'position' => 'agent',
            'phone' => '08123456789',
            'office' => 'BLK',
            'address' => 'Bali',
            'country' => 'Indonesia',
            'status' => 'Active',
            'is_approved' => true,
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'is_subscribed' => false,
            'subscriber' => false,
        ]);
    }

    private function createTransportOrder(string $orderNumber, User $agent): Orders
    {
        return Orders::create([
            'user_id' => $agent->id,
            'sales_agent' => $agent->id,
            'orderno' => $orderNumber,
            'name' => $agent->name,
            'email' => $agent->email,
            'servicename' => 'Transport Test',
            'service' => 'Transport',
            'status' => 'Pending',
        ]);
    }

    private function generateTransportOrderNumber(User $agent, Carbon $date): string
    {
        $method = new ReflectionMethod(OrderController::class, 'generateTransportOrderNumber');
        $method->setAccessible(true);

        return $method->invoke(new OrderController(), $agent, $date);
    }

    public function test_transport_order_number_uses_agent_code_order_date_and_first_daily_suffix(): void
    {
        $agent = $this->makeAgent('ABC');
        $date = Carbon::create(2026, 7, 15, 10, 30, 0);

        $this->assertSame('ABC260715A', $this->generateTransportOrderNumber($agent, $date));
    }

    public function test_transport_order_number_increments_suffix_for_same_agent_and_day(): void
    {
        $agent = $this->makeAgent('ABC');
        $date = Carbon::create(2026, 7, 15, 10, 30, 0);

        $this->createTransportOrder('ABC260715A', $agent);
        $this->createTransportOrder('ABC260715B', $agent);

        $this->assertSame('ABC260715C', $this->generateTransportOrderNumber($agent, $date));
    }

    public function test_transport_order_number_continues_after_z_until_aaa(): void
    {
        $agent = $this->makeAgent('ABC');
        $date = Carbon::create(2026, 7, 15, 10, 30, 0);

        $this->createTransportOrder('ABC260715Z', $agent);
        $this->assertSame('ABC260715AA', $this->generateTransportOrderNumber($agent, $date));

        $this->createTransportOrder('ABC260715ZZ', $agent);
        $this->assertSame('ABC260715AAA', $this->generateTransportOrderNumber($agent, $date));
    }

    public function test_transport_order_number_sequence_is_scoped_to_agent_code_and_date(): void
    {
        $agent = $this->makeAgent('ABC');
        $otherAgent = $this->makeAgent('XYZ');
        $date = Carbon::create(2026, 7, 15, 10, 30, 0);

        $this->createTransportOrder('ABC260714Z', $agent);
        $this->createTransportOrder('XYZ260715Z', $otherAgent);

        $this->assertSame('ABC260715A', $this->generateTransportOrderNumber($agent, $date));
    }

    public function test_transport_detail_page_no_longer_uses_legacy_preview_order_number_format(): void
    {
        $controller = file_get_contents(app_path('Http/Controllers/HomeController.php'));
        $template = file_get_contents(resource_path('views/frontend/landing-page/transports/detail.blade.php'));

        $this->assertStringContainsString('TransportOrderNumberService $transportOrderNumberService', $controller);
        $this->assertStringContainsString('$transportOrderNumberService->generate($selectedAgent, $now)', $controller);
        $this->assertStringContainsString('$transportOrderNumberService->generate($agent, $now)', $controller);
        $this->assertStringNotContainsString("ORD.' . \$now->format('Ymd') . '.TRN", $controller);
        $this->assertStringContainsString('data-transport-booking-order-number="{{ $orderNumber }}"', $template);
        $this->assertStringContainsString('data-review-order-number', $template);
    }
}
