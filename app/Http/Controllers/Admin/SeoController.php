<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SeoSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class SeoController extends Controller
{
    public function index()
    {
        $pages = [
            'home' => 'Home Page',
            'products' => 'Products Page',
            'product_detail' => 'Product Detail Page',
            'about' => 'About Page',
            'contact' => 'Contact Page',
            'blog' => 'Blog Page',
            'blog_detail' => 'Blog Detail Page',
            'cart' => 'Cart Page',
            'checkout' => 'Checkout Page',
        ];

        $seoSettings = [];
        foreach (array_keys($pages) as $page) {
            $seoSettings[$page] = SeoSetting::getForPage($page);
        }

        return view('admin.seo.index', compact('pages', 'seoSettings'));
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'page' => 'required|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:255',
            'og_title' => 'nullable|string|max:255',
            'og_description' => 'nullable|string|max:500',
            'og_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'canonical_url' => 'nullable|url|max:255',
            'robots' => 'nullable|string|max:255',
            'schema_markup' => 'nullable|json',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $request->except(['_token', '_method', 'og_image']);

        // Handle OG Image upload
        if ($request->hasFile('og_image')) {
            $path = $request->file('og_image')->store('seo', 'public');
            $data['og_image'] = $path;

            // Delete old image
            $old = SeoSetting::where('page', $request->page)->first();
            if ($old && $old->og_image) {
                Storage::disk('public')->delete($old->og_image);
            }
        }

        // Validate JSON schema markup
        if ($request->filled('schema_markup')) {
            json_decode($request->schema_markup);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return redirect()->back()->with('error', 'Invalid JSON format for Schema Markup');
            }
        }

        SeoSetting::updateOrCreate(
            ['page' => $request->page],
            $data
        );

        return redirect()->route('admin.seo.index')
            ->with('success', 'SEO settings updated successfully!');
    }

    public function reset($page)
    {
        $seo = SeoSetting::where('page', $page)->first();
        if ($seo) {
            if ($seo->og_image) {
                Storage::disk('public')->delete($seo->og_image);
            }
            $seo->delete();
        }

        return redirect()->route('admin.seo.index')
            ->with('success', 'SEO settings reset to default!');
    }

    public function generateSitemap()
    {
        // Generate sitemap.xml
        $pages = [
            'home' => url('/'),
            'products' => url('/products'),
            'about' => url('/about'),
            'contact' => url('/contact'),
            'blog' => url('/blog'),
        ];

        $sitemap = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $sitemap .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        foreach ($pages as $page => $url) {
            $sitemap .= '    <url>' . "\n";
            $sitemap .= '        <loc>' . $url . '</loc>' . "\n";
            $sitemap .= '        <changefreq>weekly</changefreq>' . "\n";
            $sitemap .= '        <priority>0.8</priority>' . "\n";
            $sitemap .= '    </url>' . "\n";
        }

        $sitemap .= '</urlset>';

        // Save sitemap
        file_put_contents(public_path('sitemap.xml'), $sitemap);

        return redirect()->back()->with('success', 'Sitemap generated successfully!');
    }
}