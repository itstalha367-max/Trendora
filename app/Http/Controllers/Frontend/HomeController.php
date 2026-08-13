<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\Category;
use App\Models\Product;
use App\Services\StorefrontCache;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    public function index()
    {
        $data = Cache::remember(StorefrontCache::HOME, now()->addMinutes(2), function () {
            return [
                'featuredProducts' => Product::with(['category','brand'])->where('featured', true)->where('status', true)->latest()->take(8)->get(),
                'newProducts' => Product::with(['category','brand'])->where('status', true)->latest()->take(8)->get(),
                'categories' => Category::withCount(['products' => fn ($q) => $q->where('status', true)])->where('status', true)->having('products_count', '>', 0)->take(6)->get(),
                'blogPosts' => Blog::published()->latest('published_at')->take(3)->get(),
            ];
        });

        return view('frontend.home', $data);
    }
}
