<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ReturnItem;
use Illuminate\Http\Request;

class ReturnItemApiController extends Controller
{
    public function index()
    {
        return response()->json(ReturnItem::with(['product', 'productReturn'])->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_return_id' => 'required|exists:product_returns,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'refund_amount' => 'required|numeric|min:0',
        ]);

        $item = ReturnItem::create($validated);

        return response()->json($item, 201);
    }

    public function show($id)
    {
        $item = ReturnItem::with(['product', 'productReturn'])->findOrFail($id);

        return response()->json($item);
    }

    public function update(Request $request, $id)
    {
        $item = ReturnItem::findOrFail($id);

        $validated = $request->validate([
            'product_id' => 'sometimes|exists:products,id',
            'quantity' => 'sometimes|integer|min:1',
            'refund_amount' => 'sometimes|numeric|min:0',
        ]);

        $item->update($validated);

        return response()->json($item);
    }

    public function destroy($id)
    {
        $item = ReturnItem::findOrFail($id);
        $item->delete();

        return response()->json(['message' => 'Return item deleted']);
    }
}
