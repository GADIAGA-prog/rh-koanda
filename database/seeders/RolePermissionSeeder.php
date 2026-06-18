<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $modules = ['employe', 'contrat', 'conge', 'presence', 'document', 'formation', 'performance', 'sanction', 'filiale', 'utilisateur'];
        $actions = ['view', 'create', 'update', 'delete'];

        foreach ($modules as $module) {
            foreach ($actions as $action) {
                Permission::firstOrCreate(['name' => "{$module}.{$action}", 'guard_name' => 'web']);
            }
        }
        // Permissions spécifiques
        foreach (['conge.valider', 'rapport.consulter', 'audit.consulter', 'role.manage'] as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        $toutes = Permission::all()->pluck('name')->all();

        $roles = [
            'super-admin' => $toutes,
            'direction-generale' => ['employe.view', 'contrat.view', 'conge.view', 'rapport.consulter', 'audit.consulter'],
            'drh-groupe' => $toutes,
            'rh-filiale' => [
                'employe.view', 'employe.create', 'employe.update', 'employe.delete',
                'contrat.view', 'contrat.create', 'contrat.update',
                'conge.view', 'conge.create', 'conge.valider',
                'presence.view', 'presence.create', 'document.view', 'document.create',
                'formation.view', 'sanction.view', 'sanction.create',
            ],
            'manager' => ['employe.view', 'conge.view', 'conge.valider', 'presence.view', 'performance.view', 'performance.create'],
            'employe' => ['conge.view', 'conge.create', 'document.view'],
            'auditeur-groupe' => ['employe.view', 'contrat.view', 'conge.view', 'rapport.consulter', 'audit.consulter'],
        ];

        foreach ($roles as $nom => $permissions) {
            $role = Role::firstOrCreate(['name' => $nom, 'guard_name' => 'web']);
            $role->syncPermissions($permissions);
        }
    }
}
