<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;

class CustomerReviewController extends Controller
{
    public function store(Request $request, Product $product)
    {
        $data = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:2000',
        ]);

        $order = Order::where('user_id', auth()->id())
            ->where('order_status', 'delivered')
            ->whereHas('items', fn($q) => $q->where('product_id', $product->id))
            ->latest()
            ->first();

        if (!$order) {
            return back()->with('error', 'Only customers with a delivered purchase can review this product.');
        }

        Review::updateOrCreate(
            ['user_id' => auth()->id(), 'product_id' => $product->id],
            [
                'order_id' => $order->id,
                'rating' => $data['rating'],
                'comment' => $data['comment'] ?? null,
                'status' => 'pending',
                'verified_purchase' => true,
            ]
        );

        return back()->with('success', 'Thanks! Your review was submitted for moderation.');
    }
}
