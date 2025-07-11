<?php

// routes/api.php

use App\Http\Controllers\Api\{
    AuthController,
    UserController,
    CategoryController,
    SupplierController,
    ProductController,
    InventoryController,
    InventoryItemController,
    LotController,
    CashRegisterController,
    SaleController,
    SaleItemController,
    PurchaseOrderController,
    PurchaseOrderItemController
};

// Routes d'authentification
Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
    Route::post('refresh', [AuthController::class, 'refresh'])->middleware('auth:sanctum');
    Route::get('profile', [AuthController::class, 'profile'])->middleware('auth:sanctum');
});

// Routes protégées par authentification
Route::middleware('auth:sanctum')->group(function () {
    
    // Users
    Route::apiResource('users', UserController::class);
    
    // Categories
    Route::apiResource('categories', CategoryController::class);
    
    // Suppliers
    Route::apiResource('suppliers', SupplierController::class);
    Route::get('suppliers/{supplier}/products', [SupplierController::class, 'products']);
    
    // Products
    Route::apiResource('products', ProductController::class);
    Route::get('products/{product}/inventory', [ProductController::class, 'inventory']);
    Route::get('products/category/{category}', [ProductController::class, 'byCategory']);
    Route::get('products/supplier/{supplier}', [ProductController::class, 'bySupplier']);
    
    // Inventory
    Route::apiResource('inventories', InventoryController::class);
    Route::get('inventories/{inventory}/items', [InventoryController::class, 'items']);
    
    // Inventory Items
    Route::apiResource('inventory-items', InventoryItemController::class);
    Route::post('inventory-items/{item}/adjust', [InventoryItemController::class, 'adjustQuantity']);
    
    // Lots
    Route::apiResource('lots', LotController::class);
    Route::get('lots/expiring', [LotController::class, 'expiring']);
    Route::get('lots/expired', [LotController::class, 'expired']);
    
    // Cash Registers
    Route::apiResource('cash-registers', CashRegisterController::class);
    Route::post('cash-registers/{register}/open', [CashRegisterController::class, 'open']);
    Route::post('cash-registers/{register}/close', [CashRegisterController::class, 'close']);
    Route::get('cash-registers/{register}/status', [CashRegisterController::class, 'status']);
    
    // Sales
    Route::apiResource('sales', SaleController::class);
    Route::get('sales/{sale}/items', [SaleController::class, 'items']);
    Route::post('sales/{sale}/complete', [SaleController::class, 'complete']);
    Route::post('sales/{sale}/cancel', [SaleController::class, 'cancel']);
    Route::get('sales/register/{register}', [SaleController::class, 'byRegister']);
    Route::get('sales/user/{user}', [SaleController::class, 'byUser']);
    
    // Sale Items
    Route::apiResource('sale-items', SaleItemController::class);
    Route::post('sale-items/{item}/update-quantity', [SaleItemController::class, 'updateQuantity']);
    
    // Purchase Orders
    Route::apiResource('purchase-orders', PurchaseOrderController::class);
    Route::get('purchase-orders/{order}/items', [PurchaseOrderController::class, 'items']);
    Route::post('purchase-orders/{order}/submit', [PurchaseOrderController::class, 'submit']);
    Route::post('purchase-orders/{order}/approve', [PurchaseOrderController::class, 'approve']);
    Route::post('purchase-orders/{order}/receive', [PurchaseOrderController::class, 'receive']);
    Route::post('purchase-orders/{order}/cancel', [PurchaseOrderController::class, 'cancel']);
    Route::get('purchase-orders/supplier/{supplier}', [PurchaseOrderController::class, 'bySupplier']);
    
    // Purchase Order Items
    Route::apiResource('purchase-order-items', PurchaseOrderItemController::class);
    Route::post('purchase-order-items/{item}/update-quantity', [PurchaseOrderItemController::class, 'updateQuantity']);
    
    // Routes de reporting et statistiques
    Route::prefix('reports')->group(function () {
        Route::get('sales/daily', [SaleController::class, 'dailyReport']);
        Route::get('sales/monthly', [SaleController::class, 'monthlyReport']);
        Route::get('inventory/low-stock', [InventoryItemController::class, 'lowStock']);
        Route::get('inventory/valuation', [InventoryItemController::class, 'valuation']);
        Route::get('products/best-sellers', [ProductController::class, 'bestSellers']);
    });
    
    // Routes de recherche
    Route::prefix('search')->group(function () {
        Route::get('products', [ProductController::class, 'search']);
        Route::get('sales', [SaleController::class, 'search']);
        Route::get('purchase-orders', [PurchaseOrderController::class, 'search']);
        Route::get('suppliers', [SupplierController::class, 'search']);
    });
});

// Routes publiques (si nécessaire)
Route::get('categories/public', [CategoryController::class, 'publicIndex']);
Route::get('products/public', [ProductController::class, 'publicIndex']);