<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::query()->updateOrCreate(
            ['email' => 'admin@kkknk.local'],
            [
                'name' => 'Quản trị viên',
                'password' => 'Admin123456!',
            ]
        );
        $admin->forceFill(['is_admin' => true])->save();
        $admin->syncRoles(['super_admin']);

        $demo = User::query()->updateOrCreate(
            ['email' => 'demo@kkknk.local'],
            [
                'name' => 'Demo Admin',
                'password' => 'Demo123456!',
            ]
        );
        $demo->forceFill(['is_admin' => true])->save();
        $demo->syncRoles(['reviewer']);
    }
}
