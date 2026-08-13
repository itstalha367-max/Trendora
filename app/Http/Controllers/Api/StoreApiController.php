<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;use App\Models\{Order,Product};use Illuminate\Http\Request;
class StoreApiController extends Controller
{
    public function products(Request $request){$products=Product::with(['category:id,name,slug','brand:id,name,slug'])->where('status',true)->select(['id','category_id','brand_id','name','slug','sku','price','compare_price','stock_quantity','updated_at'])->latest('updated_at')->paginate(min(100,max(1,(int)$request->get('per_page',25))));return response()->json($products);}
    public function orders(Request $request){$orders=Order::with(['items:id,order_id,product_id,product_name,product_sku,quantity,price,total'])->select(['id','user_id','order_number','subtotal','discount','tax','shipping_cost','total','payment_status','order_status','shipping_country','created_at','updated_at'])->latest()->paginate(min(100,max(1,(int)$request->get('per_page',25))));return response()->json($orders);}
}
