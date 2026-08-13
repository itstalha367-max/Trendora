<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;use App\Models\Collection;use App\Models\Product;use Illuminate\Http\Request;use Illuminate\Support\Str;use Illuminate\Validation\Rule;
class CollectionController extends Controller {
 public function index(){ $collections=Collection::withCount('products')->latest()->paginate(15);return view('admin.collections.index',compact('collections'));}
 public function create(){return view('admin.collections.form',['collection'=>new Collection,'products'=>Product::orderBy('name')->get(['id','name','sku'])]);}
 public function store(Request $r){$data=$this->valid($r);$data['slug']=Str::slug($data['slug']??$data['name']);$data['featured']=$r->boolean('featured');$data['status']=$r->boolean('status');$products=$data['products']??[];unset($data['products']);if($r->hasFile('image'))$data['image']=$r->file('image')->store('collections','public');$c=Collection::create($data);$c->products()->sync($products);return redirect()->route('admin.collections.index')->with('success','Collection created.');}
 public function edit(Collection $collection){return view('admin.collections.form',compact('collection')+['products'=>Product::orderBy('name')->get(['id','name','sku'])]);}
 public function update(Request $r,Collection $collection){$data=$this->valid($r,$collection);$data['slug']=Str::slug($data['slug']??$data['name']);$data['featured']=$r->boolean('featured');$data['status']=$r->boolean('status');$products=$data['products']??[];unset($data['products']);if($r->hasFile('image'))$data['image']=$r->file('image')->store('collections','public');$collection->update($data);$collection->products()->sync($products);return redirect()->route('admin.collections.index')->with('success','Collection updated.');}
 public function destroy(Collection $collection){$collection->delete();return back()->with('success','Collection deleted.');}
 private function valid(Request $r,?Collection $c=null){return $r->validate(['name'=>'required|string|max:140','slug'=>['nullable','string','max:160',Rule::unique('collections','slug')->ignore($c?->id)],'description'=>'nullable|string','image'=>'nullable|image|max:4096','starts_at'=>'nullable|date','ends_at'=>'nullable|date|after_or_equal:starts_at','products'=>'nullable|array','products.*'=>'exists:products,id']);}
}
