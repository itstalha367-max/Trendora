<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Exports\OrdersExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Mail\OrderConfirmationMail;
use App\Mail\OrderStatusMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use App\Services\InventoryService;
use App\Services\TemplateRenderer;
use App\Notifications\CommerceNotification;
use App\Services\WebhookDispatcher;

class OrderController extends Controller
{
    /**
     * Display a listing of orders.
     */
    public function index()
    {
        $orders = Order::with('user')->latest()->paginate(10);
        return view('admin.orders.index', compact('orders'));
    }

    /**
     * Display the specified order.
     */
    public function show($id)
    {
        $order = Order::with(['user', 'items.product', 'items.variation', 'items.warehouse', 'statusHistory.user', 'refunds'])->findOrFail($id);
        return view('admin.orders.show', compact('order'));
    }

    /**
     * Update order status.
     */
    public function updateStatus(Request $request, $id, InventoryService $inventory, TemplateRenderer $templates, WebhookDispatcher $webhooks)
    {
        $data = $request->validate(['status' => 'required|in:pending,processing,shipped,delivered,cancelled', 'note' => 'nullable|string|max:1000']);
        $order = Order::with(['user','items.product','items.variation'])->findOrFail($id);
        $oldStatus = $order->order_status;
        if ($oldStatus === $data['status']) return back()->with('success', 'Order status is already up to date.');
        if ($oldStatus === 'cancelled' && $data['status'] !== 'cancelled') return back()->with('error', 'Cancelled orders cannot be reopened automatically because stock has already been restored. Create a new order instead.');
        DB::transaction(function () use ($order, $oldStatus, $data, $inventory) {
            if ($data['status'] === 'cancelled' && $oldStatus !== 'cancelled') $inventory->restoreOrder($order, 'Order cancelled by admin');
            $updates = ['order_status' => $data['status']];
            if ($data['status'] === 'shipped' && !$order->shipped_at) $updates['shipped_at'] = now();
            if ($data['status'] === 'delivered' && !$order->delivered_at) $updates['delivered_at'] = now();
            $order->update($updates);
            $order->statusHistory()->create(['user_id'=>auth()->id(),'from_status'=>$oldStatus,'to_status'=>$data['status'],'note'=>$data['note'] ?? null]);
        });
        $notificationCopy = $templates->notification('order_status', [
            'order_number' => $order->order_number,
            'status' => ucfirst($data['status']),
        ], 'Order updated', 'Your order '.$order->order_number.' is now '.ucfirst($data['status']).'.');
        $order->user?->notify(new CommerceNotification($notificationCopy['title'], $notificationCopy['message'], route('user.order.detail',$order->id), 'fa-truck-fast'));
        if ($order->user && $order->user->email) { try { Mail::to($order->user->email)->send(new OrderStatusMail($order, $oldStatus, $data['status'])); } catch (\Throwable $e) { \Log::error('Order status email failed: '.$e->getMessage()); } }
        try { $webhooks->dispatch('order.updated', $order->fresh(['items']), ['from_status'=>$oldStatus,'to_status'=>$data['status']]); } catch (\Throwable $e) { \Log::warning('Order webhook failed: '.$e->getMessage()); }
        return back()->with('success', 'Order status updated and inventory history synchronized.');
    }
    /**
 * Update tracking information
 */
public function updateTracking(Request $request, $id, WebhookDispatcher $webhooks)
{
    $order = Order::with('user')->findOrFail($id);
    $request->validate(['tracking_number' => 'nullable|string|max:255']);
    $oldStatus = $order->order_status;
    $order->tracking_number = $request->tracking_number;
    if ($request->tracking_number && $order->isPending()) {
        $order->order_status = 'shipped';
        $order->shipped_at = now();
    }
    $order->save();
    if ($oldStatus !== $order->order_status) {
        $order->statusHistory()->create(['user_id'=>auth()->id(),'from_status'=>$oldStatus,'to_status'=>$order->order_status,'note'=>'Tracking number added: '.$order->tracking_number]);
        $order->user?->notify(new CommerceNotification('Order shipped','Your order '.$order->order_number.' has shipped. Tracking: '.$order->tracking_number,route('user.order.tracking',$order->id),'fa-truck-fast'));
    }
    try { $webhooks->dispatch('order.updated', $order->fresh(['items']), ['tracking_updated'=>true]); } catch (\Throwable $e) { \Log::warning('Tracking webhook failed: '.$e->getMessage()); }
    return back()->with('success', 'Tracking information updated!');
}
    public function export()
{
    return Excel::download(new OrdersExport, 'orders-' . date('Y-m-d') . '.xlsx');
}

    /**
     * Remove the specified order.
     */
    public function destroy($id)
    {
        Order::findOrFail($id);
        return back()->with('error', 'Orders are financial audit records and cannot be hard-deleted. Cancel the order instead.');
    }

    /**
     * Generate Invoice PDF.
     */
    public function generateInvoice($id)
    {
        $order = Order::with('user', 'items')->findOrFail($id);
        
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
        
        $pdf = Pdf::loadView('admin.orders.invoice', $data);
        $pdf->setPaper('A4', 'portrait');
        
        return $pdf->download('invoice-' . ($order->order_number ?? $order->id) . '.pdf');
    }

    /**
     * Preview Invoice in Browser.
     */
    public function previewInvoice($id)
    {
        $order = Order::with('user', 'items')->findOrFail($id);
        
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
        
        return view('admin.orders.invoice', $data);
    }

    /**
     * Send order confirmation email manually.
     */
    public function sendConfirmationEmail($id)
    {
        $order = Order::with('user')->findOrFail($id);
        
        if (!$order->user || !$order->user->email) {
            return redirect()->back()->with('error', 'Customer email not found!');
        }

        try {
            Mail::to($order->user->email)->send(new OrderConfirmationMail($order));
            return redirect()->back()->with('success', 'Order confirmation email sent successfully!');
        } catch (\Exception $e) {
            \Log::error('Order confirmation email failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to send email: ' . $e->getMessage());
        }
    }

    /**
     * Get order statistics.
     */
    public function statistics()
    {
        $stats = [
            'total' => Order::count(),
            'pending' => Order::where('order_status', 'pending')->count(),
            'processing' => Order::where('order_status', 'processing')->count(),
            'shipped' => Order::where('order_status', 'shipped')->count(),
            'delivered' => Order::where('order_status', 'delivered')->count(),
            'cancelled' => Order::where('order_status', 'cancelled')->count(),
            'total_revenue' => Order::where('payment_status', 'paid')->sum('total'),
            'today_orders' => Order::whereDate('created_at', today())->count(),
            'today_revenue' => Order::whereDate('created_at', today())->where('payment_status', 'paid')->sum('total'),
        ];

        return response()->json($stats);
    }
}