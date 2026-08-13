<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            ['key'=>'store_name','value'=>'Trendora','group'=>'general','type'=>'text','label'=>'Store Name'],
            ['key'=>'store_email','value'=>'info@trendora.test','group'=>'general','type'=>'email','label'=>'Store Email'],
            ['key'=>'store_phone','value'=>'+92 300 0000000','group'=>'general','type'=>'text','label'=>'Store Phone'],
            ['key'=>'store_address','value'=>'Lahore, Pakistan','group'=>'general','type'=>'textarea','label'=>'Store Address'],
            ['key'=>'store_description','value'=>'Modern commerce, curated for everyday life.','group'=>'general','type'=>'textarea','label'=>'Store Description'],
            ['key'=>'currency','value'=>'PKR','group'=>'general','type'=>'text','label'=>'Currency'],
            ['key'=>'currency_symbol','value'=>'Rs.','group'=>'general','type'=>'text','label'=>'Currency Symbol'],
            ['key'=>'theme','value'=>'dark','group'=>'appearance','type'=>'select','label'=>'Theme','options'=>['light'=>'Light','dark'=>'Dark']],
            ['key'=>'maintenance_mode','value'=>'off','group'=>'system','type'=>'toggle','label'=>'Maintenance Mode'],
            ['key'=>'registration_enabled','value'=>'on','group'=>'system','type'=>'toggle','label'=>'Registration Enabled'],
            ['key'=>'payment_stripe_enabled','value'=>'off','group'=>'payment','type'=>'toggle','label'=>'Stripe Enabled'],
            ['key'=>'payment_stripe_key','value'=>'','group'=>'payment','type'=>'text','label'=>'Stripe Key'],
            ['key'=>'payment_stripe_secret','value'=>'','group'=>'payment','type'=>'text','label'=>'Stripe Secret'],
            ['key'=>'payment_stripe_mode','value'=>'sandbox','group'=>'payment','type'=>'select','label'=>'Stripe Mode','options'=>['sandbox'=>'Sandbox','live'=>'Live']],
            ['key'=>'payment_paypal_enabled','value'=>'off','group'=>'payment','type'=>'toggle','label'=>'PayPal Enabled'],
            ['key'=>'payment_paypal_client_id','value'=>'','group'=>'payment','type'=>'text','label'=>'PayPal Client ID'],
            ['key'=>'payment_paypal_secret','value'=>'','group'=>'payment','type'=>'text','label'=>'PayPal Secret'],
            ['key'=>'payment_paypal_mode','value'=>'sandbox','group'=>'payment','type'=>'select','label'=>'PayPal Mode','options'=>['sandbox'=>'Sandbox','live'=>'Live']],
        ];

        foreach ($defaults as $default) {
            Setting::firstOrCreate(['key'=>$default['key']], $default);
        }
    }
}
