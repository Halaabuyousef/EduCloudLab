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
        DB::statement("ALTER TABLE experiments 
            MODIFY status ENUM('available','reserved','in_use','maintenance') 
            NOT NULL DEFAULT 'available'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('experiments', function (Blueprint $table) {
            DB::statement("ALTER TABLE experiments 
            MODIFY status ENUM('available','in_use','maintenance') 
            NOT NULL DEFAULT 'available'");
        });
    }
};
