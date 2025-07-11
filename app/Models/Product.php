<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
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
        'expiry_date',
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

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeLowStock($query)
    {
        return $query->whereColumn('stock_quantity', '<=', 'minimum_stock');
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    // Accessors
    public function getFormattedPriceAttribute()
    {
        return number_format($this->selling_price_xof, 0, ',', ' ') . ' FCFA';
    }

    public function getIsLowStockAttribute()
    {
        return $this->stock_quantity <= $this->minimum_stock;
    }

    public function getSellingPriceAttribute()
    {
        return $this->selling_price_xof;
    }

    // Mutators
    public function setCodeAttribute($value)
    {
        $this->attributes['code'] = strtoupper($value);
    }

    // Methods
    public function getSellingPriceByCurrency($currency = 'XOF')
    {
        return match($currency) {
            'EUR' => $this->selling_price_eur,
            'USD' => $this->selling_price_usd,
            default => $this->selling_price_xof,
        };
    }

    public function updateStock($quantity, $type = 'out')
    {
        if ($type === 'out') {
            $this->decrement('stock_quantity', $quantity);
        } else {
            $this->increment('stock_quantity', $quantity);
        }
    }
}