<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Admin;
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

        \App\Models\User::create([
            'name' => 'User8',
            'email' => 'user8@example.com',
            'password' => Hash::make('123456789')
        ]);

        \App\Models\Admin::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password'=>Hash::make('123456789')
        ]);
        \App\Models\Admin::create([
            'name' => 'Hala Aboyousef',
            'email' => 'halaaboyousef01@example.com',
            'password' => Hash::make('123456789')
        ]);
        \App\Models\Supervisor::create([
            'name' => 'Supervisor',
            'email' => 'supervisor78@example.com',
            'password'=>Hash::make('123456789'),
            'phone' => '0599166117',
            'country' => 'gaza',
        ]);
        // $this->call([
        //     AdminSeeder::class,
        // ]);
    }
}
