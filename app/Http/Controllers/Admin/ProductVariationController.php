<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProductVariationController extends Controller
{
    public function index($productId)
    {
        $product = Product::with('variations')->findOrFail($productId);
        return view('admin.products.variations.index', compact('product'));
    }

    public function create($productId)
    {
        $product = Product::findOrFail($productId);
        return view('admin.products.variations.create', compact('product'));
    }

    public function store(Request $request, $productId)
    {
        $validator = Validator::make($request->all(), [
            'attribute_name' => 'required|string|max:255',
            'attribute_value' => 'required|string|max:255',
            'sku' => 'nullable|string|unique:product_variations',
            'price' => 'nullable|numeric|min:0',
            'compare_price' => 'nullable|numeric|min:0',
            'stock_quantity' => 'nullable|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $product = Product::findOrFail($productId);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('variations', 'public');
        }

        // Check if variation already exists
        $exists = ProductVariation::where('product_id', $productId)
            ->where('attribute_name', $request->attribute_name)
            ->where('attribute_value', $request->attribute_value)
            ->exists();

        if ($exists) {
            return redirect()->back()->with('error', 'This variation already exists!');
        }

        $variation = ProductVariation::create([
            'product_id' => $productId,
            'sku' => $request->sku,
            'attribute_name' => $request->attribute_name,
            'attribute_value' => $request->attribute_value,
            'price' => $request->price,
            'compare_price' => $request->compare_price,
            'stock_quantity' => $request->stock_quantity ?? 0,
            'image' => $imagePath,
            'is_default' => $request->has('is_default'),
            'status' => $request->has('status'),
        ]);

        // If this is default, remove default from other variations
        if ($request->has('is_default')) {
            ProductVariation::where('product_id', $productId)
                ->where('id', '!=', $variation->id)
                ->update(['is_default' => false]);
        }

        return redirect()->route('admin.products.variations.index', $productId)
            ->with('success', 'Variation added successfully!');
    }

    public function edit($productId, $variationId)
    {
        $product = Product::findOrFail($productId);
        $variation = ProductVariation::findOrFail($variationId);
        return view('admin.products.variations.edit', compact('product', 'variation'));
    }

    public function update(Request $request, $productId, $variationId)
    {
        $validator = Validator::make($request->all(), [
            'attribute_name' => 'required|string|max:255',
            'attribute_value' => 'required|string|max:255',
            'sku' => 'nullable|string|unique:product_variations,sku,' . $variationId,
            'price' => 'nullable|numeric|min:0',
            'compare_price' => 'nullable|numeric|min:0',
            'stock_quantity' => 'nullable|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $variation = ProductVariation::findOrFail($variationId);

        // Check if variation already exists (excluding current)
        $exists = ProductVariation::where('product_id', $productId)
            ->where('attribute_name', $request->attribute_name)
            ->where('attribute_value', $request->attribute_value)
            ->where('id', '!=', $variationId)
            ->exists();

        if ($exists) {
            return redirect()->back()->with('error', 'This variation already exists!');
        }

        $imagePath = $variation->image;
        if ($request->hasFile('image')) {
            if ($imagePath) {
                Storage::disk('public')->delete($imagePath);
            }
            $imagePath = $request->file('image')->store('variations', 'public');
        }

        $variation->update([
            'sku' => $request->sku,
            'attribute_name' => $request->attribute_name,
            'attribute_value' => $request->attribute_value,
            'price' => $request->price,
            'compare_price' => $request->compare_price,
            'stock_quantity' => $request->stock_quantity ?? 0,
            'image' => $imagePath,
            'is_default' => $request->has('is_default'),
            'status' => $request->has('status'),
        ]);

        if ($request->has('is_default')) {
            ProductVariation::where('product_id', $productId)
                ->where('id', '!=', $variationId)
                ->update(['is_default' => false]);
        }

        return redirect()->route('admin.products.variations.index', $productId)
            ->with('success', 'Variation updated successfully!');
    }

    public function destroy($productId, $variationId)
    {
        $variation = ProductVariation::findOrFail($variationId);
        
        if ($variation->image) {
            Storage::disk('public')->delete($variation->image);
        }
        
        $variation->delete();

        return redirect()->route('admin.products.variations.index', $productId)
            ->with('success', 'Variation deleted successfully!');
    }

    public function toggleStatus($productId, $variationId)
    {
        $variation = ProductVariation::findOrFail($variationId);
        $variation->status = !$variation->status;
        $variation->save();

        return redirect()->back()->with('success', 'Variation status updated!');
    }
}