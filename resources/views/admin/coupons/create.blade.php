@extends('layouts.admin')

@section('title', 'Add Coupon')

@section('content')
<style>
    .form-card { animation: slideUp 0.5s ease-out; }
    @keyframes slideUp {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .btn-submit {
        background: linear-gradient(135deg, #f59e0b, #f9a825);
        color: #fff;
        border: none;
        padding: 12px 30px;
        border-radius: 12px;
        font-weight: 600;
        transition: all 0.3s;
    }
    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(253, 203, 110, 0.4);
        color: #fff;
    }
</style>

<div class="form-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0"><i class="fas fa-plus-circle me-2 text-warning"></i>Add New Coupon</h2>
            <p class="text-muted mb-0">Create a new discount coupon for your store</p>
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
            <form action="{{ route('admin.coupons.store') }}" method="POST">
                @csrf
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="code" class="form-label fw-bold">Coupon Code *</label>
                            <input type="text" class="form-control rounded-3 text-uppercase" id="code" name="code" value="{{ old('code') }}" placeholder="e.g. SUMMER2026" required>
                            <small class="text-muted">Unique code, will be automatically uppercase</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="name" class="form-label fw-bold">Coupon Name</label>
                            <input type="text" class="form-control rounded-3" id="name" name="name" value="{{ old('name') }}" placeholder="e.g. Summer Sale 2026">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="type" class="form-label fw-bold">Discount Type *</label>
                            <select class="form-control rounded-3" id="type" name="type" required>
                                <option value="percentage" {{ old('type') == 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
                                <option value="fixed" {{ old('type') == 'fixed' ? 'selected' : '' }}>Fixed Amount ($)</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="value" class="form-label fw-bold">Discount Value *</label>
                            <input type="number" step="0.01" class="form-control rounded-3" id="value" name="value" value="{{ old('value') }}" placeholder="e.g. 20 for 20% or a fixed amount" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="min_order" class="form-label fw-bold">Minimum Order Amount</label>
                            <input type="number" step="0.01" class="form-control rounded-3" id="min_order" name="min_order" value="{{ old('min_order', 0) }}" placeholder="e.g. 50">
                            <small class="text-muted">Leave 0 for no minimum</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="max_discount" class="form-label fw-bold">Maximum Discount Amount</label>
                            <input type="number" step="0.01" class="form-control rounded-3" id="max_discount" name="max_discount" value="{{ old('max_discount') }}" placeholder="e.g. 100">
                            <small class="text-muted">For percentage coupons only</small>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="usage_limit" class="form-label fw-bold">Usage Limit</label>
                            <input type="number" class="form-control rounded-3" id="usage_limit" name="usage_limit" value="{{ old('usage_limit') }}" placeholder="e.g. 100">
                            <small class="text-muted">Total times coupon can be used</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="per_user_limit" class="form-label fw-bold">Per User Limit</label>
                            <input type="number" class="form-control rounded-3" id="per_user_limit" name="per_user_limit" value="{{ old('per_user_limit') }}" placeholder="e.g. 1">
                            <small class="text-muted">How many times per user</small>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="start_date" class="form-label fw-bold">Start Date</label>
                            <input type="date" class="form-control rounded-3" id="start_date" name="start_date" value="{{ old('start_date') }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="end_date" class="form-label fw-bold">End Date</label>
                            <input type="date" class="form-control rounded-3" id="end_date" name="end_date" value="{{ old('end_date') }}">
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="status" name="status" value="1" {{ old('status', true) ? 'checked' : '' }}>
                        <label class="form-check-label fw-bold" for="status">Active Status</label>
                    </div>
                </div>

                <button type="submit" class="btn-submit">
                    <i class="fas fa-save me-2"></i>Create Coupon
                </button>
            </form>
        </div>
    </div>
</div>
@endsection