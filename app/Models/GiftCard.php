<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class GiftCard extends Model { protected $fillable=['code','initial_balance','current_balance','currency','status','purchaser_user_id','recipient_name','recipient_email','message','expires_at','created_by']; protected $casts=['initial_balance'=>'decimal:2','current_balance'=>'decimal:2','expires_at'=>'datetime']; public function transactions(){return $this->hasMany(GiftCardTransaction::class);} public function purchaser(){return $this->belongsTo(User::class,'purchaser_user_id');} public function creator(){return $this->belongsTo(User::class,'created_by');} }
