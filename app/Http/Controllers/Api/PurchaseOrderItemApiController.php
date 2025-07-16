<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrderItem;
use Illuminate\Http\Request;

class PurchaseOrderItemApiController extends Controller
{
    public function index()
    {
        $items = PurchaseOrderItem::with('purchaseOrder', 'product')->get();
        return response()->json($items);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'purchase_order_id' => 'required|exists:purchase_orders,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
        ]);

        $item = PurchaseOrderItem::create($validated);

        return response()->json($item, 201);
    }

    public function show(PurchaseOrderItem $purchaseOrderItem)
    {
        $purchaseOrderItem->load('purchaseOrder', 'product');
        return response()->json($purchaseOrderItem);
    }

    public function update(Request $request, PurchaseOrderItem $purchaseOrderItem)
    {
        $validated = $request->validate([
            'quantity' => 'sometimes|required|integer|min:1',
            'price' => 'sometimes|required|numeric|min:0',
        ]);

        $purchaseOrderItem->update($validated);

        return response()->json($purchaseOrderItem);
    }

    public function destroy(PurchaseOrderItem $purchaseOrderItem)
    {
        $purchaseOrderItem->delete();
        return response()->json(null, 204);
    }
}
