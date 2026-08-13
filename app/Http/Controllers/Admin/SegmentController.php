<?php
namespace App\Http\Controllers\Admin;use App\Http\Controllers\Controller;use App\Models\{CustomerSegment,User};use Illuminate\Http\Request;use Illuminate\Support\Str;
class SegmentController extends Controller{
 public function index(){ $segments=CustomerSegment::withCount('users')->latest()->get();return view('admin.segments.index',compact('segments'));}
 public function store(Request $r){$d=$this->validateData($r);$s=CustomerSegment::create($this->payload($d));$this->refreshMembers($s);return back()->with('success','Customer segment created and evaluated.');}
 public function update(Request $r,CustomerSegment $segment){$d=$this->validateData($r,$segment->id);$segment->update($this->payload($d));$this->refreshMembers($segment);return back()->with('success','Segment updated.');}
 public function refresh(CustomerSegment $segment){$this->refreshMembers($segment);return back()->with('success','Segment membership refreshed.');}
 public function destroy(CustomerSegment $segment){$segment->delete();return back()->with('success','Segment deleted.');}
 private function validateData(Request $r,$id=null){return $r->validate(['name'=>'required|string|max:120','slug'=>'nullable|string|max:140|unique:customer_segments,slug,'.($id??'NULL').',id','description'=>'nullable|string|max:1000','min_orders'=>'nullable|integer|min:0','min_spend'=>'nullable|numeric|min:0','inactive_days'=>'nullable|integer|min:0','status'=>'nullable|boolean']);}
 private function payload($d){return ['name'=>$d['name'],'slug'=>Str::slug($d['slug']??$d['name']),'description'=>$d['description']??null,'rules'=>['min_orders'=>(int)($d['min_orders']??0),'min_spend'=>(float)($d['min_spend']??0),'inactive_days'=>(int)($d['inactive_days']??0)],'status'=>(bool)($d['status']??false)];}
 private function refreshMembers(CustomerSegment $s){$rules=$s->rules??[];$users=User::where('role','!=','admin')->withCount('orders')->withSum('orders as lifetime_spend','total')->withMax('orders as last_order_at','created_at')->get();$ids=$users->filter(function($u)use($rules){if($u->orders_count<(int)($rules['min_orders']??0))return false;if((float)($u->lifetime_spend??0)<(float)($rules['min_spend']??0))return false;$days=(int)($rules['inactive_days']??0);if($days>0 && $u->last_order_at && now()->diffInDays($u->last_order_at)<$days)return false;return true;})->pluck('id');$s->users()->sync($ids);}
}
