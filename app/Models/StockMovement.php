<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// StockMovement Model
/**
 * StockMovement Model
 */
class StockMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference',
        'product_id',
        'lot_id',
        'type',
        'quantity',
        'previous_stock',
        'new_stock',
        'unit_cost',
        'total_cost',
        'reason',
        'notes',
        'user_id',
        'source_location',
        'destination_location',
        'movement_date'
    ];

    protected $casts = [
        'quantity' => 'integer',
        'previous_stock' => 'integer',
        'new_stock' => 'integer',
        'unit_cost' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'movement_date' => 'timestamp',
    ];

    // Relations
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function lot()
    {
        return $this->belongsTo(Lot::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
