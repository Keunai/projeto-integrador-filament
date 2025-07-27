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
        $user = User::updateOrCreate(
            ['email' => 'tenantAdm@gmail.com'],
            [
                'name' => 'tenantAdm',
                'password' => Hash::make('tenantAdm'),
            ]
        );

        $user->assignRole('Administrador');
    }
}
