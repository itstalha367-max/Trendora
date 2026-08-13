<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Collection extends Model { use HasFactory; protected $fillable=['name','slug','description','image','featured','status','starts_at','ends_at']; protected $casts=['featured'=>'boolean','status'=>'boolean','starts_at'=>'datetime','ends_at'=>'datetime']; public function products(){return $this->belongsToMany(Product::class)->withPivot('sort_order');} }
