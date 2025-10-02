<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Create roles if they don't exist
        $adminRole = Role::firstOrCreate([
            'name' => 'admin',
            'guard_name' => 'web'
        ]);

        $staffRole = Role::firstOrCreate([
            'name' => 'staff',
            'guard_name' => 'web'
        ]);

        $customerRole = Role::firstOrCreate([
            'name' => 'customer',
            'guard_name' => 'web'
        ]);


        $admin = User::updateOrCreate(
            ['email' => 'admin@clarinwaterdistrict.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('admin123'),
                'email_verified_at' => now()
            ]
        );
        $admin->syncRoles([$adminRole]);
        $admin->role = 'admin';
        $admin->save();

        // Create or update staff user
        $staff = User::updateOrCreate(
            ['email' => 'staff@clarinwaterdistrict.com'],
            [
                'name' => 'Staff',
                'password' => Hash::make('staff123'),
                'email_verified_at' => now()
            ]
        );
        $staff->syncRoles([$staffRole]);
        $staff->role = 'staff';
        $staff->save();


        $customer = User::updateOrCreate(
            ['email' => 'customer@email.com'],
            [
                'name' => 'Concessioner',
                'serial_number' => '123456789',
                'account_number' => '123-45-678',
                'password' => Hash::make('customer123'),
                'email_verified_at' => now()
            ]
        );
        $customer->syncRoles([$customerRole]);
        $customer->role = 'customer';
        $customer->save();
    }
}
