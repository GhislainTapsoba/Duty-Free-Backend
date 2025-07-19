<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;

class RoleApiController extends Controller
{
    // Liste tous les rôles avec leurs permissions
    public function index()
    {
        $roles = Role::with('permissions')->orderBy('created_at', 'desc')->get();
        return response()->json(['data' => $roles]);
    }

    // Crée un nouveau rôle avec permissions
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:roles,name',
            'description' => 'nullable|string',
            'permission_ids' => 'array',
            'permission_ids.*' => 'exists:permissions,id',
        ]);

        $role = Role::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'guard_name' => 'web', // ou le guard que tu utilises
        ]);

        if (!empty($validated['permission_ids'])) {
            $permissions = Permission::whereIn('id', $validated['permission_ids'])->pluck('name');
            $role->syncPermissions($permissions);
        }

        return response()->json(['data' => $role->load('permissions')], 201);
    }

    // Affiche un rôle spécifique
    public function show($id)
    {
        $role = Role::with('permissions')->findOrFail($id);
        return response()->json(['data' => $role]);
    }

    // Met à jour un rôle existant
    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id);

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                Rule::unique('roles')->ignore($role->id),
            ],
            'description' => 'nullable|string',
            'permission_ids' => 'array',
            'permission_ids.*' => 'exists:permissions,id',
        ]);

        $role->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
        ]);

        if (isset($validated['permission_ids'])) {
            $permissions = Permission::whereIn('id', $validated['permission_ids'])->pluck('name');
            $role->syncPermissions($permissions);
        }

        return response()->json(['data' => $role->load('permissions')]);
    }

    // Supprime un rôle
    public function destroy($id)
    {
        $role = Role::findOrFail($id);
        $role->delete();

        return response()->json(['message' => 'Rôle supprimé avec succès.']);
    }
}
