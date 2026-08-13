<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;

class NewsletterSubscriberController extends Controller
{
    public function index(Request $request)
    {
        $query = NewsletterSubscriber::query()->latest('subscribed_at');
        if ($request->filled('q')) {
            $term = trim((string) $request->q);
            $query->where(fn ($q) => $q->where('email', 'like', "%{$term}%")->orWhere('name', 'like', "%{$term}%"));
        }
        if ($request->filled('status')) $query->where('status', $request->status);

        $subscribers = $query->paginate(25)->withQueryString();
        $stats = [
            'subscribed' => NewsletterSubscriber::where('status', 'subscribed')->count(),
            'unsubscribed' => NewsletterSubscriber::where('status', 'unsubscribed')->count(),
            'total' => NewsletterSubscriber::count(),
        ];
        return view('admin.newsletter.index', compact('subscribers', 'stats'));
    }

    public function toggle(NewsletterSubscriber $subscriber)
    {
        $active = $subscriber->status === 'subscribed';
        $subscriber->update([
            'status' => $active ? 'unsubscribed' : 'subscribed',
            'subscribed_at' => $active ? $subscriber->subscribed_at : now(),
            'unsubscribed_at' => $active ? now() : null,
        ]);
        return back()->with('success', $active ? 'Subscriber marked as unsubscribed.' : 'Subscriber reactivated.');
    }

    public function destroy(NewsletterSubscriber $subscriber)
    {
        $subscriber->delete();
        return back()->with('success', 'Subscriber removed.');
    }

    public function export()
    {
        $rows = NewsletterSubscriber::subscribed()->orderBy('email')->get(['email', 'name', 'source', 'subscribed_at']);
        $stream = function () use ($rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['email', 'name', 'source', 'subscribed_at']);
            foreach ($rows as $row) fputcsv($out, [$row->email, $row->name, $row->source, optional($row->subscribed_at)->toIso8601String()]);
            fclose($out);
        };
        return response()->streamDownload($stream, 'trendora-newsletter-'.now()->format('Ymd-His').'.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
