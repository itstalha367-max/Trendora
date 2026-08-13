@extends('layouts.admin')

@section('title', 'Product Reviews')

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
    .filter-card {
        background: #111722;
        border-radius: 16px;
        padding: 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.04);
        margin-bottom: 20px;
    }
    .review-item {
        transition: all 0.3s;
    }
    .review-item:hover {
        background: #0f141e;
    }
    .stars {
        color: #f59e0b;
        font-size: 18px;
        letter-spacing: 2px;
    }
    .badge-status {
        padding: 4px 15px;
        border-radius: 50px;
        font-size: 12px;
        font-weight: 700;
    }
</style>

<div class="page-header d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-0"><i class="fas fa-star me-2 text-warning"></i>Product Reviews</h2>
        <p class="text-muted mb-0">Manage customer reviews and ratings</p>
    </div>
</div>

<!-- 📊 Stats -->
<div class="row g-3 mb-4">
    <div class="col-md-2">
        <div class="stat-box">
            <p class="label">Total Reviews</p>
            <h3 class="number text-primary">{{ $stats['total'] }}</h3>
        </div>
    </div>
    <div class="col-md-2">
        <div class="stat-box">
            <p class="label">Pending</p>
            <h3 class="number text-warning">{{ $stats['pending'] }}</h3>
        </div>
    </div>
    <div class="col-md-2">
        <div class="stat-box">
            <p class="label">Approved</p>
            <h3 class="number text-success">{{ $stats['approved'] }}</h3>
        </div>
    </div>
    <div class="col-md-2">
        <div class="stat-box">
            <p class="label">Rejected</p>
            <h3 class="number text-danger">{{ $stats['rejected'] }}</h3>
        </div>
    </div>
    <div class="col-md-2">
        <div class="stat-box">
            <p class="label">Average Rating</p>
            <h3 class="number text-warning">{{ number_format($stats['avg_rating'], 1) }} ⭐</h3>
        </div>
    </div>
</div>

<!-- 🔍 Filters -->
<div class="filter-card">
    <div class="row g-3 align-items-end">
        <div class="col-md-4">
            <label class="form-label fw-bold small text-muted">Search</label>
            <input type="text" class="form-control" id="searchInput" placeholder="Search by user, product, comment..." value="{{ request('search') }}">
        </div>
        <div class="col-md-3">
            <label class="form-label fw-bold small text-muted">Status</label>
            <select class="form-select" id="statusFilter">
                <option value="">All Status</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label fw-bold small text-muted">Rating</label>
            <select class="form-select" id="ratingFilter">
                <option value="">All Ratings</option>
                <option value="5" {{ request('rating') == '5' ? 'selected' : '' }}>⭐⭐⭐⭐⭐ (5)</option>
                <option value="4" {{ request('rating') == '4' ? 'selected' : '' }}>⭐⭐⭐⭐ (4)</option>
                <option value="3" {{ request('rating') == '3' ? 'selected' : '' }}>⭐⭐⭐ (3)</option>
                <option value="2" {{ request('rating') == '2' ? 'selected' : '' }}>⭐⭐ (2)</option>
                <option value="1" {{ request('rating') == '1' ? 'selected' : '' }}>⭐ (1)</option>
            </select>
        </div>
        <div class="col-md-2">
            <button class="btn btn-primary w-100" onclick="applyFilters()">
                <i class="fas fa-filter me-2"></i>Filter
            </button>
        </div>
    </div>
</div>

<!-- 📋 Reviews Table -->
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-0">
        <div style="overflow-x: auto;">
            <div class="table-responsive"><table class="table table-hover mb-0">
                <thead style="background: #0f141e;">
                    <tr>
                        <th style="padding: 15px 20px;">User</th>
                        <th style="padding: 15px 20px;">Product</th>
                        <th style="padding: 15px 20px;">Rating</th>
                        <th style="padding: 15px 20px;">Comment</th>
                        <th style="padding: 15px 20px;">Status</th>
                        <th style="padding: 15px 20px;">Date</th>
                        <th style="padding: 15px 20px; text-align: center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reviews as $review)
                    <tr class="review-item">
                        <td style="padding: 15px 20px;">
                            <div class="d-flex align-items-center">
                                <div style="width: 32px; height: 32px; border-radius: 50%; background: linear-gradient(135deg, #8b5cf6, #5b7cff); color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 14px; margin-right: 10px;">
                                    {{ substr($review->user->name ?? 'U', 0, 1) }}
                                </div>
                                <div>
                                    <div class="fw-bold">{{ $review->user->name ?? 'Unknown' }}</div>
                                    <small class="text-muted">{{ $review->user->email ?? '' }}</small>
                                </div>
                            </div>
                        </td>
                        <td style="padding: 15px 20px;">
                            <div>
                                <div class="fw-bold">{{ $review->product->name ?? 'N/A' }}</div>
                                <small class="text-muted">ID: #{{ $review->product_id }}</small>
                            </div>
                        </td>
                        <td style="padding: 15px 20px;">
                            <span class="stars">{{ str_repeat('⭐', $review->rating) }}{{ str_repeat('☆', 5 - $review->rating) }}</span>
                        </td>
                        <td style="padding: 15px 20px;">
                            <div style="max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                {{ $review->comment ?? 'No comment' }}
                            </div>
                        </td>
                        <td style="padding: 15px 20px;">
                            <span class="badge-status bg-{{ $review->status == 'approved' ? 'success' : ($review->status == 'pending' ? 'warning' : 'danger') }} text-{{ $review->status == 'pending' ? 'dark' : 'white' }}">
                                {{ ucfirst($review->status) }}
                            </span>
                        </td>
                        <td style="padding: 15px 20px;">
                            {{ $review->created_at->format('d M Y') }}
                        </td>
                        <td style="padding: 15px 20px; text-align: center;">
                            <div class="d-flex gap-1 justify-content-center">
                                @if($review->status == 'pending')
                                <a href="{{ route('admin.reviews.approve', $review->id) }}" class="btn btn-sm btn-success rounded-3" style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;" title="Approve">
                                    <i class="fas fa-check"></i>
                                </a>
                                <a href="{{ route('admin.reviews.reject', $review->id) }}" class="btn btn-sm btn-danger rounded-3" style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;" title="Reject">
                                    <i class="fas fa-times"></i>
                                </a>
                                @endif
                                <a href="{{ route('admin.reviews.show', $review->id) }}" class="btn btn-sm btn-primary rounded-3" style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <form action="{{ route('admin.reviews.destroy', $review->id) }}" method="POST" class="d-inline">
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
                        <td colspan="7" class="text-center py-5">
                            <i class="fas fa-star fa-3x d-block mb-3 text-muted" style="opacity: 0.2;"></i>
                            <h5 class="text-muted">No reviews found</h5>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table></div>
        </div>
        @if($reviews->hasPages())
        <div class="p-3 border-top">{{ $reviews->links() }}</div>
        @endif
    </div>
</div>

<script>
function applyFilters() {
    const search = document.getElementById('searchInput').value;
    const status = document.getElementById('statusFilter').value;
    const rating = document.getElementById('ratingFilter').value;
    
    let url = new URL(window.location.href);
    
    if (search) url.searchParams.set('search', search);
    else url.searchParams.delete('search');
    
    if (status) url.searchParams.set('status', status);
    else url.searchParams.delete('status');
    
    if (rating) url.searchParams.set('rating', rating);
    else url.searchParams.delete('rating');
    
    window.location.href = url.toString();
}

document.getElementById('searchInput').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        applyFilters();
    }
});
</script>
@endsection