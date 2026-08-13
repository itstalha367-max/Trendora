@extends('layouts.admin')

@section('title', 'SEO Settings')

@section('content')
<style>
    .page-header { animation: slideDown 0.5s ease-out; }
    @keyframes slideDown {
        from { opacity: 0; transform: translateY(-20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .seo-card {
        background: #111722;
        border-radius: 20px;
        padding: 25px;
        border: 1px solid rgba(0,0,0,0.04);
        transition: all 0.3s;
        margin-bottom: 20px;
    }
    .seo-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 30px rgba(0,0,0,0.06);
    }
    .seo-card .card-header {
        border-bottom: 2px solid #1a2230;
        padding-bottom: 15px;
        margin-bottom: 20px;
    }
    .seo-card .card-header h5 {
        font-weight: 700;
        margin: 0;
    }
    .seo-card .card-header .badge-page {
        padding: 4px 12px;
        border-radius: 50px;
        font-size: 11px;
        background: rgba(139,92,246,.12);
        color: #8b5cf6;
        font-weight: 600;
    }
    .form-control, .form-select {
        border-radius: 10px;
        border: 2px solid #273142;
        padding: 10px 15px;
        font-size: 14px;
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
        padding: 10px 25px;
        border-radius: 10px;
        font-weight: 600;
        transition: all 0.3s;
    }
    .btn-save:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        color: #fff;
    }
    .btn-reset {
        border: 2px solid #ef4444;
        color: #ef4444;
        background: transparent;
        padding: 8px 18px;
        border-radius: 10px;
        font-weight: 600;
        transition: all 0.3s;
    }
    .btn-reset:hover {
        background: #ef4444;
        color: #fff;
    }
    .og-image-preview {
        width: 120px;
        height: 63px;
        border-radius: 8px;
        border: 2px solid #273142;
        object-fit: cover;
        margin-top: 10px;
    }
    .json-editor {
        font-family: 'Courier New', monospace;
        font-size: 13px;
        background: #151d2a;
        color: #cbd5e1;
        min-height: 120px;
    }
</style>

<div class="page-header">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0"><i class="fas fa-search me-2 text-primary"></i>SEO Settings</h2>
            <p class="text-muted mb-0">Optimize your website for search engines</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.seo.sitemap') }}" class="btn btn-success rounded-3">
                <i class="fas fa-sitemap me-2"></i>Generate Sitemap
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
</div>

<div class="row">
    <!-- Home Page -->
    <div class="col-md-6">
        <div class="seo-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5>Home Page</h5>
                <span class="badge-page">HOME</span>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.seo.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="page" value="home">
                    
                    <div class="mb-3">
                        <label class="form-label">Meta Title</label>
                        <input type="text" class="form-control" name="meta_title" 
                            value="{{ old('meta_title', $seoSettings['home']->meta_title ?? '') }}" 
                            placeholder="Page title for search results">
                        <small class="text-muted">Recommended: 50-60 characters</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Meta Description</label>
                        <textarea class="form-control" name="meta_description" rows="2" 
                            placeholder="Page description for search results">{{ old('meta_description', $seoSettings['home']->meta_description ?? '') }}</textarea>
                        <small class="text-muted">Recommended: 150-160 characters</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Meta Keywords</label>
                        <input type="text" class="form-control" name="meta_keywords" 
                            value="{{ old('meta_keywords', $seoSettings['home']->meta_keywords ?? '') }}" 
                            placeholder="Comma separated keywords">
                    </div>

                    <hr>

                    <div class="mb-3">
                        <label class="form-label">Robots</label>
                        <select class="form-select" name="robots">
                            <option value="index, follow" {{ (old('robots', $seoSettings['home']->robots ?? '') == 'index, follow') ? 'selected' : '' }}>Index, Follow</option>
                            <option value="noindex, follow" {{ (old('robots', $seoSettings['home']->robots ?? '') == 'noindex, follow') ? 'selected' : '' }}>No Index, Follow</option>
                            <option value="index, nofollow" {{ (old('robots', $seoSettings['home']->robots ?? '') == 'index, nofollow') ? 'selected' : '' }}>Index, No Follow</option>
                            <option value="noindex, nofollow" {{ (old('robots', $seoSettings['home']->robots ?? '') == 'noindex, nofollow') ? 'selected' : '' }}>No Index, No Follow</option>
                        </select>
                    </div>

                    <div class="d-flex gap-2 mt-3">
                        <button type="submit" class="btn-save">
                            <i class="fas fa-save me-2"></i>Save
                        </button>
                        <form method="POST" action="{{ route('admin.seo.reset', 'home') }}" class="d-inline" onsubmit="return confirm('Reset SEO settings for Home Page?')">@csrf<button type="submit" class="btn-reset">
                            <i class="fas fa-undo me-2"></i>Reset
                        </button></form>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Products Page -->
    <div class="col-md-6">
        <div class="seo-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5>Products Page</h5>
                <span class="badge-page">PRODUCTS</span>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.seo.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="page" value="products">
                    
                    <div class="mb-3">
                        <label class="form-label">Meta Title</label>
                        <input type="text" class="form-control" name="meta_title" 
                            value="{{ old('meta_title', $seoSettings['products']->meta_title ?? '') }}" 
                            placeholder="Page title for search results">
                        <small class="text-muted">Recommended: 50-60 characters</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Meta Description</label>
                        <textarea class="form-control" name="meta_description" rows="2" 
                            placeholder="Page description for search results">{{ old('meta_description', $seoSettings['products']->meta_description ?? '') }}</textarea>
                        <small class="text-muted">Recommended: 150-160 characters</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Meta Keywords</label>
                        <input type="text" class="form-control" name="meta_keywords" 
                            value="{{ old('meta_keywords', $seoSettings['products']->meta_keywords ?? '') }}" 
                            placeholder="Comma separated keywords">
                    </div>

                    <hr>

                    <div class="mb-3">
                        <label class="form-label">Robots</label>
                        <select class="form-select" name="robots">
                            <option value="index, follow" {{ (old('robots', $seoSettings['products']->robots ?? '') == 'index, follow') ? 'selected' : '' }}>Index, Follow</option>
                            <option value="noindex, follow" {{ (old('robots', $seoSettings['products']->robots ?? '') == 'noindex, follow') ? 'selected' : '' }}>No Index, Follow</option>
                            <option value="index, nofollow" {{ (old('robots', $seoSettings['products']->robots ?? '') == 'index, nofollow') ? 'selected' : '' }}>Index, No Follow</option>
                            <option value="noindex, nofollow" {{ (old('robots', $seoSettings['products']->robots ?? '') == 'noindex, nofollow') ? 'selected' : '' }}>No Index, No Follow</option>
                        </select>
                    </div>

                    <div class="d-flex gap-2 mt-3">
                        <button type="submit" class="btn-save">
                            <i class="fas fa-save me-2"></i>Save
                        </button>
                        <form method="POST" action="{{ route('admin.seo.reset', 'products') }}" class="d-inline" onsubmit="return confirm('Reset SEO settings for Products Page?')">@csrf<button type="submit" class="btn-reset">
                            <i class="fas fa-undo me-2"></i>Reset
                        </button></form>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- About Page -->
    <div class="col-md-6">
        <div class="seo-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5>About Page</h5>
                <span class="badge-page">ABOUT</span>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.seo.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="page" value="about">
                    
                    <div class="mb-3">
                        <label class="form-label">Meta Title</label>
                        <input type="text" class="form-control" name="meta_title" 
                            value="{{ old('meta_title', $seoSettings['about']->meta_title ?? '') }}" 
                            placeholder="Page title for search results">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Meta Description</label>
                        <textarea class="form-control" name="meta_description" rows="2" 
                            placeholder="Page description for search results">{{ old('meta_description', $seoSettings['about']->meta_description ?? '') }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Meta Keywords</label>
                        <input type="text" class="form-control" name="meta_keywords" 
                            value="{{ old('meta_keywords', $seoSettings['about']->meta_keywords ?? '') }}" 
                            placeholder="Comma separated keywords">
                    </div>

                    <hr>

                    <div class="mb-3">
                        <label class="form-label">Robots</label>
                        <select class="form-select" name="robots">
                            <option value="index, follow" {{ (old('robots', $seoSettings['about']->robots ?? '') == 'index, follow') ? 'selected' : '' }}>Index, Follow</option>
                            <option value="noindex, follow" {{ (old('robots', $seoSettings['about']->robots ?? '') == 'noindex, follow') ? 'selected' : '' }}>No Index, Follow</option>
                            <option value="index, nofollow" {{ (old('robots', $seoSettings['about']->robots ?? '') == 'index, nofollow') ? 'selected' : '' }}>Index, No Follow</option>
                            <option value="noindex, nofollow" {{ (old('robots', $seoSettings['about']->robots ?? '') == 'noindex, nofollow') ? 'selected' : '' }}>No Index, No Follow</option>
                        </select>
                    </div>

                    <div class="d-flex gap-2 mt-3">
                        <button type="submit" class="btn-save">
                            <i class="fas fa-save me-2"></i>Save
                        </button>
                        <form method="POST" action="{{ route('admin.seo.reset', 'about') }}" class="d-inline" onsubmit="return confirm('Reset SEO settings for About Page?')">@csrf<button type="submit" class="btn-reset">
                            <i class="fas fa-undo me-2"></i>Reset
                        </button></form>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Contact Page -->
    <div class="col-md-6">
        <div class="seo-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5>Contact Page</h5>
                <span class="badge-page">CONTACT</span>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.seo.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="page" value="contact">
                    
                    <div class="mb-3">
                        <label class="form-label">Meta Title</label>
                        <input type="text" class="form-control" name="meta_title" 
                            value="{{ old('meta_title', $seoSettings['contact']->meta_title ?? '') }}" 
                            placeholder="Page title for search results">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Meta Description</label>
                        <textarea class="form-control" name="meta_description" rows="2" 
                            placeholder="Page description for search results">{{ old('meta_description', $seoSettings['contact']->meta_description ?? '') }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Meta Keywords</label>
                        <input type="text" class="form-control" name="meta_keywords" 
                            value="{{ old('meta_keywords', $seoSettings['contact']->meta_keywords ?? '') }}" 
                            placeholder="Comma separated keywords">
                    </div>

                    <hr>

                    <div class="mb-3">
                        <label class="form-label">Robots</label>
                        <select class="form-select" name="robots">
                            <option value="index, follow" {{ (old('robots', $seoSettings['contact']->robots ?? '') == 'index, follow') ? 'selected' : '' }}>Index, Follow</option>
                            <option value="noindex, follow" {{ (old('robots', $seoSettings['contact']->robots ?? '') == 'noindex, follow') ? 'selected' : '' }}>No Index, Follow</option>
                            <option value="index, nofollow" {{ (old('robots', $seoSettings['contact']->robots ?? '') == 'index, nofollow') ? 'selected' : '' }}>Index, No Follow</option>
                            <option value="noindex, nofollow" {{ (old('robots', $seoSettings['contact']->robots ?? '') == 'noindex, nofollow') ? 'selected' : '' }}>No Index, No Follow</option>
                        </select>
                    </div>

                    <div class="d-flex gap-2 mt-3">
                        <button type="submit" class="btn-save">
                            <i class="fas fa-save me-2"></i>Save
                        </button>
                        <form method="POST" action="{{ route('admin.seo.reset', 'contact') }}" class="d-inline" onsubmit="return confirm('Reset SEO settings for Contact Page?')">@csrf<button type="submit" class="btn-reset">
                            <i class="fas fa-undo me-2"></i>Reset
                        </button></form>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Blog Page -->
    <div class="col-md-6">
        <div class="seo-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5>Blog Page</h5>
                <span class="badge-page">BLOG</span>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.seo.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="page" value="blog">
                    
                    <div class="mb-3">
                        <label class="form-label">Meta Title</label>
                        <input type="text" class="form-control" name="meta_title" 
                            value="{{ old('meta_title', $seoSettings['blog']->meta_title ?? '') }}" 
                            placeholder="Page title for search results">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Meta Description</label>
                        <textarea class="form-control" name="meta_description" rows="2" 
                            placeholder="Page description for search results">{{ old('meta_description', $seoSettings['blog']->meta_description ?? '') }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Meta Keywords</label>
                        <input type="text" class="form-control" name="meta_keywords" 
                            value="{{ old('meta_keywords', $seoSettings['blog']->meta_keywords ?? '') }}" 
                            placeholder="Comma separated keywords">
                    </div>

                    <hr>

                    <div class="mb-3">
                        <label class="form-label">Robots</label>
                        <select class="form-select" name="robots">
                            <option value="index, follow" {{ (old('robots', $seoSettings['blog']->robots ?? '') == 'index, follow') ? 'selected' : '' }}>Index, Follow</option>
                            <option value="noindex, follow" {{ (old('robots', $seoSettings['blog']->robots ?? '') == 'noindex, follow') ? 'selected' : '' }}>No Index, Follow</option>
                            <option value="index, nofollow" {{ (old('robots', $seoSettings['blog']->robots ?? '') == 'index, nofollow') ? 'selected' : '' }}>Index, No Follow</option>
                            <option value="noindex, nofollow" {{ (old('robots', $seoSettings['blog']->robots ?? '') == 'noindex, nofollow') ? 'selected' : '' }}>No Index, No Follow</option>
                        </select>
                    </div>

                    <div class="d-flex gap-2 mt-3">
                        <button type="submit" class="btn-save">
                            <i class="fas fa-save me-2"></i>Save
                        </button>
                        <form method="POST" action="{{ route('admin.seo.reset', 'blog') }}" class="d-inline" onsubmit="return confirm('Reset SEO settings for Blog Page?')">@csrf<button type="submit" class="btn-reset">
                            <i class="fas fa-undo me-2"></i>Reset
                        </button></form>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.querySelectorAll('.json-editor').forEach(textarea => {
    textarea.addEventListener('blur', function() {
        try {
            if (this.value.trim()) {
                JSON.parse(this.value);
                this.style.borderColor = '#10b981';
            }
        } catch (e) {
            this.style.borderColor = '#ef4444';
        }
    });
});
</script>
@endsection