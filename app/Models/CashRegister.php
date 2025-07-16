<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


/**
 * CashRegister Model
 */
class CashRegister extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'location',
        'ip_address',
        'printer_config',
        'scanner_config',
        'tpe_config',
        'opening_balance',
        'current_balance',
        'is_active',
        'last_sync_at',
        'is_open',
        'opened_by',
        'opened_at',
        'closed_by',
        'closed_at'
    ];

    protected $casts = [
        'printer_config' => 'array',
        'scanner_config' => 'array',
        'tpe_config' => 'array',
        'opening_balance' => 'decimal:2',
        'current_balance' => 'decimal:2',
        'is_active' => 'boolean',
        'is_open' => 'boolean',
        'last_sync_at' => 'timestamp',
        'opened_at' => 'timestamp',
        'closed_at' => 'timestamp',
    ];

    // Relations
    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function openedBy()
    {
        return $this->belongsTo(User::class, 'opened_by');
    }

    public function closedBy()
    {
        return $this->belongsTo(User::class, 'closed_by');
    }
}