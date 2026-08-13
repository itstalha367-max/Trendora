<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'session_id', 'total'
    ];

    protected $casts = [
        'total' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(CartItem::class);
    }

    public function getItemsCountAttribute()
    {
        return $this->items()->sum('quantity');
    }

    public function getSubtotalAttribute()
    {
        return $this->items()->sum('total');
    }

    public function updateTotal()
    {
        $this->total = $this->subtotal;
        $this->save();
    }

    public function clear()
    {
        $this->items()->delete();
        $this->total = 0;
        $this->save();
    }
}