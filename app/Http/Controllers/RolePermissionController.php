<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * Matrice rôles × permissions (Spatie).
 * Consultation : permission role.manage. Modification : super-admin uniquement.
 */
class RolePermissionController extends Controller
{
    public function index(Request $request)
    {
        abort_unless($request->user()->can('role.manage'), 403);

        return view('admin.roles.index', [
            'roles' => Role::with('permissions')->orderBy('name')->get(),
            'permissions' => Permission::orderBy('name')->get(),
            'editable' => $request->user()->hasRole('super-admin'),
        ]);
    }

    public function update(Request $request, Role $role)
    {
        abort_unless($request->user()->hasRole('super-admin'), 403);

        $donnees = $request->validate([
            'permissions' => ['array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
        ]);

        $role->syncPermissions($donnees['permissions'] ?? []);
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return back()->with('succes', "Permissions du rôle « {$role->name} » mises à jour.");
    }
}
