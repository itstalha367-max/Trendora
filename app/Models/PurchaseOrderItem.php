<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class PurchaseOrderItem extends Model { protected $fillable=['purchase_order_id','product_id','product_variation_id','quantity','received_quantity','cost','total']; protected $casts=['cost'=>'decimal:2','total'=>'decimal:2']; public function purchaseOrder(){return $this->belongsTo(PurchaseOrder::class);} public function product(){return $this->belongsTo(Product::class);} public function variation(){return $this->belongsTo(ProductVariation::class,'product_variation_id');} }
