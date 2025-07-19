<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


/**
 * Product Model
 */
class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'sku',
        'barcode',
        'name',
        'name_en',
        'commercial_name',
        'description',
        'description_en',
        'images',
        'category_id',
        'supplier_id',
        'purchase_price',
        'selling_price_xof',
        'selling_price_eur',
        'selling_price_usd',
        'stock_quantity',
        'minimum_stock',
        'maximum_stock',
        'unit',
        'tax_rate',
        'tax_included',
        'is_active',
        'is_trackable',
        'storage_location',
        'expiry_date'
    ];

    protected $casts = [
        'images' => 'array',
        'purchase_price' => 'decimal:2',
        'selling_price_xof' => 'decimal:2',
        'selling_price_eur' => 'decimal:2',
        'selling_price_usd' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'tax_included' => 'boolean',
        'is_active' => 'boolean',
        'is_trackable' => 'boolean',
        'expiry_date' => 'date',
    ];

    // Relations
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function purchaseOrderItems()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function inventoryItems()
    {
        return $this->hasMany(InventoryItem::class);
    }

    public function lots()
    {
        return $this->hasMany(Lot::class);
    }
}
