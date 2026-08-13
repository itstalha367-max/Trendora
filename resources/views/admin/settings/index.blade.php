@extends('layouts.admin')

@section('title', 'Settings')

@section('content')

<div class="ad-page-head"><div><span class="ad-eyebrow">Configuration hub</span><h2>General settings</h2><p>Use the focused workspaces below for store identity, checkout, shipping and tax behavior.</p></div></div>
<div class="ad-report-grid mb-4"><a class="ad-report-card" href="{{ route('admin.commerce-settings.store') }}"><span><i class="fa-solid fa-store"></i></span><div><small>Identity</small><h4>Store Details</h4><p>Business identity, currency and order numbering.</p></div><i class="fa-solid fa-arrow-right"></i></a><a class="ad-report-card" href="{{ route('admin.commerce-settings.checkout') }}"><span><i class="fa-solid fa-bag-shopping"></i></span><div><small>Conversion</small><h4>Checkout Settings</h4><p>Minimum order, payment choices, notes and terms.</p></div><i class="fa-solid fa-arrow-right"></i></a><a class="ad-report-card" href="{{ route('admin.commerce-settings.shipping') }}"><span><i class="fa-solid fa-truck-fast"></i></span><div><small>Fulfilment</small><h4>Shipping Settings</h4><p>Fallback delivery behavior and zone workspace.</p></div><i class="fa-solid fa-arrow-right"></i></a><a class="ad-report-card" href="{{ route('admin.commerce-settings.tax') }}"><span><i class="fa-solid fa-percent"></i></span><div><small>Compliance</small><h4>Tax Settings</h4><p>Tax presentation and jurisdiction-rate controls.</p></div><i class="fa-solid fa-arrow-right"></i></a></div>

