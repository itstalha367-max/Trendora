@extends('layouts.admin')

@section('title', 'Add Blog')

@section('content')
<style>
    .form-card { animation: slideUp 0.5s ease-out; }
    @keyframes slideUp {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .btn-submit {
        background: linear-gradient(135deg, #ef4444, #f59e0b);
        color: #fff;
        border: none;
        padding: 12px 30px;
        border-radius: 12px;
        font-weight: 600;
        transition: all 0.3s;
    }
    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(225, 112, 85, 0.4);
        color: #fff;
    }
</style>

<div class="form-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0"><i class="fas fa-plus-circle me-2 text-primary"></i>Add New Blog</h2>
            <p class="text-muted mb-0">Create a new blog post</p>
        </div>
        <a href="{{ route('admin.blogs.index') }}" class="btn btn-secondary rounded-3">
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
            <form action="{{ route('admin.blogs.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="mb-3">
                    <label for="title" class="form-label fw-bold">Blog Title *</label>
                    <input type="text" class="form-control rounded-3" id="title" name="title" value="{{ old('title') }}" required>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="blog_category_id" class="form-label fw-bold">Category *</label>
                            <select class="form-control rounded-3" id="blog_category_id" name="blog_category_id" required>
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('blog_category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="status" class="form-label fw-bold">Status *</label>
                            <select class="form-control rounded-3" id="status" name="status" required>
                                <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>Published</option>
                                <option value="archived" {{ old('status') == 'archived' ? 'selected' : '' }}>Archived</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="excerpt" class="form-label fw-bold">Excerpt (Short Description)</label>
                    <textarea class="form-control rounded-3" id="excerpt" name="excerpt" rows="2">{{ old('excerpt') }}</textarea>
                    <small class="text-muted">A short summary of your blog post</small>
                </div>

                <div class="mb-3">
                    <label for="content" class="form-label fw-bold">Content *</label>
                    <textarea class="form-control rounded-3" id="content" name="content" rows="8" required>{{ old('content') }}</textarea>
                    <small class="text-muted">HTML tags allowed: &lt;p&gt;, &lt;h1&gt;-&lt;h6&gt;, &lt;ul&gt;, &lt;ol&gt;, &lt;li&gt;, &lt;strong&gt;, &lt;em&gt;</small>
                </div>

                <div class="mb-3">
                    <label for="featured_image" class="form-label fw-bold">Featured Image</label>
                    <input type="file" class="form-control rounded-3" id="featured_image" name="featured_image" accept="image/*">
                    <small class="text-muted">Upload a featured image (JPG, PNG) - Max 2MB</small>
                </div>

                <div class="mb-3">
                    <label for="tags" class="form-label fw-bold">Tags</label>
                    <select class="form-control rounded-3" id="tags" name="tags[]" multiple>
                        @foreach($tags as $tag)
                            <option value="{{ $tag->id }}">{{ $tag->name }}</option>
                        @endforeach
                    </select>
                    <small class="text-muted">Hold Ctrl/Cmd to select multiple tags</small>
                </div>

                <button type="submit" class="btn-submit">
                    <i class="fas fa-save me-2"></i>Create Blog
                </button>
            </form>
        </div>
    </div>
</div>
@endsection