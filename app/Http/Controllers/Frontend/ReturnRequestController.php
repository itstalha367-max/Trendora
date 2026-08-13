<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\ReturnRequest;
use Illuminate\Http\Request;

class ReturnRequestController extends Controller
{
    public function index()
    {
        $returns = ReturnRequest::with('order')->where('user_id', auth()->id())->latest()->paginate(10);
        $eligibleOrders = Order::where('user_id', auth()->id())
            ->whereIn('order_status', ['shipped', 'delivered'])
            ->whereDoesntHave('returnRequests', fn($q) => $q->whereNotIn('status', ['rejected', 'closed']))
            ->latest()->take(20)->get();
        return view('frontend.user.returns', compact('returns', 'eligibleOrders'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'order_id' => 'required|integer',
            'type' => 'required|in:return,refund',
            'reason' => 'required|string|max:255',
            'details' => 'nullable|string|max:2000',
        ]);

        $order = Order::where('user_id', auth()->id())->findOrFail($data['order_id']);
        abort_unless(in_array($order->order_status, ['shipped', 'delivered'], true), 422, 'This order is not eligible for a return yet.');

        $exists = ReturnRequest::where('user_id', auth()->id())
            ->where('order_id', $order->id)
            ->whereNotIn('status', ['rejected', 'closed'])
            ->exists();
        if ($exists) {
            return back()->with('error', 'An active return/refund request already exists for this order.');
        }

        ReturnRequest::create([
            'user_id' => auth()->id(),
            'order_id' => $order->id,
            'request_number' => 'RTN-' . strtoupper(substr(bin2hex(random_bytes(6)), 0, 10)),
            'type' => $data['type'],
            'reason' => $data['reason'],
            'details' => $data['details'] ?? null,
            'requested_amount' => $order->total,
            'status' => 'pending',
        ]);

        return back()->with('success', 'Your return/refund request has been submitted.');
    }

    public function show(ReturnRequest $returnRequest)
    {
        abort_unless($returnRequest->user_id === auth()->id(), 403);
        $returnRequest->load('order.items');
        return view('frontend.user.return-detail', compact('returnRequest'));
    }
}
