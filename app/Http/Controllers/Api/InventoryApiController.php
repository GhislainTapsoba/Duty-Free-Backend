<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use App\Models\InventoryItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InventoryApiController extends Controller
{
    public function index()
    {
        return Inventory::with('user')->orderBy('inventory_date', 'desc')->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'inventory_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $data['reference'] = 'INV' . date('Ymd') . str_pad(Inventory::count() + 1, 4, '0', STR_PAD_LEFT);
        $data['user_id'] = Auth::id();
        $data['status'] = 'pending';

        $inventory = Inventory::create($data);
        return response()->json($inventory, 201);
    }

    public function show($id)
    {
        return Inventory::with(['inventoryItems.product', 'user'])->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $inventory = Inventory::findOrFail($id);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'inventory_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $inventory->update($data);
        return response()->json($inventory);
    }

    public function destroy($id)
    {
        $inventory = Inventory::findOrFail($id);
        if ($inventory->status === 'completed') {
            return response()->json(['error' => 'Impossible de supprimer un inventaire terminé'], 400);
        }
        $inventory->delete();
        return response()->json(['message' => 'Inventaire supprimé']);
    }

    public function addProducts(Request $request, $id)
    {
        $inventory = Inventory::findOrFail($id);
        $data = $request->validate([
            'products' => 'required|array|min:1',
            'products.*' => 'exists:products,id',
        ]);

        foreach ($data['products'] as $productId) {
            if ($inventory->inventoryItems()->where('product_id', $productId)->exists()) continue;

            $product = Product::find($productId);
            InventoryItem::create([
                'inventory_id' => $inventory->id,
                'product_id' => $productId,
                'system_quantity' => $product->stock_quantity,
                'counted_quantity' => 0,
                'variance' => 0,
                'unit_cost' => $product->purchase_price,
                'variance_value' => 0,
            ]);
        }

        return response()->json(['message' => 'Produits ajoutés avec succès']);
    }

    public function updateCount(Request $request, $inventoryId, $itemId)
    {
        $item = InventoryItem::where('inventory_id', $inventoryId)->findOrFail($itemId);
        $data = $request->validate(['counted_quantity' => 'required|integer|min:0', 'notes' => 'nullable|string']);

        $variance = $data['counted_quantity'] - $item->system_quantity;
        $varianceValue = $variance * $item->unit_cost;

        $item->update([
            'counted_quantity' => $data['counted_quantity'],
            'variance' => $variance,
            'variance_value' => $varianceValue,
            'notes' => $data['notes'] ?? null,
        ]);

        return response()->json($item);
    }

    public function complete($id)
    {
        $inventory = Inventory::with('inventoryItems')->findOrFail($id);

        if ($inventory->status !== 'pending') {
            return response()->json(['error' => 'Seuls les inventaires en attente peuvent être finalisés'], 400);
        }

        DB::beginTransaction();
        try {
            foreach ($inventory->inventoryItems as $item) {
                if ($item->counted_quantity > 0) {
                    Product::where('id', $item->product_id)->update(['stock_quantity' => $item->counted_quantity]);
                }
            }
            $inventory->update(['status' => 'completed']);
            DB::commit();
            return response()->json(['message' => 'Inventaire finalisé avec succès']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
