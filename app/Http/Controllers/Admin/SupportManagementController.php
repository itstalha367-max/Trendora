<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use Illuminate\Http\Request;
use App\Notifications\CommerceNotification;
use App\Services\TemplateRenderer;
class SupportManagementController extends Controller {
 public function index(Request $r){$q=SupportTicket::with('user')->withCount('messages')->latest('last_reply_at'); if($r->filled('status'))$q->where('status',$r->status);if($r->filled('priority'))$q->where('priority',$r->priority);if($r->filled('q')){$t=$r->q;$q->where(fn($x)=>$x->where('ticket_number','like',"%$t%")->orWhere('subject','like',"%$t%"));}$tickets=$q->paginate(15)->withQueryString();$stats=['open'=>SupportTicket::where('status','open')->count(),'progress'=>SupportTicket::where('status','in_progress')->count(),'waiting'=>SupportTicket::where('status','waiting_customer')->count(),'urgent'=>SupportTicket::where('priority','urgent')->whereNotIn('status',['resolved','closed'])->count()];return view('admin.support.index',compact('tickets','stats'));}
 public function show(SupportTicket $ticket){$ticket->load(['user','messages.user']);return view('admin.support.show',compact('ticket'));}
 public function reply(Request $r,SupportTicket $ticket, TemplateRenderer $templates){$data=$r->validate(['message'=>'required|string|max:5000','status'=>'nullable|in:open,in_progress,waiting_customer,resolved,closed']);$ticket->messages()->create(['user_id'=>auth()->id(),'is_staff'=>true,'message'=>$data['message']]);$ticket->update(['status'=>$data['status']??'waiting_customer','last_reply_at'=>now()]);$ticket->loadMissing('user');$notificationCopy=$templates->notification('support_reply',['ticket_number'=>$ticket->ticket_number],'Support reply','Your support ticket '.$ticket->ticket_number.' has a new reply.');$ticket->user?->notify(new CommerceNotification($notificationCopy['title'],$notificationCopy['message'],route('support.show',$ticket),'fa-headset'));return back()->with('success','Reply sent to customer.');}
 public function update(Request $r,SupportTicket $ticket){$ticket->update($r->validate(['status'=>'required|in:open,in_progress,waiting_customer,resolved,closed','priority'=>'required|in:low,normal,high,urgent']));return back()->with('success','Ticket updated.');}
}
