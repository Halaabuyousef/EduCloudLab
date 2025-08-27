<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            DB::statement("ALTER TABLE reservations MODIFY status ENUM('pending','active','postponed','completed','cancelled') NOT NULL");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            DB::statement("ALTER TABLE reservations MODIFY status ENUM('pending','active','completed','cancelled') NOT NULL");
        });
    }
};
