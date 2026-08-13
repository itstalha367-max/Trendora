<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class StockMovement extends Model { use HasFactory; protected $fillable=['inventory_id','user_id','type','quantity','before_quantity','after_quantity','reference','note']; public function inventory(){return $this->belongsTo(Inventory::class);} public function user(){return $this->belongsTo(User::class);} }
