<?php
namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Collection;
use App\Models\Product;
use App\Services\StorefrontCache;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query=Product::with(['category','brand'])->withAvg('approvedReviews as rating_avg','rating')->where('status',true);
        if($request->filled('search')){$term=trim($request->search);$query->where(fn($q)=>$q->where('name','like',"%$term%")->orWhere('description','like',"%$term%")->orWhere('sku','like',"%$term%")->orWhereHas('brand',fn($b)=>$b->where('name','like',"%$term%")));}
        if($request->filled('category'))$query->whereHas('category',fn($q)=>$q->where('slug',$request->category));
        if($request->filled('brand'))$query->whereHas('brand',fn($q)=>$q->where('slug',$request->brand));
        if($request->filled('collection'))$query->whereHas('collections',fn($q)=>$q->where('slug',$request->collection));
        if($request->filled('min_price'))$query->where('price','>=',(float)$request->min_price);
        if($request->filled('max_price'))$query->where('price','<=',(float)$request->max_price);
        if($request->boolean('in_stock'))$query->where(fn($q)=>$q->where('stock_quantity','>',0)->orWhereHas('variations',fn($v)=>$v->where('status',true)->where('stock_quantity','>',0)));
        if($request->boolean('sale'))$query->whereNotNull('compare_price')->whereColumn('compare_price','>','price');
        if($request->boolean('featured'))$query->where('featured',true);
        if($request->filled('rating'))$query->having('rating_avg','>=',(float)$request->rating);
        match($request->get('sort','newest')){
            'price_asc'=>$query->orderBy('price'), 'price_desc'=>$query->orderByDesc('price'), 'name_asc'=>$query->orderBy('name'), 'name_desc'=>$query->orderByDesc('name'),
            'popular'=>$query->orderByDesc('views'), 'rating'=>$query->orderByDesc('rating_avg'), default=>$query->latest(),
        };
        $products=$query->paginate(12)->withQueryString();
        $filters=Cache::remember(StorefrontCache::CATALOG_FILTERS, now()->addMinutes(5), function(){
            return [
                'categories'=>Category::where('status',true)->orderBy('name')->get(),
                'brands'=>Brand::where('status',true)->orderBy('name')->get(),
                'collections'=>Collection::where('status',true)->orderBy('name')->get(),
                'priceBounds'=>['min'=>(float)Product::where('status',true)->min('price'),'max'=>(float)Product::where('status',true)->max('price')],
            ];
        });
        ['categories'=>$categories,'brands'=>$brands,'collections'=>$collections,'priceBounds'=>$priceBounds]=$filters;
        return view('frontend.products',compact('products','categories','brands','collections','priceBounds'));
    }

    public function categories()
    {
        $categories=Cache::remember(StorefrontCache::CATEGORIES, now()->addMinutes(5), fn()=>Category::where('status',true)->withCount(['products'=>fn($q)=>$q->where('status',true)])->orderBy('name')->get());
        return view('frontend.categories',compact('categories'));
    }

    public function category(Request $request, Category $category)
    {
        abort_unless($category->status,404);
        $request->merge(['category'=>$category->slug]);
        return $this->index($request);
    }

    public function search(Request $request)
    {
        $request->merge(['search'=>trim((string)$request->get('q',$request->get('search','')))]);
        return $this->index($request);
    }

    public function show($slug)
    {
        $product=Product::with(['category','brand','collections','variations'=>fn($q)=>$q->where('status',true),'reviews'=>fn($q)=>$q->where('status','approved')->with('user')])->where('slug',$slug)->where('status',true)->firstOrFail();
        $product->increment('views');
        $relatedProducts=Product::with(['category','brand'])->where('category_id',$product->category_id)->where('id','!=',$product->id)->where('status',true)->take(4)->get();
        $avgRating=$product->reviews()->where('status','approved')->avg('rating')??0; $totalReviews=$product->reviews()->where('status','approved')->count();
        $ratingDistribution=[];for($i=5;$i>=1;$i--)$ratingDistribution[$i]=$product->reviews()->where('status','approved')->where('rating',$i)->count();
        $questions=$product->questions()->where('status','published')->whereNotNull('answer')->with(['user','answeredBy'])->latest()->take(12)->get();
        $attributeNames=$product->variations()->where('status',true)->select('attribute_name')->distinct()->pluck('attribute_name');
        $canReview=false;$myReview=null;if(auth()->check()){$canReview=\App\Models\Order::where('user_id',auth()->id())->where('order_status','delivered')->whereHas('items',fn($q)=>$q->where('product_id',$product->id))->exists();$myReview=$product->reviews()->where('user_id',auth()->id())->first();}
        return view('frontend.product-detail',compact('product','relatedProducts','avgRating','totalReviews','ratingDistribution','questions','attributeNames','canReview','myReview'));
    }
}
