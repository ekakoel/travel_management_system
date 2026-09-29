
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('agent_id')
                ->nullable()
                ->after('id')
                ->constrained('agents')
                ->nullOnDelete();
        });

        // Pindahkan relasi lama dari agents.user_id ke users.agent_id,
        // jika data relasi lama masih tersedia.
        if (Schema::hasColumn('agents', 'user_id')) {
            $agents = DB::table('agents')
                ->whereNotNull('user_id')
                ->get(['id', 'user_id']);

            foreach ($agents as $agent) {
                DB::table('users')
                    ->where('id', $agent->user_id)
                    ->whereNull('agent_id')
                    ->update(['agent_id' => $agent->id]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('agent_id');
        });
    }
};