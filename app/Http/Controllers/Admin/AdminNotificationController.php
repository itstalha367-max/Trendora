<?php
namespace App\Http\Controllers\Admin;use App\Http\Controllers\Controller;
class AdminNotificationController extends Controller{public function index(){return view('admin.notifications.index',['notifications'=>auth()->user()->notifications()->latest()->paginate(25)]);}public function read(){auth()->user()->unreadNotifications->markAsRead();return back()->with('success','Admin notifications marked as read.');}}

