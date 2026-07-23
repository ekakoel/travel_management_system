<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('website_visits', function (Blueprint $table) {
            $table->id();
            $table->string('visitor_hash', 80)->index();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('method', 10)->default('GET');
            $table->string('path')->index();
            $table->string('url')->nullable();
            $table->string('route_name')->nullable()->index();
            $table->string('page_title')->nullable();
            $table->string('area', 40)->default('frontend')->index();
            $table->string('country_code', 8)->nullable()->index();
            $table->string('country_name')->nullable()->index();
            $table->string('referrer_host')->nullable();
            $table->string('device_type', 40)->nullable();
            $table->string('browser')->nullable();
            $table->string('platform')->nullable();
            $table->string('ip_hash', 80)->nullable();
            $table->string('user_agent_hash', 80)->nullable();
            $table->date('visit_date')->index();
            $table->timestamp('occurred_at')->index();
            $table->timestamps();

            $table->index(['visit_date', 'area']);
            $table->index(['path', 'visit_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('website_visits');
    }
};
