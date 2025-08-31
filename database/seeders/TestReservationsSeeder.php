<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use App\Models\Reservation;
use App\Models\User;
use App\Models\Experiment;

class TestReservationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // $user = User::first();
        // $exp  = Experiment::first();
        // $now  = Carbon::now();

        // if (!$user || !$exp) {
        //     $this->command->error(' No user or experiment found. Please seed/create them first.');
        //     return;
        // }

        // 1) Soon (within 10 minutes)
        // Reservation::create([
        //     'user_id' => $user->id,
        //     'experiment_id' => $exp->id,
        //     'start_time' => $now->copy()->addMinutes(5),
        //     'end_time'   => $now->copy()->addMinutes(30),
        //     'status'     => 'pending',
        //     'prestart_notified_at' => null,
        //     'notified_started_at'  => null,
        //     'notified_completed_at' => null,
        // ]);

        // // 2) Started (started 30 seconds ago)
        // Reservation::create([
        //     'user_id' => $user->id,
        //     'experiment_id' => $exp->id,
        //     'start_time' => $now->copy()->subSeconds(30),
        //     'end_time'   => $now->copy()->addMinutes(20),
        //     'status'     => 'active',
        //     'prestart_notified_at' => null,
        //     'notified_started_at'  => null,
        //     'notified_completed_at' => null,
        // ]);

        // // 3) Completed (ended 30 seconds ago)
        // Reservation::create([
        //     'user_id' => $user->id,
        //     'experiment_id' => $exp->id,
        //     'start_time' => $now->copy()->subMinutes(40),
        //     'end_time'   => $now->copy()->subSeconds(30),
        //     'status'     => 'completed',
        //     'prestart_notified_at' => null,
        //     'notified_started_at'  => null,
        //     'notified_completed_at' => null,
        // ]);

        // $this->command->info(' Test reservations created: Soon, Started, Completed');
    }
}
