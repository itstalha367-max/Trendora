<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class MailSettingsController extends Controller
{
    public function index()
    {
        return view('admin.settings.mail', [
            'values' => [
                'enabled' => Setting::get('mail_override_enabled', 'off'),
                'host' => Setting::get('mail_host', config('mail.mailers.smtp.host')),
                'port' => Setting::get('mail_port', config('mail.mailers.smtp.port')),
                'username' => Setting::get('mail_username', config('mail.mailers.smtp.username')),
                'has_password' => filled(Setting::get('mail_password')),
                'scheme' => Setting::get('mail_scheme', config('mail.mailers.smtp.scheme')),
                'from_address' => Setting::get('mail_from_address', config('mail.from.address')),
                'from_name' => Setting::get('mail_from_name', config('mail.from.name')),
            ],
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'mail_host' => 'required|string|max:255',
            'mail_port' => 'required|integer|min:1|max:65535',
            'mail_username' => 'nullable|string|max:255',
            'mail_password' => 'nullable|string|max:1000',
            'mail_scheme' => 'nullable|in:tls,ssl',
            'mail_from_address' => 'required|email|max:255',
            'mail_from_name' => 'required|string|max:255',
        ]);

        Setting::set('mail_override_enabled', $request->boolean('mail_override_enabled') ? 'on' : 'off');
        Setting::set('mail_host', $data['mail_host']);
        Setting::set('mail_port', (string) $data['mail_port']);
        Setting::set('mail_username', $data['mail_username'] ?? '');
        if (filled($data['mail_password'] ?? null)) Setting::set('mail_password', $data['mail_password']);
        Setting::set('mail_scheme', $data['mail_scheme'] ?? null);
        Setting::set('mail_from_address', $data['mail_from_address']);
        Setting::set('mail_from_name', $data['mail_from_name']);

        return back()->with('success', 'SMTP override saved. Use Production Readiness to send a real test email.');
    }
}
