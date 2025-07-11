<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Http\Request;

// app/Http/Controllers/Api/ProductController.php
class ProductApiController extends Controller
{
    public function index() {}
    public function store(Request $request) {}
    public function show(Product $product) {}
    public function update(Request $request, Product $product) {}
    public function destroy(Product $product) {}
    public function inventory(Product $product) {}
    public function byCategory(Category $category) {}
    public function bySupplier(Supplier $supplier) {}
    public function search(Request $request) {}
    public function bestSellers() {}
    public function publicIndex() {}
}
