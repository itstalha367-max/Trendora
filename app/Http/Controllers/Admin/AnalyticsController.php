<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\AbandonedCart;
use App\Models\Inventory;
use App\Models\Order;
use App\Models\Refund;
use App\Models\User;
use Carbon\Carbon;
class AnalyticsController extends Controller {
 public function index(){
  $from=Carbon::now()->subDays(29)->startOfDay();
  $daily=Order::where('created_at','>=',$from)->selectRaw("DATE(created_at) d, COUNT(*) orders, SUM(CASE WHEN payment_status IN ('paid','refunded') THEN total ELSE 0 END) revenue")->groupBy('d')->orderBy('d')->get();
  $stats=['revenue'=>Order::where('created_at','>=',$from)->whereIn('payment_status',['paid','refunded'])->sum('total'),'orders'=>Order::where('created_at','>=',$from)->count(),'refunds'=>Refund::where('created_at','>=',$from)->where('status','processed')->sum('amount'),'customers'=>User::where('created_at','>=',$from)->count(),'abandoned'=>AbandonedCart::where('created_at','>=',$from)->count(),'low_stock'=>Inventory::whereColumn('quantity','<=','reorder_level')->count()];
  $stats['aov']=$stats['orders']?($stats['revenue']/$stats['orders']):0;
  $topCountries=Order::selectRaw('shipping_country, COUNT(*) orders, SUM(total) revenue')->where('created_at','>=',$from)->groupBy('shipping_country')->orderByDesc('revenue')->take(6)->get();
  $paymentMix=Order::selectRaw("COALESCE(payment_gateway,'unknown') gateway, COUNT(*) orders, SUM(total) total")->where('created_at','>=',$from)->groupBy('payment_gateway')->orderByDesc('orders')->get();
  $repeatCustomers=User::whereHas('orders',fn($q)=>$q->where('created_at','>=',$from),'>=',2)->count();
  return view('admin.analytics.index',compact('daily','stats','topCountries','paymentMix','repeatCustomers'));
 }
}
