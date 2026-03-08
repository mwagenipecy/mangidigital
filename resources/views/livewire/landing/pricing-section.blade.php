<section id="pricing" class="py-[100px] px-[5%] bg-[#f5fbfc]">
    <div class="max-w-[1200px] mx-auto">
        <div class="text-center mb-16">
            <div class="reveal opacity-0 translate-y-6 transition-all duration-500 [&.visible]:opacity-100 [&.visible]:translate-y-0">
                <span class="inline-flex items-center gap-1.5 py-1.5 px-4 rounded-full border border-[#2AA5BD] border-[length:1.5px] bg-[rgba(42,165,189,0.10)] text-[#2AA5BD] text-[0.78rem] font-bold tracking-wider uppercase mb-5">Pricing</span>
            </div>
            <h2 class="reveal opacity-0 translate-y-6 transition-all duration-500 [&.visible]:opacity-100 [&.visible]:translate-y-0 text-[clamp(2rem,3.5vw,2.8rem)] font-[family-name:var(--font-playfair)] text-[#0b1f26] mb-4">
                Transparent Pricing,<br><em class="italic text-[#2AA5BD]">No Hidden Fees</em>
            </h2>
            <p class="reveal opacity-0 translate-y-6 transition-all duration-500 [&.visible]:opacity-100 [&.visible]:translate-y-0 text-[#6a8e99] text-[1.05rem] max-w-[560px] mx-auto">
                Choose the plan that fits your business stage. All plans include full platform access. Upgrade or cancel anytime.
            </p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            {{-- Basic --}}
            <div class="reveal opacity-0 translate-y-6 transition-all duration-500 [&.visible]:opacity-100 [&.visible]:translate-y-0 bg-white border border-black/5 border-[length:1.5px] rounded-2xl p-9 relative transition-all hover:-translate-y-1.5 hover:shadow-[0_24px_60px_rgba(42,165,189,0.13)]">
                <div class="text-[0.76rem] font-bold tracking-widest uppercase text-[#2AA5BD] mb-2.5">Starter</div>
                <div class="text-2xl font-black text-[#0b1f26] mb-5">Basic</div>
                <div class="flex items-baseline gap-1 mb-1">
                    <span class="font-mono text-[0.88rem] text-[#2AA5BD]">TZS</span>
                    <span class="font-[family-name:var(--font-playfair)] text-[3rem] font-black text-[#0b1f26] leading-none">25K</span>
                </div>
                <div class="text-[#6a8e99] text-[0.84rem]">per month</div>
                <div class="text-[#6a8e99] text-[0.84rem] my-2.5 mb-6">Perfect for small shops and solo entrepreneurs just getting started.</div>
                <div class="h-px bg-black/5 mb-6"></div>
                <ul class="list-none flex flex-col gap-2.5 mb-8">
                    <li class="flex items-start gap-2.5 text-[0.87rem] text-[#1e3a44]"><span class="text-[#2AA5BD] text-[0.9rem] shrink-0 mt-0.5">✓</span>Up to 50 clients</li>
                    <li class="flex items-start gap-2.5 text-[0.87rem] text-[#1e3a44]"><span class="text-[#2AA5BD] text-[0.9rem] shrink-0 mt-0.5">✓</span>Order management</li>
                    <li class="flex items-start gap-2.5 text-[0.87rem] text-[#1e3a44]"><span class="text-[#2AA5BD] text-[0.9rem] shrink-0 mt-0.5">✓</span>Basic expense tracking</li>
                    <li class="flex items-start gap-2.5 text-[0.87rem] text-[#1e3a44]"><span class="text-[#2AA5BD] text-[0.9rem] shrink-0 mt-0.5">✓</span>Sales recording</li>
                    <li class="flex items-start gap-2.5 text-[0.87rem] text-[#1e3a44]"><span class="text-[#2AA5BD] text-[0.9rem] shrink-0 mt-0.5">✓</span>Partial payment tracking</li>
                    <li class="flex items-start gap-2.5 text-[0.87rem] text-[#aac4cc]"><span class="text-[#c9d6db] text-[0.9rem] shrink-0 mt-0.5">✗</span>Advanced analytics</li>
                    <li class="flex items-start gap-2.5 text-[0.87rem] text-[#aac4cc]"><span class="text-[#c9d6db] text-[0.9rem] shrink-0 mt-0.5">✗</span>SMS payment reminders</li>
                    <li class="flex items-start gap-2.5 text-[0.87rem] text-[#aac4cc]"><span class="text-[#c9d6db] text-[0.9rem] shrink-0 mt-0.5">✗</span>Multi-user access</li>
                </ul>
                <a href="{{ route('register') }}" class="block w-full py-3 rounded-[10px] border border-black/5 border-[length:1.5px] bg-transparent text-[#1e3a44] font-semibold text-[0.9rem] text-center hover:border-[#2AA5BD] hover:text-[#2AA5BD] transition-all no-underline">Get Started</a>
            </div>
            {{-- Professional (featured) --}}
            <div class="reveal opacity-0 translate-y-6 transition-all duration-500 [&.visible]:opacity-100 [&.visible]:translate-y-0 bg-white border-2 border-[#2AA5BD] rounded-2xl p-9 relative transition-all bg-gradient-to-b from-[rgba(42,165,189,0.06)] to-white hover:-translate-y-1.5 shadow-[0_16px_48px_rgba(42,165,189,.14)]">
                <div class="absolute -top-3.5 left-1/2 -translate-x-1/2 bg-[#2AA5BD] text-white py-1 px-4 rounded-full text-[0.72rem] font-bold tracking-wider uppercase whitespace-nowrap shadow-[0_4px_12px_rgba(42,165,189,.4)]">Most Popular</div>
                <div class="text-[0.76rem] font-bold tracking-widest uppercase text-[#2AA5BD] mb-2.5">Growth</div>
                <div class="text-2xl font-black text-[#0b1f26] mb-5">Professional</div>
                <div class="flex items-baseline gap-1 mb-1">
                    <span class="font-mono text-[0.88rem] text-[#2AA5BD]">TZS</span>
                    <span class="font-[family-name:var(--font-playfair)] text-[3rem] font-black text-[#0b1f26] leading-none">65K</span>
                </div>
                <div class="text-[#6a8e99] text-[0.84rem]">per month</div>
                <div class="text-[#6a8e99] text-[0.84rem] my-2.5 mb-6">Built for growing businesses that need deeper insights and more clients.</div>
                <div class="h-px bg-black/5 mb-6"></div>
                <ul class="list-none flex flex-col gap-2.5 mb-8">
                    <li class="flex items-start gap-2.5 text-[0.87rem] text-[#1e3a44]"><span class="text-[#2AA5BD] text-[0.9rem] shrink-0 mt-0.5">✓</span>Up to 300 clients</li>
                    <li class="flex items-start gap-2.5 text-[0.87rem] text-[#1e3a44]"><span class="text-[#2AA5BD] text-[0.9rem] shrink-0 mt-0.5">✓</span>Advanced order management</li>
                    <li class="flex items-start gap-2.5 text-[0.87rem] text-[#1e3a44]"><span class="text-[#2AA5BD] text-[0.9rem] shrink-0 mt-0.5">✓</span>Full expense tracking + categories</li>
                    <li class="flex items-start gap-2.5 text-[0.87rem] text-[#1e3a44]"><span class="text-[#2AA5BD] text-[0.9rem] shrink-0 mt-0.5">✓</span>Sales analytics &amp; charts</li>
                    <li class="flex items-start gap-2.5 text-[0.87rem] text-[#1e3a44]"><span class="text-[#2AA5BD] text-[0.9rem] shrink-0 mt-0.5">✓</span>Instalment payment plans</li>
                    <li class="flex items-start gap-2.5 text-[0.87rem] text-[#1e3a44]"><span class="text-[#2AA5BD] text-[0.9rem] shrink-0 mt-0.5">✓</span>SMS payment reminders</li>
                    <li class="flex items-start gap-2.5 text-[0.87rem] text-[#1e3a44]"><span class="text-[#2AA5BD] text-[0.9rem] shrink-0 mt-0.5">✓</span>Up to 3 staff accounts</li>
                    <li class="flex items-start gap-2.5 text-[0.87rem] text-[#aac4cc]"><span class="text-[#c9d6db] text-[0.9rem] shrink-0 mt-0.5">✗</span>API integrations</li>
                </ul>
                <a href="{{ route('register') }}" class="block w-full py-3.5 rounded-[10px] bg-[#2AA5BD] text-white font-bold text-[0.9rem] text-center no-underline shadow-[0_4px_14px_rgba(42,165,189,.35)] hover:bg-[#1d8aa0] hover:shadow-[0_8px_24px_rgba(42,165,189,.45)] transition-all">Get Started</a>
            </div>
            {{-- Business+ --}}
            <div class="reveal opacity-0 translate-y-6 transition-all duration-500 [&.visible]:opacity-100 [&.visible]:translate-y-0 bg-white border border-black/5 border-[length:1.5px] rounded-2xl p-9 relative transition-all hover:-translate-y-1.5 hover:shadow-[0_24px_60px_rgba(42,165,189,0.13)]">
                <div class="text-[0.76rem] font-bold tracking-widest uppercase text-[#2AA5BD] mb-2.5">Enterprise</div>
                <div class="text-2xl font-black text-[#0b1f26] mb-5">Business+</div>
                <div class="flex items-baseline gap-1 mb-1">
                    <span class="font-mono text-[0.88rem] text-[#2AA5BD]">TZS</span>
                    <span class="font-[family-name:var(--font-playfair)] text-[3rem] font-black text-[#0b1f26] leading-none">150K</span>
                </div>
                <div class="text-[#6a8e99] text-[0.84rem]">per month</div>
                <div class="text-[#6a8e99] text-[0.84rem] my-2.5 mb-6">For established businesses that demand full power and customisation.</div>
                <div class="h-px bg-black/5 mb-6"></div>
                <ul class="list-none flex flex-col gap-2.5 mb-8">
                    <li class="flex items-start gap-2.5 text-[0.87rem] text-[#1e3a44]"><span class="text-[#2AA5BD] text-[0.9rem] shrink-0 mt-0.5">✓</span>Unlimited clients</li>
                    <li class="flex items-start gap-2.5 text-[0.87rem] text-[#1e3a44]"><span class="text-[#2AA5BD] text-[0.9rem] shrink-0 mt-0.5">✓</span>Everything in Professional</li>
                    <li class="flex items-start gap-2.5 text-[0.87rem] text-[#1e3a44]"><span class="text-[#2AA5BD] text-[0.9rem] shrink-0 mt-0.5">✓</span>Inventory management</li>
                    <li class="flex items-start gap-2.5 text-[0.87rem] text-[#1e3a44]"><span class="text-[#2AA5BD] text-[0.9rem] shrink-0 mt-0.5">✓</span>Full financial reports (PDF)</li>
                    <li class="flex items-start gap-2.5 text-[0.87rem] text-[#1e3a44]"><span class="text-[#2AA5BD] text-[0.9rem] shrink-0 mt-0.5">✓</span>Unlimited staff accounts</li>
                    <li class="flex items-start gap-2.5 text-[0.87rem] text-[#1e3a44]"><span class="text-[#2AA5BD] text-[0.9rem] shrink-0 mt-0.5">✓</span>API integrations</li>
                    <li class="flex items-start gap-2.5 text-[0.87rem] text-[#1e3a44]"><span class="text-[#2AA5BD] text-[0.9rem] shrink-0 mt-0.5">✓</span>Priority 24/7 support</li>
                    <li class="flex items-start gap-2.5 text-[0.87rem] text-[#1e3a44]"><span class="text-[#2AA5BD] text-[0.9rem] shrink-0 mt-0.5">✓</span>Custom branding</li>
                </ul>
                <a href="#" class="block w-full py-3 rounded-[10px] border border-black/5 border-[length:1.5px] bg-transparent text-[#1e3a44] font-semibold text-[0.9rem] text-center hover:border-[#2AA5BD] hover:text-[#2AA5BD] transition-all no-underline">Contact Sales</a>
            </div>
        </div>
        <div class="reveal opacity-0 translate-y-6 transition-all duration-500 [&.visible]:opacity-100 [&.visible]:translate-y-0 mt-10 p-6 px-8 bg-white border border-[rgba(42,165,189,0.18)] border-[length:1.5px] rounded-2xl flex items-center justify-center gap-12 flex-wrap text-center">
            <div class="flex items-center gap-2.5">
                <flux:icon.lock-closed class="size-6 shrink-0 text-[#2AA5BD]" />
                <div><div class="text-[0.84rem] font-bold text-[#0b1f26]">Secure Payments</div><div class="text-[0.73rem] text-[#6a8e99] mt-0.5">256-bit SSL encryption</div></div>
            </div>
            <div class="flex items-center gap-2.5">
                <flux:icon.arrow-path class="size-6 shrink-0 text-[#2AA5BD]" />
                <div><div class="text-[0.84rem] font-bold text-[#0b1f26]">14-Day Free Trial</div><div class="text-[0.73rem] text-[#6a8e99] mt-0.5">No credit card required</div></div>
            </div>
            <div class="flex items-center gap-2.5">
                <flux:icon.check-circle class="size-6 shrink-0 text-[#2AA5BD]" />
                <div><div class="text-[0.84rem] font-bold text-[#0b1f26]">Cancel Anytime</div><div class="text-[0.73rem] text-[#6a8e99] mt-0.5">No lock-in contracts</div></div>
            </div>
            <div class="flex items-center gap-2.5">
                <flux:icon.globe-europe-africa class="size-6 shrink-0 text-[#2AA5BD]" />
                <div><div class="text-[0.84rem] font-bold text-[#0b1f26]">Made for Tanzania</div><div class="text-[0.73rem] text-[#6a8e99] mt-0.5">M-Pesa &amp; TTCL supported</div></div>
            </div>
        </div>
    </div>
</section>
