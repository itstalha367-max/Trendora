@extends('layouts.admin')

@section('title', 'Wishlist Detail')

@section('content')
<style>
    .detail-card { animation: slideUp 0.5s ease-out; }
    @keyframes slideUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>

<div class="detail-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0"><i class="fas fa-heart me-2 text-danger"></i>Wishlist Detail</h2>
            <p class="text-muted mb-0">#{{ $wishlist->id }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.wishlist.index') }}" class="btn btn-secondary rounded-3">
                <i class="fas fa-arrow-left me-2"></i>Back
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="fw-bold text-muted small">User</label>
                        <p class="fw-bold">{{ $wishlist->user->name ?? 'Unknown' }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold text-muted small">Email</label>
                        <p>{{ $wishlist->user->email ?? 'N/A' }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold text-muted small">Added Date</label>
                        <p>{{ $wishlist->created_at->format('d M Y, h:i A') }}</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="fw-bold text-muted small">Product</label>
                        <div class="d-flex align-items-center">
                            @if($wishlist->product->thumbnail)
                                <img src="{{ asset('storage/' . $wishlist->product->thumbnail) }}" alt="{{ $wishlist->product->name }}" style="width: 60px; height: 60px; border-radius: 10px; object-fit: cover; margin-right: 15px;">
                            @endif
                            <div>
                                <p class="fw-bold mb-0">{{ $wishlist->product->name ?? 'N/A' }}</p>
                                <small class="text-muted">{{ App\Models\Setting::get('currency_symbol','Rs') }} {{ number_format($wishlist->product->price ?? 0, 2) }}</small>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold text-muted small">Variation</label>
                        @if($wishlist->variation)
                            <p>
                                <span class="badge bg-info rounded-pill px-3 py-2">
                                    {{ $wishlist->variation->attribute_name }}: {{ $wishlist->variation->attribute_value }}
                                </span>
                            </p>
                        @else
                            <p class="text-muted">No variation</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection