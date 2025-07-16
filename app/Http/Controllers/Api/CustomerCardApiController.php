<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CustomerCard;
use Illuminate\Http\Request;

class CustomerCardApiController extends Controller
{
    public function index()
    {
        return CustomerCard::withCount('sales')->orderByDesc('created_at')->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'card_number' => 'required|string|unique:customer_cards,card_number',
            'holder_name' => 'required|string|max:255',
            'email' => 'required|email|unique:customer_cards,email',
            'phone' => 'required|string|max:20',
            'expiry_date' => 'required|date|after:today',
        ]);

        $data['points_balance'] = 0;
        $data['is_active'] = true;
        $data['issued_date'] = now();

        $card = CustomerCard::create($data);

        return response()->json($card, 201);
    }

    public function show($id)
    {
        return CustomerCard::with(['sales.saleItems'])->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $card = CustomerCard::findOrFail($id);

        $data = $request->validate([
            'card_number' => 'required|string|unique:customer_cards,card_number,' . $card->id,
            'holder_name' => 'required|string|max:255',
            'email' => 'required|email|unique:customer_cards,email,' . $card->id,
            'phone' => 'required|string|max:20',
            'expiry_date' => 'required|date|after:today',
        ]);

        $card->update($data);

        return response()->json($card);
    }

    public function addPoints(Request $request, $id)
    {
        $card = CustomerCard::findOrFail($id);

        $data = $request->validate([
            'points' => 'required|integer|min:1',
        ]);

        $card->increment('points_balance', $data['points']);

        return response()->json(['message' => 'Points ajoutés avec succès', 'points_balance' => $card->points_balance]);
    }

    public function activate($id)
    {
        $card = CustomerCard::findOrFail($id);
        $card->update(['is_active' => true]);
        return response()->json(['message' => 'Carte activée']);
    }

    public function deactivate($id)
    {
        $card = CustomerCard::findOrFail($id);
        $card->update(['is_active' => false]);
        return response()->json(['message' => 'Carte désactivée']);
    }

    public function destroy($id)
    {
        $card = CustomerCard::findOrFail($id);

        if ($card->sales()->count() > 0) {
            return response()->json(['error' => 'Impossible de supprimer une carte avec des transactions'], 400);
        }

        $card->delete();

        return response()->json(['message' => 'Carte supprimée avec succès']);
    }

    public function search(Request $request)
    {
        $query = $request->get('q');

        $cards = CustomerCard::where('is_active', true)
            ->where(function ($q) use ($query) {
                $q->where('card_number', 'like', "%$query%")
                  ->orWhere('holder_name', 'like', "%$query%")
                  ->orWhere('email', 'like', "%$query%")
                  ->orWhere('phone', 'like', "%$query%");
            })
            ->limit(10)
            ->get();

        return response()->json($cards);
    }
}
