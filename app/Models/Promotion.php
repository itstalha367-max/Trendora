<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Promotion extends Model { protected $fillable=['name','code','type','value','minimum_order','maximum_discount','usage_limit','usage_count','rules','starts_at','ends_at','status']; protected $casts=['value'=>'decimal:2','minimum_order'=>'decimal:2','maximum_discount'=>'decimal:2','rules'=>'array','starts_at'=>'datetime','ends_at'=>'datetime','status'=>'boolean']; public function getIsLiveAttribute(){return $this->status && (!$this->starts_at||$this->starts_at->isPast()) && (!$this->ends_at||$this->ends_at->isFuture());} }
