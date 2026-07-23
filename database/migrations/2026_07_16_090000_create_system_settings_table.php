<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('system_settings')) {
            Schema::create('system_settings', function (Blueprint $table) {
                $table->id();
                $table->string('key')->unique();
                $table->string('value')->nullable();
                $table->boolean('status')->default(true)->index();
                $table->timestamps();
            });
        }

        DB::table('system_settings')->updateOrInsert(
            ['key' => 'registration_access'],
            [
                'value' => 'Public registration access',
                'status' => true,
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('system_settings');
    }
};
