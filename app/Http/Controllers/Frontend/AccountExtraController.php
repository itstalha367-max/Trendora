<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Review;

class AccountExtraController extends Controller
{
    public function rewards()
    {
        $paidSpend = auth()->user()->orders()->where('payment_status', 'paid')->sum('total');
        $points = (int) floor($paidSpend);
        $tier = $points >= 5000 ? 'Platinum' : ($points >= 2000 ? 'Gold' : ($points >= 500 ? 'Silver' : 'Starter'));
        $nextAt = match($tier) { 'Starter' => 500, 'Silver' => 2000, 'Gold' => 5000, default => 5000 };
        return view('frontend.user.rewards', compact('paidSpend', 'points', 'tier', 'nextAt'));
    }

    public function notifications()
    {
        $notifications = auth()->user()->notifications()->latest()->paginate(15);
        return view('frontend.user.notifications', compact('notifications'));
    }

    public function markNotificationsRead()
    {
        auth()->user()->unreadNotifications->markAsRead();
        return back()->with('success', 'Notifications marked as read.');
    }

    public function reviews()
    {
        $reviews = Review::with(['product', 'order'])->where('user_id', auth()->id())->latest()->paginate(10);
        return view('frontend.user.reviews', compact('reviews'));
    }
}
