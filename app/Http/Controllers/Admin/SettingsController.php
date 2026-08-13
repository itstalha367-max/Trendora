<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class SettingsController extends Controller
{
    public function index()
    {
        // Get all settings
        $settings = Setting::all()->groupBy('group');
        
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'store_name' => 'nullable|string|max:255',
            'store_email' => 'nullable|email|max:255',
            'store_phone' => 'nullable|string|max:20',
            'store_address' => 'nullable|string',
            'store_description' => 'nullable|string',
            'currency' => 'nullable|string|max:10',
            'currency_symbol' => 'nullable|string|max:5',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
            'favicon' => 'nullable|image|mimes:ico,png|max:1024',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $logo = $request->file('logo');
            $logoPath = $logo->store('settings', 'public');
            Setting::set('logo', $logoPath);
            
            // Delete old logo
            $oldLogo = Setting::get('logo');
            if ($oldLogo && $oldLogo !== $logoPath) {
                Storage::disk('public')->delete($oldLogo);
            }
        }

        // Handle favicon upload
        if ($request->hasFile('favicon')) {
            $favicon = $request->file('favicon');
            $faviconPath = $favicon->store('settings', 'public');
            Setting::set('favicon', $faviconPath);
            
            $oldFavicon = Setting::get('favicon');
            if ($oldFavicon && $oldFavicon !== $faviconPath) {
                Storage::disk('public')->delete($oldFavicon);
            }
        }

        // Save all settings
        $settings = [
            'store_name' => $request->store_name,
            'store_email' => $request->store_email,
            'store_phone' => $request->store_phone,
            'store_address' => $request->store_address,
            'store_description' => $request->store_description,
            'currency' => $request->currency,
            'currency_symbol' => $request->currency_symbol,
            'theme' => $request->theme ?? 'light',
            'maintenance_mode' => $request->has('maintenance_mode') ? 'on' : 'off',
            'registration_enabled' => $request->has('registration_enabled') ? 'on' : 'off',
            'facebook' => $request->facebook,
            'instagram' => $request->instagram,
            'twitter' => $request->twitter,
            'youtube' => $request->youtube,
        ];

        foreach ($settings as $key => $value) {
            Setting::set($key, $value);
        }

        return redirect()->back()->with('success', 'Settings updated successfully!');
    }

    public function reset()
    {
        // Reset all settings to default
        Setting::truncate();
        
        // Create default settings
        $this->createDefaultSettings();
        
        return redirect()->back()->with('success', 'Settings reset to default!');
    }

    private function createDefaultSettings()
    {
        $defaults = [
            ['key' => 'store_name', 'value' => 'Trendora', 'group' => 'general', 'type' => 'text', 'label' => 'Store Name'],
            ['key' => 'store_email', 'value' => 'info@trendora.com', 'group' => 'general', 'type' => 'email', 'label' => 'Store Email'],
            ['key' => 'store_phone', 'value' => '+1 234 567 8900', 'group' => 'general', 'type' => 'text', 'label' => 'Store Phone'],
            ['key' => 'store_address', 'value' => '123 Main Street, New York, USA', 'group' => 'general', 'type' => 'textarea', 'label' => 'Store Address'],
            ['key' => 'store_description', 'value' => 'Your one-stop shop for everything!', 'group' => 'general', 'type' => 'textarea', 'label' => 'Store Description'],
            ['key' => 'currency', 'value' => 'USD', 'group' => 'general', 'type' => 'text', 'label' => 'Currency'],
            ['key' => 'currency_symbol', 'value' => '$', 'group' => 'general', 'type' => 'text', 'label' => 'Currency Symbol'],
            ['key' => 'theme', 'value' => 'light', 'group' => 'appearance', 'type' => 'select', 'label' => 'Theme', 'options' => ['light' => 'Light', 'dark' => 'Dark']],
            ['key' => 'maintenance_mode', 'value' => 'off', 'group' => 'system', 'type' => 'toggle', 'label' => 'Maintenance Mode'],
            ['key' => 'registration_enabled', 'value' => 'on', 'group' => 'system', 'type' => 'toggle', 'label' => 'Registration Enabled'],
            ['key' => 'facebook', 'value' => 'https://facebook.com/trendora', 'group' => 'social', 'type' => 'url', 'label' => 'Facebook'],
            ['key' => 'instagram', 'value' => 'https://instagram.com/trendora', 'group' => 'social', 'type' => 'url', 'label' => 'Instagram'],
            ['key' => 'twitter', 'value' => 'https://twitter.com/trendora', 'group' => 'social', 'type' => 'url', 'label' => 'Twitter'],
            ['key' => 'youtube', 'value' => 'https://youtube.com/trendora', 'group' => 'social', 'type' => 'url', 'label' => 'YouTube'],
        ];

        foreach ($defaults as $default) {
            Setting::create($default);
        }
    }
}