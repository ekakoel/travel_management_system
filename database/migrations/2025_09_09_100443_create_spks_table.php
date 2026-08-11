<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        if (Schema::hasTable('spks')) {
            return;
        }

        Schema::create('spks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('order_number')->nullable();
            $table->string('type')->nullable();
            $table->foreignId('reservation_id')->constrained()->cascadeOnDelete()->nullable();
            $table->foreignId('driver_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('transport_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('operator_id')->nullable()->constrained()->nullOnDelete();
            $table->string('spk_number')->unique();
            $table->string('plate_number')->nullable();
            $table->tinyInteger('send_report')->nullable();
            $table->integer('number_of_guests')->nullable();
            $table->date('spk_date')->nullable();
            $table->enum('status', ['Pending', 'In_progress', 'Completed','Expired'])->default('Pending');
            $table->timestamps();
        });

        if (Schema::hasTable('airport_shuttles') && Schema::hasColumn('airport_shuttles', 'spk_id')) {
            Schema::table('airport_shuttles', function (Blueprint $table) {
                $table->foreign('spk_id', 'airport_shuttles_spk_id_foreign')
                    ->references('id')
                    ->on('spks')
                    ->cascadeOnDelete();
            });
        }
    }

    public function down(): void {
        if (Schema::hasTable('airport_shuttles') && Schema::hasColumn('airport_shuttles', 'spk_id')) {
            Schema::table('airport_shuttles', function (Blueprint $table) {
                $table->dropForeign('airport_shuttles_spk_id_foreign');
            });
        }

        Schema::dropIfExists('spks');
    }
};
