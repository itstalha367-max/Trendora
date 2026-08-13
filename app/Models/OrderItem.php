<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id', 'product_id', 'product_variation_id', 'warehouse_id', 'product_name', 'product_sku',
        'quantity', 'price', 'total'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function variation() { return $this->belongsTo(ProductVariation::class, 'product_variation_id'); }
    public function warehouse() { return $this->belongsTo(Warehouse::class); }
}