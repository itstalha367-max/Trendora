<?php
namespace App\Http\Controllers\Admin;use App\Http\Controllers\Controller;use App\Models\{EmailTemplate,NotificationTemplate};use Illuminate\Http\Request;
class TemplateController extends Controller{
 public function emails(){return view('admin.templates.emails',['templates'=>EmailTemplate::orderBy('name')->get()]);}
 public function updateEmail(Request $r,EmailTemplate $template){$d=$r->validate(['name'=>'required|string|max:150','subject'=>'required|string|max:255','content'=>'required|string','status'=>'nullable|boolean']);$d['status']=$r->boolean('status');$template->update($d);return back()->with('success','Email template updated.');}
 public function notifications(){return view('admin.templates.notifications',['templates'=>NotificationTemplate::orderBy('name')->get()]);}
 public function updateNotification(Request $r,NotificationTemplate $template){$d=$r->validate(['name'=>'required|string|max:150','title'=>'required|string|max:255','content'=>'required|string','channels'=>'nullable|array','channels.*'=>'in:database,email','status'=>'nullable|boolean']);$d['channels']=$d['channels']??['database'];$d['status']=$r->boolean('status');$template->update($d);return back()->with('success','Notification template updated.');}
}
