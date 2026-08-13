<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ProductsImport;
use App\Exports\ProductsExport;
use App\Models\ActivityLog;
use App\Services\WebhookDispatcher;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with(['category','brand'])->latest()->paginate(10);
        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::where('status', true)->get();
        $brands = Brand::where('status', true)->orderBy('name')->get();
        return view('admin.products.create', compact('categories','brands'));
    }

    public function store(Request $request, WebhookDispatcher $webhooks)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'compare_price' => 'nullable|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'sku' => 'nullable|string|unique:products,sku',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $slug = Str::slug($request->name);
        if (Product::where('slug', $slug)->exists()) {
            $slug = $slug . '-' . time();
        }

        $imagePaths = [];
        $thumbnail = null;

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('products', 'public');
                $imagePaths[] = $path;
                if ($index === 0) {
                    $thumbnail = $path;
                }
            }
        }

        $product = Product::create([
            'name' => $request->name,
            'slug' => $slug,
            'category_id' => $request->category_id,
            'brand_id' => $request->brand_id,
            'description' => $request->description,
            'price' => $request->price,
            'compare_price' => $request->compare_price,
            'stock_quantity' => $request->stock_quantity,
            'sku' => $request->sku,
            'images' => $imagePaths,
            'thumbnail' => $thumbnail,
            'featured' => $request->has('featured') ? 1 : 0,
            'status' => $request->has('status') ? 1 : 0,
        ]);

        // 🔥 Activity Log - Inside the method
        ActivityLog::log('CREATE', 'products', 'Product created: ' . $product->name, ['product_id' => $product->id]);
        try { $webhooks->dispatch('product.updated', $product->fresh(['category','brand']), ['action'=>'created']); } catch (\Throwable $e) { \Log::warning('Product webhook failed: '.$e->getMessage()); }

        return redirect()->route('admin.products.index')
            ->with('success', 'Product created successfully!');
    }

    public function edit(Product $product)
    {
        $categories = Category::where('status', true)->get();
        $brands = Brand::where('status', true)->orderBy('name')->get();
        return view('admin.products.edit', compact('product', 'categories', 'brands'));
    }

    public function update(Request $request, Product $product, WebhookDispatcher $webhooks)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'compare_price' => 'nullable|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'sku' => 'nullable|string|unique:products,sku,' . $product->id,
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',

        ]);
          // 🔥 Update Variations
    if ($request->has('variations')) {
        // Delete existing variations
        $product->variations()->delete();
        
        foreach ($request->variations as $variationData) {
            if (!empty($variationData['attribute_name']) && !empty($variationData['attribute_value'])) {
                $product->variations()->create([
                    'attribute_name' => $variationData['attribute_name'],
                    'attribute_value' => $variationData['attribute_value'],
                    'price' => $variationData['price'] ?? null,
                    'stock_quantity' => $variationData['stock_quantity'] ?? 0,
                    'status' => true,
                ]);
            }
        }
    }

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $imagePaths = $product->images ?? [];
        $thumbnail = $product->thumbnail;

        if ($request->hasFile('images')) {
            if ($product->images) {
                foreach ($product->images as $oldImage) {
                    Storage::disk('public')->delete($oldImage);
                }
            }
            
            $imagePaths = [];
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('products', 'public');
                $imagePaths[] = $path;
                if ($index === 0) {
                    $thumbnail = $path;
                }
            }
        }

        if ($request->has('thumbnail_index') && !empty($imagePaths)) {
            $index = (int) $request->thumbnail_index;
            if (isset($imagePaths[$index])) {
                $thumbnail = $imagePaths[$index];
            }
        }

        if ($request->has('delete_images')) {
            $deleteIndexes = explode(',', $request->delete_images);
            $newImages = [];
            foreach ($imagePaths as $index => $path) {
                if (!in_array($index, $deleteIndexes)) {
                    $newImages[] = $path;
                } else {
                    Storage::disk('public')->delete($path);
                }
            }
            $imagePaths = $newImages;
            
            if (!in_array($thumbnail, $imagePaths)) {
                $thumbnail = $imagePaths[0] ?? null;
            }
        }

        $product->update([
            'name' => $request->name,
            'category_id' => $request->category_id,
            'brand_id' => $request->brand_id,
            'description' => $request->description,
            'price' => $request->price,
            'compare_price' => $request->compare_price,
            'stock_quantity' => $request->stock_quantity,
            'sku' => $request->sku,
            'images' => $imagePaths,
            'thumbnail' => $thumbnail,
            'featured' => $request->has('featured') ? 1 : 0,
            'status' => $request->has('status') ? 1 : 0,
        ]);

        // 🔥 Activity Log - Inside the method
        ActivityLog::log('UPDATE', 'products', 'Product updated: ' . $product->name, ['product_id' => $product->id]);
        try { $webhooks->dispatch('product.updated', $product->fresh(['category','brand']), ['action'=>'updated']); } catch (\Throwable $e) { \Log::warning('Product webhook failed: '.$e->getMessage()); }

        return redirect()->route('admin.products.index')
            ->with('success', 'Product updated successfully!');
    }

    public function destroy(Product $product)
    {
        if ($product->images) {
            foreach ($product->images as $image) {
                Storage::disk('public')->delete($image);
            }
        }
        
        // 🔥 Activity Log - Inside the method
        ActivityLog::log('DELETE', 'products', 'Product deleted: ' . $product->name, ['product_id' => $product->id]);
        
        $product->delete();
        return redirect()->route('admin.products.index')
            ->with('success', 'Product deleted successfully!');
    }

    // ✅ IMPORT / EXPORT METHODS

    public function importForm()
    {
        return view('admin.products.import');
    }

    public function import(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:xlsx,xls,csv|max:5120',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        try {
            Excel::import(new ProductsImport, $request->file('file'));
            
            // 🔥 Activity Log
            ActivityLog::log('IMPORT', 'products', 'Products imported from file', ['file' => $request->file('file')->getClientOriginalName()]);
            
            return redirect()->route('admin.products.index')
                ->with('success', 'Products imported successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    public function export()
    {
        // 🔥 Activity Log
        ActivityLog::log('EXPORT', 'products', 'Products exported to Excel');
        
        return Excel::download(new ProductsExport, 'products-' . date('Y-m-d') . '.xlsx');
    }

    public function downloadSample()
    {
        $headers = [
            'name',
            'category',
            'description',
            'price',
            'compare_price',
            'stock_quantity',
            'sku',
            'status',
            'featured'
        ];

        $sample = [
            [
                'Premium T-Shirt',
                'Clothing',
                'High quality cotton t-shirt',
                '29.99',
                '49.99',
                '100',
                'TS-001',
                '1',
                '1'
            ],
            [
                'Running Shoes',
                'Footwear',
                'Comfortable running shoes',
                '89.99',
                '129.99',
                '50',
                'RS-001',
                '1',
                '0'
            ]
        ];

        $callback = function() use ($headers, $sample) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $headers);
            foreach ($sample as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="sample-products.csv"',
        ]);
    }

    // 🔥 Bulk Operations
    public function bulkDelete(Request $request)
    {
        $ids = $request->ids;
        if (empty($ids)) {
            return response()->json(['success' => false, 'message' => 'No products selected']);
        }

        Product::whereIn('id', $ids)->delete();
        
        // 🔥 Activity Log
        ActivityLog::log('BULK_DELETE', 'products', 'Bulk delete products', ['count' => count($ids)]);
        
        return response()->json(['success' => true, 'message' => 'Products deleted successfully']);
    }

    public function bulkStatus(Request $request)
    {
        $ids = $request->ids;
        $status = $request->status;

        if (empty($ids)) {
            return response()->json(['success' => false, 'message' => 'No products selected']);
        }

        Product::whereIn('id', $ids)->update(['status' => $status]);
        
        // 🔥 Activity Log
        ActivityLog::log('BULK_STATUS', 'products', 'Bulk status update products', ['count' => count($ids), 'status' => $status]);
        
        return response()->json(['success' => true, 'message' => 'Product status updated successfully']);
    }
}