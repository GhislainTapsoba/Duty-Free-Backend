<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


/**
 * ExternalData Model
 */
class ExternalData extends Model
{
    use HasFactory;

    protected $fillable = [
        'data_type',
        'period_date',
        'data',
        'source',
        'user_id'
    ];

    protected $casts = [
        'period_date' => 'date',
        'data' => 'array',
    ];

    // Relations
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
