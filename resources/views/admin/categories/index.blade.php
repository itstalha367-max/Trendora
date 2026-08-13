@extends('layouts.admin')

@section('title', 'Manage Categories')

@section('content')
<style>
    .page-header { animation: slideDown 0.5s ease-out; }
    @keyframes slideDown {
        from { opacity: 0; transform: translateY(-20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .btn-add {
        background: linear-gradient(135deg, #10b981, #059669);
        color: #fff;
        border: none;
        padding: 10px 25px;
        border-radius: 12px;
        font-weight: 600;
        transition: all 0.3s;
    }
    .btn-add:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 184, 148, 0.4);
        color: #fff;
    }
</style>

<div class="page-header d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-0"><i class="fas fa-tags me-2 text-success"></i>Manage Categories</h2>
        <p class="text-muted mb-0">Organize your products with categories</p>
    </div>
    <a href="{{ route('admin.categories.create') }}" class="btn-add">
        <i class="fas fa-plus me-2"></i>Add Category
    </a>
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
                        <th style="padding: 15px 20px;">ID</th>
                        <th style="padding: 15px 20px;">Name</th>
                        <th style="padding: 15px 20px;">Slug</th>
                        <th style="padding: 15px 20px;">Products</th>
                        <th style="padding: 15px 20px;">Status</th>
                        <th style="padding: 15px 20px; text-align: center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $category)
                    <tr>
                        <td style="padding: 15px 20px;">#{{ $category->id }}</td>
                        <td style="padding: 15px 20px;">
                            <div class="d-flex align-items-center">
                                <div style="width: 40px; height: 40px; border-radius: 10px; background: linear-gradient(135deg, rgba(16,185,129,.12), rgba(16,185,129,.10)); display: flex; align-items: center; justify-content: center; margin-right: 12px;">
                                    <i class="fas fa-tag" style="color: #10b981;"></i>
                                </div>
                                <div>
                                    <div class="fw-bold">{{ $category->name }}</div>
                                </div>
                            </div>
                        </td>
                        <td style="padding: 15px 20px;">{{ $category->slug }}</td>
                        <td style="padding: 15px 20px;">
                            <span class="badge bg-primary rounded-pill px-3 py-2">{{ $category->products->count() }}</span>
                        </td>
                        <td style="padding: 15px 20px;">
                            <span class="badge rounded-pill px-3 py-2 {{ $category->status ? 'bg-success' : 'bg-secondary' }}">
                                {{ $category->status ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td style="padding: 15px 20px; text-align: center;">
                            <div class="d-flex gap-1 justify-content-center">
                                <a href="{{ route('admin.categories.edit', $category->id) }}" class="btn btn-sm btn-primary rounded-3" style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" class="d-inline">
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
                        <td colspan="6" class="text-center py-5">
                            <i class="fas fa-tags fa-3x d-block mb-3 text-muted" style="opacity: 0.2;"></i>
                            <h5 class="text-muted">No categories found</h5>
                            <a href="{{ route('admin.categories.create') }}" class="btn btn-success mt-2">
                                <i class="fas fa-plus me-2"></i>Add Your First Category
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table></div>
        </div>
        @if($categories->hasPages())
        <div class="p-3 border-top">{{ $categories->links() }}</div>
        @endif
    </div>
</div>
@endsection