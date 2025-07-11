<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'notes',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'is_synced' => 'boolean',
        'sale_date' => 'datetime',
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

    // Scopes
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('sale_date', today());
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('sale_date', now()->month)
                    ->whereYear('sale_date', now()->year);
    }

    public function scopeByCashier($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Accessors
    public function getFormattedTotalAttribute()
    {
        return number_format($this->total_amount, 0, ',', ' ') . ' ' . $this->currency;
    }

    public function getItemsCountAttribute()
    {
        return $this->saleItems->sum('quantity');
    }

    // Methods
    public function generateSaleNumber()
    {
        $date = now()->format('Ymd');
        $lastSale = static::whereDate('created_at', today())->latest()->first();
        $sequence = $lastSale ? intval(substr($lastSale->sale_number, -4)) + 1 : 1;
        
        return 'VT' . $date . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    public function generateReceiptNumber()
    {
        $date = now()->format('Ymd');
        $lastReceipt = static::whereDate('created_at', today())->latest()->first();
        $sequence = $lastReceipt ? intval(substr($lastReceipt->receipt_number, -4)) + 1 : 1;
        
        return 'RCT' . $date . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    public function calculateTotals()
    {
        $this->subtotal = $this->saleItems->sum(function ($item) {
            return $item->quantity * $item->unit_price;
        });

        $this->tax_amount = $this->saleItems->sum('tax_amount');
        $this->total_amount = $this->subtotal + $this->tax_amount - $this->discount_amount;
        
        $this->save();
    }
}