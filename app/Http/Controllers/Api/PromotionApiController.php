<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Promotion;
use Illuminate\Http\Request;

class PromotionApiController extends Controller
{
    public function index()
    {
        return Promotion::orderByDesc('created_at')->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:promotions,code',
            'type' => 'required|in:percentage,fixed_amount,buy_x_get_y,menu',
            'discount_value' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'minimum_amount' => 'nullable|numeric|min:0',
            'minimum_quantity' => 'nullable|integer|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'applicable_days' => 'nullable|array',
            'applicable_categories' => 'nullable|array',
            'applicable_products' => 'nullable|array',
        ]);

        $data['is_active'] = true;
        $data['usage_count'] = 0;

        $promotion = Promotion::create($data);

        return response()->json($promotion, 201);
    }

    public function show($id)
    {
        return Promotion::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $promotion = Promotion::findOrFail($id);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:promotions,code,' . $promotion->id,
            'type' => 'required|in:percentage,fixed_amount,buy_x_get_y,menu',
            'discount_value' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'minimum_amount' => 'nullable|numeric|min:0',
            'minimum_quantity' => 'nullable|integer|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'applicable_days' => 'nullable|array',
            'applicable_categories' => 'nullable|array',
            'applicable_products' => 'nullable|array',
        ]);

        $promotion->update($data);

        return response()->json($promotion);
    }

    public function activate($id)
    {
        $promotion = Promotion::findOrFail($id);
        $promotion->update(['is_active' => true]);
        return response()->json(['message' => 'Promotion activée']);
    }

    public function deactivate($id)
    {
        $promotion = Promotion::findOrFail($id);
        $promotion->update(['is_active' => false]);
        return response()->json(['message' => 'Promotion désactivée']);
    }

    public function destroy($id)
    {
        $promotion = Promotion::findOrFail($id);
        $promotion->delete();
        return response()->json(['message' => 'Promotion supprimée']);
    }

    public function active()
    {
        return Promotion::where('is_active', true)
            ->get()
            ->filter(fn($promotion) => $promotion->isActive())
            ->values();
    }
}
