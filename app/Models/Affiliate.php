<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Affiliate extends Model { protected $fillable=['user_id','name','email','code','commission_rate','status','payout_details','clicks','conversions','revenue']; protected $casts=['commission_rate'=>'decimal:2','payout_details'=>'array','revenue'=>'decimal:2']; public function user(){return $this->belongsTo(User::class);} public function referrals(){return $this->hasMany(AffiliateReferral::class);} public function payouts(){return $this->hasMany(Payout::class);} }
