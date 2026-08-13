<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Warehouse extends Model { use HasFactory; protected $fillable=['name','code','email','phone','address','city','state','country','postal_code','is_default','status']; protected $casts=['is_default'=>'boolean','status'=>'boolean']; public function inventories(){return $this->hasMany(Inventory::class);} }
