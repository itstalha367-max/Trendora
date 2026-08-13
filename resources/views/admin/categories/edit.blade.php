@extends('layouts.admin')

@section('title', 'Edit Category')

@section('content')
<style>
    .form-card { animation: slideUp 0.5s ease-out; }
    @keyframes slideUp {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .btn-update {
        background: linear-gradient(135deg, #f59e0b, #f9a825);
        color: #fff;
        border: none;
        padding: 12px 30px;
        border-radius: 12px;
        font-weight: 600;
        transition: all 0.3s;
    }
    .btn-update:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(253, 203, 110, 0.4);
        color: #fff;
    }
</style>

<div class="form-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0"><i class="fas fa-edit me-2 text-warning"></i>Edit Category</h2>
            <p class="text-muted mb-0">Update category details</p>
        </div>
        <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary rounded-3">
            <i class="fas fa-arrow-left me-2"></i>Back
        </a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger border-0 rounded-4">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <form action="{{ route('admin.categories.update', $category->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="mb-3">
                    <label for="name" class="form-label fw-bold">Category Name *</label>
                    <input type="text" class="form-control rounded-3" id="name" name="name" value="{{ old('name', $category->name) }}" required>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label fw-bold">Description</label>
                    <textarea class="form-control rounded-3" id="description" name="description" rows="4">{{ old('description', $category->description) }}</textarea>
                </div>

                <div class="mb-3">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="status" name="status" value="1" {{ old('status', $category->status) ? 'checked' : '' }}>
                        <label class="form-check-label fw-bold" for="status">Active Status</label>
                    </div>
                </div>

                <button type="submit" class="btn-update">
                    <i class="fas fa-save me-2"></i>Update Category
                </button>
            </form>
        </div>
    </div>
</div>
@endsection