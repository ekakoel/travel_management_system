<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('wechat', 120)->nullable()->after('whatsapp');
            $table->string('line', 120)->nullable()->after('wechat');
            $table->string('telegram', 120)->nullable()->after('line');
            $table->string('chat_account', 150)->nullable()->after('telegram');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'wechat',
                'line',
                'telegram',
                'chat_account',
            ]);
        });
    }
};
