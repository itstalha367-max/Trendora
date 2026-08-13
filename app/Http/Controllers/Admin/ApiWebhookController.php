<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApiKey;
use App\Models\Webhook;
use App\Models\WebhookDelivery;
use App\Services\WebhookDispatcher;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ApiWebhookController extends Controller
{
    public function index()
    {
        return view('admin.developer.index', [
            'keys'=>ApiKey::with('creator')->latest()->get(),
            'webhooks'=>Webhook::withCount('deliveries')->latest()->get(),
            'deliveries'=>WebhookDelivery::with('webhook')->latest()->take(40)->get(),
        ]);
    }

    public function createKey(Request $request)
    {
        $data=$request->validate(['name'=>'required|string|max:120','abilities'=>'nullable|array','abilities.*'=>'in:catalog.read,orders.read,customers.read,inventory.read','expires_at'=>'nullable|date|after:today']);
        $plain='tr_live_'.Str::random(48);
        ApiKey::create(['name'=>$data['name'],'key_prefix'=>substr($plain,0,18),'key_hash'=>hash('sha256',$plain),'abilities'=>$data['abilities']??['catalog.read'],'expires_at'=>$data['expires_at']??null,'created_by'=>auth()->id()]);
        return back()->with('success','API key created. Copy it now — it will not be shown again.')->with('new_api_key',$plain);
    }

    public function revokeKey(ApiKey $apiKey){ $apiKey->update(['revoked_at'=>now()]); return back()->with('success','API key revoked.'); }

    public function storeWebhook(Request $request)
    {
        $data=$request->validate(['name'=>'required|string|max:120','url'=>'required|url:https|max:2000','secret'=>'nullable|string|max:500','events'=>'required|array|min:1','events.*'=>'in:order.created,order.updated,order.refunded,product.updated,inventory.low','status'=>'nullable|boolean']);
        $webhook=new Webhook(['name'=>$data['name'],'url'=>$data['url'],'events'=>$data['events'],'status'=>$request->boolean('status')]);
        $webhook->secret=$data['secret']?:Str::random(40); $webhook->save();
        return back()->with('success','Webhook endpoint created.');
    }

    public function updateWebhook(Request $request, Webhook $webhook)
    {
        $data=$request->validate(['name'=>'required|string|max:120','url'=>'required|url:https|max:2000','secret'=>'nullable|string|max:500','events'=>'required|array|min:1','events.*'=>'in:order.created,order.updated,order.refunded,product.updated,inventory.low','status'=>'nullable|boolean']);
        $webhook->fill(['name'=>$data['name'],'url'=>$data['url'],'events'=>$data['events'],'status'=>$request->boolean('status')]);
        if(!empty($data['secret'])) $webhook->secret=$data['secret'];
        $webhook->save(); return back()->with('success','Webhook updated.');
    }

    public function destroyWebhook(Webhook $webhook){ $webhook->delete(); return back()->with('success','Webhook deleted.'); }

    public function testWebhook(Webhook $webhook, WebhookDispatcher $dispatcher)
    {
        abort_unless($webhook->status,422,'Enable the webhook first.');
        $delivery=$webhook->deliveries()->create([
            'event'=>'trendora.webhook.test',
            'payload'=>['id'=>(string)Str::uuid(),'event'=>'trendora.webhook.test','created_at'=>now()->toIso8601String(),'data'=>['store'=>'Trendora','message'=>'Webhook connectivity test']],
            'status'=>'pending','attempted_at'=>now(),'attempt_count'=>0,
        ]);
        $success=$dispatcher->deliver($delivery);
        $delivery->refresh();
        return back()->with($success?'success':'error','Webhook test '.($success?'delivered':'failed').' · HTTP '.($delivery->response_code ?: 'n/a').'.');
    }

    public function retryDelivery(WebhookDelivery $delivery, WebhookDispatcher $dispatcher)
    {
        abort_if($delivery->status==='delivered',422,'Delivered webhooks do not need a retry.');
        abort_if((int)$delivery->attempt_count>=8,422,'This delivery reached the maximum manual retry count.');
        $success=$dispatcher->deliver($delivery);
        return back()->with($success?'success':'error',$success?'Webhook delivery succeeded.':'Webhook retry failed; response details were recorded.');
    }
}
