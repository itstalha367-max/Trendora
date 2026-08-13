<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AbandonedCart extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'session_id', 'email', 'name', 'items',
        'subtotal', 'total', 'coupon_code', 'last_activity_at',
        'reminder_sent_at', 'reminder_count', 'status'
    ];

    protected $casts = [
        'items' => 'array',
        'subtotal' => 'decimal:2',
        'total' => 'decimal:2',
        'last_activity_at' => 'datetime',
        'reminder_sent_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Check if cart is abandoned (no activity for 30 mins)
    public function isAbandoned()
    {
        if (!$this->last_activity_at) {
            return false;
        }
        return $this->last_activity_at->diffInMinutes(now()) > 30;
    }

    // Send reminder email
    public function sendReminder()
    {
        // Email logic here
        $this->reminder_count++;
        $this->reminder_sent_at = now();
        $this->save();
    }

    // Mark as recovered
    public function markAsRecovered()
    {
        $this->status = 'recovered';
        $this->save();
    }

    // Get abandoned carts older than specified hours
    public static function getAbandonedCarts($hours = 24)
    {
        return self::where('status', 'active')
            ->where('last_activity_at', '<', now()->subHours($hours))
            ->where('reminder_sent_at', '<', now()->subDays(1))
            ->get();
    }
}