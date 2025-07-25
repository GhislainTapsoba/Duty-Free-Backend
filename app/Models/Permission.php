<?php

namespace App\Models;

use Spatie\Permission\Models\Permission as SpatiePermission;

class Permission extends SpatiePermission
{
    protected $fillable = [
        'name',
        'guard_name',
        'description', // si tu as ce champ dans ta table permissions
    ];

    protected $casts = [
        'description' => 'string',
    ];
}
