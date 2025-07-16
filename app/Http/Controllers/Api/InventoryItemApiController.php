<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\InventoryItem;
use Illuminate\Http\Request;

class InventoryItemApiController extends Controller
{
    public function index()
    {
        $items = InventoryItem::with('product', 'inventory')->get();
        return response()->json($items);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'inventory_id' => 'required|exists:inventories,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:0',
        ]);

        $item = InventoryItem::create($validated);

        return response()->json($item, 201);
    }

    public function show(InventoryItem $inventoryItem)
    {
        $inventoryItem->load('product', 'inventory');
        return response()->json($inventoryItem);
    }

    public function update(Request $request, InventoryItem $inventoryItem)
    {
        $validated = $request->validate([
            'quantity' => 'sometimes|required|integer|min:0',
        ]);

        $inventoryItem->update($validated);

        return response()->json($inventoryItem);
    }

    public function destroy(InventoryItem $inventoryItem)
    {
        $inventoryItem->delete();
        return response()->json(null, 204);
    }

    public function adjustQuantity(Request $request, InventoryItem $inventoryItem)
    {
        $validated = $request->validate([
            'adjustment' => 'required|integer',
            'reason' => 'nullable|string',
        ]);

        $inventoryItem->quantity += $validated['adjustment'];
        if ($inventoryItem->quantity < 0) {
            return response()->json(['error' => 'Quantity cannot be negative'], 422);
        }
        $inventoryItem->save();

        // Log adjustment event here if needed

        return response()->json($inventoryItem);
    }

    public function lowStock()
    {
        $threshold = 10; // Exemple de seuil
        $lowStockItems = InventoryItem::where('quantity', '<=', $threshold)->with('product')->get();
        return response()->json($lowStockItems);
    }

    public function valuation()
    {
        $valuation = InventoryItem::with('product')->get()->sum(function ($item) {
            return $item->quantity * $item->product->price;
        });
        return response()->json(['total_valuation' => $valuation]);
    }
}
