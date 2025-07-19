<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\ReturnItem;

class ProductReturn extends Model
{
    protected $table = 'product_returns';

    protected $fillable = [
        'returned_by_user_id',
        'status',
        'total_refund_amount',
        'reason',
    ];

    public function returnedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'returned_by_user_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(ReturnItem::class);
    }
}
