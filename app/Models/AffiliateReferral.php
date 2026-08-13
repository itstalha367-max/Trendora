<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class AffiliateReferral extends Model { protected $fillable=['affiliate_id','order_id','order_amount','commission_amount','status','paid_at']; protected $casts=['order_amount'=>'decimal:2','commission_amount'=>'decimal:2','paid_at'=>'datetime']; public function affiliate(){return $this->belongsTo(Affiliate::class);} public function order(){return $this->belongsTo(Order::class);} }
