<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
          
            DB::statement("ALTER TABLE experiments MODIFY COLUMN status VARCHAR(20) NOT NULL DEFAULT 'available'");
        } elseif ($driver === 'pgsql') {
            
            DB::statement("ALTER TABLE experiments ALTER COLUMN status TYPE VARCHAR(20) USING status::text");
            DB::statement("ALTER TABLE experiments ALTER COLUMN status SET DEFAULT 'available'");
            DB::statement("ALTER TABLE experiments ALTER COLUMN status SET NOT NULL");
        }
    }

    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
        
            DB::statement("ALTER TABLE experiments MODIFY COLUMN status ENUM('available','in_use','maintenance') NOT NULL DEFAULT 'available'");
        } elseif ($driver === 'pgsql') {
         
            DB::statement("ALTER TABLE experiments ALTER COLUMN status DROP DEFAULT");
        }
    }
};
