<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Product;

class CompareController extends Controller
{
    public function index()
    {
        $ids = collect(session('compare_products', []))->unique()->take(4)->values();
        $products = Product::with(['category', 'variations'])
            ->whereIn('id', $ids)
            ->where('status', true)
            ->get()
            ->sortBy(fn($product) => $ids->search($product->id));

        return view('frontend.compare', compact('products'));
    }

    public function add(Product $product)
    {
        abort_unless($product->status, 404);
        $ids = collect(session('compare_products', []))->unique();
        if (!$ids->contains($product->id)) {
            if ($ids->count() >= 4) {
                return back()->with('error', 'You can compare up to 4 products at a time.');
            }
            $ids->push($product->id);
        }
        session(['compare_products' => $ids->values()->all()]);
        return back()->with('success', 'Product added to compare.');
    }

    public function remove(Product $product)
    {
        $ids = collect(session('compare_products', []))->reject(fn($id) => (int)$id === (int)$product->id)->values();
        session(['compare_products' => $ids->all()]);
        return back()->with('success', 'Product removed from compare.');
    }

    public function clear()
    {
        session()->forget('compare_products');
        return back()->with('success', 'Compare list cleared.');
    }
}
