<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CategoryApiController extends Controller
{
    public function index(): JsonResponse
    {
        $categories = Category::with(['parent', 'children'])->get();
        
        return response()->json([
            'status' => 'success',
            'data' => $categories
        ]);
    }
    
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:categories,id',
        ]);
        
        $category = Category::create([
            'name' => $request->name,
            'description' => $request->description,
            'parent_id' => $request->parent_id,
            'is_active' => true,
        ]);
        
        return response()->json([
            'status' => 'success',
            'message' => 'Catégorie créée avec succès',
            'data' => $category->load(['parent', 'children'])
        ], 201);
    }
    
    public function show(Category $category): JsonResponse
    {
        $category->load(['parent', 'children', 'products']);
        
        return response()->json([
            'status' => 'success',
            'data' => $category
        ]);
    }
    
    public function update(Request $request, Category $category): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:categories,id',
        ]);
        
        $category->update([
            'name' => $request->name,
            'description' => $request->description,
            'parent_id' => $request->parent_id,
        ]);
        
        return response()->json([
            'status' => 'success',
            'message' => 'Catégorie mise à jour avec succès',
            'data' => $category->load(['parent', 'children'])
        ]);
    }
    
    public function destroy(Category $category): JsonResponse
    {
        if ($category->products()->count() > 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'Impossible de supprimer une catégorie contenant des produits'
            ], 400);
        }
        
        if ($category->children()->count() > 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'Impossible de supprimer une catégorie ayant des sous-catégories'
            ], 400);
        }
        
        $category->delete();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Catégorie supprimée avec succès'
        ]);
    }
}