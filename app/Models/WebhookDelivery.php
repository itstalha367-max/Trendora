<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class WebhookDelivery extends Model { protected $fillable=['webhook_id','event','payload','response_code','response_body','status','attempted_at','attempt_count','next_retry_at','delivered_at']; protected $casts=['payload'=>'array','attempted_at'=>'datetime','next_retry_at'=>'datetime','delivered_at'=>'datetime']; public function webhook(){return $this->belongsTo(Webhook::class);} }
