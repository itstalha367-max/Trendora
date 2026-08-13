<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\ContactSubmission;
use Illuminate\Http\Request;
class ContactManagementController extends Controller {
 public function index(Request $r){$q=ContactSubmission::with('user')->latest();if($r->filled('status'))$q->where('status',$r->status);if($r->filled('q')){$t=$r->q;$q->where(fn($x)=>$x->where('name','like',"%$t%")->orWhere('email','like',"%$t%")->orWhere('subject','like',"%$t%"));}$contacts=$q->paginate(15)->withQueryString();return view('admin.contacts.index',compact('contacts'));}
 public function show(ContactSubmission $contact){if($contact->status==='new')$contact->update(['status'=>'read']);return view('admin.contacts.show',compact('contact'));}
 public function update(Request $r,ContactSubmission $contact){$contact->update($r->validate(['status'=>'required|in:new,read,replied,closed']));return back()->with('success','Contact status updated.');}
 public function destroy(ContactSubmission $contact){$contact->delete();return redirect()->route('admin.contacts.index')->with('success','Contact message deleted.');}
}
