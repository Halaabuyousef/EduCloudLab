<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app('cache')->forget('spatie.permission.cache');

     
        $adminPerms = [
            'experiments.view',
            'experiments.create',
            'experiments.update',
            'experiments.delete',
            'devices.view',
            'devices.create',
            'devices.update',
            'devices.delete',
            'reservations.view',
            'reservations.create',
            'reservations.update',
            'reservations.delete',
            'reservations.cancel',
            'users.manage',
            'reports.view',
        ];
        foreach ($adminPerms as $p) {
            Permission::firstOrCreate(['name' => $p, 'guard_name' => 'admin']);
        }
        $rSuperAdmin = Role::firstOrCreate(['name' => 'super-admin',   'guard_name' => 'admin']);
        $rLabAdmin   = Role::firstOrCreate(['name' => 'lab-admin',     'guard_name' => 'admin']);
        $rContentMgr = Role::firstOrCreate(['name' => 'content-manager', 'guard_name' => 'admin']);

        $rSuperAdmin->syncPermissions($adminPerms);
        $rLabAdmin->syncPermissions([
            'experiments.view',
            'experiments.create',
            'experiments.update',
            'experiments.delete',
            'devices.view',
            'devices.create',
            'devices.update',
            'devices.delete',
            'reservations.view',
            'reservations.create',
            'reservations.update',
            'reservations.delete',
            'reservations.cancel',
            'reports.view',
        ]);
        $rContentMgr->syncPermissions(['experiments.view', 'devices.view', 'reservations.view', 'reports.view']);

        /* ---------- SUPERVISOR ---------- */
        $supervisorPerms = [
            'experiments.view',
            'reservations.view',
            'reservations.approve',
            'reports.view',
        ];
        foreach ($supervisorPerms as $p) {
            Permission::firstOrCreate(['name' => $p, 'guard_name' => 'supervisor']); 
        }
        $rSupervisor = Role::firstOrCreate(['name' => 'supervisor',            'guard_name' => 'supervisor']);
        $rAssistant  = Role::firstOrCreate(['name' => 'assistant-supervisor',  'guard_name' => 'supervisor']);

        $rSupervisor->syncPermissions($supervisorPerms);
        $rAssistant->syncPermissions(['experiments.view', 'reservations.view']);

        /* ---------- WEB (students) ---------- */
        $studentPerms = [
            'experiments.view',
            'reservations.view',
            'reservations.create',
            'reservations.cancel',
            // 'reservations.priority', // أنشئها قبل ما تديها للدور
        ];
        foreach ($studentPerms as $p) {
            Permission::firstOrCreate(['name' => $p, 'guard_name' => 'web']);
        }
        $rStudent = Role::firstOrCreate(['name' => 'student',         'guard_name' => 'web']);
        $rPremium = Role::firstOrCreate(['name' => 'premium-student', 'guard_name' => 'web']);

        $rStudent->syncPermissions([
            'experiments.view',
            'reservations.view',
            'reservations.create',
            'reservations.cancel',
        ]);
        $rPremium->syncPermissions([
            'experiments.view',
            'reservations.view',
            'reservations.create',
            'reservations.cancel',
            // 'reservations.priority',
        ]);
        $studentPerms = [
            'experiments.view',
            'reservations.view',
            'reservations.create',
            'reservations.cancel',
            'reservations.priority', // إذا بدك للبريميوم
        ];

        foreach ($studentPerms as $p) {
            Permission::firstOrCreate(['name' => $p, 'guard_name' => 'web']); // مهم
        }

        $student = Role::firstOrCreate(['name' => 'student', 'guard_name' => 'web']);
        $premium = Role::firstOrCreate(['name' => 'premium-student', 'guard_name' => 'web']);

        $student->syncPermissions([
            'experiments.view',
            'reservations.view',
            'reservations.create',
            'reservations.cancel',
        ]);
        $premium->syncPermissions([
            'experiments.view',
            'reservations.view',
            'reservations.create',
            'reservations.cancel',
            'reservations.priority',
        ]);

        app('cache')->forget('spatie.permission.cache');

        // app('cache')->forget('spatie.permission.cache');


    }
}
