<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{

    public function run(): void
    {
        $role = Role::find(1);

        $user = User::updateOrCreate(
            ['role_id' => $role->id],
            [

                'name' => 'tenantAdm',
                'email' => 'tenantAdm@gmail.com',
                'password' => Hash::make('tenantAdm'),
            ]
        );

        $user->assignRole('Administrador');
    }
}
