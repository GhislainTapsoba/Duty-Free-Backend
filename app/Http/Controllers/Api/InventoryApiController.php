<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use Illuminate\Http\Request;

// app/Http/Controllers/Api/InventoryController.php
class InventoryApiController extends Controller
{
    public function index() {}
    public function store(Request $request) {}
    public function show(Inventory $inventory) {}
    public function update(Request $request, Inventory $inventory) {}
    public function destroy(Inventory $inventory) {}
    public function items(Inventory $inventory) {}
}
