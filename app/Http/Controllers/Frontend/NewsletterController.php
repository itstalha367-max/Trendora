<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class NewsletterController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email:rfc', 'max:255'],
            'name' => ['nullable', 'string', 'max:120'],
            'source' => ['nullable', 'string', 'max:80'],
        ]);

        $email = Str::lower(trim($validated['email']));
        $subscriber = NewsletterSubscriber::firstOrNew(['email' => $email]);
        $subscriber->fill([
            'name' => $validated['name'] ?? $subscriber->name,
            'source' => $validated['source'] ?? 'storefront',
            'status' => 'subscribed',
            'ip_hash' => hash('sha256', (string) $request->ip().'|'.config('app.key')),
            'subscribed_at' => now(),
            'unsubscribed_at' => null,
        ])->save();

        return back()->with('success', 'You are subscribed. Welcome to Trendora updates!');
    }
}
