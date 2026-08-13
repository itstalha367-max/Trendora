<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;use App\Models\Supplier;use App\Models\Product;use Illuminate\Http\Request;
class SupplierController extends Controller {
 public function index(){ $suppliers=Supplier::withCount('products')->latest()->paginate(15);return view('admin.suppliers.index',compact('suppliers'));}
 public function create(){return view('admin.suppliers.form',['supplier'=>new Supplier,'products'=>Product::orderBy('name')->get(['id','name','sku'])]);}
 public function store(Request $r){$data=$this->valid($r);$products=$data['products']??[];unset($data['products']);$s=Supplier::create($data);$s->products()->sync($products);return redirect()->route('admin.suppliers.index')->with('success','Supplier created.');}
 public function edit(Supplier $supplier){return view('admin.suppliers.form',compact('supplier')+['products'=>Product::orderBy('name')->get(['id','name','sku'])]);}
 public function update(Request $r,Supplier $supplier){$data=$this->valid($r);$products=$data['products']??[];unset($data['products']);$supplier->update($data);$supplier->products()->sync($products);return redirect()->route('admin.suppliers.index')->with('success','Supplier updated.');}
 public function destroy(Supplier $supplier){$supplier->delete();return back()->with('success','Supplier deleted.');}
 private function valid(Request $r){return $r->validate(['name'=>'required|string|max:150','contact_name'=>'nullable|string|max:120','email'=>'nullable|email','phone'=>'nullable|string|max:40','website'=>'nullable|url|max:255','address'=>'nullable|string|max:500','city'=>'nullable|string|max:100','country'=>'nullable|string|max:100','tax_id'=>'nullable|string|max:100','lead_time_days'=>'nullable|integer|min:0|max:365','status'=>'required|in:active,inactive','notes'=>'nullable|string|max:1500','products'=>'nullable|array','products.*'=>'exists:products,id']);}
}
