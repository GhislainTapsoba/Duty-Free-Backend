<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use Illuminate\Http\Request;

// app/Http/Controllers/Api/PurchaseOrderController.php
class PurchaseOrderApiController extends Controller
{
    public function index() {}
    public function store(Request $request) {}
    public function show(PurchaseOrder $purchaseOrder) {}
    public function update(Request $request, PurchaseOrder $purchaseOrder) {}
    public function destroy(PurchaseOrder $purchaseOrder) {}
    public function items(PurchaseOrder $order) {}
    public function submit(PurchaseOrder $order) {}
    public function approve(PurchaseOrder $order) {}
    public function receive(PurchaseOrder $order) {}
    public function cancel(PurchaseOrder $order) {}
    public function bySupplier(Supplier $supplier) {}
    public function search(Request $request) {}
}
