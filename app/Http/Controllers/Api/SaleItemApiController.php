<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SaleItem;
use Illuminate\Http\Request;

class SaleItemApiController extends Controller
{
    public function index()
    {
        $items = SaleItem::with('sale', 'product')->get();
        return response()->json($items);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'sale_id' => 'required|exists:sales,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
        ]);

        $item = SaleItem::create($validated);

        return response()->json($item, 201);
    }

    public function show(SaleItem $saleItem)
    {
        $saleItem->load('sale', 'product');
        return response()->json($saleItem);
    }

    public function update(Request $request, SaleItem $saleItem)
    {
        $validated = $request->validate([
            'quantity' => 'sometimes|required|integer|min:1',
            'price' => 'sometimes|required|numeric|min:0',
        ]);

        $saleItem->update($validated);

        return response()->json($saleItem);
    }

    public function destroy(SaleItem $saleItem)
    {
        $saleItem->delete();
        return response()->json(null, 204);
    }
}
