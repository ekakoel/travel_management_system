<?php

namespace Tests\Feature\Pricing;

use App\Models\Orders;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use PDO;
use RuntimeException;
use Tests\TestCase;

class MariaDbPricingVerificationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (env('RUN_PRICING_MARIADB_VERIFICATION') !== '1') {
            $this->markTestSkipped('Opt-in disposable MariaDB verification only.');
        }

        $database = (string) DB::connection()->getDatabaseName();

        if (!app()->environment('testing')
            || DB::getDriverName() !== 'mysql'
            || !str_starts_with($database, 'balikamitour_pricing_disposable_')
            || $database === 'online_bali_kami_26') {
            throw new RuntimeException('Unsafe MariaDB pricing verification target.');
        }

        $this->seedForeignKeyParents();
    }

    public function test_mariadb_version_foreign_keys_and_indexes_are_compatible(): void
    {
        $version = (string) DB::scalar('SELECT VERSION()');
        $this->assertStringContainsString('10.4.', $version);

        $foreignKeys = collect(DB::select(
            "SELECT CONSTRAINT_NAME
             FROM information_schema.REFERENTIAL_CONSTRAINTS
             WHERE CONSTRAINT_SCHEMA = DATABASE()
               AND TABLE_NAME IN ('order_pricing_snapshots', 'orders')"
        ))->pluck('CONSTRAINT_NAME');

        foreach ([
            'ops_order_fk',
            'ops_rate_fk',
            'ops_tax_policy_fk',
            'orders_pricing_snapshot_fk',
        ] as $key) {
            $this->assertTrue($foreignKeys->contains($key), "Missing foreign key {$key}");
        }

        $indexes = collect(DB::select(
            "SELECT DISTINCT INDEX_NAME
             FROM information_schema.STATISTICS
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME IN ('order_pricing_snapshots', 'orders', 'tour_prices', 'usd_rates')"
        ))->pluck('INDEX_NAME');

        foreach ([
            'ops_order_sequence_unique',
            'ops_service_calculated_idx',
            'orders_service_user_submission_unique',
            'tour_prices_pricing_eligibility_idx',
            'usd_rates_name_retrieved_idx',
        ] as $index) {
            $this->assertTrue($indexes->contains($index), "Missing index {$index}");
        }
    }

    public function test_snapshot_relationship_and_foreign_key_rejection(): void
    {
        $orderId = $this->insertOrder('MARIADB-SNAPSHOT', hash('sha256', 'snapshot'));
        $snapshotId = $this->insertSnapshot($orderId);
        DB::table('orders')->where('id', $orderId)->update([
            'pricing_snapshot_id' => $snapshotId,
            'pricing_version' => 'tour-pricing-v1',
        ]);

        $order = Orders::with('activePricingSnapshot')->findOrFail($orderId);
        $this->assertSame($snapshotId, $order->activePricingSnapshot->id);
        $this->assertSame('Tour Package', $order->activePricingSnapshot->service);

        try {
            DB::table('order_pricing_snapshots')->insert(
                $this->snapshotPayload(9_999_999, 2)
            );
            $this->fail('Missing order foreign key should be rejected.');
        } catch (QueryException $exception) {
            $this->assertSame('23000', $exception->getCode());
        }
    }

    public function test_database_idempotency_constraint_rejects_duplicate_submission(): void
    {
        $tokenHash = hash('sha256', 'duplicate-submission');
        $this->insertOrder('MARIADB-IDEMPOTENT-1', $tokenHash);

        $this->expectException(QueryException::class);
        $this->insertOrder('MARIADB-IDEMPOTENT-2', $tokenHash);
    }

    public function test_transaction_rollback_removes_partial_order_and_snapshot_writes(): void
    {
        try {
            DB::transaction(function () {
                $orderId = $this->insertOrder(
                    'MARIADB-ROLLBACK',
                    hash('sha256', 'rollback')
                );
                $this->insertSnapshot($orderId);

                throw new RuntimeException('force rollback');
            });
        } catch (RuntimeException $exception) {
            $this->assertSame('force rollback', $exception->getMessage());
        }

        $this->assertFalse(DB::table('orders')->where('orderno', 'MARIADB-ROLLBACK')->exists());
    }

    public function test_row_lock_contention_is_enforced_by_innodb(): void
    {
        $orderId = $this->insertOrder('MARIADB-LOCK', hash('sha256', 'lock'));
        $first = $this->pdo();
        $second = $this->pdo();
        $lockRejected = false;

        $first->beginTransaction();
        $first->query("SELECT id FROM orders WHERE id = {$orderId} FOR UPDATE")->fetch();

        try {
            $second->exec('SET SESSION innodb_lock_wait_timeout = 1');
            $second->beginTransaction();
            $second->query("SELECT id FROM orders WHERE id = {$orderId} FOR UPDATE")->fetch();
        } catch (\PDOException $exception) {
            $lockRejected = str_contains($exception->getMessage(), '1205')
                || str_contains(strtolower($exception->getMessage()), 'lock wait timeout');
        } finally {
            if ($second->inTransaction()) {
                $second->rollBack();
            }
            if ($first->inTransaction()) {
                $first->rollBack();
            }
        }

        $this->assertTrue($lockRejected, 'The competing row lock was not rejected.');
    }

    private function seedForeignKeyParents(): void
    {
        DB::table('users')->updateOrInsert(['id' => 9_001], [
            'username' => 'pricing-mariadb',
            'name' => 'Pricing MariaDB',
            'type' => 'staff',
            'email' => 'pricing-mariadb@example.test',
            'is_subscribed' => false,
            'subscriber' => false,
            'password' => 'not-used',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('usd_rates')->updateOrInsert(['id' => 9_001], [
            'name' => 'USD',
            'rate' => '16000',
            'sell' => '16000',
            'buy' => '15900',
            'difference' => '100',
            'retrieved_at' => now(),
            'retrieval_source' => 'mariadb-test',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('tax_policies')->updateOrInsert(['id' => 9_001], [
            'service' => 'Tour Package',
            'name' => 'MariaDB Test Tax',
            'percentage_scaled' => 1_500_000,
            'percentage_scale' => 1_000_000,
            'calculation_type' => 'exclusive',
            'taxable_base' => 'contract_plus_markup',
            'status' => 'active',
            'effective_from' => now()->subDay(),
            'effective_until' => null,
            'approved_by' => 9_001,
            'approved_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function insertOrder(string $orderNumber, string $tokenHash): int
    {
        return DB::table('orders')->insertGetId([
            'user_id' => 9_001,
            'orderno' => $orderNumber,
            'confirmation_order' => '',
            'name' => 'MariaDB Test',
            'email' => 'pricing-mariadb@example.test',
            'servicename' => 'Disposable Tour',
            'service' => 'Tour Package',
            'service_id' => 1,
            'submission_token_hash' => $tokenHash,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function insertSnapshot(int $orderId): int
    {
        return DB::table('order_pricing_snapshots')->insertGetId(
            $this->snapshotPayload($orderId, 1)
        );
    }

    private function snapshotPayload(int $orderId, int $sequence): array
    {
        return [
            'order_id' => $orderId,
            'snapshot_sequence' => $sequence,
            'pricing_version' => 'tour-pricing-v1',
            'service' => 'Tour Package',
            'service_id' => 1,
            'price_id' => 1,
            'quantity' => 2,
            'contract_rate_idr' => 1_000_000,
            'markup_amount_minor' => 2_000,
            'markup_currency' => 'USD',
            'markup_idr' => 320_000,
            'subtotal_idr' => 1_320_000,
            'tax_policy_id' => 9_001,
            'tax_percentage_scaled' => 1_500_000,
            'tax_amount_idr' => 19_800,
            'rate_id' => 9_001,
            'rate_value_scaled' => 16_000_000_000,
            'rate_source' => 'mariadb-test',
            'rate_retrieved_at' => now(),
            'unit_price_idr' => 1_339_800,
            'unit_price_usd_minor' => 8_374,
            'gross_total_idr' => 2_679_600,
            'gross_total_usd_minor' => 16_748,
            'final_total_idr' => 2_679_600,
            'final_total_usd_minor' => 16_748,
            'calculated_at' => now(),
            'calculated_by' => 9_001,
            'input_fingerprint' => hash('sha256', "input-{$orderId}-{$sequence}"),
            'snapshot_checksum' => hash('sha256', "snapshot-{$orderId}-{$sequence}"),
            'breakdown' => '{}',
            'created_at' => now(),
        ];
    }

    private function pdo(): PDO
    {
        $connection = config('database.connections.mysql');

        return new PDO(
            "mysql:host={$connection['host']};port={$connection['port']};dbname={$connection['database']}",
            $connection['username'],
            $connection['password'],
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_TIMEOUT => 3,
            ]
        );
    }
}
