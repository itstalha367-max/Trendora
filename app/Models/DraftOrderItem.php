<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class DraftOrderItem extends Model { protected $fillable=['draft_order_id','product_id','product_variation_id','title','sku','quantity','unit_price','line_total']; protected $casts=['unit_price'=>'decimal:2','line_total'=>'decimal:2']; public function draftOrder(){return $this->belongsTo(DraftOrder::class);} public function product(){return $this->belongsTo(Product::class);} public function variation(){return $this->belongsTo(ProductVariation::class,'product_variation_id');} }
