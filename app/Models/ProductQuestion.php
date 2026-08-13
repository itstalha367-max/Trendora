<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class ProductQuestion extends Model { protected $fillable=['product_id','user_id','name','email','question','answer','answered_by','answered_at','status']; protected $casts=['answered_at'=>'datetime']; public function product(){return $this->belongsTo(Product::class);} public function user(){return $this->belongsTo(User::class);} public function answeredBy(){return $this->belongsTo(User::class,'answered_by');} }
