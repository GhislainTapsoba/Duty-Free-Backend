<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


/**
 * InventoryItem Model
 */
class InventoryItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'inventory_id',
        'product_id',
        'system_quantity',
        'counted_quantity',
        'variance',
        'unit_cost',
        'variance_value',
        'notes'
    ];

    protected $casts = [
        'system_quantity' => 'integer',
        'counted_quantity' => 'integer',
        'variance' => 'integer',
        'unit_cost' => 'decimal:2',
        'variance_value' => 'decimal:2',
    ];

    // Relations
    public function inventory()
    {
        return $this->belongsTo(Inventory::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
