<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role', 'admin_role_id', 'store_credit', 'phone', 
        'address', 'city', 'state', 'zip', 'country',
        'google2fa_secret'
    ];

    protected $hidden = [
        'password', 'remember_token', 'google2fa_secret'
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'store_credit' => 'decimal:2',
        ];
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function cart()
    {
        return $this->hasOne(Cart::class);
    }

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    public function returnRequests()
    {
        return $this->hasMany(ReturnRequest::class);
    }

    public function supportTickets()
    {
        return $this->hasMany(SupportTicket::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }


    public function segments()
    {
        return $this->belongsToMany(CustomerSegment::class, 'customer_segment_user')->withTimestamps();
    }

    public function walletTransactions()
    {
        return $this->hasMany(WalletTransaction::class);
    }

    public function adminRole()
    {
        return $this->belongsTo(AdminRole::class);
    }

    public function hasAdminPermission(string $key): bool
    {
        if ($this->role !== 'admin') return false;
        if (!$this->admin_role_id) return true;
        return $this->adminRole?->permissions()->where('key', $key)->exists() ?? false;
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isVendor()
    {
        return $this->role === 'vendor';
    }

    // 2FA Methods
    public function getGoogle2faSecretAttribute($value)
    {
        return $value ? decrypt($value) : null;
    }

    public function setGoogle2faSecretAttribute($value)
    {
        $this->attributes['google2fa_secret'] = $value ? encrypt($value) : null;
    }
}