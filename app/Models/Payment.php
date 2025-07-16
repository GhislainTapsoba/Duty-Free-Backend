<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


/**
 * Payment Model
 */
class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_id',
        'payment_method',
        'amount',
        'currency',
        'exchange_rate',
        'amount_in_base_currency',
        'card_type',
        'card_last_four',
        'transaction_reference',
        'authorization_code',
        'mobile_money_provider',
        'mobile_money_number',
        'terminal_id',
        'merchant_id',
        'status',
        'notes',
        'payment_date'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'exchange_rate' => 'decimal:4',
        'amount_in_base_currency' => 'decimal:2',
        'payment_date' => 'timestamp',
    ];

    // Relations
    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }
}