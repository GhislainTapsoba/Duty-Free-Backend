<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;


/**
 * User Model
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'role',
        'employee_code',
        'is_active',
        'last_login_at',
        'permissions'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'timestamp',
        'last_login_at' => 'timestamp',
        'is_active' => 'boolean',
        'permissions' => 'array',
    ];

    // Relations
    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    public function inventories()
    {
        return $this->hasMany(Inventory::class);
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    public function openedCashRegisters()
    {
        return $this->hasMany(CashRegister::class, 'opened_by');
    }

    public function closedCashRegisters()
    {
        return $this->hasMany(CashRegister::class, 'closed_by');
    }

    public function externalData()
    {
        return $this->hasMany(ExternalData::class);
    }
}
