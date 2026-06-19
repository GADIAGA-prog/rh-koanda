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

        $modules = ['employe', 'contrat', 'conge', 'presence', 'absence', 'mission', 'paie', 'organisation', 'document', 'formation', 'performance', 'sanction', 'filiale', 'utilisateur'];
        $actions = ['view', 'create', 'update', 'delete'];

        foreach ($modules as $module) {
            foreach ($actions as $action) {
                Permission::firstOrCreate(['name' => "{$module}.{$action}", 'guard_name' => 'web']);
            }
        }
        // Permissions spécifiques
        foreach (['conge.valider', 'mission.valider', 'rapport.consulter', 'audit.consulter', 'role.manage'] as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        $toutes = Permission::all()->pluck('name')->all();
        // Lecture seule sur tous les modules (rôles de consultation Groupe).
        $vuesGroupe = array_map(fn ($m) => "{$m}.view", $modules);

        $roles = [
            'super-admin' => $toutes,
            'direction-generale' => array_merge($vuesGroupe, ['rapport.consulter', 'audit.consulter']),
            'drh-groupe' => $toutes,
            'rh-filiale' => [
                'employe.view', 'employe.create', 'employe.update', 'employe.delete',
                'contrat.view', 'contrat.create', 'contrat.update', 'contrat.delete',
                'conge.view', 'conge.create', 'conge.valider',
                'presence.view', 'presence.create', 'presence.update',
                'absence.view', 'absence.create', 'absence.update',
                'mission.view', 'mission.create', 'mission.update', 'mission.valider',
                'paie.view', 'paie.create', 'paie.update',
                'organisation.view', 'organisation.create', 'organisation.update', 'organisation.delete',
                'document.view', 'document.create', 'document.update',
                'formation.view', 'formation.create', 'formation.update',
                'performance.view',
                'sanction.view', 'sanction.create', 'sanction.update',
                'rapport.consulter',
            ],
            'manager' => [
                'employe.view', 'organisation.view',
                'conge.view', 'conge.valider',
                'presence.view', 'absence.view',
                'mission.view', 'mission.create',
                'performance.view', 'performance.create',
            ],
            'employe' => ['conge.view', 'conge.create', 'document.view', 'mission.view'],
            'auditeur-groupe' => array_merge($vuesGroupe, ['rapport.consulter', 'audit.consulter']),
        ];

        foreach ($roles as $nom => $permissions) {
            $role = Role::firstOrCreate(['name' => $nom, 'guard_name' => 'web']);
            $role->syncPermissions($permissions);
        }
    }
}
