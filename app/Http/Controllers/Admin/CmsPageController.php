<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\CmsPage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
class CmsPageController extends Controller {
 public function index(){ $pages=CmsPage::orderBy('sort_order')->orderBy('title')->paginate(20); return view('admin.cms.index',compact('pages')); }
 public function create(){ return view('admin.cms.form',['page'=>new CmsPage]); }
 public function store(Request $r){$data=$this->validatePage($r);$data['slug']=$data['slug']?:Str::slug($data['title']);$data['status']=$r->boolean('status');CmsPage::create($data);return redirect()->route('admin.cms.index')->with('success','CMS page created.');}
 public function edit(CmsPage $cms){return view('admin.cms.form',['page'=>$cms]);}
 public function update(Request $r,CmsPage $cms){$data=$this->validatePage($r,$cms->id);$data['slug']=$data['slug']?:Str::slug($data['title']);$data['status']=$r->boolean('status');$cms->update($data);return redirect()->route('admin.cms.index')->with('success','CMS page updated.');}
 public function destroy(CmsPage $cms){$cms->delete();return back()->with('success','CMS page deleted.');}
 private function validatePage(Request $r,?int $id=null):array{return $r->validate(['title'=>'required|string|max:160','slug'=>'nullable|string|max:180|unique:cms_pages,slug,'.($id??'NULL'),'eyebrow'=>'nullable|string|max:120','excerpt'=>'nullable|string|max:500','content'=>'nullable|string','meta_title'=>'nullable|string|max:180','meta_description'=>'nullable|string|max:500','sort_order'=>'nullable|integer|min:0']);}
}
