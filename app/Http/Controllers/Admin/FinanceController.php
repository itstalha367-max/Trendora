<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\PaymentTransaction;
use App\Models\Refund;
use App\Models\Setting;
use App\Notifications\CommerceNotification;
use App\Services\TemplateRenderer;
use App\Services\WebhookDispatcher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FinanceController extends Controller
{
    public function index(Request $request)
    {
        $transactions=$this->transactionQuery($request)->paginate(20)->withQueryString();
        $stats=$this->stats();
        return view('admin.finance.index',compact('transactions','stats'));
    }

    public function transactions(Request $request)
    {
        $transactions=$this->transactionQuery($request)->paginate(30)->withQueryString();
        $stats=$this->stats();
        $gateways=PaymentTransaction::whereNotNull('gateway')->distinct()->orderBy('gateway')->pluck('gateway');
        return view('admin.finance.transactions',compact('transactions','stats','gateways'));
    }

    public function markPaid(Order $order, WebhookDispatcher $webhooks)
    {
        if($order->payment_status==='paid') return back()->with('success','Order is already paid.');
        if($order->payment_status==='refunded') return back()->with('error','A fully refunded order cannot be marked paid.');
        DB::transaction(function()use($order){
            $order->update(['payment_status'=>'paid']);
            $tx=$order->transactions()->where('status','pending')->latest()->first();
            if($tx) $tx->update(['status'=>'succeeded','transaction_id'=>$tx->transaction_id ?: 'MANUAL-'.strtoupper(uniqid()),'note'=>'Marked paid by admin']);
            else $order->transactions()->create(['gateway'=>$order->payment_gateway ?: 'manual','transaction_id'=>'MANUAL-'.strtoupper(uniqid()),'type'=>'capture','status'=>'succeeded','amount'=>$order->total,'currency'=>Setting::get('currency','PKR'),'note'=>'Marked paid by admin']);
        });
        $order->user?->notify(new CommerceNotification('Payment confirmed','Payment for '.$order->order_number.' has been confirmed.',route('user.order.detail',$order->id),'fa-circle-check'));
        try{$webhooks->dispatch('order.updated',$order->fresh(['transactions']),['payment_status'=>'paid','source'=>'admin']);}catch(\Throwable $e){\Log::warning('Payment webhook failed: '.$e->getMessage());}
        return back()->with('success','Payment marked as paid.');
    }

    public function refunds(Request $request)
    {
        $refunds=Refund::with(['order.user','processor','returnRequest'])->when($request->filled('status'),fn($q)=>$q->where('status',$request->status))->latest()->paginate(20)->withQueryString();
        $orders=Order::whereIn('payment_status',['paid','refunded'])->whereColumn('refunded_amount','<','total')->latest()->take(100)->get();
        return view('admin.finance.refunds',compact('refunds','orders'));
    }

    public function storeRefund(Request $request, TemplateRenderer $templates, WebhookDispatcher $webhooks)
    {
        $data=$request->validate(['order_id'=>'required|exists:orders,id','amount'=>'required|numeric|min:0.01','method'=>'required|in:original,manual','reason'=>'nullable|string|max:2000','return_request_id'=>'nullable|exists:return_requests,id']);
        $order=Order::findOrFail($data['order_id']);
        if(!in_array($order->payment_status,['paid','refunded'],true)) return back()->withInput()->with('error','Only paid orders can be refunded.');
        if(!empty($data['return_request_id']) && !\App\Models\ReturnRequest::whereKey($data['return_request_id'])->where('order_id',$order->id)->exists()) return back()->withInput()->with('error','Selected return request does not belong to this order.');
        $remaining=max(0,(float)$order->total-(float)$order->refunded_amount);
        if((float)$data['amount']>$remaining) return back()->withInput()->with('error','Refund exceeds remaining refundable amount of '.Setting::get('currency_symbol','Rs').' '.number_format($remaining,2));

        DB::transaction(function()use($data,$order){
            $refund=Refund::create($data+['processed_by'=>auth()->id(),'refund_number'=>'RFD-'.strtoupper(uniqid()),'status'=>'processed','processed_at'=>now()]);
            $order->increment('refunded_amount',$refund->amount); $order->refresh();
            if((float)$order->refunded_amount>=(float)$order->total) $order->update(['payment_status'=>'refunded']);
            $order->transactions()->create(['gateway'=>$order->payment_gateway,'type'=>'refund','status'=>'succeeded','amount'=>$refund->amount,'currency'=>Setting::get('currency','PKR'),'note'=>$refund->reason]);
            if($refund->returnRequest) $refund->returnRequest->update(['status'=>'refunded']);
        });
        $notificationCopy=$templates->notification('refund_processed',['amount'=>Setting::get('currency_symbol','Rs').' '.number_format($data['amount'],2),'order_number'=>$order->order_number],'Refund processed','A refund was processed for '.$order->order_number.'.');
        $order->user?->notify(new CommerceNotification($notificationCopy['title'],$notificationCopy['message'],route('user.order.detail',$order->id),'fa-rotate-left'));
        try{$webhooks->dispatch('order.refunded',$order->fresh(['refunds','transactions']),['refund_amount'=>(float)$data['amount']]);}catch(\Throwable $e){\Log::warning('Refund webhook failed: '.$e->getMessage());}
        return back()->with('success','Refund recorded successfully.');
    }

    private function transactionQuery(Request $request)
    {
        return PaymentTransaction::with('order.user')
            ->when($request->filled('status'),fn($q)=>$q->where('status',$request->status))
            ->when($request->filled('gateway'),fn($q)=>$q->where('gateway',$request->gateway))
            ->when($request->filled('type'),fn($q)=>$q->where('type',$request->type))
            ->latest();
    }

    private function stats(): array
    {
        return [
            'captured'=>PaymentTransaction::where('status','succeeded')->whereIn('type',['charge','capture'])->sum('amount'),
            'refunded'=>Refund::where('status','processed')->sum('amount'),
            'pending'=>PaymentTransaction::where('status','pending')->sum('amount'),
            'failed'=>PaymentTransaction::where('status','failed')->count(),
        ];
    }
}
