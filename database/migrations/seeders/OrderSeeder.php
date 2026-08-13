<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\User;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();
        
        if ($user) {
            Order::create([
                'user_id' => $user->id,
                'order_number' => Order::generateOrderNumber(),
                'subtotal' => 100.00,
                'tax' => 10.00,
                'shipping_cost' => 5.00,
                'discount' => 0,
                'total' => 115.00,
                'shipping_name' => $user->name,
                'shipping_email' => $user->email,
                'shipping_phone' => '1234567890',
                'shipping_address' => '123 Main Street',
                'shipping_city' => 'New York',
                'shipping_state' => 'NY',
                'shipping_zip' => '10001',
                'shipping_country' => 'USA',
                'payment_status' => 'pending',
                'order_status' => 'pending',
            ]);
        }
    }
}