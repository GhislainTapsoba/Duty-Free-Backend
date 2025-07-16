<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\StockMovement;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StockMovementApiController extends Controller
{
    public function index(Request $request)
    {
        $query = StockMovement::with(['product', 'lot', 'user']);

        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('movement_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('movement_date', '<=', $request->date_to);
        }

        return $query->orderByDesc('movement_date')->paginate(20);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'type' => 'required|in:in,out,adjustment,transfer',
            'quantity' => 'required|integer|min:1',
            'unit_cost' => 'required|numeric|min:0',
            'reason' => 'required|string|max:255',
            'movement_date' => 'required|date',
            'lot_id' => 'nullable|exists:lots,id',
            'notes' => 'nullable|string',
            'source_location' => 'nullable|string|max:255',
            'destination_location' => 'nullable|string|max:255',
        ]);

        $product = Product::find($data['product_id']);
        $previousStock = $product->stock_quantity;

        $adjustedQty = $data['quantity'];
        if (in_array($data['type'], ['out', 'adjustment'])) {
            $adjustedQty = -$adjustedQty;
        }

        $newStock = $previousStock + $adjustedQty;
        if ($newStock < 0) {
            return response()->json(['error' => 'Stock insuffisant pour cette opération'], 400);
        }

        $movement = StockMovement::create([
            'reference' => 'MVT' . date('Ymd') . str_pad(StockMovement::count() + 1, 6, '0', STR_PAD_LEFT),
            'product_id' => $data['product_id'],
            'lot_id' => $data['lot_id'] ?? null,
            'type' => $data['type'],
            'quantity' => $adjustedQty,
            'previous_stock' => $previousStock,
            'new_stock' => $newStock,
            'unit_cost' => $data['unit_cost'],
            'total_cost' => abs($adjustedQty) * $data['unit_cost'],
            'reason' => $data['reason'],
            'notes' => $data['notes'] ?? null,
            'user_id' => Auth::id(),
            'source_location' => $data['source_location'],
            'destination_location' => $data['destination_location'],
            'movement_date' => $data['movement_date'],
        ]);

        $product->update(['stock_quantity' => $newStock]);

        return response()->json($movement, 201);
    }

    public function show($id)
    {
        return StockMovement::with(['product', 'lot', 'user'])->findOrFail($id);
    }

    public function destroy($id)
    {
        $movement = StockMovement::findOrFail($id);

        $product = Product::find($movement->product_id);
        $product->update(['stock_quantity' => $movement->previous_stock]);

        $movement->delete();

        return response()->json(['message' => 'Mouvement supprimé et stock réinitialisé']);
    }
}