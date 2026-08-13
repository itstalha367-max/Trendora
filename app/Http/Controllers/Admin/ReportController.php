<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AffiliateReferral;
use App\Models\Campaign;
use App\Models\Inventory;
use App\Models\Order;
use App\Models\Product;
use App\Models\Promotion;
use App\Models\StockMovement;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index(){ return view('admin.reports.index'); }

    public function sales(Request $request)
    {
        [$startDate,$endDate,$period] = $this->range($request->period);
        $salesData = Order::whereBetween('created_at',[$startDate,$endDate])->where('payment_status','paid')
            ->selectRaw('DATE(created_at) as date, SUM(total) as revenue, COUNT(*) as orders')->groupBy('date')->orderBy('date')->get();
        $totalOrders = Order::whereBetween('created_at',[$startDate,$endDate])->count();
        $totalRevenue = Order::whereBetween('created_at',[$startDate,$endDate])->where('payment_status','paid')->sum('total');
        $summary=['total_revenue'=>$totalRevenue,'total_orders'=>$totalOrders,'avg_order_value'=>$totalOrders ? $totalRevenue/$totalOrders : 0];
        $topProducts=Product::withCount(['orderItems'=>fn($q)=>$q->whereHas('order',fn($o)=>$o->whereBetween('created_at',[$startDate,$endDate])->where('payment_status','paid'))])
            ->withSum(['orderItems'=>fn($q)=>$q->whereHas('order',fn($o)=>$o->whereBetween('created_at',[$startDate,$endDate])->where('payment_status','paid'))],'total')
            ->orderByDesc('order_items_count')->take(10)->get();
        return view('admin.reports.sales',compact('salesData','summary','topProducts','period'));
    }

    public function products(Request $request)
    {
        $products=Product::with(['category','brand'])->withCount('orderItems')->withSum('orderItems','total')->orderByDesc('order_items_count')->take(50)->get();
        return view('admin.reports.products',compact('products'));
    }

    public function users(Request $request)
    {
        [$startDate,$endDate,$period]=$this->range($request->period);
        $newUsers=User::whereBetween('created_at',[$startDate,$endDate])->selectRaw('DATE(created_at) as date, COUNT(*) as count')->groupBy('date')->orderBy('date')->get();
        $totalUsers=User::count(); $newUsersCount=User::whereBetween('created_at',[$startDate,$endDate])->count();
        $buyers=User::whereHas('orders')->count();
        $repeatBuyers=User::withCount('orders')->get()->where('orders_count','>',1)->count();
        return view('admin.reports.users',compact('newUsers','totalUsers','newUsersCount','period','buyers','repeatBuyers'));
    }

    public function inventory(Request $request)
    {
        $inventory=Inventory::with(['warehouse','product','variation'])
            ->when($request->filled('warehouse_id'),fn($q)=>$q->where('warehouse_id',$request->warehouse_id))
            ->when($request->boolean('low_stock'),fn($q)=>$q->whereColumn('quantity','<=','reorder_level'))
            ->orderByRaw('(quantity - reserved_quantity) asc')->paginate(30)->withQueryString();
        $summary=[
            'units'=>Inventory::sum('quantity'),
            'reserved'=>Inventory::sum('reserved_quantity'),
            'low'=>Inventory::whereColumn('quantity','<=','reorder_level')->count(),
            'locations'=>Inventory::distinct('warehouse_id')->count('warehouse_id'),
            'movement_30d'=>StockMovement::where('created_at','>=',now()->subDays(30))->sum('quantity'),
        ];
        $warehouses=\App\Models\Warehouse::orderBy('name')->get();
        return view('admin.reports.inventory',compact('inventory','summary','warehouses'));
    }

    public function marketing(Request $request)
    {
        [$startDate,$endDate,$period]=$this->range($request->period);
        $campaigns=Campaign::where(function($q)use($startDate,$endDate){$q->whereBetween('starts_at',[$startDate,$endDate])->orWhereBetween('created_at',[$startDate,$endDate]);})->latest()->get();
        $promotions=Promotion::whereBetween('created_at',[$startDate,$endDate])->orderByDesc('usage_count')->get();
        $affiliateCommission=AffiliateReferral::whereBetween('created_at',[$startDate,$endDate])->sum('commission_amount');
        $summary=[
            'spend'=>$campaigns->sum('spent'),
            'impressions'=>$campaigns->sum('impressions'),
            'clicks'=>$campaigns->sum('clicks'),
            'conversions'=>$campaigns->sum('conversions'),
            'promo_uses'=>$promotions->sum('usage_count'),
            'affiliate_commission'=>$affiliateCommission,
        ];
        return view('admin.reports.marketing',compact('campaigns','promotions','summary','period'));
    }

    public function export(Request $request, string $report): StreamedResponse
    {
        abort_unless(in_array($report,['sales','products','customers','inventory','marketing'],true),404);
        $filename='trendora-'.$report.'-'.now()->format('Y-m-d-His').'.csv';
        return response()->streamDownload(function()use($report){
            $out=fopen('php://output','w');
            if($report==='sales'){
                fputcsv($out,['Order','Date','Customer','Payment','Status','Subtotal','Tax','Shipping','Discount','Total']);
                Order::with('user')->latest()->chunk(300,function($rows)use($out){foreach($rows as $o)fputcsv($out,[$o->order_number,$o->created_at,$o->user?->email,$o->payment_status,$o->order_status,$o->subtotal,$o->tax,$o->shipping_cost,$o->discount,$o->total]);});
            } elseif($report==='products'){
                fputcsv($out,['Product','SKU','Category','Brand','Stock','Price','Orders','Revenue']);
                Product::with(['category','brand'])->withCount('orderItems')->withSum('orderItems','total')->chunk(300,function($rows)use($out){foreach($rows as $p)fputcsv($out,[$p->name,$p->sku,$p->category?->name,$p->brand?->name,$p->stock_quantity,$p->price,$p->order_items_count,$p->order_items_sum_total]);});
            } elseif($report==='customers'){
                fputcsv($out,['Name','Email','Joined','Orders','Lifetime value']);
                User::withCount('orders')->withSum('orders','total')->chunk(300,function($rows)use($out){foreach($rows as $u)fputcsv($out,[$u->name,$u->email,$u->created_at,$u->orders_count,$u->orders_sum_total]);});
            } elseif($report==='inventory'){
                fputcsv($out,['Warehouse','Product','Variant','SKU','Quantity','Reserved','Available','Reorder level']);
                Inventory::with(['warehouse','product','variation'])->chunk(300,function($rows)use($out){foreach($rows as $i)fputcsv($out,[$i->warehouse?->name,$i->product?->name,$i->variation?->attribute_value,$i->sku,$i->quantity,$i->reserved_quantity,$i->available_quantity,$i->reorder_level]);});
            } else {
                fputcsv($out,['Campaign','Type','Status','Budget','Spent','Impressions','Clicks','Conversions','CTR %','Conversion %']);
                Campaign::latest()->chunk(300,function($rows)use($out){foreach($rows as $c)fputcsv($out,[$c->name,$c->type,$c->status,$c->budget,$c->spent,$c->impressions,$c->clicks,$c->conversions,$c->ctr,$c->conversion_rate]);});
            }
            fclose($out);
        },$filename,['Content-Type'=>'text/csv']);
    }

    private function range(?string $period): array
    {
        $period=in_array($period,['today','week','month','year'],true)?$period:'month';
        return match($period){
            'today'=>[Carbon::today()->startOfDay(),Carbon::today()->endOfDay(),$period],
            'week'=>[Carbon::now()->startOfWeek(),Carbon::now()->endOfWeek(),$period],
            'year'=>[Carbon::now()->startOfYear(),Carbon::now()->endOfYear(),$period],
            default=>[Carbon::now()->startOfMonth(),Carbon::now()->endOfMonth(),$period],
        };
    }
}
