<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;

// app/Http/Controllers/Api/SupplierController.php
class SupplierApiController extends Controller
{
    public function index() {}
    public function store(Request $request) {}
    public function show(Supplier $supplier) {}
    public function update(Request $request, Supplier $supplier) {}
    public function destroy(Supplier $supplier) {}
    public function products(Supplier $supplier) {}
    public function search(Request $request) {}
}
