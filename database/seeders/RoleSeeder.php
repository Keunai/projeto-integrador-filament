<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $guardName = 'web';

        $rolesPermission = Permission::firstOrCreate(['name' => 'Gerenciar Funções']);
        $usersPermission = Permission::firstOrCreate(['name' => 'Gerenciar Usuários']);
        $warehousePermission = Permission::firstOrCreate(['name' => 'Gerenciar Armazéns']);
        $productsPermission = Permission::firstOrCreate(['name' => 'Gerenciar Produtos']);
        $categoriesPermission = Permission::firstOrCreate(['name' => 'Gerenciar Categorias']);
        $movementsPermission = Permission::firstOrCreate(['name' => 'Gerenciar Movimentações']);
        $roomLocationsPermission = Permission::firstOrCreate(['name' => 'Gerenciar Localizações de Salas']);
        $roomsPermission = Permission::firstOrCreate(['name' => 'Gerenciar Salas']);
        $shelvesPermission = Permission::firstOrCreate(['name' => 'Gerenciar Prateleiras']);
        $streetsPermission = Permission::firstOrCreate(['name' => 'Gerenciar Ruas']);
        $zonesPermission = Permission::firstOrCreate(['name' => 'Gerenciar Zonas']);

        $adminRole = Role::updateOrCreate(
            ['name' => 'Administrador', 'guard_name' => $guardName],
            ['description' => 'Administrador do sistema com acesso total.']
        );

        $managerRole = Role::updateOrCreate(
            ['name' => 'Gerente', 'guard_name' => $guardName],
            ['description' => 'Gerencia equipes e supervisiona operações.']
        );

        $staffRole = Role::updateOrCreate(
            ['name' => 'Operação', 'guard_name' => $guardName],
            ['description' => 'Funcionário com permissões limitadas.']
        );

        $hrRole = Role::updateOrCreate(
            ['name' => 'RH', 'guard_name' => $guardName],
            ['description' => 'Funcionário com permissões limitadas à gestão de usuários.']
        );

        $adminRole->givePermissionTo([
            $rolesPermission,
            $usersPermission,
            $warehousePermission,
            $productsPermission,
            $categoriesPermission,
            $movementsPermission,
            $roomLocationsPermission,
            $roomsPermission,
            $shelvesPermission,
            $streetsPermission,
            $zonesPermission,
        ]);

        $managerRole->givePermissionTo([
            $warehousePermission,
            $productsPermission,
            $categoriesPermission,
            $movementsPermission,
            $roomLocationsPermission,
            $roomsPermission,
            $shelvesPermission,
            $streetsPermission,
            $zonesPermission,
        ]);

        $staffRole->givePermissionTo([
            $productsPermission,
            $movementsPermission,
        ]);

        $hrRole->givePermissionTo([
            $usersPermission,
            $rolesPermission,
        ]);

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }
}