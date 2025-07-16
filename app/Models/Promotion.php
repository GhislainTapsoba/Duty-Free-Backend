<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


/**
 * Promotion Model
 */
class Promotion extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'type',
        'discount_value',
        'minimum_amount',
        'minimum_quantity',
        'start_date',
        'end_date',
        'start_time',
        'end_time',
        'applicable_days',
        'applicable_categories',
        'applicable_products',
        'is_active',
        'usage_limit',
        'usage_count'
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'minimum_amount' => 'decimal:2',
        'minimum_quantity' => 'integer',
        'start_date' => 'date',
        'end_date' => 'date',
        'start_time' => 'time',
        'end_time' => 'time',
        'applicable_days' => 'array',
        'applicable_categories' => 'array',
        'applicable_products' => 'array',
        'is_active' => 'boolean',
        'usage_limit' => 'integer',
        'usage_count' => 'integer',
    ];

    // Relations
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'promotion_categories');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'promotion_products');
    }

    // Helper methods
    public function isActive()
    {
        if (!$this->is_active) {
            return false;
        }

        $now = now();
        $today = $now->toDateString();
        $currentTime = $now->format('H:i:s');

        // Check date range
        if ($today < $this->start_date || $today > $this->end_date) {
            return false;
        }

        // Check time range if set
        if ($this->start_time && $this->end_time) {
            if ($currentTime < $this->start_time || $currentTime > $this->end_time) {
                return false;
            }
        }

        // Check applicable days
        if ($this->applicable_days) {
            $currentDay = strtolower($now->format('l'));
            if (!in_array($currentDay, $this->applicable_days)) {
                return false;
            }
        }

        // Check usage limit
        if ($this->usage_limit && $this->usage_count >= $this->usage_limit) {
            return false;
        }

        return true;
    }

    public function canBeAppliedToProduct($productId)
    {
        if (!$this->isActive()) {
            return false;
        }

        // If specific products are set, check if product is in the list
        if ($this->applicable_products && !empty($this->applicable_products)) {
            return in_array($productId, $this->applicable_products);
        }

        // If specific categories are set, check if product's category is in the list
        if ($this->applicable_categories && !empty($this->applicable_categories)) {
            $product = Product::find($productId);
            return $product && in_array($product->category_id, $this->applicable_categories);
        }

        // If no specific products or categories, promotion applies to all
        return true;
    }

    public function canBeAppliedToCart($cartSubtotal, $cartQuantity)
    {
        if (!$this->isActive()) {
            return false;
        }

        // Check minimum amount
        if ($cartSubtotal < $this->minimum_amount) {
            return false;
        }

        // Check minimum quantity
        if ($cartQuantity < $this->minimum_quantity) {
            return false;
        }

        return true;
    }

    public function calculateDiscount($amount, $quantity = 1)
    {
        if (!$this->isActive()) {
            return 0;
        }

        switch ($this->type) {
            case 'percentage':
                return $amount * ($this->discount_value / 100);
            
            case 'fixed_amount':
                return min($this->discount_value, $amount);
            
            case 'buy_x_get_y':
                // Logic for buy X get Y promotions
                // This would need more complex implementation based on your business rules
                return 0;
            
            case 'menu':
                // Logic for menu promotions
                // This would need more complex implementation based on your business rules
                return 0;
            
            default:
                return 0;
        }
    }

    public function incrementUsage()
    {
        $this->increment('usage_count');
    }

    public function decrementUsage()
    {
        if ($this->usage_count > 0) {
            $this->decrement('usage_count');
        }
    }
}