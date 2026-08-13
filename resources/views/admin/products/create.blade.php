@extends('layouts.admin')

@section('title', 'Add Product')

@section('content')
<style>
    .form-card { animation: slideUp 0.5s ease-out; }
    @keyframes slideUp {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .btn-submit {
        background: linear-gradient(135deg, #8b5cf6, #5b7cff);
        color: #fff;
        border: none;
        padding: 12px 30px;
        border-radius: 12px;
        font-weight: 600;
        transition: all 0.3s;
    }
    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
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
</style>

<div class="form-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0"><i class="fas fa-plus-circle me-2 text-primary"></i>Add New Product</h2>
            <p class="text-muted mb-0">Create a new product for your store</p>
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
            <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="name" class="form-label fw-bold">Product Name *</label>
                            <input type="text" class="form-control rounded-3" id="name" name="name" value="{{ old('name') }}" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="category_id" class="form-label fw-bold">Category *</label>
                            <select class="form-control rounded-3" id="category_id" name="category_id" required>
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
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
                                    <option value="{{ $brand->id }}" {{ old('brand_id') == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label fw-bold">Description *</label>
                    <textarea class="form-control rounded-3" id="description" name="description" rows="4" required>{{ old('description') }}</textarea>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="price" class="form-label fw-bold">Price ($) *</label>
                            <input type="number" step="0.01" class="form-control rounded-3" id="price" name="price" value="{{ old('price') }}" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="compare_price" class="form-label fw-bold">Compare Price ($)</label>
                            <input type="number" step="0.01" class="form-control rounded-3" id="compare_price" name="compare_price" value="{{ old('compare_price') }}">
                            <small class="text-muted">Original price for sale display</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="stock_quantity" class="form-label fw-bold">Stock Quantity *</label>
                            <input type="number" class="form-control rounded-3" id="stock_quantity" name="stock_quantity" value="{{ old('stock_quantity', 0) }}" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="sku" class="form-label fw-bold">SKU</label>
                            <input type="text" class="form-control rounded-3" id="sku" name="sku" value="{{ old('sku') }}">
                            <small class="text-muted">Stock Keeping Unit (unique identifier)</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="featured" name="featured" value="1" {{ old('featured') ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold" for="featured">Featured Product</label>
                            </div>
                            <div class="form-check mt-2">
                                <input type="checkbox" class="form-check-input" id="status" name="status" value="1" {{ old('status', true) ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold" for="status">Active Status</label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 🔥 Image Upload Section -->
                <div class="mb-3">
                    <label for="images" class="form-label fw-bold">Product Images</label>
                    <input type="file" class="form-control rounded-3" id="images" name="images[]" multiple accept="image/*" onchange="previewImages(event)">
                    <small class="text-muted">Upload multiple images (JPG, PNG, GIF) - Max 2MB each</small>
                    <div class="image-preview-container" id="imagePreviewContainer"></div>
                </div>
                <!-- 🔥 Variations Section -->
<div class="mb-4">
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-light">
            <h5 class="mb-0 fw-bold">
                <i class="fas fa-layer-group me-2 text-primary"></i>Product Variations
                <span class="badge bg-info text-white ms-2">Optional</span>
            </h5>
            <small class="text-muted">Add size, color, or other variations</small>
        </div>
        <div class="card-body">
            <div id="variations-container">
                <div class="variation-row row g-3 mb-3">
                    <div class="col-md-3">
                        <label class="form-label fw-bold small">Attribute Name</label>
                        <select class="form-select variation-attribute" name="variations[0][attribute_name]">
                            <option value="">Select...</option>
                            <option value="Size">Size</option>
                            <option value="Color">Color</option>
                            <option value="Material">Material</option>
                            <option value="Style">Style</option>
                            <option value="Custom">Custom</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold small">Attribute Value</label>
                        <input type="text" class="form-control" name="variations[0][attribute_value]" placeholder="e.g., Large, Red, Cotton">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-bold small">Price</label>
                        <input type="number" step="0.01" class="form-control" name="variations[0][price]" placeholder="Optional">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-bold small">Stock</label>
                        <input type="number" class="form-control" name="variations[0][stock_quantity]" placeholder="0" value="0">
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="button" class="btn btn-danger btn-sm w-100" onclick="removeVariation(this)">
                            <i class="fas fa-times"></i> Remove
                        </button>
                    </div>
                </div>
            </div>
            
            <button type="button" class="btn btn-outline-primary btn-sm" onclick="addVariation()">
                <i class="fas fa-plus me-2"></i>Add Another Variation
            </button>
            
            <small class="text-muted d-block mt-2">
                <i class="fas fa-info-circle me-1"></i>
                Leave Price and Stock empty to use product defaults
            </small>
        </div>
    </div>
</div>

                <button type="submit" class="btn-submit">
                    <i class="fas fa-save me-2"></i>Create Product
                </button>
            </form>
        </div>
    </div>
</div>

<script>
function previewImages(event) {
    const container = document.getElementById('imagePreviewContainer');
    container.innerHTML = '';
    
    const files = event.target.files;
    for (let i = 0; i < files.length; i++) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const div = document.createElement('div');
            div.className = 'image-preview';
            div.innerHTML = `
                <img src="${e.target.result}" alt="Preview">
                <button type="button" class="remove-btn" onclick="removeImage(this, ${i})">×</button>
            `;
            container.appendChild(div);
        };
        reader.readAsDataURL(files[i]);
    }
}

function removeImage(btn, index) {
    const container = document.getElementById('imagePreviewContainer');
    const previews = container.querySelectorAll('.image-preview');
    if (previews[index]) {
        previews[index].remove();
    }
    // Note: This doesn't remove from actual file input
    // We'll handle this on server side
}

let variationIndex = 1;

function addVariation() {
    const container = document.getElementById('variations-container');
    const html = `
        <div class="variation-row row g-3 mb-3">
            <div class="col-md-3">
                <label class="form-label fw-bold small">Attribute Name</label>
                <select class="form-select variation-attribute" name="variations[${variationIndex}][attribute_name]">
                    <option value="">Select...</option>
                    <option value="Size">Size</option>
                    <option value="Color">Color</option>
                    <option value="Material">Material</option>
                    <option value="Style">Style</option>
                    <option value="Custom">Custom</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold small">Attribute Value</label>
                <input type="text" class="form-control" name="variations[${variationIndex}][attribute_value]" placeholder="e.g., Large, Red, Cotton">
            </div>
            <div class="col-md-2">
                <label class="form-label fw-bold small">Price</label>
                <input type="number" step="0.01" class="form-control" name="variations[${variationIndex}][price]" placeholder="Optional">
            </div>
            <div class="col-md-2">
                <label class="form-label fw-bold small">Stock</label>
                <input type="number" class="form-control" name="variations[${variationIndex}][stock_quantity]" placeholder="0" value="0">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="button" class="btn btn-danger btn-sm w-100" onclick="removeVariation(this)">
                    <i class="fas fa-times"></i> Remove
                </button>
            </div>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', html);
    variationIndex++;
}

function removeVariation(btn) {
    const row = btn.closest('.variation-row');
    if (document.querySelectorAll('.variation-row').length > 1) {
        row.remove();
    } else {
        alert('You need at least one variation row');
    }
}

</script>
@endsection