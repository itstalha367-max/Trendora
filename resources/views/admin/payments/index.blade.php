@extends('layouts.admin')

@section('title', 'Payment Settings')

@section('content')
<style>
    .payment-card {
        background: #111722;
        border-radius: 20px;
        padding: 25px;
        border: 1px solid rgba(0,0,0,0.04);
        transition: all 0.3s;
        animation: slideUp 0.5s ease-out forwards;
        opacity: 0;
    }
    .payment-card:nth-child(1) { animation-delay: 0.1s; }
    .payment-card:nth-child(2) { animation-delay: 0.2s; }
    
    @keyframes slideUp {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .payment-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 30px rgba(0,0,0,0.06);
    }
    
    .payment-card .card-header {
        background: transparent;
        border-bottom: 2px solid #1a2230;
        padding: 0 0 15px;
        margin-bottom: 20px;
    }
    
    .payment-card .card-header h5 {
        font-weight: 700;
        margin: 0;
    }
    
    .payment-card .card-header .gateway-icon {
        font-size: 32px;
        margin-right: 15px;
    }
    
    .form-control, .form-select {
        border-radius: 10px;
        border: 2px solid #273142;
        padding: 10px 15px;
        transition: all 0.3s;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: #8b5cf6;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }
    
    .form-label {
        font-weight: 600;
        font-size: 13px;
        color: #151d2a;
    }
    
    .btn-save {
        background: linear-gradient(135deg, #8b5cf6, #5b7cff);
        color: #fff;
        border: none;
        padding: 12px 35px;
        border-radius: 12px;
        font-weight: 600;
        transition: all 0.3s;
    }
    
    .btn-save:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        color: #fff;
    }
    
    .btn-test {
        border: 2px solid #8b5cf6;
        color: #8b5cf6;
        background: transparent;
        padding: 8px 20px;
        border-radius: 10px;
        font-weight: 600;
        transition: all 0.3s;
    }
    
    .btn-test:hover {
        background: #8b5cf6;
        color: #fff;
    }
    
    .toggle-switch {
        position: relative;
        width: 50px;
        height: 26px;
        background: #273142;
        border-radius: 50px;
        cursor: pointer;
        transition: all 0.3s;
    }
    
    .toggle-switch.active {
        background: #8b5cf6;
    }
    
    .toggle-switch .slider {
        position: absolute;
        top: 3px;
        left: 3px;
        width: 20px;
        height: 20px;
        background: #111722;
        border-radius: 50%;
        transition: all 0.3s;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .toggle-switch.active .slider {
        left: 27px;
    }
    
    .toggle-input {
        display: none;
    }
    
    .badge-gateway {
        padding: 4px 15px;
        border-radius: 50px;
        font-size: 12px;
        font-weight: 700;
    }
    
    .badge-gateway.enabled { background: rgba(16,185,129,.12); color: #10b981; }
    .badge-gateway.disabled { background: #ef444420; color: #ef4444; }
</style>

<div class="settings-header">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0"><i class="fas fa-credit-card me-2 text-primary"></i>Payment Settings</h2>
            <p class="text-muted mb-0">Configure gateways and run safe read-only connection tests. Tests never create a charge.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 rounded-4">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show border-0 rounded-4">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form action="{{ route('admin.payments.update') }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row g-4">
            <!-- Stripe -->
            <div class="col-md-6">
                <div class="payment-card">
                    <div class="card-header">
                        <div class="d-flex align-items-center">
                            <span class="gateway-icon">💳</span>
                            <div>
                                <h5>Stripe</h5>
                                <span class="badge-gateway {{ $gateways['stripe']['enabled'] ? 'enabled' : 'disabled' }}">
                                    {{ $gateways['stripe']['enabled'] ? '✅ Enabled' : '❌ Disabled' }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label d-flex justify-content-between align-items-center">
                                <span>Enable Stripe</span>
                                <label class="toggle-switch {{ $gateways['stripe']['enabled'] ? 'active' : '' }}">
                                    <input type="checkbox" class="toggle-input" name="stripe_enabled" value="on" 
                                        {{ $gateways['stripe']['enabled'] ? 'checked' : '' }} 
                                        onchange="this.parentElement.classList.toggle('active')">
                                    <span class="slider"></span>
                                </label>
                            </label>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Publishable Key</label>
                            <input type="text" class="form-control" name="stripe_key" 
                                value="{{ $gateways['stripe']['key'] }}" 
                                placeholder="pk_test_xxxxxxxxxxxxx">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Secret Key</label>
                            <input type="password" class="form-control" name="stripe_secret" 
                                value="{{ $gateways['stripe']['secret'] }}" 
                                placeholder="sk_test_xxxxxxxxxxxxx">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Mode</label>
                            <select class="form-select" name="stripe_mode">
                                <option value="sandbox" {{ $gateways['stripe']['mode'] == 'sandbox' ? 'selected' : '' }}>
                                    Sandbox (Test)
                                </option>
                                <option value="live" {{ $gateways['stripe']['mode'] == 'live' ? 'selected' : '' }}>
                                    Live (Production)
                                </option>
                            </select>
                        </div>
                        
                        <button type="button" class="btn-test" onclick="testStripe()">
                            <i class="fas fa-play me-2"></i>Test Connection
                        </button>
                        <div id="stripeTestResult" class="mt-2" style="display: none;"></div>
                    </div>
                </div>
            </div>

            <!-- PayPal -->
            <div class="col-md-6">
                <div class="payment-card">
                    <div class="card-header">
                        <div class="d-flex align-items-center">
                            <span class="gateway-icon">💳</span>
                            <div>
                                <h5>PayPal</h5>
                                <span class="badge-gateway {{ $gateways['paypal']['enabled'] ? 'enabled' : 'disabled' }}">
                                    {{ $gateways['paypal']['enabled'] ? '✅ Enabled' : '❌ Disabled' }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label d-flex justify-content-between align-items-center">
                                <span>Enable PayPal</span>
                                <label class="toggle-switch {{ $gateways['paypal']['enabled'] ? 'active' : '' }}">
                                    <input type="checkbox" class="toggle-input" name="paypal_enabled" value="on" 
                                        {{ $gateways['paypal']['enabled'] ? 'checked' : '' }} 
                                        onchange="this.parentElement.classList.toggle('active')">
                                    <span class="slider"></span>
                                </label>
                            </label>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Client ID</label>
                            <input type="text" class="form-control" name="paypal_client_id" 
                                value="{{ $gateways['paypal']['client_id'] }}" 
                                placeholder="AWxxxxxxxxxxxxx">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Secret</label>
                            <input type="password" class="form-control" name="paypal_secret" 
                                value="{{ $gateways['paypal']['secret'] }}" 
                                placeholder="EIxxxxxxxxxxxxx">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Mode</label>
                            <select class="form-select" name="paypal_mode">
                                <option value="sandbox" {{ $gateways['paypal']['mode'] == 'sandbox' ? 'selected' : '' }}>
                                    Sandbox (Test)
                                </option>
                                <option value="live" {{ $gateways['paypal']['mode'] == 'live' ? 'selected' : '' }}>
                                    Live (Production)
                                </option>
                            </select>
                        </div>
                        
                        <button type="button" class="btn-test" onclick="testPayPal()">
                            <i class="fas fa-play me-2"></i>Test Connection
                        </button>
                        <div id="paypalTestResult" class="mt-2" style="display: none;"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4 text-end">
            <button type="submit" class="btn-save">
                <i class="fas fa-save me-2"></i>Save Payment Settings
            </button>
        </div>
    </form>
</div>

<script>
document.querySelectorAll('.toggle-input').forEach(input => {
    input.addEventListener('change', function() {
        this.parentElement.classList.toggle('active');
    });
});

function runGatewayTest(url, resultId, label) {
    const result = document.getElementById(resultId);
    result.style.display = 'block';
    result.innerHTML = '<div class="alert alert-info">Testing ' + label + ' authentication...</div>';
    fetch(url, {
        method: 'POST',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(async response => {
        const data = await response.json();
        if (!response.ok) throw new Error(data.message || 'Connection failed');
        return data;
    })
    .then(data => result.innerHTML = '<div class="alert alert-success">✅ ' + data.message + '</div>')
    .catch(error => result.innerHTML = '<div class="alert alert-danger">❌ ' + error.message + '</div>');
}

function testStripe() {
    runGatewayTest('{{ route("admin.payments.test-stripe") }}', 'stripeTestResult', 'Stripe');
}

function testPayPal() {
    runGatewayTest('{{ route("admin.payments.test-paypal") }}', 'paypalTestResult', 'PayPal');
}
</script>
@endsection