<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'cart_id', 'product_id', 'product_variation_id',
        'quantity', 'price', 'total'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function variation()
    {
        return $this->belongsTo(ProductVariation::class, 'product_variation_id');
    }

    public function getSubtotalAttribute()
    {
        return $this->price * $this->quantity;
    }

    public function getProductNameAttribute()
    {
        return $this->product->name ?? 'Product';
    }

    public function getVariationNameAttribute()
    {
        if ($this->variation) {
            return $this->variation->attribute_name . ': ' . $this->variation->attribute_value;
        }
        return null;
    }

    public function getImageAttribute()
    {
        if ($this->variation && $this->variation->image) {
            return $this->variation->image;
        }
        return $this->product->thumbnail ?? null;
    }
}