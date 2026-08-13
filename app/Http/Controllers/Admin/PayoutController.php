<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Affiliate;
use App\Models\Payout;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PayoutController extends Controller
{
    public function index(Request $request)
    {
        $payouts = Payout::with(['affiliate','processor'])
            ->when($request->filled('status'), fn($q) => $q->where('status', $request->status))
            ->latest()->paginate(20)->withQueryString();
        $affiliates = Affiliate::with(['referrals' => fn($q) => $q->where('status','pending')])->where('status','active')->orderBy('name')->get();
        $stats = [
            'pending' => Payout::whereIn('status',['pending','processing'])->sum('amount'),
            'paid' => Payout::where('status','paid')->sum('amount'),
            'failed' => Payout::where('status','failed')->sum('amount'),
            'ready' => $affiliates->sum(fn($a) => $a->referrals->sum('commission_amount')),
        ];
        return view('admin.finance.payouts', compact('payouts','affiliates','stats'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'affiliate_id' => 'required|exists:affiliates,id',
            'method' => 'required|in:bank_transfer,jazzcash,easypaisa,paypal,manual',
            'note' => 'nullable|string|max:2000',
        ]);
        $affiliate = Affiliate::findOrFail($data['affiliate_id']);
        $created = false;
        DB::transaction(function () use ($affiliate, $data, &$created) {
            $referrals = $affiliate->referrals()->where('status','pending')->lockForUpdate()->get();
            if ($referrals->isEmpty()) return;
            Payout::create([
                'payout_number' => 'PAY-'.strtoupper(uniqid()),
                'affiliate_id' => $affiliate->id,
                'amount' => $referrals->sum('commission_amount'),
                'currency' => Setting::get('currency','PKR'),
                'method' => $data['method'],
                'status' => 'pending',
                'metadata' => ['referral_ids' => $referrals->pluck('id')->all()],
                'note' => $data['note'] ?? null,
                'requested_at' => now(),
            ]);
            $affiliate->referrals()->whereIn('id',$referrals->pluck('id'))->update(['status' => 'approved']);
            $created = true;
        });
        return $created ? back()->with('success','Payout batch created from unpaid affiliate commissions.') : back()->with('error','This affiliate has no unpaid commission ready for payout.');
    }

    public function process(Request $request, Payout $payout)
    {
        $data = $request->validate(['reference'=>'nullable|string|max:255','status'=>'required|in:paid,failed']);
        if (!in_array($payout->status,['pending','processing'],true)) return back()->with('error','Only pending payouts can be processed.');
        DB::transaction(function () use ($payout, $data) {
            $payout->update([
                'status' => $data['status'],
                'reference' => $data['reference'] ?? null,
                'processed_at' => now(),
                'processed_by' => auth()->id(),
            ]);
            $ids = collect($payout->metadata['referral_ids'] ?? [])->filter();
            if ($ids->isNotEmpty() && $payout->affiliate) {
                $payout->affiliate->referrals()->whereIn('id',$ids)->update([
                    'status' => $data['status'] === 'paid' ? 'paid' : 'pending',
                    'paid_at' => $data['status'] === 'paid' ? now() : null,
                ]);
            }
        });
        return back()->with('success','Payout status updated.');
    }

    public function cancel(Payout $payout)
    {
        if ($payout->status !== 'pending') return back()->with('error','Only pending payouts can be cancelled.');
        DB::transaction(function () use ($payout) {
            $ids = collect($payout->metadata['referral_ids'] ?? [])->filter();
            if ($ids->isNotEmpty() && $payout->affiliate) $payout->affiliate->referrals()->whereIn('id',$ids)->update(['status'=>'pending']);
            $payout->update(['status'=>'cancelled','processed_at'=>now(),'processed_by'=>auth()->id()]);
        });
        return back()->with('success','Payout cancelled and referrals returned to the unpaid queue.');
    }
}
