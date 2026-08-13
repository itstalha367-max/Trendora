@extends('layouts.admin')

@section('title', 'Manage Products')

@section('content')
<style>
    .page-header { animation: slideDown 0.5s ease-out; }
    @keyframes slideDown {
        from { opacity: 0; transform: translateY(-20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .btn-add {
        background: linear-gradient(135deg, #8b5cf6, #5b7cff);
        color: #fff;
        border: none;
        padding: 10px 25px;
        border-radius: 12px;
        font-weight: 600;
        transition: all 0.3s;
    }
    .btn-add:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        color: #fff;
    }
    .product-image {
        width: 60px;
        height: 60px;
        border-radius: 10px;
        object-fit: cover;
        border: 2px solid #1a2230;
        background: #0f141e;
    }
    .product-image-placeholder {
        width: 60px;
        height: 60px;
        border-radius: 10px;
        background: linear-gradient(135deg, #0f141e, #273142);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #93a1b4;
        font-size: 24px;
        border: 2px dashed #2d3748;
    }
    .filter-card {
        background: #111722;
        border-radius: 16px;
        padding: 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.04);
        margin-bottom: 20px;
        animation: slideDown 0.5s ease-out;
    }
    .filter-card .form-control,
    .filter-card .form-select {
        border-radius: 10px;
        border: 2px solid #273142;
        padding: 10px 15px;
        font-size: 14px;
        transition: all 0.3s;
    }
    .filter-card .form-control:focus,
    .filter-card .form-select:focus {
        border-color: #8b5cf6;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }
    .bulk-actions {
        background: #0f141e;
        border-radius: 12px;
        padding: 12px 20px;
        display: none;
        align-items: center;
        gap: 15px;
        animation: slideUp 0.3s ease-out;
    }
    .bulk-actions.show {
        display: flex;
    }
    @keyframes slideUp {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .btn-bulk {
        padding: 6px 18px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 13px;
        border: none;
        transition: all 0.3s;
    }
    .btn-bulk:hover {
        transform: translateY(-2px);
    }
</style>

<div class="page-header d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-0"><i class="fas fa-box me-2 text-primary"></i>Manage Products</h2>
        <p class="text-muted mb-0">Add, edit and manage your products</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.products.import') }}" class="btn btn-success rounded-3">
            <i class="fas fa-file-import me-2"></i>Import
        </a>
        <a href="{{ route('admin.products.export') }}" class="btn btn-info rounded-3 text-white">
            <i class="fas fa-file-export me-2"></i>Export
        </a>
        <a href="{{ route('admin.products.create') }}" class="btn-add">
            <i class="fas fa-plus me-2"></i>Add Product
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 rounded-4">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show border-0 rounded-4">
        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<!-- 🔍 Filter Section -->
<div class="filter-card">
    <div class="row g-3 align-items-end">
        <div class="col-md-3">
            <label class="form-label fw-bold small text-muted">Search</label>
            <div class="input-group">
                <input type="text" class="form-control" id="searchInput" placeholder="Search products..." value="{{ request('search') }}">
                <button class="btn btn-primary" onclick="applyFilters()">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>
        <div class="col-md-2">
            <label class="form-label fw-bold small text-muted">Category</label>
            <select class="form-select" id="categoryFilter" onchange="applyFilters()">
                <option value="">All Categories</option>
                @foreach($categories ?? [] as $category)
                    <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label fw-bold small text-muted">Status</label>
            <select class="form-select" id="statusFilter" onchange="applyFilters()">
                <option value="">All Status</option>
                <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Active</option>
                <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label fw-bold small text-muted">Stock</label>
            <select class="form-select" id="stockFilter" onchange="applyFilters()">
                <option value="">All Stock</option>
                <option value="in_stock" {{ request('stock') == 'in_stock' ? 'selected' : '' }}>In Stock</option>
                <option value="out_of_stock" {{ request('stock') == 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
                <option value="low_stock" {{ request('stock') == 'low_stock' ? 'selected' : '' }}>Low Stock</option>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label fw-bold small text-muted">Sort By</label>
            <select class="form-select" id="sortFilter" onchange="applyFilters()">
                <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest</option>
                <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest</option>
                <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Name A-Z</option>
                <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Name Z-A</option>
                <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Price Low-High</option>
                <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Price High-Low</option>
            </select>
        </div>
        <div class="col-md-1">
            <button class="btn btn-outline-secondary w-100" onclick="resetFilters()" title="Reset Filters">
                <i class="fas fa-undo"></i>
            </button>
        </div>
    </div>
</div>

<!-- 📊 Bulk Actions Toolbar -->
<div class="bulk-actions" id="bulkActions">
    <span class="fw-bold me-2">
        <i class="fas fa-check-circle text-primary me-1"></i>
        <span id="selectedCount">0</span> selected
    </span>
    <div class="d-flex gap-2">
        <select class="form-select form-select-sm" id="bulkStatus" style="width: 150px;">
            <option value="">Change Status...</option>
            <option value="1">Active</option>
            <option value="0">Inactive</option>
        </select>
        <button class="btn btn-sm btn-primary btn-bulk" onclick="bulkStatusUpdate()">
            <i class="fas fa-check"></i> Apply
        </button>
        <button class="btn btn-sm btn-danger btn-bulk" onclick="bulkDelete()">
            <i class="fas fa-trash"></i> Delete
        </button>
        <button class="btn btn-sm btn-secondary btn-bulk" onclick="deselectAll()">
            <i class="fas fa-times"></i> Deselect All
        </button>
    </div>
</div>

<!-- 📋 Products Table -->
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-0">
        <div style="overflow-x: auto;">
            <div class="table-responsive"><table class="table table-hover mb-0">
                <thead style="background: #0f141e;">
                    <tr>
                        <th style="padding: 15px 20px; width: 40px;">
                            <input type="checkbox" id="selectAllCheckbox" onchange="toggleAllCheckboxes()">
                        </th>
                        <th style="padding: 15px 20px;">ID</th>
                        <th style="padding: 15px 20px;">Image</th>
                        <th style="padding: 15px 20px;">Name</th>
                        <th style="padding: 15px 20px;">Category</th>
                        <th style="padding: 15px 20px;">Variations</th>
                        <th style="padding: 15px 20px;">Price</th>
                        <th style="padding: 15px 20px;">Stock</th>
                        <th style="padding: 15px 20px;">Status</th>
                        <th style="padding: 15px 20px; text-align: center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                    <tr>
                        <td style="padding: 15px 20px;">
                            <input type="checkbox" class="product-checkbox" value="{{ $product->id }}" onchange="updateSelectedCount()">
                        </td>
                        <td style="padding: 15px 20px;">#{{ $product->id }}</td>
                        <td style="padding: 15px 20px;">
                            @if($product->thumbnail && file_exists(storage_path('app/public/' . $product->thumbnail)))
                                <img src="{{ asset('storage/' . $product->thumbnail) }}" alt="{{ $product->name }}" class="product-image">
                            @elseif($product->images && is_array($product->images) && count($product->images) > 0 && file_exists(storage_path('app/public/' . $product->images[0])))
                                <img src="{{ asset('storage/' . $product->images[0]) }}" alt="{{ $product->name }}" class="product-image">
                            @else
                                <div class="product-image-placeholder">
                                    <i class="fas fa-image"></i>
                                </div>
                            @endif
                        </td>
                        <td style="padding: 15px 20px;">
                            <div>
                                <div class="fw-bold">{{ $product->name }}</div>
                                <small class="text-muted">SKU: {{ $product->sku ?? 'N/A' }}</small>
                            </div>
                        </td>
                        <td style="padding: 15px 20px;">
                            <span class="badge bg-light text-dark rounded-pill px-3 py-2">
                                {{ $product->category->name ?? 'N/A' }}
                            </span>
                        </td>
                        <td style="padding: 15px 20px;">
                            @if($product->hasVariations)
                                <span class="badge bg-info rounded-pill px-3 py-2">
                                    {{ $product->variations->count() }} 
                                    <i class="fas fa-layer-group ms-1"></i>
                                </span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td style="padding: 15px 20px;">
                            <span class="fw-bold">{{ App\Models\Setting::get('currency_symbol','Rs') }} {{ number_format($product->price, 2) }}</span>
                            @if($product->compare_price)
                                <br><small class="text-muted text-decoration-line-through">{{ App\Models\Setting::get('currency_symbol','Rs') }} {{ number_format($product->compare_price, 2) }}</small>
                            @endif
                        </td>
                        <td style="padding: 15px 20px;">
                            <span class="badge rounded-pill px-3 py-2 {{ $product->stock_quantity > 0 ? 'bg-success' : 'bg-danger' }}">
                                {{ $product->stock_quantity }}
                            </span>
                        </td>
                        <td style="padding: 15px 20px;">
                            <span class="badge rounded-pill px-3 py-2 {{ $product->status ? 'bg-success' : 'bg-secondary' }}">
                                {{ $product->status ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td style="padding: 15px 20px; text-align: center;">
                            <div class="d-flex gap-1 justify-content-center">
                                <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-sm btn-primary rounded-3" style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" class="d-inline">
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
                        <td colspan="9" class="text-center py-5">
                            <i class="fas fa-box-open fa-3x d-block mb-3 text-muted" style="opacity: 0.2;"></i>
                            <h5 class="text-muted">No products found</h5>
                            <a href="{{ route('admin.products.create') }}" class="btn btn-primary mt-2">
                                <i class="fas fa-plus me-2"></i>Add Your First Product
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table></div>
        </div>
        @if($products->hasPages())
        <div class="p-3 border-top">{{ $products->links() }}</div>
        @endif
    </div>
</div>

<script>
// ============================================
// 🔍 FILTER FUNCTIONS
// ============================================

function applyFilters() {
    const search = document.getElementById('searchInput').value;
    const category = document.getElementById('categoryFilter').value;
    const status = document.getElementById('statusFilter').value;
    const stock = document.getElementById('stockFilter').value;
    const sort = document.getElementById('sortFilter').value;
    
    let url = new URL(window.location.href);
    
    // Remove empty params
    if (search) url.searchParams.set('search', search);
    else url.searchParams.delete('search');
    
    if (category) url.searchParams.set('category', category);
    else url.searchParams.delete('category');
    
    if (status) url.searchParams.set('status', status);
    else url.searchParams.delete('status');
    
    if (stock) url.searchParams.set('stock', stock);
    else url.searchParams.delete('stock');
    
    if (sort && sort !== 'newest') url.searchParams.set('sort', sort);
    else url.searchParams.delete('sort');
    
    window.location.href = url.toString();
}

function resetFilters() {
    window.location.href = window.location.pathname;
}

// Enter key press for search
document.getElementById('searchInput').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        applyFilters();
    }
});

// ============================================
// 📊 BULK ACTIONS FUNCTIONS
// ============================================

function toggleAllCheckboxes() {
    const checked = document.getElementById('selectAllCheckbox').checked;
    document.querySelectorAll('.product-checkbox').forEach(cb => {
        cb.checked = checked;
    });
    updateSelectedCount();
}

function updateSelectedCount() {
    const count = document.querySelectorAll('.product-checkbox:checked').length;
    document.getElementById('selectedCount').textContent = count;
    
    const bulkActions = document.getElementById('bulkActions');
    if (count > 0) {
        bulkActions.classList.add('show');
    } else {
        bulkActions.classList.remove('show');
    }
}

function deselectAll() {
    document.getElementById('selectAllCheckbox').checked = false;
    document.querySelectorAll('.product-checkbox').forEach(cb => {
        cb.checked = false;
    });
    updateSelectedCount();
}

function getSelectedIds() {
    const ids = [];
    document.querySelectorAll('.product-checkbox:checked').forEach(cb => {
        ids.push(cb.value);
    });
    return ids;
}

function bulkDelete() {
    const ids = getSelectedIds();
    if (ids.length === 0) {
        alert('Please select products to delete');
        return;
    }
    
    if (!confirm('Delete ' + ids.length + ' products?')) return;
    
    fetch('{{ route("admin.products.bulk-delete") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ ids: ids })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'Something went wrong');
        }
    })
    .catch(error => {
        alert('Error: ' + error);
    });
}

function bulkStatusUpdate() {
    const ids = getSelectedIds();
    const status = document.getElementById('bulkStatus').value;
    
    if (ids.length === 0) {
        alert('Please select products');
        return;
    }
    
    if (!status) {
        alert('Please select a status');
        return;
    }
    
    if (!confirm('Update status for ' + ids.length + ' products?')) return;
    
    fetch('{{ route("admin.products.bulk-status") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ ids: ids, status: status })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'Something went wrong');
        }
    })
    .catch(error => {
        alert('Error: ' + error);
    });
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    updateSelectedCount();
});
</script>
@endsection