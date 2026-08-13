<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\ContactSubmission;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function about() { return view('frontend.pages.about'); }
    public function faq() { return view('frontend.pages.faq'); }
    public function help() { return view('frontend.pages.help'); }
    public function shipping() { return view('frontend.pages.shipping'); }
    public function returnsPolicy() { return view('frontend.pages.returns-policy'); }
    public function privacy() { return view('frontend.pages.privacy'); }
    public function terms() { return view('frontend.pages.terms'); }
    public function contact() { return view('frontend.pages.contact'); }

    public function contactStore(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:30',
            'subject' => 'required|string|max:180',
            'message' => 'required|string|max:3000',
        ]);
        $data['user_id'] = auth()->id();
        ContactSubmission::create($data);
        return back()->with('success', 'Thanks — your message has been received.');
    }
}
