<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AbandonedCart;
use Illuminate\Http\Request;

class AbandonedCartController extends Controller
{
    public function index(Request $request)
    {
        $query = AbandonedCart::with('user');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('email', 'LIKE', '%' . $request->search . '%')
                  ->orWhere('name', 'LIKE', '%' . $request->search . '%');
            });
        }

        $carts = $query->latest()->paginate(15)->appends($request->all());

        // Stats
        $stats = [
            'total' => AbandonedCart::count(),
            'active' => AbandonedCart::where('status', 'active')->count(),
            'recovered' => AbandonedCart::where('status', 'recovered')->count(),
            'expired' => AbandonedCart::where('status', 'expired')->count(),
            'total_value' => AbandonedCart::where('status', 'active')->sum('total'),
        ];

        return view('admin.abandoned-carts.index', compact('carts', 'stats'));
    }

    public function show($id)
    {
        $cart = AbandonedCart::with('user')->findOrFail($id);
        return view('admin.abandoned-carts.show', compact('cart'));
    }

    public function sendReminder($id)
    {
        $cart = AbandonedCart::findOrFail($id);
        $cart->sendReminder();
        
        return redirect()->back()->with('success', 'Reminder email sent successfully!');
    }

    public function markRecovered($id)
    {
        $cart = AbandonedCart::findOrFail($id);
        $cart->markAsRecovered();
        
        return redirect()->back()->with('success', 'Cart marked as recovered!');
    }

    public function destroy($id)
    {
        $cart = AbandonedCart::findOrFail($id);
        $cart->delete();
        
        return redirect()->route('admin.abandoned-carts.index')
            ->with('success', 'Abandoned cart deleted successfully!');
    }

    public function sendBulkReminder(Request $request)
    {
        $ids = $request->ids;
        if (empty($ids)) {
            return response()->json(['success' => false, 'message' => 'No carts selected']);
        }

        $carts = AbandonedCart::whereIn('id', $ids)->get();
        foreach ($carts as $cart) {
            $cart->sendReminder();
        }

        return response()->json(['success' => true, 'message' => 'Reminders sent successfully!']);
    }
}