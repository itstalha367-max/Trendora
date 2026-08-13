<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class WalletTransaction extends Model { protected $fillable=['user_id','order_id','processed_by','type','amount','balance_after','note']; protected $casts=['amount'=>'decimal:2','balance_after'=>'decimal:2']; public function user(){return $this->belongsTo(User::class);} public function order(){return $this->belongsTo(Order::class);} public function processor(){return $this->belongsTo(User::class,'processed_by');} }
