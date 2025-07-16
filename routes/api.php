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
    CashRegisterApiController,
    SaleApiController,
    SaleItemApiController,
    PurchaseOrderApiController,
    PurchaseOrderItemApiController,
    PromotionApiController,
    PasswordResetApiController
};

// Routes d'authentification
Route::prefix('auth')->group(function () {
    Route::post('register', [AuthApiController::class, 'register']);
    Route::post('login', [AuthApiController::class, 'login']);
    // Change ici le middleware auth:sanctum en auth:api
    Route::post('logout', [AuthApiController::class, 'logout'])->middleware('auth:api');
    Route::post('refresh', [AuthApiController::class, 'refresh'])->middleware('auth:api');
    Route::get('profile', [AuthApiController::class, 'profile'])->middleware('auth:api');
});

// Routes protégées par authentification token
Route::middleware('auth:api')->group(function () {
    
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

});

// Routes publiques (si nécessaire)
Route::get('categories/public', [CategoryApiController::class, 'publicIndex']);
Route::get('products/public', [ProductApiController::class, 'publicIndex']);

// Route fallback 404 (optionnel)
Route::fallback(function () {
    return response()->json(['message' => 'Route non trouvée.'], 404);
});

// Réinitialisation du mot de passe
Route::post('/auth/forgot-password', [AuthApiController::class, 'forgotPassword']);
Route::post('/auth/reset-password', [AuthApiController::class, 'resetPassword']);

// Routes pour la réinitialisation de mot de passe
Route::post('/auth/forgot-password', [PasswordResetApiController::class, 'forgot']);
Route::post('/auth/reset-password', [PasswordResetApiController::class, 'reset']);