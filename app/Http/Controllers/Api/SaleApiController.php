<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use App\Models\CashRegister;
use App\Models\CustomerCard;
use App\Models\Payment;
use App\Models\Promotion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;

class SaleApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Sale::with(['user', 'cashRegister', 'customerCard']);
        
        if ($request->has('date_from')) {
            $query->whereDate('sale_date', '>=', $request->date_from);
        }
        
        if ($request->has('date_to')) {
            $query->whereDate('sale_date', '<=', $request->date_to);
        }
        
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        
        $sales = $query->orderBy('sale_date', 'desc')->paginate(20);
        
        return response()->json([
            'status' => 'success',
            'data' => $sales
        ]);
    }
    
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'cash_register_id' => 'required|exists:cash_registers,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'payments' => 'required|array|min:1',
            'payments.*.method' => 'required|string',
            'payments.*.amount' => 'required|numeric|min:0',
        ]);
        
        DB::beginTransaction();
        
        try {
            // Create sale
            $sale = Sale::create([
                'sale_number' => 'VTE' . date('Ymd') . str_pad(Sale::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT),
                'receipt_number' => 'REC' . time(),
                'cash_register_id' => $request->cash_register_id,
                'user_id' => Auth::id(),
                'customer_card_id' => $request->customer_card_id,
                'customer_name' => $request->customer_name,
                'flight_number' => $request->flight_number,
                'destination' => $request->destination,
                'airline' => $request->airline,
                'currency' => $request->currency ?? 'XOF',
                'subtotal' => 0,
                'tax_amount' => 0,
                'discount_amount' => 0,
                'total_amount' => 0,
                'status' => 'completed',
                'sale_date' => now(),
                'notes' => $request->notes
            ]);
            
            $subtotal = 0;
            $totalTax = 0;
            $totalDiscount = 0;
            
            // Process sale items
            foreach ($request->items as $item) {
                $product = Product::find($item['product_id']);
                
                if (!$product || $product->stock_quantity < $item['quantity']) {
                    throw new \Exception("Stock insuffisant pour le produit: " . $product->name);
                }
                
                $unitPrice = $product->selling_price_xof;
                $quantity = $item['quantity'];
                $totalPrice = $unitPrice * $quantity;
                
                // Calculate tax
                $taxAmount = $product->tax_included ? 
                    ($totalPrice * $product->tax_rate) / (100 + $product->tax_rate) :
                    ($totalPrice * $product->tax_rate) / 100;
                
                // Apply promotions
                $discountAmount = 0;
                $promotions = Promotion::where('is_active', true)->get();
                foreach ($promotions as $promotion) {
                    if ($promotion->canBeAppliedToProduct($product->id)) {
                        $discountAmount += $promotion->calculateDiscount($totalPrice, $quantity);
                    }
                }
                
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'product_code' => $product->code,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'total_price' => $totalPrice,
                    'tax_rate' => $product->tax_rate,
                    'tax_amount' => $taxAmount,
                    'discount_amount' => $discountAmount
                ]);
                
                // Update product stock
                $product->decrement('stock_quantity', $quantity);
                
                $subtotal += $totalPrice;
                $totalTax += $taxAmount;
                $totalDiscount += $discountAmount;
            }
            
            // Process payments
            foreach ($request->payments as $payment) {
                Payment::create([
                    'sale_id' => $sale->id,
                    'payment_method' => $payment['method'],
                    'amount' => $payment['amount'],
                    'currency' => $request->currency ?? 'XOF',
                    'amount_in_base_currency' => $payment['amount'],
                    'status' => 'completed',
                    'payment_date' => now(),
                    'card_type' => $payment['card_type'] ?? null,
                    'transaction_reference' => $payment['transaction_reference'] ?? null,
                    'mobile_money_provider' => $payment['mobile_money_provider'] ?? null,
                    'mobile_money_number' => $payment['mobile_money_number'] ?? null,
                ]);
            }
            
            // Update sale totals
            $sale->update([
                'subtotal' => $subtotal,
                'tax_amount' => $totalTax,
                'discount_amount' => $totalDiscount,
                'total_amount' => $subtotal + $totalTax - $totalDiscount
            ]);
            
            DB::commit();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Vente créée avec succès',
                'data' => $sale->load(['saleItems.product', 'payments', 'user', 'cashRegister', 'customerCard'])
            ], 201);
            
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 400);
        }
    }
    
    public function show(Sale $sale): JsonResponse
    {
        $sale->load(['saleItems.product', 'payments', 'user', 'cashRegister', 'customerCard']);
        
        return response()->json([
            'status' => 'success',
            'data' => $sale
        ]);
    }
    
    public function cancel(Sale $sale): JsonResponse
    {
        if ($sale->status !== 'completed') {
            return response()->json([
                'status' => 'error',
                'message' => 'Seules les ventes terminées peuvent être annulées'
            ], 400);
        }
        
        DB::beginTransaction();
        
        try {
            // Restore stock
            foreach ($sale->saleItems as $item) {
                $product = Product::find($item->product_id);
                if ($product) {
                    $product->increment('stock_quantity', $item->quantity);
                }
            }
            
            // Update sale status
            $sale->update(['status' => 'cancelled']);
            
            DB::commit();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Vente annulée avec succès'
            ]);
            
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 400);
        }
    }
    
    public function dailyReport(Request $request): JsonResponse
    {
        $date = $request->get('date', today());
        
        $sales = Sale::whereDate('sale_date', $date)
                    ->where('status', 'completed')
                    ->with(['saleItems', 'payments', 'user', 'cashRegister'])
                    ->get();
        
        $totalSales = $sales->sum('total_amount');
        $totalTax = $sales->sum('tax_amount');
        $totalDiscount = $sales->sum('discount_amount');
        $salesCount = $sales->count();
        
        $paymentMethods = $sales->load('payments')
                               ->pluck('payments')
                               ->flatten()
                               ->groupBy('payment_method')
                               ->map(function ($payments) {
                                   return $payments->sum('amount');
                               });
        
        return response()->json([
            'status' => 'success',
            'data' => [
                'sales' => $sales,
                'summary' => [
                    'total_sales' => $totalSales,
                    'total_tax' => $totalTax,
                    'total_discount' => $totalDiscount,
                    'sales_count' => $salesCount,
                    'payment_methods' => $paymentMethods
                ],
                'date' => $date
            ]
        ]);
    }
}