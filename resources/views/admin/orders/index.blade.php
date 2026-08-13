@extends('layouts.admin')

@section('title', 'Manage Orders')

@section('content')
<style>
    .page-header { animation: slideDown 0.5s ease-out; }
    @keyframes slideDown {
        from { opacity: 0; transform: translateY(-20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .badge-status {
        padding: 5px 15px;
        border-radius: 50px;
        font-size: 12px;
        font-weight: 700;
    }
</style>

<div class="page-header d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-0"><i class="fas fa-shopping-cart me-2 text-primary"></i>Manage Orders</h2>
        <p class="text-muted mb-0">View and manage all customer orders</p>
    </div>
    <div>
        <span class="badge bg-warning rounded-pill px-3 py-2 me-2">
            <i class="fas fa-clock me-1"></i>Pending: {{ App\Models\Order::where('order_status', 'pending')->count() }}
        </span>
        <span class="badge bg-success rounded-pill px-3 py-2">
            <i class="fas fa-check me-1"></i>Total: {{ App\Models\Order::count() }}
        </span>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 rounded-4">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-0">
        <div style="overflow-x: auto;">
            <div class="table-responsive"><table class="table table-hover mb-0">
                <thead style="background: #0f141e;">
                    <tr>
                        <th style="padding: 15px 20px;">Order #</th>
                        <th style="padding: 15px 20px;">Customer</th>
                        <th style="padding: 15px 20px;">Total</th>
                        <th style="padding: 15px 20px;">Status</th>
                        <th style="padding: 15px 20px;">Payment</th>
                        <th style="padding: 15px 20px;">Date</th>
                        <th style="padding: 15px 20px; text-align: center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                    <tr>
                        <td style="padding: 15px 20px;">
                            <span class="fw-bold">#{{ $order->order_number ?? $order->id }}</span>
                        </td>
                        <td style="padding: 15px 20px;">
                            <div class="d-flex align-items-center">
                                <div style="width: 32px; height: 32px; border-radius: 50%; background: linear-gradient(135deg, #8b5cf6, #5b7cff); color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 14px; margin-right: 10px;">
                                    {{ substr($order->user->name ?? 'G', 0, 1) }}
                                </div>
                                <div>
                                    <div class="fw-bold">{{ $order->user->name ?? 'Guest' }}</div>
                                    <small class="text-muted">{{ $order->user->email ?? 'N/A' }}</small>
                                </div>
                            </div>
                        </td>
                        <td style="padding: 15px 20px;">
                            <span class="fw-bold">{{ App\Models\Setting::get('currency_symbol','Rs') }} {{ number_format($order->total ?? 0, 2) }}</span>
                        </td>
                        <td style="padding: 15px 20px;">
                            <span class="badge-status bg-{{ $order->order_status == 'pending' ? 'warning' : ($order->order_status == 'processing' ? 'info' : ($order->order_status == 'delivered' ? 'success' : 'danger')) }} text-{{ $order->order_status == 'pending' ? 'dark' : 'white' }}">
                                <i class="fas fa-{{ $order->order_status == 'pending' ? 'clock' : ($order->order_status == 'processing' ? 'spinner' : ($order->order_status == 'delivered' ? 'check' : 'times')) }} me-1"></i>
                                {{ ucfirst($order->order_status ?? 'Pending') }}
                            </span>
                        </td>
                        <td style="padding: 15px 20px;">
                            <span class="badge-status bg-{{ $order->payment_status == 'paid' ? 'success' : ($order->payment_status == 'pending' ? 'warning' : 'danger') }} text-white">
                                {{ ucfirst($order->payment_status ?? 'Pending') }}
                            </span>
                        </td>
                        <td style="padding: 15px 20px;">
                            {{ $order->created_at ? $order->created_at->format('d M Y') : 'N/A' }}
                        </td>
                        <td style="padding: 15px 20px; text-align: center;">
                            <div class="d-flex gap-1 justify-content-center">
                                <!-- View Button -->
                                <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-sm btn-primary rounded-3" style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-eye"></i>
                                </a>
                                
                                <!-- 🔥 PDF Button - YEH ADD KAREIN -->
                                <a href="{{ route('admin.orders.invoice', $order->id) }}" class="btn btn-sm btn-success rounded-3" style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;" target="_blank">
                                    <i class="fas fa-file-pdf"></i>
                                </a>
                                <form method="POST" action="{{ route('admin.orders.send-email', $order->id) }}" class="d-inline" onsubmit="return confirm('Send confirmation email?')">@csrf
                                    <button type="submit" class="btn btn-sm btn-warning rounded-3" style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;" aria-label="Send confirmation email"><i class="fas fa-envelope"></i></button>
                                </form>
                                
                                <!-- Delete Button -->
                                <form action="{{ route('admin.orders.destroy', $order->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger rounded-3" style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;" onclick="return confirm('Are you sure?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <i class="fas fa-inbox fa-3x d-block mb-3 text-muted" style="opacity: 0.2;"></i>
                            <h5 class="text-muted">No orders found</h5>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table></div>
        </div>
        @if($orders->hasPages())
        <div class="p-3 border-top">{{ $orders->links() }}</div>
        @endif
    </div>
</div>
@endsection