<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->timestamp('notified_started_at')->nullable()->after('end_time');
            $table->timestamp('notified_completed_at')->nullable()->after('notified_started_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn(['notified_started_at', 'notified_completed_at']);
        });
    }
};
