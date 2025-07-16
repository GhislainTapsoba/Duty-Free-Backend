<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\Product;
use App\Models\CashRegister;
use App\Models\Promotion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardApiController extends Controller
{
    public function summary()
    {
        $todaySales = Sale::whereDate('sale_date', today())
            ->where('status', 'completed')
            ->sum('total_amount');

        $monthlySales = Sale::whereMonth('sale_date', now()->month)
            ->whereYear('sale_date', now()->year)
            ->where('status', 'completed')
            ->sum('total_amount');

        $totalProducts = Product::where('is_active', true)->count();

        $lowStockProducts = Product::whereColumn('stock_quantity', '<=', 'minimum_stock')
            ->where('is_active', true)
            ->count();

        $recentSales = Sale::with(['user', 'cashRegister'])
            ->where('status', 'completed')
            ->orderByDesc('sale_date')
            ->limit(5)
            ->get();

        $topProducts = DB::table('sale_items')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->where('sales.status', 'completed')
            ->whereMonth('sales.sale_date', now()->month)
            ->select('products.name', 'products.code', DB::raw('SUM(sale_items.quantity) as total_sold'))
            ->groupBy('products.id', 'products.name', 'products.code')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get();

        $salesByCategory = DB::table('sale_items')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->where('sales.status', 'completed')
            ->whereMonth('sales.sale_date', now()->month)
            ->select('categories.name', DB::raw('SUM(sale_items.total_price) as total_sales'))
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('total_sales')
            ->get();

        $dailySales = Sale::select(
                DB::raw('DATE(sale_date) as date'),
                DB::raw('SUM(total_amount) as total')
            )
            ->where('status', 'completed')
            ->where('sale_date', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $cashRegisters = CashRegister::where('is_active', true)->get();

        $activePromotionsCount = Promotion::where('is_active', true)
            ->get()
            ->filter(fn($promotion) => $promotion->isActive())
            ->count();

        $promotionsExpiringSoon = Promotion::where('is_active', true)
            ->whereBetween('end_date', [now(), now()->addDays(7)])
            ->get();

        $totalPromotions = Promotion::count();

        return response()->json(compact(
            'todaySales',
            'monthlySales',
            'totalProducts',
            'lowStockProducts',
            'recentSales',
            'topProducts',
            'salesByCategory',
            'dailySales',
            'cashRegisters',
            'activePromotionsCount',
            'promotionsExpiringSoon',
            'totalPromotions'
        ));

    }

    public function salesReport(Request $request)
    {
        $dateFrom = $request->get('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', now()->format('Y-m-d'));

        $sales = Sale::with(['saleItems.product', 'user', 'cashRegister'])
            ->where('status', 'completed')
            ->whereBetween('sale_date', [$dateFrom, $dateTo])
            ->orderByDesc('sale_date')
            ->get();

        $totalSales = $sales->sum('total_amount');
        $totalTax = $sales->sum('tax_amount');
        $totalDiscount = $sales->sum('discount_amount');
        $salesCount = $sales->count();

        return response()->json(compact('sales', 'totalSales', 'totalTax', 'totalDiscount', 'salesCount', 'dateFrom', 'dateTo'));
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

        return response()->json(compact('products', 'totalValue', 'lowStockCount', 'outOfStockCount'));
    }
}
