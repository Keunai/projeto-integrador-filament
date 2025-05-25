<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        Role::updateOrCreate(
            ['name' => 'Adm'],
            ['descrption' => 'System administrator with full access.']
        );

        Role::updateOrCreate(
            ['name' => 'Manager'],
            ['descrption' => 'Manages teams and oversees operations.']
        );

        Role::updateOrCreate(
            ['name' => 'Staff'],
            ['descrption' => 'General staff with limited permissions.']
        );
    }
}