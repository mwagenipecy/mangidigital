<section id="testimonials" class="py-[100px] px-[5%] bg-white">
    <div class="max-w-[1200px] mx-auto">
        <div class="text-center mb-16">
            <div class="reveal opacity-0 translate-y-6 transition-all duration-500 [&.visible]:opacity-100 [&.visible]:translate-y-0">
                <span class="inline-flex items-center gap-1.5 py-1.5 px-4 rounded-full border border-[#2AA5BD] border-[length:1.5px] bg-[rgba(42,165,189,0.10)] text-[#2AA5BD] text-[0.78rem] font-bold tracking-wider uppercase mb-5">Customer Stories</span>
            </div>
            <h2 class="reveal opacity-0 translate-y-6 transition-all duration-500 [&.visible]:opacity-100 [&.visible]:translate-y-0 text-[clamp(2rem,3.5vw,2.8rem)] font-[family-name:var(--font-playfair)] text-[#0b1f26] mb-4">
                Businesses That Trust<br><em class="italic text-[#2AA5BD]">Mangi Digital</em>
            </h2>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach([
                ['stars' => 5, 'text' => '"Before Mangi Digital, I had no idea who owed me money. Now I can check from my phone while I\'m at the market. The instalment tracker is exactly what my customers needed."', 'initials' => 'AM', 'name' => 'Amina Mkwawa', 'role' => "Owner, Amina's Fashion Store — Dar es Salaam", 'avatar' => 'av1'],
                ['stars' => 5, 'text' => '"The expense tracking saved us from overspending every month. We finally have a clear picture of our costs vs revenue. The team onboarding was super fast too."', 'initials' => 'JK', 'name' => 'Joseph Kamau', 'role' => 'Director, Kamau Distributors — Arusha', 'avatar' => 'av2'],
                ['stars' => 4, 'text' => '"Managing 200+ clients with partial payments used to be a nightmare in Excel. Mangi Digital automated it all. SMS reminders alone have cut my late payments by 60%."', 'initials' => 'FS', 'name' => 'Fatuma Salim', 'role' => 'CEO, Salim Electronics — Mwanza', 'avatar' => 'av3'],
            ] as $t)
                <div class="reveal opacity-0 translate-y-6 transition-all duration-500 [&.visible]:opacity-100 [&.visible]:translate-y-0 bg-[#f5fbfc] border border-black/5 border-[length:1.5px] rounded-2xl p-7 transition-all hover:shadow-[0_12px_36px_rgba(42,165,189,0.13)] hover:-translate-y-0.5">
                    <div class="flex items-center gap-0.5 text-[#2AA5BD] mb-3.5">
                        @for ($i = 0; $i < $t['stars']; $i++)
                            <flux:icon.star variant="solid" class="size-4 shrink-0" />
                        @endfor
                        @for ($i = $t['stars']; $i < 5; $i++)
                            <flux:icon.star variant="outline" class="size-4 shrink-0 opacity-60" />
                        @endfor
                    </div>
                    <p class="text-[#1e3a44] text-[0.9rem] leading-[1.78] mb-5 italic">{{ $t['text'] }}</p>
                    <div class="flex items-center gap-3">
                        <div class="w-[42px] h-[42px] rounded-full shrink-0 flex items-center justify-center font-bold text-[0.85rem] text-white bg-gradient-to-br from-[#2AA5BD] to-[#1d8aa0]">{{ $t['initials'] }}</div>
                        <div>
                            <div class="font-bold text-[0.88rem] text-[#0b1f26]">{{ $t['name'] }}</div>
                            <div class="text-[0.77rem] text-[#6a8e99]">{{ $t['role'] }}</div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
