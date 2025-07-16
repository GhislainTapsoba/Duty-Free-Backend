<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


/**
 * CustomerCard Model
 */
class CustomerCard extends Model
{
    use HasFactory;

    protected $fillable = [
        'card_number',
        'holder_name',
        'email',
        'phone',
        'points_balance',
        'is_active',
        'issued_date',
        'expiry_date'
    ];

    protected $casts = [
        'points_balance' => 'integer',
        'is_active' => 'boolean',
        'issued_date' => 'date',
        'expiry_date' => 'date',
    ];

    // Relations
    public function sales()
    {
        return $this->hasMany(Sale::class);
    }
}