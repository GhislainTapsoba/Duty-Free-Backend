<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrderItem;
use Illuminate\Http\Request;

// app/Http/Controllers/Api/PurchaseOrderItemController.php
class PurchaseOrderItemApiController extends Controller
{
    public function index() {}
    public function store(Request $request) {}
    public function show(PurchaseOrderItem $purchaseOrderItem) {}
    public function update(Request $request, PurchaseOrderItem $purchaseOrderItem) {}
    public function destroy(PurchaseOrderItem $purchaseOrderItem) {}
    public function updateQuantity(Request $request, PurchaseOrderItem $item) {}
}
