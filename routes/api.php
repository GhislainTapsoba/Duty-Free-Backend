<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\{
    AuthApiController,
    UserApiController,
    CategoryApiController,
    DashboardApiController,
    SupplierApiController,
    ProductApiController,
    InventoryApiController,
    InventoryItemApiController,
    LotApiController,
    InventoryAdjustmentApiController,
    CashRegisterApiController,
    SaleApiController,
    SaleItemApiController,
    PurchaseOrderApiController,
    PurchaseOrderItemApiController,
    PromotionApiController,
    PasswordResetApiController,
    ProductReturnApiController,
    ReturnItemApiController,
    SystemSettingApiController,
    ReportApiController,
    PosApiController,
    AlertApiController
    
};

// Routes d'authentification
Route::middleware('web')->prefix('auth')->group(function () {
    Route::post('register', [AuthApiController::class, 'register']);
    Route::post('login', [AuthApiController::class, 'login']);
    Route::post('logout', [AuthApiController::class, 'logout'])->middleware('auth:sanctum');
    Route::post('refresh', [AuthApiController::class, 'refresh'])->middleware('auth:sanctum');
    Route::get('profile', [AuthApiController::class, 'profile'])->middleware('auth:sanctum');
    Route::post('forgot-password', [AuthApiController::class, 'forgotPassword']);
    Route::post('reset-password', [AuthApiController::class, 'resetPassword']);
    Route::get('csrf-token', function () {
        return response()->json(['csrf_token' => csrf_token()]);
    })->middleware('web'); // CSRF Token pour Sanctum (SPA)
    // Password Reset
    Route::post('/auth/forgot-password', [PasswordResetApiController::class, 'forgotPassword']);
    Route::post('/auth/reset-password', [PasswordResetApiController::class, 'resetPassword']);
});


