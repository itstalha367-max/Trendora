<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;use App\Models\Banner;use Illuminate\Http\Request;
class BannerController extends Controller {
 public function index(){ $banners=Banner::orderBy('sort_order')->latest()->paginate(15);return view('admin.banners.index',compact('banners'));}
 public function create(){return view('admin.banners.form',['banner'=>new Banner]);}
 public function store(Request $r){$data=$this->valid($r);$data['status']=$r->boolean('status');if($r->hasFile('image'))$data['image']=$r->file('image')->store('banners','public');Banner::create($data);return redirect()->route('admin.banners.index')->with('success','Banner created.');}
 public function edit(Banner $banner){return view('admin.banners.form',compact('banner'));}
 public function update(Request $r,Banner $banner){$data=$this->valid($r);$data['status']=$r->boolean('status');if($r->hasFile('image'))$data['image']=$r->file('image')->store('banners','public');$banner->update($data);return redirect()->route('admin.banners.index')->with('success','Banner updated.');}
 public function destroy(Banner $banner){$banner->delete();return back()->with('success','Banner deleted.');}
 private function valid(Request $r){return $r->validate(['title'=>'required|string|max:160','subtitle'=>'nullable|string|max:255','image'=>'nullable|image|max:5120','button_text'=>'nullable|string|max:80','button_url'=>'nullable|string|max:255','placement'=>'required|in:hero,homepage,category,checkout,sidebar','sort_order'=>'nullable|integer|min:0','starts_at'=>'nullable|date','ends_at'=>'nullable|date|after_or_equal:starts_at']);}
}
