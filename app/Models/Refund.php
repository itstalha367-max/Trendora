<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Refund extends Model {
    use HasFactory;
    protected $fillable=['order_id','return_request_id','processed_by','refund_number','amount','method','status','gateway_reference','reason','processed_at'];
    protected $casts=['amount'=>'decimal:2','processed_at'=>'datetime'];
    public function order(){return $this->belongsTo(Order::class);}
    public function returnRequest(){return $this->belongsTo(ReturnRequest::class);}
    public function processor(){return $this->belongsTo(User::class,'processed_by');}
}
