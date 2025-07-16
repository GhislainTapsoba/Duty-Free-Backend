<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lot;
use Illuminate\Http\Request;

class LotApiController extends Controller
{
    public function index()
    {
        return Lot::with(['product', 'supplier', 'purchaseOrder', 'stockMovements'])->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'lot_number' => 'required|string|unique:lots,lot_number',
            'product_id' => 'required|exists:products,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'purchase_order_id' => 'nullable|exists:purchase_orders,id',
            'expiry_date' => 'nullable|date',
            'quantity' => 'required|integer|min:0',
            'unit_cost' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $lot = Lot::create($data);
        return response()->json($lot->load(['product', 'supplier']), 201);
    }

    public function show($id)
    {
        $lot = Lot::with(['product', 'supplier', 'purchaseOrder', 'stockMovements'])->findOrFail($id);
        return response()->json($lot);
    }

    public function update(Request $request, $id)
    {
        $lot = Lot::findOrFail($id);

        $data = $request->validate([
            'lot_number' => 'required|string|unique:lots,lot_number,' . $lot->id,
            'product_id' => 'required|exists:products,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'purchase_order_id' => 'nullable|exists:purchase_orders,id',
            'expiry_date' => 'nullable|date',
            'quantity' => 'required|integer|min:0',
            'unit_cost' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $lot->update($data);
        return response()->json($lot->load(['product', 'supplier']));
    }

    public function destroy($id)
    {
        $lot = Lot::findOrFail($id);

        if ($lot->stockMovements()->exists()) {
            return response()->json(['error' => 'Impossible de supprimer un lot avec des mouvements.'], 400);
        }

        $lot->delete();
        return response()->json(['message' => 'Lot supprimé avec succès.']);
    }
}
