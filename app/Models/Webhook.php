<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Webhook extends Model { protected $fillable=['name','url','encrypted_secret','events','status','last_triggered_at']; protected $casts=['events'=>'array','status'=>'boolean','last_triggered_at'=>'datetime']; public function deliveries(){return $this->hasMany(WebhookDelivery::class);} public function getSecretAttribute(){try{return $this->encrypted_secret?decrypt($this->encrypted_secret):null;}catch(\Throwable $e){return null;}} public function setSecretAttribute($value){if($value)$this->attributes['encrypted_secret']=encrypt($value);} }
