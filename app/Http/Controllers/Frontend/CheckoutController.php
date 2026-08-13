<?php
namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Coupon;
use App\Models\Inventory;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PaymentTransaction;
use App\Models\Setting;
use App\Models\WalletTransaction;
use App\Notifications\CommerceNotification;
use App\Services\CommerceCalculator;
use App\Services\InventoryService;
use App\Services\WebhookDispatcher;
use App\Mail\OrderConfirmationMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class CheckoutController extends Controller
{
    public function index(CommerceCalculator $calculator)
    {
        $cart=$this->getCart(); if(!$cart || $cart->items_count==0) return redirect()->route('cart.index')->with('error','Your cart is empty!');
        $user=Auth::user(); $addresses=$user->addresses()->orderByDesc('is_default')->latest()->get();
        $default=$addresses->firstWhere('is_default',true) ?: $addresses->first();
        $fallbackCountry=(string)Setting::get('checkout_default_country','Pakistan');
        $country=$default?->country ?: ($user->country ?: $fallbackCountry); $state=$default?->state ?: $user->state;
        $shippingMethods=$calculator->shippingMethods($country,$state,(float)$cart->subtotal);
        try {
            $quote=$calculator->quote((float)$cart->subtotal,(float)session('coupon_discount',0),$country,$state,$shippingMethods->first()['id'] ?? null,(bool)session('promotion_free_shipping',false));
        } catch (ValidationException $e) {
            $discount=(float)session('coupon_discount',0); $base=max(0,(float)$cart->subtotal-$discount);
            $quote=['subtotal'=>(float)$cart->subtotal,'discount'=>$discount,'shipping'=>0,'shipping_method'=>['id'=>null,'name'=>'Select an eligible delivery address','cost'=>0],'tax'=>0,'tax_name'=>null,'tax_rate'=>0,'total'=>$base];
        }
        $minimumOrder=(float)Setting::get('minimum_order_amount',0);
        return view('frontend.checkout',compact('cart','user','addresses','shippingMethods','quote','minimumOrder'));
    }

    public function quote(Request $request, CommerceCalculator $calculator)
    {
        $data=$request->validate(['country'=>'required|string|max:100','state'=>'nullable|string|max:100','shipping_method_id'=>'nullable|integer']);
        $cart=$this->getCart(); abort_unless($cart,404);
        $methods=$calculator->shippingMethods($data['country'],$data['state']??null,(float)$cart->subtotal);
        if($methods->isEmpty()) return response()->json(['message'=>'No shipping method is available for this address. Please contact support or choose another delivery address.'],422);
        $requested=$data['shipping_method_id']??null;
        $selected=$methods->contains(fn($m)=>(string)($m['id']??'')===(string)($requested??''))?$requested:($methods->first()['id']??null);
        $quote=$calculator->quote((float)$cart->subtotal,(float)session('coupon_discount',0),$data['country'],$data['state']??null,$selected,(bool)session('promotion_free_shipping',false));
        return response()->json(['methods'=>$methods->values(),'quote'=>$quote]);
    }

    public function process(Request $request, CommerceCalculator $calculator, InventoryService $inventory, WebhookDispatcher $webhooks)
    {
        $allowedPayments=['jazzcash','easypaisa','stripe','paypal'];
        if(Setting::get('checkout_cod_enabled','on')==='on') $allowedPayments[]='cod';
        if(Setting::get('checkout_wallet_enabled','on')==='on') $allowedPayments[]='wallet';
        $termsRequired=Setting::get('checkout_terms_required','on')==='on';
        $data=$request->validate([
            'shipping_name'=>'required|string|max:255','shipping_email'=>'required|email|max:255','shipping_phone'=>'required|string|max:20',
            'shipping_address'=>'required|string','shipping_city'=>'required|string|max:100','shipping_state'=>'nullable|string|max:100','shipping_zip'=>'nullable|string|max:20',
            'shipping_country'=>'required|string|max:100','shipping_method_id'=>'nullable|integer','payment_method'=>'required|in:'.implode(',',$allowedPayments),'notes'=>'nullable|string|max:2000',
            'terms_accepted'=>$termsRequired?'accepted':'nullable',
        ]);
        $cart=$this->getCart(); if(!$cart || $cart->items_count==0) return redirect()->route('cart.index')->with('error','Your cart is empty!');
        $minimum=(float)Setting::get('minimum_order_amount',0); if((float)$cart->subtotal<$minimum) return back()->withInput()->with('error','Minimum order amount is '.Setting::get('currency_symbol','Rs').' '.number_format($minimum,2).'.');
        $discount=(float)session('coupon_discount',0); $couponCode=session('coupon_code'); $promotionId=session('promotion_id'); $freeShipping=(bool)session('promotion_free_shipping',false);
        if ($promotionId) {
            $promotion=\App\Models\Promotion::find($promotionId);
            $live=$promotion && $promotion->status && (!$promotion->starts_at || $promotion->starts_at->isPast()) && (!$promotion->ends_at || $promotion->ends_at->isFuture());
            $underLimit=$promotion && (!$promotion->usage_limit || $promotion->usage_count < $promotion->usage_limit);
            if (!$live || !$underLimit || (float)$cart->subtotal < (float)($promotion->minimum_order ?? 0)) {
                session()->forget(['coupon_code','coupon_discount','promotion_id','promotion_free_shipping']);
                return back()->withInput()->with('error','The promotion applied to your cart is no longer available. Please review your cart and try again.');
            }
            $discount=0.0; $freeShipping=$promotion->type==='free_shipping';
            if ($promotion->type==='percentage') $discount=(float)$cart->subtotal*((float)$promotion->value/100);
            elseif ($promotion->type==='fixed') $discount=(float)$promotion->value;
            if ($promotion->maximum_discount !== null) $discount=min($discount,(float)$promotion->maximum_discount);
            $discount=min((float)$cart->subtotal,max(0,round($discount,2)));
        }
        $quote=$calculator->quote((float)$cart->subtotal,$discount,$data['shipping_country'],$data['shipping_state']??null,$data['shipping_method_id']??null,$freeShipping);
        $isWallet=$data['payment_method']==='wallet';
        if($isWallet && (float)Auth::user()->store_credit < (float)$quote['total']) return back()->withInput()->with('error','Your store credit balance is not enough for this order.');

        DB::beginTransaction();
        try {
            $walletUser=null; $lockedPromotion=null;
            if($isWallet){$walletUser=\App\Models\User::whereKey(Auth::id())->lockForUpdate()->firstOrFail();if((float)$walletUser->store_credit < (float)$quote['total'])throw ValidationException::withMessages(['payment_method'=>'Store credit balance changed and is no longer sufficient.']);}
            if($promotionId){$lockedPromotion=\App\Models\Promotion::whereKey($promotionId)->lockForUpdate()->first();$valid=$lockedPromotion && $lockedPromotion->status && (!$lockedPromotion->starts_at||$lockedPromotion->starts_at->isPast()) && (!$lockedPromotion->ends_at||$lockedPromotion->ends_at->isFuture()) && (!$lockedPromotion->usage_limit||$lockedPromotion->usage_count<$lockedPromotion->usage_limit);if(!$valid)throw ValidationException::withMessages(['payment_method'=>'The promotion reached its limit while you were checking out. Please review your cart.']);}
            $order=Order::create([
                'user_id'=>Auth::id(),'order_number'=>Order::generateOrderNumber(),'subtotal'=>$quote['subtotal'],'tax'=>$quote['tax'],'tax_name'=>$quote['tax_name'],'tax_rate'=>$quote['tax_rate'],
                'shipping_cost'=>$quote['shipping'],'shipping_method_id'=>$quote['shipping_method']['id'],'shipping_method_name'=>$quote['shipping_method']['name'],'discount'=>$quote['discount'],'total'=>$quote['total'],
                'shipping_name'=>$data['shipping_name'],'shipping_email'=>$data['shipping_email'],'shipping_phone'=>$data['shipping_phone'],'shipping_address'=>$data['shipping_address'],
                'shipping_city'=>$data['shipping_city'],'shipping_state'=>$data['shipping_state']??null,'shipping_zip'=>$data['shipping_zip']??null,'shipping_country'=>$data['shipping_country'],
                'notes'=>Setting::get('checkout_notes_enabled','on')==='on'?($data['notes']??null):null,'payment_status'=>$isWallet?'paid':'pending','order_status'=>'pending','payment_gateway'=>$data['payment_method'],
            ]);
            foreach($cart->items as $item){
                $warehouseId=$inventory->deduct($item->product,$item->variation,$item->quantity,$order->order_number);
                OrderItem::create(['order_id'=>$order->id,'product_id'=>$item->product_id,'product_variation_id'=>$item->product_variation_id,'warehouse_id'=>$warehouseId,'product_name'=>$item->product->name,'product_sku'=>$item->variation?->sku ?? $item->product->sku,'quantity'=>$item->quantity,'price'=>$item->price,'total'=>$item->total]);
            }
            $order->statusHistory()->create(['user_id'=>Auth::id(),'to_status'=>'pending','note'=>'Order placed by customer']);
            PaymentTransaction::create(['order_id'=>$order->id,'gateway'=>$data['payment_method'],'type'=>'charge','status'=>$isWallet?'succeeded':'pending','amount'=>$order->total,'currency'=>Setting::get('currency','PKR'),'note'=>$isWallet?'Paid with store credit':'Checkout initiated']);
            if($isWallet){$new=(float)$walletUser->store_credit-(float)$order->total;$walletUser->update(['store_credit'=>$new]);WalletTransaction::create(['user_id'=>$walletUser->id,'order_id'=>$order->id,'type'=>'purchase','amount'=>-(float)$order->total,'balance_after'=>$new,'note'=>'Payment for '.$order->order_number]);}
            if($couponCode && ($coupon=Coupon::where('code',$couponCode)->first())) $coupon->incrementUsed();
            if($lockedPromotion) $lockedPromotion->increment('usage_count');
            session()->forget(['coupon_code','coupon_discount','promotion_id','promotion_free_shipping']); $cart->clear(); DB::commit();

            Auth::user()->notify(new CommerceNotification('Order placed','Your order '.$order->order_number.' has been placed.',route('user.order.detail',$order->id),'fa-circle-check'));
            try{Mail::to($order->shipping_email)->send(new OrderConfirmationMail($order));}catch(\Throwable $e){\Log::error('Order confirmation email failed: '.$e->getMessage());}
            try{$webhooks->dispatch('order.created',$order->load('items'),['source'=>'checkout']);$this->dispatchLowStock($order,$webhooks);}catch(\Throwable $e){\Log::warning('Webhook dispatch failed after checkout: '.$e->getMessage());}
            return in_array($data['payment_method'],['cod','wallet']) ? redirect()->route('orders.success',$order->id)->with('success','Order placed successfully!') : redirect()->route('payment.pay',[$order->id,$data['payment_method']]);
        } catch(ValidationException $e){DB::rollBack(); return back()->withInput()->withErrors($e->errors());} catch(\Throwable $e){DB::rollBack(); \Log::error('Checkout failed: '.$e->getMessage()); return back()->withInput()->with('error','We could not place your order. Please review your details and try again.');}
    }

    public function success($id){$order=Order::with('items')->where('user_id',Auth::id())->findOrFail($id);return view('frontend.order-success',compact('order'));}
    private function getCart(){return Auth::check()?Cart::with(['items.product','items.variation'])->where('user_id',Auth::id())->first():null;}
    private function dispatchLowStock(Order $order, WebhookDispatcher $webhooks): void
    {
        $threshold=(int)Setting::get('low_stock_threshold',5);
        foreach($order->items as $item){
            if($item->warehouse_id){
                $row=Inventory::with(['product','warehouse'])->where('warehouse_id',$item->warehouse_id)->where('product_id',$item->product_id)->where('product_variation_id',$item->product_variation_id)->first();
                if($row && $row->available_quantity <= max($threshold,(int)$row->reorder_level)) $webhooks->dispatch('inventory.low',$row,['order_number'=>$order->order_number,'available'=>$row->available_quantity]);
            } else {
                $available=$item->variation?->stock_quantity ?? $item->product?->stock_quantity;
                if($available!==null && (int)$available<=$threshold) $webhooks->dispatch('inventory.low',['product_id'=>$item->product_id,'variation_id'=>$item->product_variation_id,'available'=>(int)$available],['order_number'=>$order->order_number]);
            }
        }
    }
}
