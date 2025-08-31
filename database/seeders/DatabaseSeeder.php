<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Admin;
use App\Models\Reservation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::create([
        //     'name' => 'User8',
        //     'email' => 'user8@example.com',
        //     'password' => Hash::make('123456789')
        // ]);

        // \App\Models\Admin::create([
        //     'name' => 'Admin',
        //     'email' => 'admin@example.com',
        //     'password'=>Hash::make('123456789')
        // ]);
        // \App\Models\Admin::create([
        //     'name' => 'Hala Aboyousef',
        //     'email' => 'halaaboyousef01@example.com',
        //     'password' => Hash::make('123456789')
        // ]);
        // \App\Models\Supervisor::create([
        //     'name' => 'Supervisor',
        //     'email' => 'supervisor78@example.com',
        //     'password'=>Hash::make('123456789'),
        //     'phone' => '0599166117',
        //     'country' => 'gaza',
        // ]);
        // $this->call([
        //     AdminSeeder::class,
        // ]);
        Reservation::factory()->create([
            'user_id' => 1,
            'experiment_id' => 1,
            'start_time' => now()->addMinutes(5),
            'end_time'   => now()->addMinutes(30),
            'status' => 'pending',
        ]);

        \App\Models\Reservation::factory()->create([
            'user_id' => 1,
            'experiment_id' => 1,
            'start_time' => now()->subSeconds(30),
            'end_time'   => now()->addMinutes(20),
            'status' => 'active',
        ]);

        \App\Models\Reservation::factory()->create([
            'user_id' => 1,
            'experiment_id' => 1,
            'start_time' => now()->subMinutes(40),
            'end_time'   => now()->subSeconds(30),
            'status' => 'completed',
        ]);
    }
}
