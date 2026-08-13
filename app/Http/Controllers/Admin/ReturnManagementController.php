<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\ReturnRequest;
use Illuminate\Http\Request;
class ReturnManagementController extends Controller {
 public function index(Request $r){$q=ReturnRequest::with(['user','order'])->latest(); if($r->filled('status'))$q->where('status',$r->status); if($r->filled('type'))$q->where('type',$r->type); if($r->filled('q')){$term=$r->q;$q->where(function($x)use($term){$x->where('request_number','like',"%$term%")->orWhereHas('user',fn($u)=>$u->where('name','like',"%$term%")->orWhere('email','like',"%$term%"));});} $returns=$q->paginate(15)->withQueryString(); $stats=['pending'=>ReturnRequest::where('status','pending')->count(),'approved'=>ReturnRequest::where('status','approved')->count(),'refunded'=>ReturnRequest::where('status','refunded')->count(),'requested'=>ReturnRequest::sum('requested_amount')]; return view('admin.returns.index',compact('returns','stats'));}
 public function show(ReturnRequest $returnRequest){$returnRequest->load(['user','order.items.product']);return view('admin.returns.show',compact('returnRequest'));}
 public function update(Request $r,ReturnRequest $returnRequest){$data=$r->validate(['status'=>'required|in:pending,approved,rejected,received,refunded,closed','admin_note'=>'nullable|string|max:3000']);if($data['status']==='refunded' && !$returnRequest->refunds()->where('status','processed')->exists())return redirect()->route('admin.finance.refunds',['order'=>$returnRequest->order_id,'return_request'=>$returnRequest->id])->with('error','Record the financial refund first; the return will then be marked refunded automatically.');$returnRequest->update($data);return back()->with('success','Return request updated.');}
}
