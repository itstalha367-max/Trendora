<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function showAdminLoginForm()
    {
        if (Auth::check() && Auth::user()->role === 'admin') return redirect()->route('admin.dashboard');
        return view('auth.admin-login');
    }

    public function adminLogin(Request $request)
    {
        $credentials = $request->validate(['email'=>'required|email','password'=>'required']);
        if (!Auth::attempt($credentials)) return back()->withErrors(['email'=>'The provided credentials do not match our records.'])->onlyInput('email');
        $request->session()->regenerate();
        if (Auth::user()->role !== 'admin') {
            Auth::logout(); $request->session()->regenerateToken();
            return back()->withErrors(['email'=>'This sign-in is restricted to Trendora administrators.'])->onlyInput('email');
        }
        if (Auth::user()->google2fa_secret) { session(['2fa_verified'=>false]); return redirect()->route('2fa.verify'); }
        return redirect()->intended(route('admin.dashboard'));
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            // Check if user has 2FA enabled
            if (Auth::user()->google2fa_secret) {
                session(['2fa_verified' => false]);
                return redirect()->route('2fa.verify');
            }
            
            if (Auth::user()->role === 'admin') {
                return redirect()->route('admin.dashboard');
            }
            
            return redirect()->intended('/');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        session()->forget('2fa_verified');
        return redirect('/');
    }
}