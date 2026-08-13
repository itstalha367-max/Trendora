@extends('layouts.app')
@section('title','Saved Addresses — Trendora')
@section('content')
<section class="tr-page"><div class="tr-shell">
    <div class="tr-page-head tr-reveal"><div><span class="tr-eyebrow">Account</span><h1>Saved addresses</h1><p>Keep delivery details ready for faster checkout.</p></div><button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAddress"><i class="fa-solid fa-plus me-2"></i>Add address</button></div>
    <div class="tr-account-grid">
        @include('frontend.partials.account-nav')
        <div>
            <div class="row g-3">
                @forelse($addresses as $address)
                <div class="col-lg-6 tr-reveal"><article class="tr-card tr-address-card h-100 {{ $address->is_default ? 'is-default' : '' }}">
                    <div class="d-flex justify-content-between gap-3"><div><span class="tr-chip">{{ $address->label }}</span>@if($address->is_default)<span class="tr-chip success ms-2">Default</span>@endif</div><i class="fa-solid fa-location-dot tr-icon-orb"></i></div>
                    <h5 class="mt-3 mb-1">{{ $address->name }}</h5><p class="mb-1">{{ $address->phone }}</p><p class="text-muted mb-3">{{ $address->address_line }}, {{ $address->city }}{{ $address->state ? ', '.$address->state : '' }} {{ $address->zip }}<br>{{ $address->country }}</p>
                    <div class="d-flex gap-2 flex-wrap">@unless($address->is_default)<form method="POST" action="{{ route('user.addresses.default',$address) }}">@csrf<button class="btn btn-sm btn-outline-light">Make default</button></form>@endunless<form method="POST" action="{{ route('user.addresses.destroy',$address) }}" onsubmit="return confirm('Remove this address?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Remove</button></form></div>
                </article></div>
                @empty
                <div class="col-12"><div class="tr-empty tr-card"><i class="fa-regular fa-map"></i><h4>No saved addresses yet</h4><p>Add your first delivery address to speed up checkout.</p></div></div>
                @endforelse
            </div>
        </div>
    </div>
</div></section>
<div class="modal fade" id="addAddress" tabindex="-1"><div class="modal-dialog modal-lg modal-dialog-centered"><div class="modal-content tr-modal"><form method="POST" action="{{ route('user.addresses.store') }}">@csrf<div class="modal-header"><h5 class="modal-title">Add delivery address</h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div><div class="modal-body"><div class="row g-3">
<div class="col-md-4"><label class="form-label">Label</label><input class="form-control" name="label" value="Home" required></div><div class="col-md-4"><label class="form-label">Full name</label><input class="form-control" name="name" value="{{ auth()->user()->name }}" required></div><div class="col-md-4"><label class="form-label">Phone</label><input class="form-control" name="phone" value="{{ auth()->user()->phone }}" required></div><div class="col-12"><label class="form-label">Address</label><input class="form-control" name="address_line" required></div><div class="col-md-4"><label class="form-label">City</label><input class="form-control" name="city" required></div><div class="col-md-4"><label class="form-label">State</label><input class="form-control" name="state"></div><div class="col-md-4"><label class="form-label">ZIP</label><input class="form-control" name="zip"></div><div class="col-md-8"><label class="form-label">Country</label><input class="form-control" name="country" value="Pakistan" required></div><div class="col-md-4 d-flex align-items-end"><div class="form-check mb-2"><input type="hidden" name="is_default" value="0"><input class="form-check-input" type="checkbox" name="is_default" value="1" id="defaultAddress"><label class="form-check-label" for="defaultAddress">Default address</label></div></div>
</div></div><div class="modal-footer"><button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Cancel</button><button class="btn btn-primary">Save address</button></div></form></div></div></div>
@endsection
