@extends('layouts.admin')

@section('title', 'Wishlists')

@section('content')
<style>
    .page-header { animation: slideDown 0.5s ease-out; }
    @keyframes slideDown {
        from { opacity: 0; transform: translateY(-20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .stat-box {
        background: #111722;
        border-radius: 16px;
        padding: 20px;
        border: 1px solid rgba(0,0,0,0.04);
        transition: all 0.3s;
        text-align: center;
    }
    .stat-box:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 30px rgba(0,0,0,0.06);
    }
    .stat-box .number {
        font-size: 28px;
        font-weight: 800;
        margin: 0;
    }
    .stat-box .label {
        color: #93a1b4;
        font-size: 14px;
        margin: 0;
    }
    .wishlist-item {
        transition: all 0.3s;
    }
    .wishlist-item:hover {
        background: #0f141e;
    }
</style>

<div class="page-header">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0"><i class="fas fa-heart me-2 text-danger"></i>Wishlists</h2>
            <p class="text-muted mb-0">Manage customer wishlists</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 rounded-4">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
</div>

<!-- 📊 Stats -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="stat-box">
            <p class="label">Total Wishlist Items</p>
            <h3 class="number text-danger">{{ $stats['total'] }}</h3>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-box">
            <p class="label">Unique Users</p>
            <h3 class="number text-primary">{{ $stats['unique_users'] }}</h3>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-box">
            <p class="label">Unique Products</p>
            <h3 class="number text-success">{{ $stats['unique_products'] }}</h3>
        </div>
    </div>
</div>

<!-- 📋 Wishlist Table -->
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-0">
        <div style="overflow-x: auto;">
            <div class="table-responsive"><table class="table table-hover mb-0">
                <thead style="background: #0f141e;">
                    <tr>
                        <th style="padding: 15px 20px;">#</th>
                        <th style="padding: 15px 20px;">User</th>
                        <th style="padding: 15px 20px;">Product</th>
                        <th style="padding: 15px 20px;">Variation</th>
                        <th style="padding: 15px 20px;">Added Date</th>
                        <th style="padding: 15px 20px; text-align: center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($wishlists as $index => $item)
                    <tr class="wishlist-item">
                        <td style="padding: 15px 20px;">{{ $index + 1 }}</td>
                        <td style="padding: 15px 20px;">
                            <div class="d-flex align-items-center">
                                <div style="width: 32px; height: 32px; border-radius: 50%; background: linear-gradient(135deg, #8b5cf6, #5b7cff); color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 14px; margin-right: 10px;">
                                    {{ substr($item->user->name ?? 'U', 0, 1) }}
                                </div>
                                <div>
                                    <div class="fw-bold">{{ $item->user->name ?? 'Unknown' }}</div>
                                    <small class="text-muted">{{ $item->user->email ?? '' }}</small>
                                </div>
                            </div>
                        </td>
                        <td style="padding: 15px 20px;">
                            <div class="d-flex align-items-center">
                                @if($item->product->thumbnail)
                                    <img src="{{ asset('storage/' . $item->product->thumbnail) }}" alt="{{ $item->product->name }}" style="width: 40px; height: 40px; border-radius: 8px; object-fit: cover; margin-right: 10px;">
                                @endif
                                <div>
                                    <div class="fw-bold">{{ $item->product->name ?? 'N/A' }}</div>
                                    <small class="text-muted">{{ App\Models\Setting::get('currency_symbol','Rs') }} {{ number_format($item->product->price ?? 0, 2) }}</small>
                                </div>
                            </div>
                        </td>
                        <td style="padding: 15px 20px;">
                            @if($item->variation)
                                <span class="badge bg-info rounded-pill px-3 py-2">
                                    {{ $item->variation->attribute_name }}: {{ $item->variation->attribute_value }}
                                </span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td style="padding: 15px 20px;">
                            {{ $item->created_at->format('d M Y') }}
                        </td>
                        <td style="padding: 15px 20px; text-align: center;">
                            <div class="d-flex gap-1 justify-content-center">
                                <a href="{{ route('admin.wishlist.show', $item->id) }}" class="btn btn-sm btn-primary rounded-3" style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <form action="{{ route('admin.wishlist.destroy', $item->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger rounded-3" style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;" onclick="return confirm('Are you sure?')" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <i class="fas fa-heart fa-3x d-block mb-3 text-muted" style="opacity: 0.2;"></i>
                            <h5 class="text-muted">No wishlist items found</h5>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table></div>
        </div>
        @if($wishlists->hasPages())
        <div class="p-3 border-top">{{ $wishlists->links() }}</div>
        @endif
    </div>
</div>
@endsection