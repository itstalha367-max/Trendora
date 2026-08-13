<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Wishlist;
use App\Models\User;
use App\Models\Product;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function index()
    {
        $wishlists = Wishlist::with(['user', 'product'])
            ->latest()
            ->paginate(20);

        $stats = [
            'total' => Wishlist::count(),
            'unique_users' => Wishlist::distinct('user_id')->count(),
            'unique_products' => Wishlist::distinct('product_id')->count(),
        ];

        return view('admin.wishlist.index', compact('wishlists', 'stats'));
    }

    public function show($id)
    {
        $wishlist = Wishlist::with(['user', 'product', 'variation'])->findOrFail($id);
        return view('admin.wishlist.show', compact('wishlist'));
    }

    public function destroy($id)
    {
        $wishlist = Wishlist::findOrFail($id);
        $wishlist->delete();

        return redirect()->route('admin.wishlist.index')
            ->with('success', 'Wishlist item deleted successfully!');
    }

    public function clearUser($userId)
    {
        Wishlist::where('user_id', $userId)->delete();
        
        return redirect()->route('admin.wishlist.index')
            ->with('success', 'User wishlist cleared successfully!');
    }

    public function getStats()
    {
        $stats = [
            'total' => Wishlist::count(),
            'top_products' => Wishlist::selectRaw('product_id, count(*) as count')
                ->with('product')
                ->groupBy('product_id')
                ->orderBy('count', 'desc')
                ->take(10)
                ->get(),
            'top_users' => Wishlist::selectRaw('user_id, count(*) as count')
                ->with('user')
                ->groupBy('user_id')
                ->orderBy('count', 'desc')
                ->take(10)
                ->get(),
        ];

        return response()->json($stats);
    }
}