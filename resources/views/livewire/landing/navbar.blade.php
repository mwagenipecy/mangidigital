<nav id="navbar"
     class="fixed top-0 left-0 right-0 z-[100] h-[72px] px-[5%] flex items-center justify-between bg-white/93 backdrop-blur-[18px] border-b border-black/5 transition-all duration-300 shadow-[0_2px_20px_rgba(42,165,189,0.07)]">
    <a href="#" class="flex items-center gap-2.5 font-[family-name:var(--font-playfair)] text-[1.45rem] font-black text-[#0b1f26] no-underline">
        <span class="w-[38px] h-[38px] rounded-lg bg-gradient-to-br from-[#2AA5BD] to-[#1d8aa0] flex items-center justify-center text-sm font-bold text-white font-mono shadow-[0_4px_12px_rgba(42,165,189,.35)]">MD</span>
        Mangi<span class="text-[#2AA5BD]">Digital</span>
    </a>
    <ul class="hidden md:flex items-center gap-8 list-none">
        <li><a href="#about" class="text-[#6a8e99] no-underline text-[0.92rem] font-medium hover:text-[#2AA5BD] transition-colors">What We Do</a></li>
        <li><a href="#how" class="text-[#6a8e99] no-underline text-[0.92rem] font-medium hover:text-[#2AA5BD] transition-colors">How It Works</a></li>
        <li><a href="#pricing" class="text-[#6a8e99] no-underline text-[0.92rem] font-medium hover:text-[#2AA5BD] transition-colors">Pricing</a></li>
        <li><a href="#testimonials" class="text-[#6a8e99] no-underline text-[0.92rem] font-medium hover:text-[#2AA5BD] transition-colors">Reviews</a></li>
    </ul>
    <div class="hidden md:flex gap-3 items-center">
        <a href="{{ route('login') }}" class="inline-flex items-center justify-center gap-2 border border-[rgba(0,0,0,.07)] border-[length:1.5px] bg-transparent text-[#1e3a44] py-2 px-5 text-[0.88rem] font-semibold rounded-[10px] no-underline whitespace-nowrap hover:border-[#2AA5BD] hover:text-[#2AA5BD] transition-all">Sign In</a>
        <a href="{{ route('register') }}" class="inline-flex items-center justify-center gap-2 bg-[#2AA5BD] text-white py-2.5 px-6 text-[0.9rem] font-semibold rounded-[10px] shadow-[0_4px_14px_rgba(42,165,189,0.28)] no-underline whitespace-nowrap hover:bg-[#1d8aa0] hover:-translate-y-px hover:shadow-[0_8px_24px_rgba(42,165,189,.38)] transition-all">Get Started →</a>
    </div>
    <button type="button"
            class="md:hidden flex flex-col gap-1.5 p-1 cursor-pointer"
            wire:click="toggleMenu"
            aria-label="Open menu">
        <span class="block w-6 h-0.5 bg-[#1e3a44] rounded-sm"></span>
        <span class="block w-6 h-0.5 bg-[#1e3a44] rounded-sm"></span>
        <span class="block w-6 h-0.5 bg-[#1e3a44] rounded-sm"></span>
    </button>
</nav>

{{-- Mobile menu --}}
@if($mobileMenuOpen)
<div class="md:hidden fixed inset-0 z-[200] bg-white/97 backdrop-blur-[20px] flex flex-col items-center justify-center gap-8">
    <button type="button" class="absolute top-6 right-[5%] text-2xl text-[#6a8e99] cursor-pointer" wire:click="toggleMenu" aria-label="Close">✕</button>
    <a href="#about" class="text-[#0b1f26] no-underline text-2xl font-[family-name:var(--font-playfair)] font-bold hover:text-[#2AA5BD] transition-colors" wire:click="toggleMenu">What We Do</a>
    <a href="#how" class="text-[#0b1f26] no-underline text-2xl font-[family-name:var(--font-playfair)] font-bold hover:text-[#2AA5BD] transition-colors" wire:click="toggleMenu">How It Works</a>
    <a href="#pricing" class="text-[#0b1f26] no-underline text-2xl font-[family-name:var(--font-playfair)] font-bold hover:text-[#2AA5BD] transition-colors" wire:click="toggleMenu">Pricing</a>
    <a href="#testimonials" class="text-[#0b1f26] no-underline text-2xl font-[family-name:var(--font-playfair)] font-bold hover:text-[#2AA5BD] transition-colors" wire:click="toggleMenu">Reviews</a>
    <a href="{{ route('login') }}" class="text-[#0b1f26] no-underline text-2xl font-[family-name:var(--font-playfair)] font-bold hover:text-[#2AA5BD] transition-colors" wire:click="toggleMenu">Sign In</a>
    <a href="{{ route('register') }}" class="inline-flex items-center justify-center bg-[#2AA5BD] text-white py-3.5 px-6 rounded-[10px] font-semibold no-underline" wire:click="toggleMenu">Get Started →</a>
</div>
@endif
