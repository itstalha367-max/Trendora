@extends('layouts.app')
@section('title',$page->meta_title ?: $page->title.' — Trendora')
@push('styles') @if($page->meta_description)<meta name="description" content="{{ $page->meta_description }}">@endif @endpush
@section('content')
<section class="tr-page"><div class="tr-shell"><div class="tr-page-head"><div><span class="tr-eyebrow">{{ $page->eyebrow ?: 'Trendora' }}</span><h1>{{ $page->title }}</h1>@if($page->excerpt)<p>{{ $page->excerpt }}</p>@endif</div></div><article class="tr-card p-4 p-lg-5 tr-cms-content">{!! $page->content !!}</article></div></section>
@endsection
