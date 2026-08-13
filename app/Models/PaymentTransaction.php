<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class PaymentTransaction extends Model {
    use HasFactory;
    protected $fillable=['order_id','gateway','transaction_id','type','status','amount','currency','payload','note'];
    protected $casts=['amount'=>'decimal:2','payload'=>'array'];
    public function order(){return $this->belongsTo(Order::class);}
}
