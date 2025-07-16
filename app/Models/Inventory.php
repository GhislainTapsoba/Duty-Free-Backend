<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


/**
 * Inventory Model
 */
class Inventory extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference',
        'name',
        'inventory_date',
        'user_id',
        'status',
        'notes'
    ];

    protected $casts = [
        'inventory_date' => 'date',
    ];

    // Relations
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function inventoryItems()
    {
        return $this->hasMany(InventoryItem::class);
    }
}