// Routes protégées par authentification Sanctum
Route::middleware('auth:sanctum')->group(function () {
    
    // Users
    Route::apiResource('users', UserApiController::class);
    Route::post('users/{user}/reset-password', [UserApiController::class, 'resetPassword']);

    // Dashboard
    Route::prefix('dashboard')->group(function () {
        Route::get('summary', [DashboardApiController::class, 'summary']);
        Route::get('sales-report', [DashboardApiController::class, 'salesReport']);
        Route::get('stock-report', [DashboardApiController::class, 'stockReport']);
    });

    // Categories
    Route::apiResource('categories', CategoryApiController::class);
    
    // Suppliers
    Route::apiResource('suppliers', SupplierApiController::class);
    Route::get('suppliers/{supplier}/products', [SupplierApiController::class, 'products']);
    
    // Adjustments
    Route::apiResource('inventory-adjustments', InventoryAdjustmentApiController::class);

    // Products
    Route::apiResource('products', ProductApiController::class);
    Route::get('products/{product}/inventory', [ProductApiController::class, 'inventory']);
    Route::get('products/category/{category}', [ProductApiController::class, 'byCategory']);
    Route::get('products/supplier/{supplier}', [ProductApiController::class, 'bySupplier']);
    
    // Inventory
    Route::apiResource('inventories', InventoryApiController::class);
    Route::get('inventories/{inventory}/items', [InventoryApiController::class, 'items']);
    
    // Inventory Items
    Route::apiResource('inventory-items', InventoryItemApiController::class);
    Route::post('inventory-items/{item}/adjust', [InventoryItemApiController::class, 'adjustQuantity']);
    
    // Lots
    Route::apiResource('lots', LotApiController::class);
    Route::get('lots/expiring', [LotApiController::class, 'expiring']);
    Route::get('lots/expired', [LotApiController::class, 'expired']);
    
    // Cash Registers
    Route::apiResource('cash-registers', CashRegisterApiController::class);
    Route::post('cash-registers/{register}/open', [CashRegisterApiController::class, 'open']);
    Route::post('cash-registers/{register}/close', [CashRegisterApiController::class, 'close']);
    Route::get('cash-registers/{register}/status', [CashRegisterApiController::class, 'status']);
    
    // Sales
    Route::apiResource('sales', SaleApiController::class);
    Route::get('sales/{sale}/items', [SaleApiController::class, 'items']);
    Route::post('sales/{sale}/complete', [SaleApiController::class, 'complete']);
    Route::post('sales/{sale}/cancel', [SaleApiController::class, 'cancel']);
    Route::get('sales/register/{register}', [SaleApiController::class, 'byRegister']);
    Route::get('sales/user/{user}', [SaleApiController::class, 'byUser']);
    
    // Sale Items
    Route::apiResource('sale-items', SaleItemApiController::class);
    Route::post('sale-items/{item}/update-quantity', [SaleItemApiController::class, 'updateQuantity']);
    
     Route::get('/pos', [PosApiController::class, 'index']);

    // Alertes
    Route::get('/alerts', [AlertApiController::class, 'index']);
    // Si besoin d'autres méthodes, tu peux ajouter store, update, delete, etc.

    // Rapports
    Route::get('/reports', [ReportApiController::class, 'index']);

    // Purchase Orders
    Route::apiResource('purchase-orders', PurchaseOrderApiController::class);
    Route::get('purchase-orders/{order}/items', [PurchaseOrderApiController::class, 'items']);
    Route::post('purchase-orders/{order}/submit', [PurchaseOrderApiController::class, 'submit']);
    Route::post('purchase-orders/{order}/approve', [PurchaseOrderApiController::class, 'approve']);
    Route::post('purchase-orders/{order}/receive', [PurchaseOrderApiController::class, 'receive']);
    Route::post('purchase-orders/{order}/cancel', [PurchaseOrderApiController::class, 'cancel']);
    Route::get('purchase-orders/supplier/{supplier}', [PurchaseOrderApiController::class, 'bySupplier']);
    
    // Purchase Order Items
    Route::apiResource('purchase-order-items', PurchaseOrderItemApiController::class);
    Route::post('purchase-order-items/{item}/update-quantity', [PurchaseOrderApiController::class, 'updateQuantity']);
    
    // Routes de reporting et statistiques
    Route::prefix('reports')->group(function () {
        Route::get('sales/daily', [SaleApiController::class, 'dailyReport']);
        Route::get('sales/monthly', [SaleApiController::class, 'monthlyReport']);
        Route::get('inventory/low-stock', [InventoryItemApiController::class, 'lowStock']);
        Route::get('inventory/valuation', [InventoryItemApiController::class, 'valuation']);
        Route::get('products/best-sellers', [ProductApiController::class, 'bestSellers']);
    });
    
    // Routes de recherche
    Route::prefix('search')->group(function () {
        Route::get('products', [ProductApiController::class, 'search']);
        Route::get('sales', [SaleApiController::class, 'search']);
        Route::get('purchase-orders', [PurchaseOrderApiController::class, 'search']);
        Route::get('suppliers', [SupplierApiController::class, 'search']);
    });

    // Promotions
    Route::apiResource('promotions', PromotionApiController::class);
    Route::post('promotions/{promotion}/activate', [PromotionApiController::class, 'activate']);
    Route::post('promotions/{promotion}/deactivate', [PromotionApiController::class, 'deactivate']);
    Route::get('promotions-active', [PromotionApiController::class, 'active']);


    // Returns
    Route::get('returns/{id}', [ProductReturnApiController::class, 'show']);
    Route::apiResource('returns', ProductReturnApiController::class);
    Route::get('returns/{return}/items', [ProductReturnApiController::class, 'items']);
    Route::post('returns/{return}/process', [ProductReturnApiController::class, 'processReturn']);
    Route::post('returns/{return}/cancel', [ProductReturnApiController::class, 'cancelReturn']);
    Route::get('returns/user/{user}', [ProductReturnApiController::class, 'byUser']);
    Route::get('returns/status/{status}', [ProductReturnApiController::class, 'byStatus']);
    Route::apiResource('return-items', ReturnItemApiController::class);
    Route::get('return-items/{item}/product', [ReturnItemApiController::class, 'product']);
    Route::get('return-items/{item}/product-return', [ReturnItemApiController::class, 'productReturn']);
    Route::post('return-items/{item}/update-quantity', [ReturnItemApiController::class, 'updateQuantity']);
    Route::post('return-items/{item}/update-refund', [ReturnItemApiController::class, 'updateRefundAmount']);

    // Settings
    Route::get('settings', [SystemSettingApiController::class, 'index']);
    Route::put('settings', [SystemSettingApiController::class, 'update']);
    // CSRF Token pour Sanctum (SPA)

});

// Routes publiques (si nécessaire)
Route::get('categories/public', [CategoryApiController::class, 'publicIndex']);
Route::get('products/public', [ProductApiController::class, 'publicIndex']);

// Réinitialisation du mot de passe (routes publiques)
Route::post('/auth/forgot-password', [AuthApiController::class, 'forgotPassword']);
Route::post('/auth/reset-password', [AuthApiController::class, 'resetPassword']);

// Route fallback 404
Route::fallback(function () {
    return response()->json(['message' => 'Route non trouvée.'], 404);
});