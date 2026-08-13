<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Banner extends Model { use HasFactory; protected $fillable=['title','subtitle','image','button_text','button_url','placement','sort_order','status','starts_at','ends_at']; protected $casts=['status'=>'boolean','starts_at'=>'datetime','ends_at'=>'datetime']; }
