<?php
namespace App\Http\Controllers\Admin;use App\Http\Controllers\Controller;use App\Models\Integration;use Illuminate\Http\Request;use Illuminate\Support\Str;
class IntegrationController extends Controller{
 public function index(){ $this->seedDefaults();return view('admin.integrations.index',['integrations'=>Integration::orderBy('category')->orderBy('name')->get()]);}
 public function update(Request $r,Integration $integration){$d=$r->validate(['enabled'=>'nullable|boolean','api_key'=>'nullable|string|max:2000','api_secret'=>'nullable|string|max:2000','endpoint'=>'nullable|url|max:2000','account_id'=>'nullable|string|max:255']);$config=$integration->config;foreach(['api_key','api_secret','endpoint','account_id'] as $k)if($r->filled($k))$config[$k]=$d[$k];$integration->config=$config;$integration->enabled=$r->boolean('enabled');$integration->health_status=$integration->enabled?'configured':'disabled';$integration->save();return back()->with('success',$integration->name.' integration updated.');}
 private function seedDefaults(){foreach([['Google Analytics','google-analytics','analytics'],['Meta Pixel','meta-pixel','marketing'],['Google Merchant Center','google-merchant','sales-channel'],['Slack Alerts','slack','operations'],['Shippo / Shipping API','shippo','shipping']] as [$n,$s,$c])Integration::firstOrCreate(['slug'=>$s],['name'=>$n,'category'=>$c]);}
}
