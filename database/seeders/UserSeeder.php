<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $company = Company::find(1);
        $role = Role::find(1);

        $user = User::updateOrCreate(
            ['company_id' => $company->id],
            [
                'role_id' => $role->id,
                'name' => 'tenantAdm',
                'email' => 'tenantAdm@gmail.com',
                'password' => Hash::make('tenantAdm'),
            ]
        );

        $user->assignRole('Administrador');
    }
}
