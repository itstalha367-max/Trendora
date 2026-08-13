<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class ApiKey extends Model { protected $fillable=['name','key_prefix','key_hash','abilities','last_used_at','expires_at','revoked_at','created_by']; protected $casts=['abilities'=>'array','last_used_at'=>'datetime','expires_at'=>'datetime','revoked_at'=>'datetime']; public function creator(){return $this->belongsTo(User::class,'created_by');} public function getIsActiveAttribute(){return !$this->revoked_at && (!$this->expires_at||$this->expires_at->isFuture());} }
