<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payout extends Model
{
    use HasFactory;
    protected $fillable = ['payout_number','affiliate_id','amount','currency','method','status','reference','metadata','note','requested_at','processed_at','processed_by'];
    protected $casts = ['amount'=>'decimal:2','metadata'=>'array','requested_at'=>'datetime','processed_at'=>'datetime'];
    public function affiliate(){ return $this->belongsTo(Affiliate::class); }
    public function processor(){ return $this->belongsTo(User::class,'processed_by'); }
}
