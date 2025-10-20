<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $role = [
            ['role' => 'SU', 'role_name' => 'SuperUser'],
            ['role' => 'A', 'role_name' => 'Admin'],
            ['role' => 'U', 'role_name' => 'User'],
            ['role' => 'A1', 'role_name' => 'Admin 1'],
            ['role' => 'A2', 'role_name' => 'Admin 2'],
            ['role' => 'A3', 'role_name' => 'Admin 3'],
            ['role' => 'A4', 'role_name' => 'Admin 4'],
            ['role' => 'A5', 'role_name' => 'Admin 5'],
        ];

        foreach ($role as $key => $v) {
            Role::create([
                'code' => $v['role'],
                'name' => $v['role_name'],
            ]);
        };
    }
}
