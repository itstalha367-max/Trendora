<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Inventory extends Model { use HasFactory; protected $fillable=['warehouse_id','product_id','product_variation_id','sku','quantity','reserved_quantity','reorder_level','bin_location']; public function warehouse(){return $this->belongsTo(Warehouse::class);} public function product(){return $this->belongsTo(Product::class);} public function variation(){return $this->belongsTo(ProductVariation::class,'product_variation_id');} public function movements(){return $this->hasMany(StockMovement::class);} public function getAvailableQuantityAttribute(){return max(0,$this->quantity-$this->reserved_quantity);} }
