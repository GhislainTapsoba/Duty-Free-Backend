<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportApiController extends Controller
{
    public function dailySales()
    {
        $sales = Sale::today()->with('saleItems')->get();
        return response()->json([
            'count' => $sales->count(),
            'total' => $sales->sum('total_amount'),
            'sales' => $sales,
        ]);
    }

    public function salesByCashier(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        $sales = Sale::byCashier($request->user_id)->with('saleItems')->get();

        return response()->json([
            'user_id' => $request->user_id,
            'count' => $sales->count(),
            'total' => $sales->sum('total_amount'),
            'sales' => $sales,
        ]);
    }

    public function monthlySales()
    {
        $sales = Sale::whereMonth('sale_date', now()->month)
            ->whereYear('sale_date', now()->year)
            ->where('status', 'completed')
            ->with('saleItems')
            ->get();

        return response()->json([
            'month' => now()->format('F'),
            'count' => $sales->count(),
            'total' => $sales->sum('total_amount'),
            'sales' => $sales,
        ]);
    }

    public function topProducts()
    {
        $topProducts = DB::table('sale_items')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->where('sales.status', 'completed')
            ->whereMonth('sales.sale_date', now()->month)
            ->select('products.id', 'products.name', 'products.code', DB::raw('SUM(sale_items.quantity) as total_sold'))
            ->groupBy('products.id', 'products.name', 'products.code')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get();

        return response()->json($topProducts);
    }

    public function stockReport()
    {
        $products = Product::with(['category', 'supplier'])
            ->where('is_active', true)
            ->orderBy('stock_quantity', 'asc')
            ->get();

        $totalValue = $products->sum(fn($product) => $product->stock_quantity * $product->purchase_price);
        $lowStockCount = $products->where('stock_quantity', '<=', 'minimum_stock')->count();
        $outOfStockCount = $products->where('stock_quantity', 0)->count();

        return response()->json([
            'products' => $products,
            'totalValue' => $totalValue,
            'lowStockCount' => $lowStockCount,
            'outOfStockCount' => $outOfStockCount
        ]);
    }
}
