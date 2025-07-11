<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SaleItem;
use Illuminate\Http\Request;


// app/Http/Controllers/Api/SaleItemController.php
class SaleItemApiController extends Controller
{
    public function index() {}
    public function store(Request $request) {}
    public function show(SaleItem $saleItem) {}
    public function update(Request $request, SaleItem $saleItem) {}
    public function destroy(SaleItem $saleItem) {}
    public function updateQuantity(Request $request, SaleItem $item) {}
}
