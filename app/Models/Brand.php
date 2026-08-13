<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Brand extends Model { use HasFactory; protected $fillable=['name','slug','description','logo','website','featured','status','sort_order']; protected $casts=['featured'=>'boolean','status'=>'boolean']; public function products(){return $this->hasMany(Product::class);} }
