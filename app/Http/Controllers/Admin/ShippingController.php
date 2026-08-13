<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;use App\Models\ShippingZone;use App\Models\ShippingMethod;use Illuminate\Http\Request;
class ShippingController extends Controller {
 public function index(){ $zones=ShippingZone::with('methods')->latest()->get();return view('admin.shipping.index',compact('zones'));}
 public function storeZone(Request $r){$data=$r->validate(['name'=>'required|string|max:120','countries'=>'nullable|string','states'=>'nullable|string']);$data['countries']=$this->list($data['countries']??'');$data['states']=$this->list($data['states']??'');$data['status']=$r->boolean('status');ShippingZone::create($data);return back()->with('success','Shipping zone created.');}
 public function updateZone(Request $r,ShippingZone $zone){$data=$r->validate(['name'=>'required|string|max:120','countries'=>'nullable|string','states'=>'nullable|string']);$data['countries']=$this->list($data['countries']??'');$data['states']=$this->list($data['states']??'');$data['status']=$r->boolean('status');$zone->update($data);return back()->with('success','Shipping zone updated.');}
 public function destroyZone(ShippingZone $zone){$zone->delete();return back()->with('success','Shipping zone deleted.');}
 public function storeMethod(Request $r,ShippingZone $zone){$data=$r->validate(['name'=>'required|string|max:120','type'=>'required|in:flat_rate,free,local_pickup','cost'=>'required|numeric|min:0','free_over'=>'nullable|numeric|min:0','min_days'=>'nullable|integer|min:0','max_days'=>'nullable|integer|min:0']);$data['status']=$r->boolean('status');$zone->methods()->create($data);return back()->with('success','Shipping method added.');}
 public function destroyMethod(ShippingMethod $method){$method->delete();return back()->with('success','Shipping method deleted.');}
 private function list(string $v):array{return array_values(array_filter(array_map('trim',explode(',',$v))));}
}
