<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'order_number', 'subtotal', 'tax', 'tax_name', 'tax_rate', 'shipping_cost',
        'shipping_method_id', 'shipping_method_name', 'discount', 'refunded_amount', 'total', 'shipping_name', 'shipping_email',
        'shipping_phone', 'shipping_address', 'shipping_city',
        'shipping_state', 'shipping_zip', 'shipping_country',
        'notes', 'payment_status', 'order_status',
        'payment_gateway', 'transaction_id', 'payment_data',
        'tracking_number', 'shipped_at', 'delivered_at'
    ];

    protected $casts = [
        'payment_data' => 'array',
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'tax_rate' => 'decimal:4',
        'refunded_amount' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    // ============================================
    // 🔗 RELATIONSHIPS
    // ============================================

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function returnRequests()
    {
        return $this->hasMany(ReturnRequest::class);
    }

    public function shippingMethod() { return $this->belongsTo(ShippingMethod::class); }
    public function transactions() { return $this->hasMany(PaymentTransaction::class); }
    public function refunds() { return $this->hasMany(Refund::class); }
    public function statusHistory() { return $this->hasMany(OrderStatusHistory::class)->latest(); }

    // ============================================
    // 🎨 STATUS HELPERS
    // ============================================

    public function getStatusBadgeAttribute()
    {
        $colors = [
            'pending' => 'warning',
            'processing' => 'info',
            'shipped' => 'primary',
            'delivered' => 'success',
            'cancelled' => 'danger',
        ];
        return $colors[$this->order_status] ?? 'secondary';
    }

    public function getStatusIconAttribute()
    {
        $icons = [
            'pending' => 'fa-clock',
            'processing' => 'fa-spinner',
            'shipped' => 'fa-truck',
            'delivered' => 'fa-check-circle',
            'cancelled' => 'fa-times-circle',
        ];
        return $icons[$this->order_status] ?? 'fa-circle';
    }

    public function getStatusTextAttribute()
    {
        $statuses = [
            'pending' => 'Order Placed',
            'processing' => 'Processing',
            'shipped' => 'Shipped',
            'delivered' => 'Delivered',
            'cancelled' => 'Cancelled',
        ];
        return $statuses[$this->order_status] ?? ucfirst($this->order_status);
    }

    public function getStatusProgressAttribute()
    {
        $progress = [
            'pending' => 20,
            'processing' => 40,
            'shipped' => 70,
            'delivered' => 100,
            'cancelled' => 0,
        ];
        return $progress[$this->order_status] ?? 0;
    }

    // ============================================
    // ✅ STATUS CHECKERS
    // ============================================

    public function isPending()
    {
        return $this->order_status === 'pending';
    }

    public function isProcessing()
    {
        return $this->order_status === 'processing';
    }

    public function isShipped()
    {
        return $this->order_status === 'shipped';
    }

    public function isDelivered()
    {
        return $this->order_status === 'delivered';
    }

    public function isCancelled()
    {
        return $this->order_status === 'cancelled';
    }

    public function isPaid()
    {
        return $this->payment_status === 'paid';
    }

    public function isPendingPayment()
    {
        return $this->payment_status === 'pending';
    }

    // ============================================
    // 📦 ORDER NUMBER GENERATOR
    // ============================================

    public static function generateOrderNumber()
    {
        return (string) \App\Models\Setting::get('order_prefix', 'ORD-') . strtoupper(uniqid());
    }

    // ============================================
    // 💰 TOTAL CALCULATIONS
    // ============================================

    public function getSubtotalAttribute($value)
    {
        return (float) $value;
    }

    public function getTotalAttribute($value)
    {
        return (float) $value;
    }

    public function getDiscountAttribute($value)
    {
        return (float) $value;
    }

    // ============================================
    // 🔍 SCOPES
    // ============================================

    public function scopePending($query)
    {
        return $query->where('order_status', 'pending');
    }

    public function scopeProcessing($query)
    {
        return $query->where('order_status', 'processing');
    }

    public function scopeShipped($query)
    {
        return $query->where('order_status', 'shipped');
    }

    public function scopeDelivered($query)
    {
        return $query->where('order_status', 'delivered');
    }

    public function scopeCancelled($query)
    {
        return $query->where('order_status', 'cancelled');
    }

    public function scopePaid($query)
    {
        return $query->where('payment_status', 'paid');
    }

    public function scopePendingPayment($query)
    {
        return $query->where('payment_status', 'pending');
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeDateRange($query, $start, $end)
    {
        return $query->whereBetween('created_at', [$start, $end]);
    }
}