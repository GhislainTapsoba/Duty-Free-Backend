<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PermissionApiController extends Controller
{
    // Liste toutes les permissions
    public function index()
    {
        $permissions = Permission::orderBy('name')->get();
        return response()->json(['data' => $permissions]);
    }

    // Crée une nouvelle permission
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:permissions,name',
            'description' => 'nullable|string',
        ]);

        $permission = Permission::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'guard_name' => 'web', // ou le guard que tu utilises
        ]);

        return response()->json(['data' => $permission], 201);
    }

    // Affiche une permission spécifique
    public function show($id)
    {
        $permission = Permission::findOrFail($id);
        return response()->json(['data' => $permission]);
    }

    // Met à jour une permission
    public function update(Request $request, $id)
    {
        $permission = Permission::findOrFail($id);

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                Rule::unique('permissions')->ignore($permission->id),
            ],
            'description' => 'nullable|string',
        ]);

        $permission->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
        ]);

        return response()->json(['data' => $permission]);
    }

    // Supprime une permission
    public function destroy($id)
    {
        $permission = Permission::findOrFail($id);
        $permission->delete();

        return response()->json(['message' => 'Permission supprimée avec succès.']);
    }
}
