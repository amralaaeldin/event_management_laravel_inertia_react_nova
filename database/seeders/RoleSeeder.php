<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create roles
        $roles = ['admin', 'user'];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }

        // Assign the first user as an admin
        $admin = User::first();

        if (!$admin) {
            $admin = User::create(
                [
                    'name' => 'Admin',
                    'email' => 'admin@example.com',
                    'password' => bcrypt('12345678'),
                ]
            );
        }

        $admin->assignRole('admin');
    }
}
