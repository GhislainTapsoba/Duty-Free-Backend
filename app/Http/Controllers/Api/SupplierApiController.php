<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SupplierApiController extends Controller
{
    public function index(): JsonResponse
    {
        $suppliers = Supplier::withCount('products')->get();
        
        return response()->json([
            'status' => 'success',
            'data' => $suppliers
        ]);
    }
    
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:suppliers,email',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'city' => 'required|string|max:100',
            'country' => 'required|string|max:100',
        ]);
        
        $supplier = Supplier::create([
            'name' => $request->name,
            'company_name' => $request->company_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'city' => $request->city,
            'country' => $request->country,
            'tax_number' => $request->tax_number,
            'contact_person' => $request->contact_person,
            'payment_terms' => $request->payment_terms,
            'is_active' => true,
        ]);
        
        return response()->json([
            'status' => 'success',
            'message' => 'Fournisseur créé avec succès',
            'data' => $supplier
        ], 201);
    }
    
    public function show(Supplier $supplier): JsonResponse
    {
        $supplier->load(['products', 'purchaseOrders']);
        
        return response()->json([
            'status' => 'success',
            'data' => $supplier
        ]);
    }
    
    public function update(Request $request, Supplier $supplier): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:suppliers,email,' . $supplier->id,
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'city' => 'required|string|max:100',
            'country' => 'required|string|max:100',
        ]);
        
        $supplier->update([
            'name' => $request->name,
            'company_name' => $request->company_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'city' => $request->city,
            'country' => $request->country,
            'tax_number' => $request->tax_number,
            'contact_person' => $request->contact_person,
            'payment_terms' => $request->payment_terms,
        ]);
        
        return response()->json([
            'status' => 'success',
            'message' => 'Fournisseur mis à jour avec succès',
            'data' => $supplier
        ]);
    }
    
    public function destroy(Supplier $supplier): JsonResponse
    {
        if ($supplier->products()->count() > 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'Impossible de supprimer un fournisseur ayant des produits'
            ], 400);
        }
        
        $supplier->delete();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Fournisseur supprimé avec succès'
        ]);
    }
}
