{{-- Responsive landing nav: desktop links + slide-out drawer on small screens (vanilla JS in layouts/landing.blade.php) --}}
<nav id="navbar"
     class="fixed top-0 left-0 right-0 z-[100] h-[72px] px-[5%] flex items-center justify-between bg-white/93 backdrop-blur-[18px] border-b border-black/5 transition-all duration-300 shadow-[0_2px_20px_rgba(42,165,189,0.07)]">
    <a href="{{ url('/') }}" class="flex items-center gap-2.5 font-[family-name:var(--font-playfair)] text-[1.45rem] font-black text-[#0b1f26] no-underline shrink-0">
        <span class="w-[38px] h-[38px] rounded-lg bg-gradient-to-br from-[#2AA5BD] to-[#1d8aa0] flex items-center justify-center text-sm font-bold text-white font-mono shadow-[0_4px_12px_rgba(42,165,189,.35)]">MD</span>
        Mangi<span class="text-[#2AA5BD]">Digital</span>
    </a>

    <ul class="hidden md:flex items-center gap-6 lg:gap-8 list-none m-0 p-0">
        <li><a href="{{ url('/#about') }}" class="text-[#6a8e99] no-underline text-[0.92rem] font-medium hover:text-[#2AA5BD] transition-colors">What We Do</a></li>
        <li><a href="{{ url('/#how') }}" class="text-[#6a8e99] no-underline text-[0.92rem] font-medium hover:text-[#2AA5BD] transition-colors">How It Works</a></li>
        <li><a href="{{ url('/#pricing') }}" class="text-[#6a8e99] no-underline text-[0.92rem] font-medium hover:text-[#2AA5BD] transition-colors">Pricing</a></li>
        <li><a href="{{ url('/#testimonials') }}" class="text-[#6a8e99] no-underline text-[0.92rem] font-medium hover:text-[#2AA5BD] transition-colors">Reviews</a></li>
        <li><a href="{{ route('cargo.track.form') }}" class="text-[#2AA5BD] no-underline text-[0.92rem] font-semibold hover:text-[#1d8aa0] transition-colors">Track cargo</a></li>
    </ul>

    <div class="hidden md:flex gap-3 items-center shrink-0">
        <a href="{{ route('login') }}" class="inline-flex items-center justify-center gap-2 border border-[rgba(0,0,0,.07)] border-[length:1.5px] bg-transparent text-[#1e3a44] py-2 px-5 text-[0.88rem] font-semibold rounded-[10px] no-underline whitespace-nowrap hover:border-[#2AA5BD] hover:text-[#2AA5BD] transition-all">Sign In</a>
        <a href="{{ route('register') }}" class="inline-flex items-center justify-center gap-2 bg-[#2AA5BD] text-white py-2.5 px-6 text-[0.9rem] font-semibold rounded-[10px] shadow-[0_4px_14px_rgba(42,165,189,0.28)] no-underline whitespace-nowrap hover:bg-[#1d8aa0] hover:-translate-y-px hover:shadow-[0_8px_24px_rgba(42,165,189,.38)] transition-all">Get Started →</a>
    </div>

    <button type="button"
            id="landingNavToggle"
            class="md:hidden flex flex-col justify-center items-center gap-1.5 w-11 h-11 -mr-2 rounded-lg border border-black/10 bg-white/80 shrink-0 cursor-pointer touch-manipulation"
            aria-label="Open menu"
            aria-expanded="false"
            aria-controls="landingMobilePanel">
        <span class="landing-nav-icon-open flex flex-col gap-1.5 items-center">
            <span class="block w-6 h-0.5 bg-[#1e3a44] rounded-sm"></span>
            <span class="block w-6 h-0.5 bg-[#1e3a44] rounded-sm"></span>
            <span class="block w-6 h-0.5 bg-[#1e3a44] rounded-sm"></span>
        </span>
        <span class="landing-nav-icon-close hidden text-2xl leading-none text-[#1e3a44] font-light" aria-hidden="true">×</span>
    </button>
</nav>

{{-- Mobile drawer: below md --}}
<div id="landingMobilePanel"
     class="md:hidden fixed inset-0 z-[200] hidden"
     aria-hidden="true"
     role="dialog"
     aria-modal="true"
     aria-label="Site menu">
    <button type="button"
            class="absolute inset-0 w-full h-full bg-black/45 border-0 cursor-pointer"
            data-close-landing-nav
            aria-label="Close menu"></button>
    <div class="absolute top-0 right-0 bottom-0 w-[min(100%,340px)] max-w-full bg-white shadow-[-8px_0_40px_rgba(15,23,42,.12)] flex flex-col pt-[88px] px-6 pb-10 overflow-y-auto">
        <button type="button"
                class="absolute top-5 right-5 w-10 h-10 flex items-center justify-center text-2xl text-[#6a8e99] hover:text-[#2AA5BD] cursor-pointer rounded-lg border border-transparent hover:border-black/10"
                data-close-landing-nav
                aria-label="Close">×</button>
        <nav class="flex flex-col gap-1">
            <a href="{{ url('/#about') }}" class="text-[#0b1f26] no-underline text-lg font-semibold py-3 border-b border-black/5 hover:text-[#2AA5BD] transition-colors" data-close-landing-nav>What We Do</a>
            <a href="{{ url('/#how') }}" class="text-[#0b1f26] no-underline text-lg font-semibold py-3 border-b border-black/5 hover:text-[#2AA5BD] transition-colors" data-close-landing-nav>How It Works</a>
            <a href="{{ url('/#pricing') }}" class="text-[#0b1f26] no-underline text-lg font-semibold py-3 border-b border-black/5 hover:text-[#2AA5BD] transition-colors" data-close-landing-nav>Pricing</a>
            <a href="{{ url('/#testimonials') }}" class="text-[#0b1f26] no-underline text-lg font-semibold py-3 border-b border-black/5 hover:text-[#2AA5BD] transition-colors" data-close-landing-nav>Reviews</a>
            <a href="{{ route('cargo.track.form') }}" class="text-[#2AA5BD] no-underline text-lg font-bold py-3 border-b border-black/5 hover:text-[#1d8aa0] transition-colors" data-close-landing-nav>Track cargo</a>
            <a href="{{ route('login') }}" class="text-[#0b1f26] no-underline text-lg font-semibold py-3 border-b border-black/5 hover:text-[#2AA5BD] transition-colors mt-2" data-close-landing-nav>Sign In</a>
            <a href="{{ route('register') }}" class="inline-flex items-center justify-center bg-[#2AA5BD] text-white py-3.5 px-6 rounded-[10px] font-semibold no-underline mt-4 text-center" data-close-landing-nav>Get Started →</a>
        </nav>
    </div>
</div>
