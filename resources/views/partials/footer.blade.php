@php
    try {
        $footerShopItems = App\Models\NavigationMenu::where('location','footer_shop')->where('status',true)->first()?->items()->where('status',true)->get() ?? collect();
        $footerCompanyItems = App\Models\NavigationMenu::where('location','footer_company')->where('status',true)->first()?->items()->where('status',true)->get() ?? collect();
        $socialLinks = collect([
            ['label'=>'Instagram','icon'=>'fa-instagram','url'=>App\Models\Setting::get('social_instagram')],
            ['label'=>'Facebook','icon'=>'fa-facebook-f','url'=>App\Models\Setting::get('social_facebook')],
            ['label'=>'X','icon'=>'fa-x-twitter','url'=>App\Models\Setting::get('social_x')],
            ['label'=>'YouTube','icon'=>'fa-youtube','url'=>App\Models\Setting::get('social_youtube')],
        ])->filter(fn($item) => filled($item['url']));
    } catch (\Throwable $e) { $footerShopItems=collect(); $footerCompanyItems=collect(); $socialLinks=collect(); }
@endphp
<footer class="tr-footer">
    <div class="tr-footer-main"><div class="tr-shell"><div class="row g-4 g-lg-5">
        <div class="col-lg-4"><a class="tr-brand d-inline-flex mb-3" href="{{ route('home') }}"><span class="tr-logo"><i class="fa-solid fa-bag-shopping"></i></span><span>Trend<em>ora</em></span></a><p class="mb-4" style="max-width:360px">A modern shopping experience built around fast discovery, secure checkout and a clean customer journey.</p>@if($socialLinks->isNotEmpty())<div class="tr-socials">@foreach($socialLinks as $social)<a href="{{ $social['url'] }}" target="_blank" rel="noopener noreferrer" aria-label="{{ $social['label'] }}"><i class="fa-brands {{ $social['icon'] }}"></i></a>@endforeach</div>@endif</div>
        <div class="col-6 col-lg-2"><h6>Shop</h6><div class="tr-footer-links">@if($footerShopItems->isNotEmpty())@foreach($footerShopItems as $item)<a href="{{ $item->url }}" target="{{ $item->target }}" @if($item->target==='_blank') rel="noopener noreferrer" @endif>{{ $item->label }}</a>@endforeach @else<a href="{{ route('products.index') }}">All products</a><a href="{{ route('categories.index') }}">Categories</a><a href="{{ route('products.index', ['sort' => 'newest']) }}">New arrivals</a><a href="{{ route('compare.index') }}">Compare</a>@endif</div></div>
        <div class="col-6 col-lg-2"><h6>Help</h6><div class="tr-footer-links"><a href="{{ route('pages.help') }}">Help center</a><a href="{{ route('pages.faq') }}">FAQ</a><a href="{{ route('pages.shipping') }}">Shipping</a><a href="{{ route('pages.returns') }}">Returns</a><a href="{{ route('pages.contact') }}">Contact</a><a href="{{ route('blogs.index') }}">Journal</a>@foreach($footerCompanyItems as $item)<a href="{{ $item->url }}" target="{{ $item->target }}" @if($item->target==='_blank') rel="noopener noreferrer" @endif>{{ $item->label }}</a>@endforeach @php try { $footerPages=App\Models\CmsPage::where('status',true)->orderBy('sort_order')->take(3)->get(); } catch (\Throwable $e) { $footerPages=collect(); } @endphp @foreach($footerPages as $footerPage)<a href="{{ route('cms.show',$footerPage->slug) }}">{{ $footerPage->title }}</a>@endforeach</div></div>
        <div class="col-lg-4"><h6>Stay in the loop</h6><p>Product drops, offers and useful updates — without clutter.</p><form class="d-flex gap-2" method="POST" action="{{ route('newsletter.subscribe') }}">@csrf<input type="hidden" name="source" value="footer"><label class="visually-hidden" for="footerNewsletter">Newsletter email</label><input id="footerNewsletter" class="form-control" type="email" name="email" autocomplete="email" placeholder="Email address" required><button class="btn btn-primary px-4" type="submit">Join</button></form></div>
    </div></div></div>
    <div class="tr-footer-bottom"><div class="tr-shell"><span>© {{ date('Y') }} Trendora. All rights reserved.</span><span><a href="{{ route('pages.privacy') }}">Privacy</a> · <a href="{{ route('pages.terms') }}">Terms</a> · Laravel 12 • NPM-free</span></div></div>
</footer>
