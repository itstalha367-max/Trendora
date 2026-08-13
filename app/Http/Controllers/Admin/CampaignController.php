<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;use App\Models\Campaign;use Illuminate\Http\Request;
class CampaignController extends Controller {
 public function index(){ $campaigns=Campaign::latest()->paginate(15);$stats=['active'=>Campaign::where('status','active')->count(),'budget'=>Campaign::sum('budget'),'spent'=>Campaign::sum('spent'),'conversions'=>Campaign::sum('conversions')];return view('admin.campaigns.index',compact('campaigns','stats'));}
 public function create(){return view('admin.campaigns.form',['campaign'=>new Campaign]);}
 public function store(Request $r){Campaign::create($this->valid($r));return redirect()->route('admin.campaigns.index')->with('success','Campaign created.');}
 public function edit(Campaign $campaign){return view('admin.campaigns.form',compact('campaign'));}
 public function update(Request $r,Campaign $campaign){$campaign->update($this->valid($r));return redirect()->route('admin.campaigns.index')->with('success','Campaign updated.');}
 public function destroy(Campaign $campaign){$campaign->delete();return back()->with('success','Campaign deleted.');}
 private function valid(Request $r){return $r->validate(['name'=>'required|string|max:160','type'=>'required|in:sale,coupon,email,social,seasonal,other','status'=>'required|in:draft,scheduled,active,paused,completed','budget'=>'nullable|numeric|min:0','spent'=>'nullable|numeric|min:0','impressions'=>'nullable|integer|min:0','clicks'=>'nullable|integer|min:0','conversions'=>'nullable|integer|min:0','starts_at'=>'nullable|date','ends_at'=>'nullable|date|after_or_equal:starts_at','notes'=>'nullable|string|max:2000']);}
}
