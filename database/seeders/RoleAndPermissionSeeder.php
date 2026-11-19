<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            'submissions.view',
            'submissions.review',
            'submissions.export',
            'submissions.download_report',
            'users.manage',
        ];

        foreach ($permissions as $permission) {
            Permission::query()->firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        $superAdmin = Role::query()->firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $superAdmin->syncPermissions($permissions);

        $reviewer = Role::query()->firstOrCreate(['name' => 'reviewer', 'guard_name' => 'web']);
        $reviewer->syncPermissions([
            'submissions.view',
            'submissions.review',
            'submissions.export',
            'submissions.download_report',
        ]);

        $viewer = Role::query()->firstOrCreate(['name' => 'viewer', 'guard_name' => 'web']);
        $viewer->syncPermissions([
            'submissions.view',
            'submissions.export',
            'submissions.download_report',
        ]);
    }
}
