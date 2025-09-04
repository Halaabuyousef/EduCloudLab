<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use Illuminate\Support\Facades\DB;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        // تعطيل فحص المفاتيح الأجنبية مؤقتًا (MySQL)
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // تفريغ الجداول الخاصة بالباكج فقط
        DB::table('role_has_permissions')->truncate();
        DB::table('model_has_roles')->truncate();
        DB::table('model_has_permissions')->truncate();
        DB::table('roles')->truncate();
        DB::table('permissions')->truncate();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        foreach (['admin', 'supervisor', 'web'] as $guard) {
            foreach (
                [
                    'experiments.view',
                    'experiments.create',
                    'experiments.update',
                    'experiments.delete',
                    'reservations.view',
                    'reservations.create',
                    'reservations.update',
                    'reservations.approve',
                    'users.view',
                    'users.create',
                    'users.update',
                    'users.delete',
                    'contacts.view',
                    'contacts.reply',
                    'contacts.delete',
                    'dashboard.view',
                ] as $p
            ) {
                Permission::firstOrCreate(['name' => $p, 'guard_name' => $guard]);
            }
        }

        $adminRole      = Role::firstOrCreate(['name' => 'admin',      'guard_name' => 'admin']);
        $supervisorRole = Role::firstOrCreate(['name' => 'supervisor', 'guard_name' => 'supervisor']);
        $studentRole    = Role::firstOrCreate(['name' => 'student',    'guard_name' => 'web']);

        $adminRole->syncPermissions(Permission::where('guard_name', 'admin')->pluck('name'));

        $supervisorRole->syncPermissions([
           
            'experiments.view',
            'experiments.create',
            'experiments.update',
            'reservations.view',
            'reservations.update',
            'reservations.approve',
            'users.view',
        ]);

        $studentRole->syncPermissions(['reservations.view', 'reservations.create']);

        $admin = \App\Models\Admin::first();
        $admin?->assignRole('admin');

        $supervisor = \App\Models\Supervisor::first();
        $supervisor?->assignRole('supervisor');

        $student = \App\Models\User::first();
        $student?->assignRole('student');

        
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
        // app('cache')->forget('spatie.permission.cache');

     
        // $adminPerms = [
        //     'experiments.read',
        //     'experiments.manage',      // create/update/delete

        //     'reservations.create',
        //     'reservations.read',
        //     'reservations.manage',     

        //     'notifications.read',
        //     'notifications.manage',
        // ];
        // foreach ($adminPerms as $p) {
        //     Permission::firstOrCreate(['name' => $p, 'guard_name' => 'admin']);
        // }
        // $rSuperAdmin = Role::firstOrCreate(['name' => 'super-admin',   'guard_name' => 'admin']);
        // $rLabAdmin   = Role::firstOrCreate(['name' => 'lab-admin',     'guard_name' => 'admin']);
        // $rContentMgr = Role::firstOrCreate(['name' => 'content-manager', 'guard_name' => 'admin']);

        // $rSuperAdmin->syncPermissions($adminPerms);
        // $rLabAdmin->syncPermissions([
        //     'experiments.view',
        //     'experiments.create',
        //     'experiments.update',
        //     'experiments.delete',
        //     'devices.view',
        //     'devices.create',
        //     'devices.update',
        //     'devices.delete',
        //     'reservations.view',
        //     'reservations.create',
        //     'reservations.update',
        //     'reservations.delete',
        //     'reservations.cancel',
        //     'reports.view',
        // ]);
        // $rContentMgr->syncPermissions(['experiments.view', 'devices.view', 'reservations.view', 'reports.view']);

        // /* ---------- SUPERVISOR ---------- */
        // $supervisorPerms = [
        //     'experiments.view',
        //     'reservations.view',
        //     'reservations.approve',
        //     'reports.view',
        // ];
        // foreach ($supervisorPerms as $p) {
        //     Permission::firstOrCreate(['name' => $p, 'guard_name' => 'supervisor']); 
        // }
        // $rSupervisor = Role::firstOrCreate(['name' => 'supervisor',            'guard_name' => 'supervisor']);
        // $rAssistant  = Role::firstOrCreate(['name' => 'assistant-supervisor',  'guard_name' => 'supervisor']);

        // $rSupervisor->syncPermissions($supervisorPerms);
        // $rAssistant->syncPermissions(['experiments.view', 'reservations.view']);

        // /* ---------- WEB (students) ---------- */
        // $studentPerms = [
        //     'experiments.view',
        //     'reservations.view',
        //     'reservations.create',
        //     'reservations.cancel',
        //     // 'reservations.priority', // أنشئها قبل ما تديها للدور
        // ];
        // foreach ($studentPerms as $p) {
        //     Permission::firstOrCreate(['name' => $p, 'guard_name' => 'web']);
        // }
        // $rStudent = Role::firstOrCreate(['name' => 'student',         'guard_name' => 'web']);
        // $rPremium = Role::firstOrCreate(['name' => 'premium-student', 'guard_name' => 'web']);

        // $rStudent->syncPermissions([
        //     'experiments.view',
        //     'reservations.view',
        //     'reservations.create',
        //     'reservations.cancel',
        // ]);
        // $rPremium->syncPermissions([
        //     'experiments.view',
        //     'reservations.view',
        //     'reservations.create',
        //     'reservations.cancel',
        //     // 'reservations.priority',
        // ]);
        // $studentPerms = [
        //     'experiments.view',
        //     'reservations.view',
        //     'reservations.create',
        //     'reservations.cancel',
        //     'reservations.priority', // إذا بدك للبريميوم
        // ];

        // foreach ($studentPerms as $p) {
        //     Permission::firstOrCreate(['name' => $p, 'guard_name' => 'web']); // مهم
        // }

        // $student = Role::firstOrCreate(['name' => 'student', 'guard_name' => 'web']);
        // $premium = Role::firstOrCreate(['name' => 'premium-student', 'guard_name' => 'web']);

        // $student->syncPermissions([
        //     'experiments.view',
        //     'reservations.view',
        //     'reservations.create',
        //     'reservations.cancel',
        // ]);
        // $premium->syncPermissions([
        //     'experiments.view',
        //     'reservations.view',
        //     'reservations.create',
        //     'reservations.cancel',
        //     'reservations.priority',
        // ]);

        // app('cache')->forget('spatie.permission.cache');

        // // app('cache')->forget('spatie.permission.cache');
// }

}
