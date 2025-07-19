<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\InventoryAdjustment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class InventoryAdjustmentApiController extends Controller
{
    // Liste tous les ajustements d'inventaire
    public function index()
    {
        $adjustments = InventoryAdjustment::with('product', 'user')->orderBy('created_at', 'desc')->get();
        return response()->json($adjustments);
    }

    // Crée un nouvel ajustement
    public function store(Request $request)
    {
        $data = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'adjustment_type' => ['required', Rule::in(['addition', 'subtraction'])],
            'quantity' => ['required', 'integer', 'min:1'],
            'reason' => ['required', 'string', 'max:500'],
        ]);

        $product = Product::findOrFail($data['product_id']);

        // Calcul nouvelle quantité en fonction du type d'ajustement
        $newQuantity = $product->stock_quantity;
        if ($data['adjustment_type'] === 'addition') {
            $newQuantity += $data['quantity'];
        } else {
            $newQuantity -= $data['quantity'];
            if ($newQuantity < 0) {
                return response()->json(['error' => "La quantité en stock ne peut pas devenir négative."], 422);
            }
        }

        // Mise à jour du stock produit
        $product->stock_quantity = $newQuantity;
        $product->save();

        // Création de l'ajustement
        $adjustment = InventoryAdjustment::create([
            'product_id' => $data['product_id'],
            'adjustment_type' => $data['adjustment_type'],
            'quantity' => $data['quantity'],
            'reason' => $data['reason'],
            'user_id' => Auth::id(),
        ]);

        return response()->json($adjustment, 201);
    }

    // Récupère un ajustement par ID
    public function show($id)
    {
        $adjustment = InventoryAdjustment::with('product', 'user')->findOrFail($id);
        return response()->json($adjustment);
    }

    // Met à jour un ajustement (uniquement raison et quantité, pas le produit ni type)
    public function update(Request $request, $id)
    {
        $adjustment = InventoryAdjustment::findOrFail($id);

        $data = $request->validate([
            // Si tu veux autoriser modification du type ou produit, il faudra plus de logique
            'quantity' => ['required', 'integer', 'min:1'],
            'reason' => ['required', 'string', 'max:500'],
        ]);

        $product = Product::findOrFail($adjustment->product_id);

        // Annuler ancien ajustement (inverser l'ancien effet)
        if ($adjustment->adjustment_type === 'addition') {
            $product->stock_quantity -= $adjustment->quantity;
        } else {
            $product->stock_quantity += $adjustment->quantity;
        }

        // Appliquer le nouvel ajustement
        if ($adjustment->adjustment_type === 'addition') {
            $newQuantity = $product->stock_quantity + $data['quantity'];
        } else {
            $newQuantity = $product->stock_quantity - $data['quantity'];
        }

        if ($newQuantity < 0) {
            return response()->json(['error' => "La quantité en stock ne peut pas devenir négative."], 422);
        }

        $product->stock_quantity = $newQuantity;
        $product->save();

        // Mise à jour de l'ajustement
        $adjustment->quantity = $data['quantity'];
        $adjustment->reason = $data['reason'];
        $adjustment->save();

        return response()->json($adjustment);
    }

    // Supprime un ajustement et inverse son effet sur le stock
    public function destroy($id)
    {
        $adjustment = InventoryAdjustment::findOrFail($id);
        $product = Product::findOrFail($adjustment->product_id);

        // Inverse l'effet de l'ajustement sur le stock
        if ($adjustment->adjustment_type === 'addition') {
            $newQuantity = $product->stock_quantity - $adjustment->quantity;
        } else {
            $newQuantity = $product->stock_quantity + $adjustment->quantity;
        }

        if ($newQuantity < 0) {
            return response()->json(['error' => "Suppression impossible : stock deviendrait négatif."], 422);
        }

        $product->stock_quantity = $newQuantity;
        $product->save();

        $adjustment->delete();

        return response()->json(['message' => 'Ajustement supprimé avec succès.']);
    }
}
