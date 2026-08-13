<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class ShippingZone extends Model { use HasFactory; protected $fillable=['name','countries','states','status']; protected $casts=['countries'=>'array','states'=>'array','status'=>'boolean']; public function methods(){return $this->hasMany(ShippingMethod::class);} }
