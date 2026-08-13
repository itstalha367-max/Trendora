<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\Setting;
use App\Models\Warehouse;
use App\Services\WebhookDispatcher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $q=Inventory::with(['warehouse','product','variation']);
        if($request->filled('warehouse')) $q->where('warehouse_id',$request->warehouse);
        if($request->filled('stock')&&$request->stock==='low') $q->whereColumn('quantity','<=','reorder_level');
        if($request->filled('q')){$term=$request->q;$q->where(fn($x)=>$x->where('sku','like',"%$term%")->orWhereHas('product',fn($p)=>$p->where('name','like',"%$term%")));}
        $inventory=$q->orderBy('quantity')->paginate(20)->withQueryString();
        $warehouses=Warehouse::where('status',true)->orderBy('name')->get();
        $stats=['units'=>Inventory::sum('quantity'),'reserved'=>Inventory::sum('reserved_quantity'),'low'=>Inventory::whereColumn('quantity','<=','reorder_level')->count(),'locations'=>Warehouse::where('status',true)->count()];
        return view('admin.inventory.index',compact('inventory','warehouses','stats'));
    }
    public function create(){return view('admin.inventory.form',['item'=>new Inventory,'products'=>Product::with('variations')->orderBy('name')->get(),'warehouses'=>Warehouse::where('status',true)->orderBy('name')->get()]);}
    public function store(Request $request)
    {
        $data=$request->validate(['warehouse_id'=>'required|exists:warehouses,id','product_id'=>'required|exists:products,id','product_variation_id'=>'nullable|exists:product_variations,id','sku'=>'nullable|string|max:120','quantity'=>'required|integer|min:0','reserved_quantity'=>'nullable|integer|min:0','reorder_level'=>'required|integer|min:0','bin_location'=>'nullable|string|max:80']);
        if(!empty($data['product_variation_id']) && !\App\Models\ProductVariation::whereKey($data['product_variation_id'])->where('product_id',$data['product_id'])->exists()) return back()->withInput()->with('error','Selected variation does not belong to that product.');
        $exists=Inventory::where('warehouse_id',$data['warehouse_id'])->where('product_id',$data['product_id'])->where('product_variation_id',$data['product_variation_id']??null)->exists();
        if($exists) return back()->withInput()->with('error','This item already exists in that warehouse. Use stock adjustment instead.');
        Inventory::create($data); return redirect()->route('admin.inventory.index')->with('success','Inventory item created.');
    }
    public function adjust(Request $request, Inventory $inventory, WebhookDispatcher $webhooks)
    {
        $data=$request->validate(['mode'=>'required|in:add,remove,set','quantity'=>'required|integer|min:0','reference'=>'nullable|string|max:120','note'=>'nullable|string|max:500']);
        DB::transaction(function()use($inventory,$data){
            $before=$inventory->quantity; $after=match($data['mode']){'add'=>$before+$data['quantity'],'remove'=>max(0,$before-$data['quantity']),'set'=>$data['quantity']};
            $inventory->update(['quantity'=>$after]);
            $inventory->movements()->create(['user_id'=>auth()->id(),'type'=>'adjustment','quantity'=>$after-$before,'before_quantity'=>$before,'after_quantity'=>$after,'reference'=>$data['reference']??null,'note'=>$data['note']??null]);
            if(!$inventory->product_variation_id) $inventory->product()->update(['stock_quantity'=>Inventory::where('product_id',$inventory->product_id)->whereNull('product_variation_id')->sum('quantity')]);
        });
        $inventory->refresh()->load(['product','warehouse']);
        $threshold=max((int)$inventory->reorder_level,(int)Setting::get('low_stock_threshold',5));
        if($inventory->available_quantity <= $threshold){try{$webhooks->dispatch('inventory.low',$inventory,['source'=>'admin_adjustment','available'=>$inventory->available_quantity]);}catch(\Throwable $e){\Log::warning('Inventory webhook failed: '.$e->getMessage());}}
        return back()->with('success','Stock adjusted.');
    }
    public function movements(Inventory $inventory){$inventory->load(['product','warehouse']);$movements=$inventory->movements()->with('user')->latest()->paginate(25);return view('admin.inventory.movements',compact('inventory','movements'));}
}
