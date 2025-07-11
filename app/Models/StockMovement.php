<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// StockMovement Model
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
        'movement_date',
    ];

    protected $casts = [
        'unit_cost' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'movement_date' => 'datetime',
    ];

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

    public function generateReference()
    {
        $date = now()->format('Ymd');
        $typeCode = match($this->type) {
            'in' => 'IN',
            'out' => 'OUT',
            'adjustment' => 'ADJ',
            'transfer' => 'TRF',
            'waste' => 'WST',
            default => 'MVT',
        };
        
        $lastMovement = static::whereDate('created_at', today())->latest()->first();
        $sequence = $lastMovement ? intval(substr($lastMovement->reference, -4)) + 1 : 1;
        
        return $typeCode . $date . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByProduct($query, $productId)
    {
        return $query->where('product_id', $productId);
    }
}