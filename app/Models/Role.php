<?php

namespace App\Models;

use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    protected $fillable = [
        'name',
        'guard_name',
        'description',  // si tu as ce champ dans ta table roles
    ];

    protected $casts = [
        'description' => 'string',
    ];
}
