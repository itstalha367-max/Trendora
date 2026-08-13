@extends('layouts.admin')

@section('title', 'Manage Blogs')

@section('content')
<style>
    .page-header { animation: slideDown 0.5s ease-out; }
    @keyframes slideDown {
        from { opacity: 0; transform: translateY(-20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .btn-add {
        background: linear-gradient(135deg, #ef4444, #f59e0b);
        color: #fff;
        border: none;
        padding: 10px 25px;
        border-radius: 12px;
        font-weight: 600;
        transition: all 0.3s;
    }
    .btn-add:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(225, 112, 85, 0.4);
        color: #fff;
    }
    .blog-image {
        width: 60px;
        height: 60px;
        border-radius: 10px;
        object-fit: cover;
        border: 2px solid #1a2230;
    }
    .blog-image-placeholder {
        width: 60px;
        height: 60px;
        border-radius: 10px;
        background: linear-gradient(135deg, #0f141e, #273142);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #93a1b4;
        font-size: 24px;
        border: 2px dashed #2d3748;
    }
    .badge-status {
        padding: 5px 15px;
        border-radius: 50px;
        font-size: 12px;
        font-weight: 700;
    }
</style>

<div class="page-header d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-0"><i class="fas fa-blog me-2 text-primary"></i>Manage Blogs</h2>
        <p class="text-muted mb-0">Create and manage blog posts</p>
    </div>
    <a href="{{ route('admin.blogs.create') }}" class="btn-add">
        <i class="fas fa-plus me-2"></i>Add Blog
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
                        <th style="padding: 15px 20px;">Image</th>
                        <th style="padding: 15px 20px;">Title</th>
                        <th style="padding: 15px 20px;">Category</th>
                        <th style="padding: 15px 20px;">Status</th>
                        <th style="padding: 15px 20px;">Views</th>
                        <th style="padding: 15px 20px;">Date</th>
                        <th style="padding: 15px 20px; text-align: center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($blogs as $blog)
                    <tr>
                        <td style="padding: 15px 20px;">
                            @if($blog->featured_image)
                                <img src="{{ asset('storage/' . $blog->featured_image) }}" alt="{{ $blog->title }}" class="blog-image">
                            @else
                                <div class="blog-image-placeholder">
                                    <i class="fas fa-image"></i>
                                </div>
                            @endif
                        </td>
                        <td style="padding: 15px 20px;">
                            <div>
                                <div class="fw-bold">{{ $blog->title }}</div>
                                <small class="text-muted">by {{ $blog->user->name }}</small>
                            </div>
                        </td>
                        <td style="padding: 15px 20px;">
                            <span class="badge bg-light text-dark rounded-pill px-3 py-2">
                                {{ $blog->category->name ?? 'N/A' }}
                            </span>
                        </td>
                        <td style="padding: 15px 20px;">
                            <span class="badge-status bg-{{ $blog->status == 'published' ? 'success' : ($blog->status == 'draft' ? 'warning' : 'secondary') }} text-white">
                                {{ ucfirst($blog->status) }}
                            </span>
                        </td>
                        <td style="padding: 15px 20px;">
                            {{ $blog->views }}
                        </td>
                        <td style="padding: 15px 20px;">
                            {{ $blog->created_at->format('d M Y') }}
                        </td>
                        <td style="padding: 15px 20px; text-align: center;">
                            <div class="d-flex gap-1 justify-content-center">
                                <a href="{{ route('admin.blogs.edit', $blog->id) }}" class="btn btn-sm btn-primary rounded-3" style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="{{ route('admin.blogs.comments', $blog->id) }}" class="btn btn-sm btn-info rounded-3" style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-comments"></i>
                                </a>
                                <form method="POST" action="{{ route('admin.blogs.toggle', $blog->id) }}" class="d-inline">@csrf<button type="submit" class="btn btn-sm {{ $blog->status == 'published' ? 'btn-warning' : 'btn-success' }} rounded-3" style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;" aria-label="Toggle publish status">
                                    <i class="fas {{ $blog->status == 'published' ? 'fa-pause' : 'fa-play' }}"></i>
                                </button></form>
                                <form action="{{ route('admin.blogs.destroy', $blog->id) }}" method="POST" class="d-inline">
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
                        <td colspan="7" class="text-center py-5">
                            <i class="fas fa-blog fa-3x d-block mb-3 text-muted" style="opacity: 0.2;"></i>
                            <h5 class="text-muted">No blog posts found</h5>
                            <a href="{{ route('admin.blogs.create') }}" class="btn btn-primary mt-2">
                                <i class="fas fa-plus me-2"></i>Create Your First Blog
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table></div>
        </div>
        @if($blogs->hasPages())
        <div class="p-3 border-top">{{ $blogs->links() }}</div>
        @endif
    </div>
</div>
@endsection