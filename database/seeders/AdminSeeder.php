<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Setting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Admin::updateOrCreate(
            ['email' => 'hala@educloudlab.local'],
            ['name' => 'Hala Abu Yousef', 'password' => Hash::make('123456789')]
        );

        Setting::updateOrCreate(['key' => 'system_name'], ['value' => 'EduCloudLab']);
        Setting::updateOrCreate(['key' => 'session_max_duration'], ['value' => '30', 'type' => 'int']);
        Setting::updateOrCreate(['key' => 'ota_storage'], ['value' => 'firebase', 'type' => 'string']);
    }
}
