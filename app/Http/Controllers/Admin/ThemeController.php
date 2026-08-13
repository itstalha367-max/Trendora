<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
class ThemeController extends Controller {
 public function index(){ $theme=['accent'=>Setting::get('theme_accent','#8b5cf6'),'accent_2'=>Setting::get('theme_accent_2','#22d3ee'),'hero_title'=>Setting::get('hero_title','Style that moves with you.'),'hero_subtitle'=>Setting::get('hero_subtitle','Discover curated products, premium experiences and effortless shopping.'),'hero_cta'=>Setting::get('hero_cta','Shop collection'),'announcement'=>Setting::get('announcement','Free delivery on qualifying orders'),'card_radius'=>Setting::get('theme_card_radius','22')];return view('admin.theme.index',compact('theme')); }
 public function update(Request $r){$data=$r->validate(['accent'=>'required|regex:/^#[0-9A-Fa-f]{6}$/','accent_2'=>'required|regex:/^#[0-9A-Fa-f]{6}$/','hero_title'=>'required|string|max:120','hero_subtitle'=>'required|string|max:300','hero_cta'=>'required|string|max:40','announcement'=>'nullable|string|max:160','card_radius'=>'required|integer|min:8|max:40']);foreach($data as $k=>$v)Setting::set($k==='accent'?'theme_accent':($k==='accent_2'?'theme_accent_2':($k==='card_radius'?'theme_card_radius':$k)),$v);return back()->with('success','Storefront theme updated.');}
}
