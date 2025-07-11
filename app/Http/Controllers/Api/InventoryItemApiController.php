<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\InventoryItem;
use Illuminate\Http\Request;

// app/Http/Controllers/Api/InventoryItemController.php
class InventoryItemApiController extends Controller
{
    public function index() {}
    public function store(Request $request) {}
    public function show(InventoryItem $inventoryItem) {}
    public function update(Request $request, InventoryItem $inventoryItem) {}
    public function destroy(InventoryItem $inventoryItem) {}
    public function adjustQuantity(Request $request, InventoryItem $item) {}
    public function lowStock() {}
    public function valuation() {}
}
