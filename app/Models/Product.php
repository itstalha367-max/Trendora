<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id', 'brand_id', 'name', 'slug', 'description', 'price',
        'compare_price', 'stock_quantity', 'sku', 'images',
        'thumbnail', 'featured', 'status', 'views'
    ];

    protected $casts = [
        'images' => 'array', // 🔥 JSON store for multiple images
        'price' => 'decimal:2',
        'compare_price' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($product) {
            $product->slug = Str::slug($product->name);
        });
    }

    // 🔥 Get thumbnail URL
    public function getThumbnailUrlAttribute()
    {
        if ($this->thumbnail) {
            return asset('storage/' . $this->thumbnail);
        }
        return asset('images/no-image.png');
    }

    // 🔥 Get all images URLs
    public function getImageUrlsAttribute()
    {
        if ($this->images && is_array($this->images)) {
            return array_map(function ($image) {
                return asset('storage/' . $image);
            }, $this->images);
        }
        return [];
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function collections()
    {
        return $this->belongsToMany(Collection::class);
    }

    public function inventories()
    {
        return $this->hasMany(Inventory::class);
    }

    public function suppliers()
    {
        return $this->belongsToMany(Supplier::class)->withPivot(['supplier_sku','cost_price','minimum_order_quantity','preferred']);
    }
      public function variations()
    {
        return $this->hasMany(ProductVariation::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('featured', true);
    }

    public function scopeInStock($query)
    {
        return $query->where('stock_quantity', '>', 0);
    }

    public function isInStock()
    {
        return $this->stock_quantity > 0;
    }
    public function reviews()
{
    return $this->hasMany(Review::class);
}

public function questions()
{
    return $this->hasMany(ProductQuestion::class);
}

public function approvedReviews()
{
    return $this->hasMany(Review::class)->where('status', 'approved');
}

public function getAverageRatingAttribute()
{
    return $this->approvedReviews()->avg('rating') ?? 0;
}

public function getTotalReviewsAttribute()
{
    return $this->approvedReviews()->count();
}

public function getStarsAttribute()
{
    $rating = round($this->average_rating);
    return str_repeat('⭐', $rating) . str_repeat('☆', 5 - $rating);
}
}