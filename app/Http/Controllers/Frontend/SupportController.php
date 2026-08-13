<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SupportController extends Controller
{
    public function index()
    {
        $tickets = SupportTicket::where('user_id', auth()->id())->latest('last_reply_at')->latest()->paginate(10);
        return view('frontend.user.support.index', compact('tickets'));
    }

    public function create()
    {
        return view('frontend.user.support.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'subject' => 'required|string|max:180',
            'category' => 'required|in:order,payment,return,product,account,other',
            'priority' => 'required|in:low,normal,high,urgent',
            'message' => 'required|string|max:5000',
        ]);

        $ticket = DB::transaction(function () use ($data) {
            $ticket = SupportTicket::create([
                'user_id' => auth()->id(),
                'ticket_number' => 'SUP-' . strtoupper(substr(bin2hex(random_bytes(6)), 0, 10)),
                'subject' => $data['subject'],
                'category' => $data['category'],
                'priority' => $data['priority'],
                'status' => 'open',
                'last_reply_at' => now(),
            ]);
            $ticket->messages()->create([
                'user_id' => auth()->id(),
                'is_staff' => false,
                'message' => $data['message'],
            ]);
            return $ticket;
        });

        return redirect()->route('support.show', $ticket)->with('success', 'Support ticket created.');
    }

    public function show(SupportTicket $ticket)
    {
        abort_unless($ticket->user_id === auth()->id(), 403);
        $ticket->load(['messages.user']);
        return view('frontend.user.support.show', compact('ticket'));
    }

    public function reply(Request $request, SupportTicket $ticket)
    {
        abort_unless($ticket->user_id === auth()->id(), 403);
        abort_if(in_array($ticket->status, ['resolved', 'closed'], true), 422, 'This ticket is closed.');
        $data = $request->validate(['message' => 'required|string|max:5000']);
        $ticket->messages()->create([
            'user_id' => auth()->id(),
            'is_staff' => false,
            'message' => $data['message'],
        ]);
        $ticket->update(['status' => 'open', 'last_reply_at' => now()]);
        return back()->with('success', 'Reply sent.');
    }
}
