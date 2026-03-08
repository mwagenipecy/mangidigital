<section id="how" class="py-[100px] px-[5%] bg-white">
    <div class="max-w-[1200px] mx-auto">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-20 items-center">
            <div>
                <div class="reveal opacity-0 translate-y-6 transition-all duration-500 [&.visible]:opacity-100 [&.visible]:translate-y-0">
                    <span class="inline-flex items-center gap-1.5 py-1.5 px-4 rounded-full border border-[#2AA5BD] border-[length:1.5px] bg-[rgba(42,165,189,0.10)] text-[#2AA5BD] text-[0.78rem] font-bold tracking-wider uppercase mb-5">How It Works</span>
                </div>
                <h2 class="reveal opacity-0 translate-y-6 transition-all duration-500 [&.visible]:opacity-100 [&.visible]:translate-y-0 text-[clamp(2rem,3.5vw,2.8rem)] font-[family-name:var(--font-playfair)] text-[#0b1f26] text-left mb-3">
                    Simple Setup,<br><em class="italic text-[#2AA5BD]">Powerful Results</em>
                </h2>
                <p class="reveal opacity-0 translate-y-6 transition-all duration-500 [&.visible]:opacity-100 [&.visible]:translate-y-0 text-[#6a8e99] mb-10 text-[0.95rem]">
                    Get your business running on Mangi Digital in minutes — no technical knowledge required.
                </p>
                <div class="flex flex-col">
                    @foreach([
                        ['num' => '01', 'title' => 'Create Your Business Account', 'text' => 'Sign up in under 2 minutes. Add your business name, logo, and sector. Your dashboard is ready immediately.'],
                        ['num' => '02', 'title' => 'Add Clients & Products', 'text' => 'Import or manually add your client list and product catalogue. Set prices, payment terms, and credit limits per client.'],
                        ['num' => '03', 'title' => 'Record Orders, Sales & Expenses', 'text' => 'Start recording transactions from day one. Every entry updates your dashboard in real time — no manual reconciliation needed.'],
                        ['num' => '04', 'title' => 'Track Payments & Get Paid Faster', 'text' => 'Monitor instalment payments, send automated reminders, and know exactly who has paid and who still owes you.'],
                    ] as $step)
                        <div class="reveal opacity-0 translate-y-6 transition-all duration-500 [&.visible]:opacity-100 [&.visible]:translate-y-0 flex gap-6 py-6 border-b border-black/5 last:border-0">
                            <div class="w-11 h-11 rounded-full flex-shrink-0 bg-[rgba(42,165,189,0.10)] border-2 border-[#2AA5BD] flex items-center justify-center font-mono text-[0.82rem] text-[#2AA5BD] font-bold">{{ $step['num'] }}</div>
                            <div>
                                <h4 class="text-[#0b1f26] text-base font-bold mb-1.5">{{ $step['title'] }}</h4>
                                <p class="text-[#6a8e99] text-[0.88rem]">{{ $step['text'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="reveal opacity-0 translate-y-6 transition-all duration-500 [&.visible]:opacity-100 [&.visible]:translate-y-0 bg-[#f5fbfc] border border-[rgba(42,165,189,0.18)] border-[length:1.5px] rounded-2xl p-7">
                <div class="flex items-center gap-2 text-[0.72rem] text-[#6a8e99] mb-3.5 font-mono tracking-wider">
                    <flux:icon.device-phone-mobile class="size-3.5 shrink-0" />
                    MOBILE VIEW
                </div>
                <div class="bg-white rounded-[22px] border border-black/5 border-[length:1.5px] p-4 mx-auto max-w-[260px] shadow-[0_12px_40px_rgba(42,165,189,.1)]">
                    <div class="flex justify-center mb-3.5">
                        <div class="w-14 h-2 bg-[#eaf6f9] rounded"></div>
                    </div>
                    <div class="flex items-center gap-1.5 text-[0.7rem] text-[#6a8e99] mb-2">
                    <flux:icon.chart-bar class="size-3.5 shrink-0" />
                    Today's Summary
                </div>
                    <div class="bg-[#f5fbfc] rounded-[10px] p-3 mb-2 border border-black/5">
                        <div class="flex justify-between items-center text-[0.75rem] text-[#6a8e99] mb-1.5"><strong class="text-[#0b1f26]">Total Sales</strong><span class="text-[#2AA5BD] font-mono text-[0.7rem]">TZS 1.4M</span></div>
                        <div class="h-1.5 bg-[#eaf6f9] rounded overflow-hidden"><div class="h-full rounded bg-[#2AA5BD] w-[72%]"></div></div>
                    </div>
                    <div class="bg-[#f5fbfc] rounded-[10px] p-3 mb-2 border border-black/5">
                        <div class="flex justify-between items-center text-[0.75rem] text-[#6a8e99] mb-1.5"><strong class="text-[#0b1f26]">Expenses</strong><span class="text-amber-500 font-mono text-[0.7rem]">TZS 420K</span></div>
                        <div class="h-1.5 bg-[#eaf6f9] rounded overflow-hidden"><div class="h-full rounded bg-amber-500 w-[35%]"></div></div>
                    </div>
                    <div class="bg-[#f5fbfc] rounded-[10px] p-3 mb-2 border border-black/5">
                        <div class="flex justify-between items-center text-[0.75rem] text-[#6a8e99] mb-1.5"><strong class="text-[#0b1f26]">Pending Pay.</strong><span class="text-red-500 font-mono text-[0.7rem]">TZS 680K</span></div>
                        <div class="h-1.5 bg-[#eaf6f9] rounded overflow-hidden"><div class="h-full rounded bg-red-500 w-[50%]"></div></div>
                    </div>
                    <div class="mt-3">
                        <div class="text-[0.68rem] text-[#6a8e99] mb-1.5">Recent Orders</div>
                        <div class="flex justify-between items-center py-1.5 border-b border-black/5 text-[0.7rem]"><span class="text-[#1e3a44]"><span class="inline-block w-1.5 h-1.5 rounded-full bg-green-500 mr-1"></span>Order #1041</span><span class="text-[#2AA5BD] font-mono text-[0.65rem]">Delivered</span></div>
                        <div class="flex justify-between items-center py-1.5 border-b border-black/5 text-[0.7rem]"><span class="text-[#1e3a44]"><span class="inline-block w-1.5 h-1.5 rounded-full bg-amber-500 mr-1"></span>Order #1042</span><span class="text-amber-500 font-mono text-[0.65rem]">Pending</span></div>
                        <div class="flex justify-between items-center py-1.5 text-[0.7rem]"><span class="text-[#1e3a44]"><span class="inline-block w-1.5 h-1.5 rounded-full bg-green-500 mr-1"></span>Order #1043</span><span class="text-[#2AA5BD] font-mono text-[0.65rem]">Delivered</span></div>
                    </div>
                </div>
                <div class="mt-4 p-3.5 px-4 bg-[rgba(42,165,189,0.10)] rounded-xl border border-[rgba(42,165,189,0.18)] border-[length:1.5px]">
                    <div class="flex items-center gap-2 mb-2"><span class="w-2 h-2 rounded-full bg-[#2AA5BD]"></span><span class="text-[0.78rem] text-[#6a8e99]">Works on all devices</span></div>
                    <div class="flex gap-2">
                        <span class="text-[0.7rem] py-1 px-3 bg-white border border-[rgba(42,165,189,0.18)] border-[length:1.5px] rounded-full text-[#2AA5BD] font-semibold">Web</span>
                        <span class="text-[0.7rem] py-1 px-3 bg-[#2AA5BD] rounded-full text-white font-semibold">Mobile</span>
                        <span class="text-[0.7rem] py-1 px-3 bg-white border border-black/5 border-[length:1.5px] rounded-full text-[#6a8e99] font-semibold">Tablet</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