<style>
    .settings-header { animation: slideDown 0.5s ease-out; }
    @keyframes slideDown {
        from { opacity: 0; transform: translateY(-20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .settings-card {
        background: #111722;
        border-radius: 20px;
        padding: 25px;
        border: 1px solid rgba(0,0,0,0.04);
        transition: all 0.3s;
        animation: slideUp 0.5s ease-out forwards;
        opacity: 0;
    }
    .settings-card:nth-child(1) { animation-delay: 0.1s; }
    .settings-card:nth-child(2) { animation-delay: 0.2s; }
    .settings-card:nth-child(3) { animation-delay: 0.3s; }
    .settings-card:nth-child(4) { animation-delay: 0.4s; }
    
    @keyframes slideUp {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .settings-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 30px rgba(0,0,0,0.06);
    }
    .settings-card .card-header {
        background: transparent;
        border-bottom: 2px solid #1a2230;
        padding: 0 0 15px;
        margin-bottom: 20px;
    }
    .settings-card .card-header h5 {
        font-weight: 700;
        margin: 0;
    }
    .settings-card .card-header small {
        color: #93a1b4;
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
    .logo-preview {
        width: 120px;
        height: 120px;
        border-radius: 16px;
        border: 2px solid #273142;
        object-fit: contain;
        background: #0f141e;
        padding: 10px;
    }
    .favicon-preview {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        border: 2px solid #273142;
        object-fit: contain;
        background: #0f141e;
        padding: 4px;
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
</style>

<div class="settings-header">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0"><i class="fas fa-cog me-2 text-primary"></i>System Settings</h2>
            <p class="text-muted mb-0">Configure your store settings</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.settings.reset') }}" class="btn btn-danger rounded-3" onclick="return confirm('Reset all settings to default?')">
                <i class="fas fa-undo me-2"></i>Reset
            </a>
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

    <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row g-4">
            <!-- General Settings -->
            <div class="col-md-6">
                <div class="settings-card">
                    <div class="card-header">
                        <h5><i class="fas fa-store me-2 text-primary"></i>General Settings</h5>
                        <small>Basic store information</small>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Store Name</label>
                            @php $storeName = App\Models\Setting::get('store_name', 'Trendora'); @endphp
                            <input type="text" class="form-control" name="store_name" value="{{ $storeName }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Store Email</label>
                            @php $storeEmail = App\Models\Setting::get('store_email', 'info@trendora.com'); @endphp
                            <input type="email" class="form-control" name="store_email" value="{{ $storeEmail }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Store Phone</label>
                            @php $storePhone = App\Models\Setting::get('store_phone', '+1 234 567 8900'); @endphp
                            <input type="text" class="form-control" name="store_phone" value="{{ $storePhone }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Store Address</label>
                            @php $storeAddress = App\Models\Setting::get('store_address', '123 Main Street, New York, USA'); @endphp
                            <textarea class="form-control" name="store_address" rows="2">{{ $storeAddress }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Store Description</label>
                            @php $storeDescription = App\Models\Setting::get('store_description', 'Your one-stop shop for everything!'); @endphp
                            <textarea class="form-control" name="store_description" rows="2">{{ $storeDescription }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Currency & Logo -->
            <div class="col-md-6">
                <div class="settings-card">
                    <div class="card-header">
                        <h5><i class="fas fa-money-bill me-2 text-success"></i>Currency & Branding</h5>
                        <small>Store currency and logo settings</small>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Currency</label>
                                    @php $currency = App\Models\Setting::get('currency', 'USD'); @endphp
                                    <input type="text" class="form-control" name="currency" value="{{ $currency }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Currency Symbol</label>
                                    @php $currencySymbol = App\Models\Setting::get('currency_symbol', '$'); @endphp
                                    <input type="text" class="form-control" name="currency_symbol" value="{{ $currencySymbol }}">
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Logo</label>
                            <input type="file" class="form-control" name="logo" accept="image/*">
                            @php $logo = App\Models\Setting::get('logo'); @endphp
                            @if($logo)
                                <div class="mt-2">
                                    <img src="{{ asset('storage/' . $logo) }}" alt="Logo" class="logo-preview">
                                </div>
                            @endif
                            <small class="text-muted">Recommended: 200x200px (JPG, PNG, SVG)</small>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Favicon</label>
                            <input type="file" class="form-control" name="favicon" accept="image/*">
                            @php $favicon = App\Models\Setting::get('favicon'); @endphp
                            @if($favicon)
                                <div class="mt-2">
                                    <img src="{{ asset('storage/' . $favicon) }}" alt="Favicon" class="favicon-preview">
                                </div>
                            @endif
                            <small class="text-muted">Recommended: 32x32px (ICO, PNG)</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Social Media -->
            <div class="col-md-6">
                <div class="settings-card">
                    <div class="card-header">
                        <h5><i class="fas fa-share-alt me-2 text-info"></i>Social Media</h5>
                        <small>Connect your social media accounts</small>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label"><i class="fab fa-facebook me-2 text-primary"></i>Facebook</label>
                            @php $facebook = App\Models\Setting::get('facebook', 'https://facebook.com/trendora'); @endphp
                            <input type="url" class="form-control" name="facebook" value="{{ $facebook }}" placeholder="https://facebook.com/yourpage">
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><i class="fab fa-instagram me-2 text-danger"></i>Instagram</label>
                            @php $instagram = App\Models\Setting::get('instagram', 'https://instagram.com/trendora'); @endphp
                            <input type="url" class="form-control" name="instagram" value="{{ $instagram }}" placeholder="https://instagram.com/yourpage">
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><i class="fab fa-twitter me-2 text-info"></i>Twitter</label>
                            @php $twitter = App\Models\Setting::get('twitter', 'https://twitter.com/trendora'); @endphp
                            <input type="url" class="form-control" name="twitter" value="{{ $twitter }}" placeholder="https://twitter.com/yourpage">
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><i class="fab fa-youtube me-2 text-danger"></i>YouTube</label>
                            @php $youtube = App\Models\Setting::get('youtube', 'https://youtube.com/trendora'); @endphp
                            <input type="url" class="form-control" name="youtube" value="{{ $youtube }}" placeholder="https://youtube.com/yourchannel">
                        </div>
                    </div>
                </div>
            </div>

            <!-- System Settings -->
            <div class="col-md-6">
                <div class="settings-card">
                    <div class="card-header">
                        <h5><i class="fas fa-server me-2 text-warning"></i>System Settings</h5>
                        <small>System configuration options</small>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Theme</label>
                            @php $theme = App\Models\Setting::get('theme', 'light'); @endphp
                            <select class="form-select" name="theme">
                                <option value="light" {{ $theme == 'light' ? 'selected' : '' }}>Light</option>
                                <option value="dark" {{ $theme == 'dark' ? 'selected' : '' }}>Dark</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label d-flex justify-content-between align-items-center">
                                <span>Maintenance Mode</span>
                                @php $maintenance = App\Models\Setting::get('maintenance_mode', 'off'); @endphp
                                <label class="toggle-switch {{ $maintenance == 'on' ? 'active' : '' }}">
                                    <input type="checkbox" class="toggle-input" name="maintenance_mode" value="on" {{ $maintenance == 'on' ? 'checked' : '' }} onchange="this.parentElement.classList.toggle('active')">
                                    <span class="slider"></span>
                                </label>
                            </label>
                            <small class="text-muted">Enable maintenance mode for visitors</small>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label d-flex justify-content-between align-items-center">
                                <span>Registration Enabled</span>
                                @php $registration = App\Models\Setting::get('registration_enabled', 'on'); @endphp
                                <label class="toggle-switch {{ $registration == 'on' ? 'active' : '' }}">
                                    <input type="checkbox" class="toggle-input" name="registration_enabled" value="on" {{ $registration == 'on' ? 'checked' : '' }} onchange="this.parentElement.classList.toggle('active')">
                                    <span class="slider"></span>
                                </label>
                            </label>
                            <small class="text-muted">Allow new user registration</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4 text-end">
            <button type="submit" class="btn-save">
                <i class="fas fa-save me-2"></i>Save Settings
            </button>
        </div>
    </form>
</div>

<script>
// Toggle switch animation
document.querySelectorAll('.toggle-input').forEach(input => {
    input.addEventListener('change', function() {
        this.parentElement.classList.toggle('active');
    });
});
</script>
@endsection