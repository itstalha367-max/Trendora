<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = ['key', 'value', 'group', 'type', 'label', 'description', 'options', 'sort_order'];
    protected $casts = ['options' => 'array', 'value' => 'json'];

    private const SENSITIVE_KEYS = [
        'payment_stripe_secret',
        'payment_paypal_secret',
        'mail_password',
    ];

    public static function get($key, $default = null)
    {
        $setting = self::where('key', $key)->first();
        if (!$setting) return $default;
        $value = $setting->value;

        if (in_array($key, self::SENSITIVE_KEYS, true) && is_string($value) && str_starts_with($value, 'enc:')) {
            try { return Crypt::decryptString(substr($value, 4)); }
            catch (\Throwable $e) { return $default; }
        }
        return $value;
    }

    public static function set($key, $value)
    {
        if (in_array($key, self::SENSITIVE_KEYS, true) && filled($value)) {
            $value = 'enc:'.Crypt::encryptString((string) $value);
        }
        return self::updateOrCreate(['key' => $key], ['value' => $value]);
    }

    public static function getGroup($group)
    {
        return self::where('group', $group)->get();
    }

    public static function isPaymentEnabled($gateway)
    {
        $enabled = self::get('payment_'.$gateway.'_enabled', false);
        return $enabled === 'on' || $enabled === true || $enabled === 1 || $enabled === '1';
    }

    public static function getPaymentConfig($gateway)
    {
        return [
            'enabled' => self::isPaymentEnabled($gateway),
            'key' => self::get('payment_'.$gateway.'_key'),
            'secret' => self::get('payment_'.$gateway.'_secret'),
            'mode' => self::get('payment_'.$gateway.'_mode', 'sandbox'),
        ];
    }
}
