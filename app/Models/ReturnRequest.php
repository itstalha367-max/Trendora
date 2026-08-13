<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReturnRequest extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'order_id', 'request_number', 'type', 'reason', 'details', 'requested_amount', 'status', 'admin_note'];
    protected $casts = ['requested_amount' => 'decimal:2'];

    public function user() { return $this->belongsTo(User::class); }
    public function order() { return $this->belongsTo(Order::class); }
    public function refunds() { return $this->hasMany(Refund::class); }
}
