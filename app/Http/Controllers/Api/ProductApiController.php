<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;

class ProductApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Product::with(['category', 'supplier']);
        
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('code', 'like', '%' . $request->search . '%')
                  ->orWhere('barcode', 'like', '%' . $request->search . '%');
        }
        
        if ($request->has('category')) {
            $query->where('category_id', $request->category);
        }
        
        if ($request->has('supplier')) {
            $query->where('supplier_id', $request->supplier);
        }
        
        $products = $query->paginate(20);
        
        return response()->json([
            'status' => 'success',
            'data' => $products
        ]);
    }
    
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:products,code',
            'category_id' => 'required|exists:categories,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'purchase_price' => 'required|numeric|min:0',
            'selling_price_xof' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'minimum_stock' => 'required|integer|min:0',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);
        
        $data = $request->all();
        
        // Handle images upload
        if ($request->hasFile('images')) {
            $images = [];
            foreach ($request->file('images') as $image) {
                $filename = Str::random(40) . '.' . $image->getClientOriginalExtension();
                $image->storeAs('products', $filename, 'public');
                $images[] = $filename;
            }
            $data['images'] = $images;
        }
        
        // Generate barcode if not provided
        if (!$request->barcode) {
            $data['barcode'] = 'PRD' . str_pad(Product::count() + 1, 6, '0', STR_PAD_LEFT);
        }
        
        $product = Product::create($data);
        
        return response()->json([
            'status' => 'success',
            'message' => 'Produit créé avec succès',
            'data' => $product->load(['category', 'supplier'])
        ], 201);
    }
    
    public function show(Product $product): JsonResponse
    {
        $product->load(['category', 'supplier', 'stockMovements', 'lots']);
        
        return response()->json([
            'status' => 'success',
            'data' => $product
        ]);
    }
    
    public function update(Request $request, Product $product): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:products,code,' . $product->id,
            'category_id' => 'required|exists:categories,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'purchase_price' => 'required|numeric|min:0',
            'selling_price_xof' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'minimum_stock' => 'required|integer|min:0',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);
        
        $data = $request->all();
        
        // Handle images upload
        if ($request->hasFile('images')) {
            // Delete old images
            if ($product->images) {
                foreach ($product->images as $image) {
                    Storage::disk('public')->delete('products/' . $image);
                }
            }
            
            $images = [];
            foreach ($request->file('images') as $image) {
                $filename = Str::random(40) . '.' . $image->getClientOriginalExtension();
                $image->storeAs('products', $filename, 'public');
                $images[] = $filename;
            }
            $data['images'] = $images;
        }
        
        $product->update($data);
        
        return response()->json([
            'status' => 'success',
            'message' => 'Produit mis à jour avec succès',
            'data' => $product->load(['category', 'supplier'])
        ]);
    }
    
    public function destroy(Product $product): JsonResponse
    {
        // Delete images
        if ($product->images) {
            foreach ($product->images as $image) {
                Storage::disk('public')->delete('products/' . $image);
            }
        }
        
        $product->delete();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Produit supprimé avec succès'
        ]);
    }
    
    public function lowStock(): JsonResponse
    {
        $products = Product::whereColumn('stock_quantity', '<=', 'minimum_stock')
                          ->where('is_active', true)
                          ->with(['category', 'supplier'])
                          ->get();
        
        return response()->json([
            'status' => 'success',
            'data' => $products
        ]);
    }
    
    public function search(Request $request): JsonResponse
    {
        $query = $request->get('q');
        $products = Product::where('is_active', true)
                          ->where(function($q) use ($query) {
                              $q->where('name', 'like', '%' . $query . '%')
                                ->orWhere('code', 'like', '%' . $query . '%')
                                ->orWhere('barcode', 'like', '%' . $query . '%');
                          })
                          ->with(['category'])
                          ->limit(10)
                          ->get();
        
        return response()->json([
            'status' => 'success',
            'data' => $products
        ]);
    }
}
