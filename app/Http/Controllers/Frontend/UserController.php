<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;

class UserController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $orders = Order::where('user_id', $user->id)
            ->latest()
            ->paginate(10);

        $stats = [
            'total_orders' => Order::where('user_id', $user->id)->count(),
            'pending_orders' => Order::where('user_id', $user->id)
                ->where('order_status', 'pending')
                ->count(),
            'delivered_orders' => Order::where('user_id', $user->id)
                ->where('order_status', 'delivered')
                ->count(),
            'total_spent' => Order::where('user_id', $user->id)
                ->where('payment_status', 'paid')
                ->sum('total'),
        ];

        return view('frontend.user.dashboard', compact('user', 'orders', 'stats'));
    }

    public function orders()
    {
        $orders = Order::with('items')->where('user_id', Auth::id())
            ->latest()
            ->paginate(10);
        return view('frontend.user.orders', compact('orders'));
    }
    public function tracking($id)
{
    $order = Order::with('items')
        ->where('user_id', Auth::id())
        ->findOrFail($id);
    return view('frontend.user.tracking', compact('order'));
}

    public function orderDetail($id)
    {
        $order = Order::with(['items.product','items.variation','statusHistory','refunds'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);
        return view('frontend.user.order-detail', compact('order'));
    }

    public function profile()
    {
        $user = Auth::user();
        return view('frontend.user.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'zip' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'city' => $request->city,
            'state' => $request->state,
            'zip' => $request->zip,
            'country' => $request->country,
        ]);

        return redirect()->back()->with('success', 'Profile updated successfully!');
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->back()->with('error', 'Current password is incorrect!');
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return redirect()->back()->with('success', 'Password updated successfully!');
    }
     public function downloadInvoice($id)
    {
        $order = Order::with('items')
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        $data = [
            'order' => $order,
            'company' => [
                'name' => 'Trendora',
                'address' => '123 Main Street, New York, USA',
                'phone' => '+1 234 567 8900',
                'email' => 'info@trendora.com',
                'website' => 'www.trendora.com'
            ]
        ];

        $pdf = Pdf::loadView('frontend.user.invoice', $data);
        $pdf->setPaper('A4', 'portrait');

        return $pdf->download('invoice-' . ($order->order_number ?? $order->id) . '.pdf');
    }

    /**
     * Preview Invoice
     */
    public function previewInvoice($id)
    {
        $order = Order::with('items')
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        $data = [
            'order' => $order,
            'company' => [
                'name' => 'Trendora',
                'address' => '123 Main Street, New York, USA',
                'phone' => '+1 234 567 8900',
                'email' => 'info@trendora.com',
                'website' => 'www.trendora.com'
            ]
        ];

        return view('frontend.user.invoice', $data);
    }
}