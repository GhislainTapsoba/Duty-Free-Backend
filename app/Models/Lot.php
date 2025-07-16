<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


/**
 * Lot Model
 */
class Lot extends Model
{
    use HasFactory;

    protected $fillable = [
        'lot_number',
        'product_id',
        'expiry_date',
        'quantity',
        'unit_cost',
        'supplier_id',
        'purchase_order_id',
        'is_active'
    ];

    protected $casts = [
        'expiry_date' => 'date',
        'quantity' => 'integer',
        'unit_cost' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // Relations
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }
}
