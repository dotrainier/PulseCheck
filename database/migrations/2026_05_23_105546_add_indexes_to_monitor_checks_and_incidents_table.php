<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('monitor_checks', function (Blueprint $table) {
            $table->index(['monitor_id', 'created_at']);
            $table->index('success');
        });

        Schema::table('incidents', function (Blueprint $table) {
            $table->index(['monitor_id', 'created_at']);
            $table->index('status');
        });

        Schema::table('monitors', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('status');
            $table->index('last_checked_at');
        });
    }

    public function down(): void
    {
        Schema::table('monitor_checks', function (Blueprint $table) {
            $table->dropIndex(['monitor_id', 'created_at']);
            $table->dropIndex(['success']);
        });

        Schema::table('incidents', function (Blueprint $table) {
            $table->dropIndex(['monitor_id', 'created_at']);
            $table->dropIndex(['status']);
        });

        Schema::table('monitors', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['status']);
            $table->dropIndex(['last_checked_at']);
        });
    }
};
