<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\ProductVariation;
use App\Models\Promotion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index()
    {
        $cart = $this->getCart();
        return view('frontend.cart', compact('cart'));
    }

    public function add(Request $request)
    {
        try {
            $request->validate([
                'product_id' => 'required|exists:products,id',
                'quantity' => 'required|integer|min:1',
                'variation_id' => 'nullable|exists:product_variations,id',
            ]);

            $product = Product::findOrFail($request->product_id);
            $cart = $this->getCart();

            // Check variation
            $variation = null;
            $price = $product->price;
            $stock = $product->stock_quantity;

            if ($request->variation_id) {
                $variation = ProductVariation::where('product_id', $product->id)->find($request->variation_id);
                if (!$variation) {
                    return response()->json(['success' => false, 'message' => 'Invalid product variation.'], 422);
                }
                if ($variation) {
                    $price = $variation->price ?? $product->price;
                    $stock = $variation->stock_quantity;
                }
            }

            // Check if item already in cart
            $cartItem = CartItem::where('cart_id', $cart->id)
                ->where('product_id', $request->product_id)
                ->where('product_variation_id', $request->variation_id)
                ->first();

            $requestedTotalQty = ($cartItem?->quantity ?? 0) + (int) $request->quantity;
            if ($requestedTotalQty > $stock) {
                return response()->json([
                    'success' => false,
                    'message' => 'Only ' . max(0, $stock) . ' item(s) are available in stock.',
                ], 422);
            }

            if ($cartItem) {
                $cartItem->quantity = $requestedTotalQty;
                $cartItem->total = $cartItem->price * $cartItem->quantity;
                $cartItem->save();
            } else {
                $cartItem = CartItem::create([
                    'cart_id' => $cart->id,
                    'product_id' => $request->product_id,
                    'product_variation_id' => $request->variation_id,
                    'quantity' => $request->quantity,
                    'price' => $price,
                    'total' => $price * $request->quantity,
                ]);
            }

            $cart->updateTotal();

            return response()->json([
                'success' => true,
                'message' => 'Added to cart! 🛒',
                'count' => $cart->items_count,
                'total' => number_format($cart->total, 2),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }


    public function update(Request $request)
    {
        try {
            $request->validate([
                'item_id' => 'required|exists:cart_items,id',
                'quantity' => 'required|integer|min:1',
            ]);

            $cart = $this->getCart();
            $cartItem = CartItem::where('cart_id', $cart->id)->findOrFail($request->item_id);
            
            // Check stock
            $stock = $cartItem->variation ? $cartItem->variation->stock_quantity : $cartItem->product->stock_quantity;
            if ($request->quantity > $stock) {
                return response()->json([
                    'success' => false,
                    'message' => 'Not enough stock available!',
                ]);
            }

            $cartItem->quantity = $request->quantity;
            $cartItem->total = $cartItem->price * $cartItem->quantity;
            $cartItem->save();

            $cart->updateTotal();

            return response()->json([
                'success' => true,
                'message' => 'Cart updated!',
                'item_total' => number_format($cartItem->total, 2),
                'subtotal' => number_format($cart->subtotal, 2),
                'total' => number_format($cart->total, 2),
                'count' => $cart->items_count,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }
    public function applyCoupon(Request $request)
    {
        $request->validate(['coupon_code' => 'required|string|max:80']);
        $cart = $this->getCart();
        if (!$cart || $cart->items_count == 0) return response()->json(['success'=>false,'message'=>'Your cart is empty!']);

        $code = strtoupper(trim($request->coupon_code));
        $subtotal = (float) $cart->subtotal;
        $coupon = Coupon::where('code', $code)->first();
        $discount = 0.0; $freeShipping = false; $promotionId = null;

        if ($coupon) {
            if (!$coupon->isValid()) return response()->json(['success'=>false,'message'=>'Coupon is expired or invalid!']);
            $discount = (float) $coupon->calculateDiscount($subtotal);
            if ($discount <= 0) return response()->json(['success'=>false,'message'=>'Coupon minimum order requirements are not met.']);
        } else {
            $promotion = Promotion::where('code', $code)->where('status', true)->first();
            $live = $promotion && (!$promotion->starts_at || $promotion->starts_at->isPast()) && (!$promotion->ends_at || $promotion->ends_at->isFuture());
            $underLimit = $promotion && (!$promotion->usage_limit || $promotion->usage_count < $promotion->usage_limit);
            if (!$live || !$underLimit || $subtotal < (float)($promotion->minimum_order ?? 0)) return response()->json(['success'=>false,'message'=>'Promotion code is invalid or not currently available.']);
            if ($promotion->type === 'percentage') $discount = $subtotal * ((float)$promotion->value / 100);
            elseif ($promotion->type === 'fixed') $discount = (float)$promotion->value;
            elseif ($promotion->type === 'free_shipping') $freeShipping = true;
            if ($promotion->maximum_discount !== null) $discount = min($discount, (float)$promotion->maximum_discount);
            $discount = min($subtotal, max(0, round($discount, 2)));
            $promotionId = $promotion->id;
        }

        session(['coupon_code'=>$code,'coupon_discount'=>$discount,'promotion_id'=>$promotionId,'promotion_free_shipping'=>$freeShipping]);
        $cart->total = max(0, $subtotal - $discount); $cart->save();
        return response()->json(['success'=>true,'message'=>$freeShipping?'Free shipping promotion applied! 🚚':'Discount applied successfully! 🎉','discount'=>number_format($discount,2),'subtotal'=>number_format($subtotal,2),'total'=>number_format($cart->total,2),'free_shipping'=>$freeShipping]);
    }

    /**
     * Remove coupon from cart
     */
    public function removeCoupon(Request $request)
    {
        $cart = $this->getCart();
        
        session()->forget(['coupon_code','coupon_discount','promotion_id','promotion_free_shipping']);

        $cart->total = $cart->subtotal;
        $cart->save();

        return response()->json([
            'success' => true,
            'message' => 'Coupon removed!',
            'subtotal' => number_format($cart->subtotal, 2),
            'total' => number_format($cart->total, 2),
        ]);
    }

    public function remove(Request $request)
    {
        try {
            $request->validate([
                'item_id' => 'required|exists:cart_items,id',
            ]);

            $cart = $this->getCart();
            $cartItem = CartItem::where('cart_id', $cart->id)->findOrFail($request->item_id);
            $cartItem->delete();
            $cart->updateTotal();

            return response()->json([
                'success' => true,
                'message' => 'Item removed from cart!',
                'subtotal' => number_format($cart->subtotal, 2),
                'total' => number_format($cart->total, 2),
                'count' => $cart->items_count,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function clear()
    {
        $cart = $this->getCart();
        $cart->clear();

        return redirect()->route('cart.index')->with('success', 'Cart cleared!');
    }

    public function count()
    {
        $cart = $this->getCart();
        return response()->json([
            'count' => $cart->items_count,
            'total' => number_format($cart->total, 2),
        ]);
    }

    public function getCart()
    {
        if (Auth::check()) {
            $cart = Cart::firstOrCreate([
                'user_id' => Auth::id(),
            ]);
        } else {
            $sessionId = session()->getId();
            $cart = Cart::firstOrCreate([
                'session_id' => $sessionId,
            ]);
        }
        return $cart;
    }
}