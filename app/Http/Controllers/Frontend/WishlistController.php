<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    public function index()
    {
        $wishlist = Wishlist::with(['product', 'variation'])
            ->where('user_id', Auth::id())
            ->get();

        return view('frontend.user.wishlist', compact('wishlist'));
    }

    public function add($productId)
    {
        $product = Product::findOrFail($productId);

        // Check if already in wishlist
        $exists = Wishlist::where('user_id', Auth::id())
            ->where('product_id', $productId)
            ->exists();

        if ($exists) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product already in wishlist!'
                ]);
            }
            return redirect()->back()->with('info', 'Product already in wishlist!');
        }

        Wishlist::create([
            'user_id' => Auth::id(),
            'product_id' => $productId,
        ]);

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Added to wishlist! ❤️',
                'count' => Wishlist::where('user_id', Auth::id())->count()
            ]);
        }

        return redirect()->back()->with('success', 'Added to wishlist! ❤️');
    }

    public function remove($id)
    {
        $wishlist = Wishlist::where('user_id', Auth::id())
            ->where('id', $id)
            ->firstOrFail();

        $wishlist->delete();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Removed from wishlist!',
                'count' => Wishlist::where('user_id', Auth::id())->count()
            ]);
        }

        return redirect()->back()->with('success', 'Removed from wishlist!');
    }

    public function toggle($productId)
    {
        $exists = Wishlist::where('user_id', Auth::id())
            ->where('product_id', $productId)
            ->first();

        if ($exists) {
            $exists->delete();
            $message = 'Removed from wishlist!';
            $inWishlist = false;
        } else {
            Wishlist::create([
                'user_id' => Auth::id(),
                'product_id' => $productId,
            ]);
            $message = 'Added to wishlist! ❤️';
            $inWishlist = true;
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'in_wishlist' => $inWishlist,
            'count' => Wishlist::where('user_id', Auth::id())->count()
        ]);
    }

    public function count()
    {
        $count = Wishlist::where('user_id', Auth::id())->count();
        return response()->json(['count' => $count]);
    }

    public function check($productId)
    {
        $exists = Wishlist::where('user_id', Auth::id())
            ->where('product_id', $productId)
            ->exists();
        return response()->json(['in_wishlist' => $exists]);
    }

    public function clear()
    {
        Wishlist::where('user_id', Auth::id())->delete();
        return redirect()->route('user.wishlist')->with('success', 'Wishlist cleared!');
    }
}