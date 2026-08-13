@extends('layouts.admin')
@section('title','Production Readiness')
@section('page-title','Production Readiness')

@section('content')
<div class="ad-page-head">
    <div>
        <span class="ad-eyebrow">Release control</span>
        <h2>Production readiness</h2>
        <p>Pre-launch health checks plus safe SMTP and payment connectivity tests. Gateway tests authenticate only; they do not create a payment.</p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <a href="{{ route('admin.operations.system') }}" class="btn btn-outline-light btn-sm"><i class="fa-solid fa-heart-pulse me-2"></i>System status</a>
        <a href="{{ route('admin.developer.index') }}" class="btn btn-outline-light btn-sm"><i class="fa-solid fa-plug me-2"></i>Webhooks</a>
    </div>
</div>

<div class="ad-release-hero ad-release-{{ $report['status'] }}">
    <div class="ad-release-score"><strong>{{ $report['score'] }}</strong><span>%</span></div>
    <div>
        <span class="ad-eyebrow">Release score</span>
        <h3 class="mb-1">{{ $report['status']==='ready' ? 'Ready for final live tests' : ($report['status']==='attention' ? 'Launch blockers need attention' : 'Not ready for production') }}</h3>
        <p class="mb-0">{{ $report['counts']['ok'] }} passed · {{ $report['counts']['warn'] }} warnings · {{ $report['counts']['fail'] }} failed</p>
    </div>
    <div class="ad-release-cli"><code>php artisan trendora:doctor --strict</code><small>Run the same checks from CLI/deployment.</small></div>
</div>

<div class="row g-4 mt-1">
    <div class="col-xl-8">
        @foreach($report['groups'] as $group => $checks)
            <div class="card p-0 mb-4 overflow-hidden">
                <div class="p-4 border-bottom border-secondary border-opacity-25 d-flex justify-content-between align-items-center">
                    <div><span class="ad-eyebrow">Checklist</span><h5 class="mb-0 mt-1">{{ $group }}</h5></div>
                    <span class="ad-pill">{{ count($checks) }} checks</span>
                </div>
                <div class="ad-check-list">
                    @foreach($checks as $check)
                        <div class="ad-check-row">
                            <div class="ad-check-icon is-{{ $check['status'] }}"><i class="fa-solid {{ $check['status']==='ok' ? 'fa-check' : ($check['status']==='warn' ? 'fa-triangle-exclamation' : 'fa-xmark') }}"></i></div>
                            <div class="ad-check-copy"><strong>{{ $check['label'] }}</strong><span>{{ $check['help'] }}</span></div>
                            <div class="ad-check-value"><strong>{{ $check['value'] }}</strong><span class="ad-status {{ $check['status']==='ok'?'is-active':($check['status']==='warn'?'is-pending':'is-urgent') }}">{{ strtoupper($check['status']) }}</span></div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>

    <div class="col-xl-4">
        <div class="card p-4 mb-4">
            <span class="ad-eyebrow">Email delivery</span>
            <h5 class="mt-2">SMTP / mail test</h5>
            <p class="text-muted small">Sends one real test email using Laravel's configured mail transport. Use an inbox you control.</p>
            <form method="POST" action="{{ route('admin.readiness.mail-test') }}">
                @csrf
                <label class="form-label">Recipient</label>
                <input class="form-control mb-3" type="email" name="email" required value="{{ auth()->user()->email }}" autocomplete="email">
                <button class="btn btn-primary w-100"><i class="fa-regular fa-paper-plane me-2"></i>Send test email</button>
            </form>
        </div>

        <div class="card p-4 mb-4">
            <span class="ad-eyebrow">Payment gateways</span>
            <h5 class="mt-2">Safe connection tests</h5>
            <p class="text-muted small">Stripe reads the account balance endpoint, PayPal requests an OAuth token, and RaftarPay validates merchant configuration. No charge/order is created.</p>
            <div class="d-grid gap-2">
                @foreach(['stripe'=>'Stripe','paypal'=>'PayPal','raftarpay'=>'RaftarPay'] as $key=>$label)
                    <form method="POST" action="{{ route('admin.readiness.gateway-test',$key) }}">@csrf
                        <button class="btn btn-outline-light w-100 text-start"><i class="fa-solid fa-shield-halved me-2"></i>Test {{ $label }}</button>
                    </form>
                @endforeach
            </div>
            <a href="{{ route('admin.payments') }}" class="btn btn-link text-decoration-none px-0 mt-2">Open payment settings <i class="fa-solid fa-arrow-right ms-1"></i></a>
        </div>

        <div class="card p-4">
            <span class="ad-eyebrow">Recent diagnostics</span>
            <h5 class="mt-2">Test history</h5>
            @forelse($tests as $test)
                <div class="ad-test-row">
                    <div><strong>{{ str($test->type)->replace('.',' ')->title() }}</strong><span>{{ $test->message }}</span></div>
                    <div class="text-end"><span class="ad-status {{ $test->status==='passed'?'is-active':'is-urgent' }}">{{ $test->status }}</span><small>{{ $test->created_at->diffForHumans() }}</small></div>
                </div>
            @empty
                <p class="text-muted small mb-0">No diagnostic connectivity tests recorded yet.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
