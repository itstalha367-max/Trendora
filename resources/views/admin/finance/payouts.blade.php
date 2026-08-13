@extends('layouts.admin')
@section('title','Payouts')
@section('page-title','Payout Center')
@section('content')
<div class="ad-page-head">
    <div>
        <span class="ad-eyebrow">Finance operations</span>
        <h2>Payout Center</h2>
        <p>Bundle approved affiliate commissions into auditable payout batches and record settlement references.</p>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newPayout"><i class="fa-solid fa-plus me-2"></i>Create payout</button>
</div>
<div class="ad-stat-grid mb-4">
    <div class="ad-stat"><span>Ready commissions</span><strong>{{ number_format($stats['ready'],2) }}</strong><i class="fa-solid fa-hourglass-half"></i></div>
    <div class="ad-stat"><span>Pending batches</span><strong>{{ number_format($stats['pending'],2) }}</strong><i class="fa-solid fa-clock"></i></div>
    <div class="ad-stat"><span>Paid</span><strong>{{ number_format($stats['paid'],2) }}</strong><i class="fa-solid fa-circle-check"></i></div>
    <div class="ad-stat"><span>Failed</span><strong>{{ number_format($stats['failed'],2) }}</strong><i class="fa-solid fa-triangle-exclamation"></i></div>
</div>
<div class="card p-4">
    <div class="d-flex justify-content-between gap-3 flex-wrap mb-3">
        <h5 class="mb-0">Payout ledger</h5>
        <form class="d-flex gap-2">
            <select class="form-select" name="status">
                <option value="">All statuses</option>
                @foreach(['pending','processing','paid','failed','cancelled'] as $s)
                    <option value="{{ $s }}" @selected(request('status')===$s)>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
            <button class="btn btn-outline-light">Filter</button>
        </form>
    </div>
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead><tr><th>Batch</th><th>Affiliate</th><th>Method</th><th>Amount</th><th>Status</th><th>Reference</th><th class="text-end">Action</th></tr></thead>
            <tbody>
            @forelse($payouts as $p)
                <tr>
                    <td><strong>{{ $p->payout_number }}</strong><small class="d-block text-muted">{{ $p->created_at->format('d M Y H:i') }}</small></td>
                    <td>{{ $p->affiliate?->name ?? '—' }}</td>
                    <td>{{ str_replace('_',' ',ucfirst($p->method)) }}</td>
                    <td><strong>{{ $p->currency }} {{ number_format($p->amount,2) }}</strong></td>
                    <td><span class="ad-status {{ $p->status==='paid'?'is-active':($p->status==='failed'?'is-inactive':'') }}">{{ $p->status }}</span></td>
                    <td>{{ $p->reference ?: '—' }}</td>
                    <td class="text-end">
                        @if(in_array($p->status,['pending','processing']))
                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#process{{ $p->id }}">Process</button>
                            <form class="d-inline" method="POST" action="{{ route('admin.payouts.cancel',$p) }}" onsubmit="return confirm('Cancel this payout?')">@csrf<button class="btn btn-sm btn-outline-danger">Cancel</button></form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center text-muted py-5">No payout batches yet.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-3">{{ $payouts->links() }}</div>
</div>

@foreach($payouts as $p)
    @if(in_array($p->status,['pending','processing']))
        <div class="modal fade" id="process{{ $p->id }}">
            <div class="modal-dialog">
                <form method="POST" class="modal-content ad-modal" action="{{ route('admin.payouts.process',$p) }}">
                    @csrf @method('PUT')
                    <div class="modal-header"><h5>Process {{ $p->payout_number }}</h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
                    <div class="modal-body">
                        <label class="form-label">Result</label>
                        <select class="form-select" name="status"><option value="paid">Paid</option><option value="failed">Failed</option></select>
                        <label class="form-label mt-3">Gateway / bank reference</label>
                        <input class="form-control" name="reference" maxlength="255">
                    </div>
                    <div class="modal-footer"><button class="btn btn-primary">Save result</button></div>
                </form>
            </div>
        </div>
    @endif
@endforeach

<div class="modal fade" id="newPayout">
    <div class="modal-dialog">
        <form method="POST" class="modal-content ad-modal" action="{{ route('admin.payouts.store') }}">
            @csrf
            <div class="modal-header"><h5>Create payout batch</h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <label class="form-label">Affiliate</label>
                <select class="form-select" name="affiliate_id" required>
                    <option value="">Select partner</option>
                    @foreach($affiliates as $a)
                        @php $due=$a->referrals->sum('commission_amount'); @endphp
                        @if($due>0)
                            <option value="{{ $a->id }}">{{ $a->name }} · {{ number_format($due,2) }} due</option>
                        @endif
                    @endforeach
                </select>
                <label class="form-label mt-3">Method</label>
                <select class="form-select" name="method"><option value="bank_transfer">Bank transfer</option><option value="jazzcash">JazzCash</option><option value="easypaisa">Easypaisa</option><option value="paypal">PayPal</option><option value="manual">Manual</option></select>
                <label class="form-label mt-3">Internal note</label>
                <textarea class="form-control" name="note" rows="3"></textarea>
                <div class="alert alert-info mt-3 mb-0">All currently pending commissions for the selected affiliate will be locked into this payout batch.</div>
            </div>
            <div class="modal-footer"><button class="btn btn-primary">Create batch</button></div>
        </form>
    </div>
</div>
@endsection
