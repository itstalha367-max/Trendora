@extends('layouts.admin')

@section('title', 'Edit Product')

@section('content')
<style>
    .form-card { animation: slideUp 0.5s ease-out; }
    @keyframes slideUp {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .btn-update {
        background: linear-gradient(135deg, #10b981, #059669);
        color: #fff;
        border: none;
        padding: 12px 30px;
        border-radius: 12px;
        font-weight: 600;
        transition: all 0.3s;
    }
    .btn-update:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 184, 148, 0.4);
        color: #fff;
    }
    .image-preview-container {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 10px;
    }
    .image-preview {
        width: 100px;
        height: 100px;
        border-radius: 10px;
        overflow: hidden;
        border: 2px solid #2d3748;
        position: relative;
    }
    .image-preview img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .image-preview .remove-btn {
        position: absolute;
        top: 5px;
        right: 5px;
        background: #ef4444;
        color: #fff;
        border: none;
        border-radius: 50%;
        width: 25px;
        height: 25px;
        font-size: 12px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .image-preview .thumbnail-badge {
        position: absolute;
        bottom: 5px;
        left: 5px;
        background: #8b5cf6;
        color: #fff;
        padding: 2px 8px;
        border-radius: 4px;
        font-size: 10px;
        font-weight: 700;
    }
    .image-preview .set-thumbnail {
        position: absolute;
        bottom: 5px;
        right: 5px;
        background: #10b981;
        color: #fff;
        border: none;
        border-radius: 4px;
        padding: 2px 8px;
        font-size: 10px;
        cursor: pointer;
    }
</style>

<div class="form-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0"><i class="fas fa-edit me-2 text-warning"></i>Edit Product</h2>
            <p class="text-muted mb-0">Update product details</p>
        </div>
        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary rounded-3">
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
            <form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="name" class="form-label fw-bold">Product Name *</label>
                            <input type="text" class="form-control rounded-3" id="name" name="name" value="{{ old('name', $product->name) }}" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="category_id" class="form-label fw-bold">Category *</label>
                            <select class="form-control rounded-3" id="category_id" name="category_id" required>
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="brand_id" class="form-label fw-bold">Brand</label>
                            <select class="form-control rounded-3" id="brand_id" name="brand_id">
                                <option value="">No Brand</option>
                                @foreach($brands as $brand)
                                    <option value="{{ $brand->id }}" {{ old('brand_id', $product->brand_id) == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="mt-3">
                    <a href="{{ route('admin.products.variations.index', $product->id) }}" class="btn btn-info rounded-3">
                        <i class="fas fa-layer-group me-2"></i>Manage Variations
                        @if($product->hasVariations)
                            <span class="badge bg-white text-dark ms-2">{{ $product->variations->count() }}</span>
                        @endif
                    </a>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label fw-bold">Description *</label>
                    <textarea class="form-control rounded-3" id="description" name="description" rows="4" required>{{ old('description', $product->description) }}</textarea>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="price" class="form-label fw-bold">Price ($) *</label>
                            <input type="number" step="0.01" class="form-control rounded-3" id="price" name="price" value="{{ old('price', $product->price) }}" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="compare_price" class="form-label fw-bold">Compare Price ($)</label>
                            <input type="number" step="0.01" class="form-control rounded-3" id="compare_price" name="compare_price" value="{{ old('compare_price', $product->compare_price) }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="stock_quantity" class="form-label fw-bold">Stock Quantity *</label>
                            <input type="number" class="form-control rounded-3" id="stock_quantity" name="stock_quantity" value="{{ old('stock_quantity', $product->stock_quantity) }}" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="sku" class="form-label fw-bold">SKU</label>
                            <input type="text" class="form-control rounded-3" id="sku" name="sku" value="{{ old('sku', $product->sku) }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="featured" name="featured" value="1" {{ old('featured', $product->featured) ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold" for="featured">Featured Product</label>
                            </div>
                            <div class="form-check mt-2">
                                <input type="checkbox" class="form-check-input" id="status" name="status" value="1" {{ old('status', $product->status) ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold" for="status">Active Status</label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 🔥 Existing Images -->
                @if($product->images && count($product->images) > 0)
                <div class="mb-3">
                    <label class="form-label fw-bold">Current Images</label>
                    <div class="image-preview-container">
                        @foreach($product->images as $index => $image)
                        <div class="image-preview" id="image-{{ $index }}">
                            <img src="{{ asset('storage/' . $image) }}" alt="Product Image">
                            @if($product->thumbnail == $image)
                                <span class="thumbnail-badge">⭐ Thumbnail</span>
                            @endif
                            <button type="button" class="remove-btn" onclick="markForDelete({{ $index }})">×</button>
                            @if($product->thumbnail != $image)
                                <button type="button" class="set-thumbnail" onclick="setThumbnail({{ $index }})">Set as Thumbnail</button>
                            @endif
                        </div>
                        <!-- 🔥 Variations Management -->
<div class="card border-0 shadow-sm rounded-4 mt-4">
    <div class="card-body d-flex justify-content-between align-items-center">
        <div>
            <h5 class="fw-bold mb-0">
                <i class="fas fa-layer-group me-2 text-primary"></i>Product Variations
            </h5>
            <small class="text-muted">Manage size, color, and other variations</small>
        </div>
        <div>
            @if($product->hasVariations)
                <span class="badge bg-info rounded-pill px-3 py-2 me-2">
                    {{ $product->variations->count() }} Variations
                </span>
            @endif
            <a href="{{ route('admin.products.variations.index', $product->id) }}" class="btn btn-primary rounded-3">
                <i class="fas fa-layer-group me-2"></i>Manage Variations
            </a>
        </div>
    </div>
</div>
                        @endforeach
                    </div>
                    <input type="hidden" name="delete_images" id="deleteImages" value="">
                    <input type="hidden" name="thumbnail_index" id="thumbnailIndex" value="">
                </div>
                @endif

                <!-- 🔥 Upload New Images -->
                <div class="mb-3">
                    <label for="images" class="form-label fw-bold">Upload New Images</label>
                    <input type="file" class="form-control rounded-3" id="images" name="images[]" multiple accept="image/*" onchange="previewNewImages(event)">
                    <small class="text-muted">Upload multiple images (JPG, PNG, GIF) - Max 2MB each</small>
                    <div class="image-preview-container" id="newImagePreview"></div>
                </div>

                <button type="submit" class="btn-update">
                    <i class="fas fa-save me-2"></i>Update Product
                </button>
            </form>
        </div>
    </div>
</div>

<script>
let deleteIndexes = [];

function markForDelete(index) {
    if (!confirm('Remove this image?')) return;
    deleteIndexes.push(index);
    document.getElementById('deleteImages').value = deleteIndexes.join(',');
    document.getElementById('image-' + index).style.opacity = '0.3';
    document.getElementById('image-' + index).style.borderColor = '#ef4444';
}

function setThumbnail(index) {
    document.getElementById('thumbnailIndex').value = index;
    // Visual feedback
    document.querySelectorAll('.image-preview').forEach(el => {
        el.querySelector('.thumbnail-badge')?.remove();
    });
    const el = document.getElementById('image-' + index);
    const badge = document.createElement('span');
    badge.className = 'thumbnail-badge';
    badge.textContent = '⭐ Thumbnail';
    el.appendChild(badge);
}

function previewNewImages(event) {
    const container = document.getElementById('newImagePreview');
    container.innerHTML = '';
    
    const files = event.target.files;
    for (let i = 0; i < files.length; i++) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const div = document.createElement('div');
            div.className = 'image-preview';
            div.innerHTML = `<img src="${e.target.result}" alt="Preview">`;
            container.appendChild(div);
        };
        reader.readAsDataURL(files[i]);
    }
}
</script>
@endsection