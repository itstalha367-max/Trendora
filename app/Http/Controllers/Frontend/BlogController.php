<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        $query = Blog::published()->with(['user', 'category'])->latest('published_at');

        if ($request->filled('q')) {
            $term = trim((string) $request->q);
            $query->where(function ($builder) use ($term) {
                $builder->where('title', 'like', "%{$term}%")
                    ->orWhere('excerpt', 'like', "%{$term}%")
                    ->orWhere('content', 'like', "%{$term}%");
            });
        }

        $posts = $query->paginate(9)->withQueryString();
        return view('frontend.blog.index', compact('posts'));
    }

    public function show(Blog $blog)
    {
        abort_unless($blog->status === 'published' && (!$blog->published_at || $blog->published_at->isPast()), 404);
        $blog->increment('views');
        $related = Blog::published()
            ->whereKeyNot($blog->getKey())
            ->when($blog->blog_category_id, fn ($q) => $q->where('blog_category_id', $blog->blog_category_id))
            ->latest('published_at')
            ->take(3)
            ->get();

        return view('frontend.blog.show', compact('blog', 'related'));
    }
}
