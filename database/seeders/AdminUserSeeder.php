<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Create (or update) the admin user from config/admin.php.
     * role and is_active aren't fillable, so they are set one by one.
     */
    public function run(): void
    {
        $admin = User::firstOrNew(['email' => config('admin.email')]);

        $admin->name = 'Admin';
        $admin->password = Hash::make(config('admin.password'));
        $admin->role = User::ROLE_ADMIN;
        $admin->is_active = true;
        $admin->save();
    }
}
