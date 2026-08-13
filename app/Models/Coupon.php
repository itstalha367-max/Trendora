<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code', 'name', 'type', 'value', 'min_order',
        'max_discount', 'usage_limit', 'used_count',
        'per_user_limit', 'start_date', 'end_date', 'status'
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'min_order' => 'decimal:2',
        'max_discount' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
        'status' => 'boolean',
    ];

    // Check if coupon is valid
    public function isValid()
    {
        $now = now();
        
        // Check status
        if (!$this->status) {
            return false;
        }
        
        // Check date range
        if ($this->start_date && $now->lt($this->start_date)) {
            return false;
        }
        
        if ($this->end_date && $now->gt($this->end_date)) {
            return false;
        }
        
        // Check usage limit
        if ($this->usage_limit && $this->used_count >= $this->usage_limit) {
            return false;
        }
        
        return true;
    }

    // Calculate discount
    public function calculateDiscount($subtotal)
    {
        if (!$this->isValid()) {
            return 0;
        }
        
        // Check min order
        if ($this->min_order > 0 && $subtotal < $this->min_order) {
            return 0;
        }
        
        if ($this->type === 'fixed') {
            return min($this->value, $subtotal);
        }
        
        // Percentage
        $discount = ($this->value / 100) * $subtotal;
        
        // Max discount limit
        if ($this->max_discount && $discount > $this->max_discount) {
            $discount = $this->max_discount;
        }
        
        return $discount;
    }

    // Increment used count
    public function incrementUsed()
    {
        $this->increment('used_count');
    }
}