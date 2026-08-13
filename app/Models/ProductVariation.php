<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariation extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id', 'sku', 'attribute_name', 'attribute_value',
        'price', 'compare_price', 'stock_quantity', 'image',
        'is_default', 'status'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'compare_price' => 'decimal:2',
        'is_default' => 'boolean',
        'status' => 'boolean',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getImageUrlAttribute()
    {
        if ($this->image) {
            return asset('storage/' . $this->image);
        }
        return asset('images/no-image.png');
    }

    public function isInStock()
    {
        return $this->stock_quantity > 0;
    }

    public function getFullNameAttribute()
    {
        return $this->attribute_name . ': ' . $this->attribute_value;
    }
}