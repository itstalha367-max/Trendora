<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PragmaRX\Google2FALaravel\Facade as Google2FA;

class TwoFactorController extends Controller
{
    public function showSetup()
    {
        $user = Auth::user();
        
        // Generate secret key
        $secret = Google2FA::generateSecretKey();
        
        // Store secret in session temporarily
        session(['2fa_secret' => $secret]);
        
        // Generate QR Code
        $qrCode = Google2FA::getQRCodeInline(
            config('app.name'),
            $user->email,
            $secret
        );
        
        return view('auth.2fa-setup', compact('qrCode', 'secret'));
    }

    public function enable(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        $user = Auth::user();
        $secret = session('2fa_secret');

        if (!$secret) {
            return redirect()->back()->with('error', 'Please generate a new secret key.');
        }

        // Verify the code
        $valid = Google2FA::verifyKey($secret, $request->code);

        if (!$valid) {
            return redirect()->back()->with('error', 'Invalid verification code. Please try again.');
        }

        // Save secret to user
        $user->google2fa_secret = $secret;
        $user->save();

        session()->forget('2fa_secret');

        return redirect()->route('home')->with('success', 'Two-Factor Authentication enabled successfully!');
    }

    public function showVerify()
    {
        return view('auth.2fa-verify');
    }

    public function verify(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        $user = Auth::user();

        if (!$user->google2fa_secret) {
            return redirect()->route('home');
        }

        $valid = Google2FA::verifyKey($user->google2fa_secret, $request->code);

        if ($valid) {
            session(['2fa_verified' => true]);
            return redirect()->intended('/');
        }

        return redirect()->back()->with('error', 'Invalid verification code.');
    }

    public function disable(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        $user = Auth::user();

        if (!$user->google2fa_secret) {
            return redirect()->back()->with('error', '2FA is not enabled.');
        }

        $valid = Google2FA::verifyKey($user->google2fa_secret, $request->code);

        if (!$valid) {
            return redirect()->back()->with('error', 'Invalid verification code.');
        }

        $user->google2fa_secret = null;
        $user->save();

        session()->forget('2fa_verified');

        return redirect()->route('home')->with('success', 'Two-Factor Authentication disabled successfully.');
    }

    public function resendCode()
    {
        // For 2FA, we don't resend codes, user uses authenticator app
        return redirect()->back()->with('info', 'Open your authenticator app to get the verification code.');
    }
}