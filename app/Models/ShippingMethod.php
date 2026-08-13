<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class ShippingMethod extends Model { use HasFactory; protected $fillable=['shipping_zone_id','name','type','cost','free_over','min_days','max_days','status']; protected $casts=['cost'=>'decimal:2','free_over'=>'decimal:2','status'=>'boolean']; public function zone(){return $this->belongsTo(ShippingZone::class,'shipping_zone_id');} }
