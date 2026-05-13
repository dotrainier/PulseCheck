<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('monitors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('url');
            $table->string('check_interval')->default('1m');
            $table->integer('expected_status_code')->nullable();
            $table->integer('timeout')->default(30);
            $table->boolean('track_ssl')->default(false);
            $table->string('status')->default('pending');
            $table->decimal('uptime', 5, 2)->default(100);
            $table->integer('avg_response_time')->nullable();
            $table->integer('total_checks')->default(0);
            $table->timestamp('last_checked_at')->nullable();
            $table->string('ssl_expiry_date')->nullable();
            $table->string('ssl_issuer')->nullable();
            $table->integer('ssl_days_remaining')->nullable();
            $table->boolean('ssl_expiring')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('monitors');
    }
};
