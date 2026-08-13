<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;use App\Models\Brand;use Illuminate\Http\Request;use Illuminate\Support\Str;use Illuminate\Validation\Rule;
class BrandController extends Controller {
 public function index(Request $r){$q=Brand::withCount('products')->orderBy('sort_order');if($r->filled('q'))$q->where('name','like','%'.$r->q.'%');$brands=$q->paginate(15)->withQueryString();return view('admin.brands.index',compact('brands'));}
 public function create(){return view('admin.brands.form',['brand'=>new Brand]);}
 public function store(Request $r){$data=$this->valid($r);$data['slug']=Str::slug($data['slug']??$data['name']);$data['featured']=$r->boolean('featured');$data['status']=$r->boolean('status');if($r->hasFile('logo'))$data['logo']=$r->file('logo')->store('brands','public');Brand::create($data);return redirect()->route('admin.brands.index')->with('success','Brand created.');}
 public function edit(Brand $brand){return view('admin.brands.form',compact('brand'));}
 public function update(Request $r,Brand $brand){$data=$this->valid($r,$brand);$data['slug']=Str::slug($data['slug']??$data['name']);$data['featured']=$r->boolean('featured');$data['status']=$r->boolean('status');if($r->hasFile('logo'))$data['logo']=$r->file('logo')->store('brands','public');$brand->update($data);return redirect()->route('admin.brands.index')->with('success','Brand updated.');}
 public function destroy(Brand $brand){$brand->delete();return back()->with('success','Brand deleted.');}
 private function valid(Request $r,?Brand $b=null){return $r->validate(['name'=>'required|string|max:120','slug'=>['nullable','string','max:140',Rule::unique('brands','slug')->ignore($b?->id)],'description'=>'nullable|string','website'=>'nullable|url|max:255','sort_order'=>'nullable|integer|min:0','logo'=>'nullable|image|max:3072']);}
}
