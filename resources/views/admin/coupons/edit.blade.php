@extends('layouts.admin')

@section('title', 'Edit Coupon')

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
            <h2 class="fw-bold mb-0"><i class="fas fa-edit me-2 text-warning"></i>Edit Coupon</h2>
            <p class="text-muted mb-0">Update coupon details</p>
        </div>
        <a href="{{ route('admin.coupons.index') }}" class="btn btn-secondary rounded-3">
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
            <form action="{{ route('admin.coupons.update', $coupon->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="code" class="form-label fw-bold">Coupon Code *</label>
                            <input type="text" class="form-control rounded-3 text-uppercase" id="code" name="code" value="{{ old('code', $coupon->code) }}" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="name" class="form-label fw-bold">Coupon Name</label>
                            <input type="text" class="form-control rounded-3" id="name" name="name" value="{{ old('name', $coupon->name) }}">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="type" class="form-label fw-bold">Discount Type *</label>
                            <select class="form-control rounded-3" id="type" name="type" required>
                                <option value="percentage" {{ old('type', $coupon->type) == 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
                                <option value="fixed" {{ old('type', $coupon->type) == 'fixed' ? 'selected' : '' }}>Fixed Amount ($)</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="value" class="form-label fw-bold">Discount Value *</label>
                            <input type="number" step="0.01" class="form-control rounded-3" id="value" name="value" value="{{ old('value', $coupon->value) }}" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="min_order" class="form-label fw-bold">Minimum Order Amount</label>
                            <input type="number" step="0.01" class="form-control rounded-3" id="min_order" name="min_order" value="{{ old('min_order', $coupon->min_order) }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="max_discount" class="form-label fw-bold">Maximum Discount Amount</label>
                            <input type="number" step="0.01" class="form-control rounded-3" id="max_discount" name="max_discount" value="{{ old('max_discount', $coupon->max_discount) }}">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="usage_limit" class="form-label fw-bold">Usage Limit</label>
                            <input type="number" class="form-control rounded-3" id="usage_limit" name="usage_limit" value="{{ old('usage_limit', $coupon->usage_limit) }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="per_user_limit" class="form-label fw-bold">Per User Limit</label>
                            <input type="number" class="form-control rounded-3" id="per_user_limit" name="per_user_limit" value="{{ old('per_user_limit', $coupon->per_user_limit) }}">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="start_date" class="form-label fw-bold">Start Date</label>
                            <input type="date" class="form-control rounded-3" id="start_date" name="start_date" value="{{ old('start_date', $coupon->start_date ? $coupon->start_date->format('Y-m-d') : '') }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="end_date" class="form-label fw-bold">End Date</label>
                            <input type="date" class="form-control rounded-3" id="end_date" name="end_date" value="{{ old('end_date', $coupon->end_date ? $coupon->end_date->format('Y-m-d') : '') }}">
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="status" name="status" value="1" {{ old('status', $coupon->status) ? 'checked' : '' }}>
                        <label class="form-check-label fw-bold" for="status">Active Status</label>
                    </div>
                </div>

                <button type="submit" class="btn-update">
                    <i class="fas fa-save me-2"></i>Update Coupon
                </button>
            </form>
        </div>
    </div>
</div>
@endsection