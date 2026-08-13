<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;use App\Models\Warehouse;use Illuminate\Http\Request;use Illuminate\Validation\Rule;
class WarehouseController extends Controller {
 public function index(){ $warehouses=Warehouse::withCount('inventories')->latest()->paginate(15);return view('admin.warehouses.index',compact('warehouses'));}
 public function create(){return view('admin.warehouses.form',['warehouse'=>new Warehouse]);}
 public function store(Request $r){$data=$this->valid($r);$data['is_default']=$r->boolean('is_default');$data['status']=$r->boolean('status');if($data['is_default'])Warehouse::query()->update(['is_default'=>false]);Warehouse::create($data);return redirect()->route('admin.warehouses.index')->with('success','Warehouse created.');}
 public function edit(Warehouse $warehouse){return view('admin.warehouses.form',compact('warehouse'));}
 public function update(Request $r,Warehouse $warehouse){$data=$this->valid($r,$warehouse);$data['is_default']=$r->boolean('is_default');$data['status']=$r->boolean('status');if($data['is_default'])Warehouse::where('id','!=',$warehouse->id)->update(['is_default'=>false]);$warehouse->update($data);return redirect()->route('admin.warehouses.index')->with('success','Warehouse updated.');}
 public function destroy(Warehouse $warehouse){if($warehouse->inventories()->exists())return back()->with('error','Move/remove inventory before deleting this warehouse.');$warehouse->delete();return back()->with('success','Warehouse deleted.');}
 private function valid(Request $r,?Warehouse $w=null){return $r->validate(['name'=>'required|string|max:120','code'=>['required','string','max:30',Rule::unique('warehouses','code')->ignore($w?->id)],'email'=>'nullable|email','phone'=>'nullable|string|max:40','address'=>'nullable|string|max:500','city'=>'nullable|string|max:100','state'=>'nullable|string|max:100','country'=>'nullable|string|max:100','postal_code'=>'nullable|string|max:30']);}
}
