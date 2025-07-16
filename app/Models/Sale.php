<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


/**
 * Sale Model
 */
class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_number',
        'receipt_number',
        'cash_register_id',
        'user_id',
        'customer_card_id',
        'subtotal',
        'tax_amount',
        'discount_amount',
        'total_amount',
        'currency',
        'customer_name',
        'flight_number',
        'destination',
        'airline',
        'status',
        'is_synced',
        'sale_date',
        'notes'
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'is_synced' => 'boolean',
        'sale_date' => 'timestamp',
    ];

    // Relations
    public function cashRegister()
    {
        return $this->belongsTo(CashRegister::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function customerCard()
    {
        return $this->belongsTo(CustomerCard::class);
    }

    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
