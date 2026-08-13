<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Supplier extends Model { use HasFactory; protected $fillable=['name','contact_name','email','phone','website','address','city','country','tax_id','lead_time_days','status','notes']; public function products(){return $this->belongsToMany(Product::class)->withPivot(['supplier_sku','cost_price','minimum_order_quantity','preferred']);} }
