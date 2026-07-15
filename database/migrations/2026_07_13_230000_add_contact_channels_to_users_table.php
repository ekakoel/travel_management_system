<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->text('contact_channels')->nullable()->after('chat_account');
        });

        User::query()->select(['id', 'whatsapp', 'wechat', 'line', 'telegram', 'chat_account'])->chunkById(100, function ($users) {
            foreach ($users as $user) {
                $user->forceFill(
                    User::syncLegacyContactChannelAttributes([
                        ['platform' => User::CONTACT_CHANNEL_PLATFORM_WHATSAPP, 'value' => $user->whatsapp],
                        ['platform' => User::CONTACT_CHANNEL_PLATFORM_WECHAT, 'value' => $user->wechat],
                        ['platform' => User::CONTACT_CHANNEL_PLATFORM_LINE, 'value' => $user->line],
                        ['platform' => User::CONTACT_CHANNEL_PLATFORM_TELEGRAM, 'value' => $user->telegram],
                        ['platform' => User::CONTACT_CHANNEL_PLATFORM_OTHER, 'value' => $user->chat_account],
                    ])
                )->save();
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('contact_channels');
        });
    }
};
