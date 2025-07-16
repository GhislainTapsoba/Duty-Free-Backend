<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PurchaseOrderApiController extends Controller
{
    public function index()
    {
        return PurchaseOrder::with(['supplier', 'user'])->orderByDesc('order_date')->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'order_date' => 'required|date',
            'expected_delivery_date' => 'required|date|after_or_equal:order_date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            $purchaseOrder = PurchaseOrder::create([
                'order_number' => 'CMD' . date('Ymd') . str_pad(PurchaseOrder::count() + 1, 4, '0', STR_PAD_LEFT),
                'supplier_id' => $request->supplier_id,
                'user_id' => Auth::id(),
                'order_date' => $request->order_date,
                'expected_delivery_date' => $request->expected_delivery_date,
                'currency' => $request->currency ?? 'XOF',
                'status' => 'pending',
                'notes' => $request->notes,
                'subtotal' => 0,
                'tax_amount' => 0,
                'total_amount' => 0,
            ]);

            $subtotal = 0;
            $totalTax = 0;

            foreach ($request->items as $item) {
                $product = Product::find($item['product_id']);
                $totalPrice = $item['quantity'] * $item['unit_price'];
                $taxAmount = ($totalPrice * $product->tax_rate) / 100;

                PurchaseOrderItem::create([
                    'purchase_order_id' => $purchaseOrder->id,
                    'product_id' => $item['product_id'],
                    'quantity_ordered' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $totalPrice,
                    'tax_rate' => $product->tax_rate,
                    'tax_amount' => $taxAmount,
                    'notes' => $item['notes'] ?? null,
                ]);

                $subtotal += $totalPrice;
                $totalTax += $taxAmount;
            }

            $purchaseOrder->update([
                'subtotal' => $subtotal,
                'tax_amount' => $totalTax,
                'shipping_cost' => $request->shipping_cost ?? 0,
                'insurance_cost' => $request->insurance_cost ?? 0,
                'other_costs' => $request->other_costs ?? 0,
                'total_amount' => $subtotal + $totalTax + ($request->shipping_cost ?? 0) + ($request->insurance_cost ?? 0) + ($request->other_costs ?? 0),
            ]);

            DB::commit();

            return response()->json($purchaseOrder->load('purchaseOrderItems.product'), 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        return PurchaseOrder::with(['supplier', 'user', 'purchaseOrderItems.product'])->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $purchaseOrder = PurchaseOrder::findOrFail($id);

        if ($purchaseOrder->status !== 'pending') {
            return response()->json(['error' => 'Seules les commandes en attente peuvent être modifiées'], 400);
        }

        $data = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'order_date' => 'required|date',
            'expected_delivery_date' => 'required|date|after_or_equal:order_date',
            'notes' => 'nullable|string',
        ]);

        $purchaseOrder->update($data);

        return response()->json($purchaseOrder);
    }

    public function receive(Request $request, $id)
    {
        $purchaseOrder = PurchaseOrder::findOrFail($id);

        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:purchase_order_items,id',
            'items.*.quantity_received' => 'required|integer|min:0',
        ]);

        DB::beginTransaction();

        try {
            foreach ($request->items as $itemData) {
                $item = PurchaseOrderItem::find($itemData['id']);

                if ($itemData['quantity_received'] > $item->quantity_ordered) {
                    throw new \Exception("Quantité reçue supérieure à la quantité commandée pour le produit: " . $item->product->name);
                }

                $item->update([
                    'quantity_received' => $itemData['quantity_received'],
                    'notes' => $itemData['notes'] ?? $item->notes,
                ]);

                if ($itemData['quantity_received'] > 0) {
                    Product::find($item->product_id)->increment('stock_quantity', $itemData['quantity_received']);
                }
            }

            $purchaseOrder->update([
                'status' => 'received',
                'actual_delivery_date' => now(),
            ]);

            DB::commit();
            return response()->json(['message' => 'Commande réceptionnée avec succès']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function cancel($id)
    {
        $purchaseOrder = PurchaseOrder::findOrFail($id);

        if ($purchaseOrder->status !== 'pending') {
            return response()->json(['error' => 'Seules les commandes en attente peuvent être annulées'], 400);
        }

        $purchaseOrder->update(['status' => 'cancelled']);
        return response()->json(['message' => 'Commande annulée avec succès']);
    }

    public function destroy($id)
    {
        $purchaseOrder = PurchaseOrder::findOrFail($id);

        if ($purchaseOrder->status === 'received') {
            return response()->json(['error' => 'Impossible de supprimer une commande reçue'], 400);
        }

        $purchaseOrder->delete();
        return response()->json(['message' => 'Commande supprimée avec succès']);
    }
}
