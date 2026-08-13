<?php
namespace App\Http\Controllers\Admin;use App\Http\Controllers\Controller;use App\Models\{ActivityLog,ApiKey,User,Webhook};
class SecurityCenterController extends Controller{public function index(){return view('admin.security.index',['admins'=>User::where('role','admin')->with('adminRole')->get(),'twoFactorCount'=>User::where('role','admin')->whereNotNull('google2fa_secret')->count(),'activeKeys'=>ApiKey::whereNull('revoked_at')->count(),'webhookCount'=>Webhook::where('status',true)->count(),'recentActivity'=>ActivityLog::with('user')->latest()->take(20)->get()]);}}

