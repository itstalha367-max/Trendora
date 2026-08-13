<?php
namespace App\Http\Controllers\Admin;use App\Http\Controllers\Controller;use Illuminate\Http\Request;use Illuminate\Support\Facades\Hash;
class AdminProfileController extends Controller{public function edit(){return view('admin.profile.index',['user'=>auth()->user()]);}public function update(Request $r){$d=$r->validate(['name'=>'required|string|max:120','email'=>'required|email|max:255|unique:users,email,'.auth()->id(),'phone'=>'nullable|string|max:40']);auth()->user()->update($d);return back()->with('success','Admin profile updated.');}public function password(Request $r){$d=$r->validate(['current_password'=>'required|current_password','password'=>'required|min:8|confirmed']);auth()->user()->update(['password'=>$d['password']]);return back()->with('success','Password updated.');}}

