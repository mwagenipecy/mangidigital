<section id="about" class="py-[100px] px-[5%] bg-[#f5fbfc]">
    <div class="max-w-[1200px] mx-auto">
        <div class="text-center mb-16">
            <div class="reveal opacity-0 translate-y-6 transition-all duration-500 [&.visible]:opacity-100 [&.visible]:translate-y-0">
                <span class="inline-flex items-center gap-1.5 py-1.5 px-4 rounded-full border border-[#2AA5BD] border-[length:1.5px] bg-[rgba(42,165,189,0.10)] text-[#2AA5BD] text-[0.78rem] font-bold tracking-wider uppercase mb-5">What We Do</span>
            </div>
            <h2 class="reveal opacity-0 translate-y-6 transition-all duration-500 [&.visible]:opacity-100 [&.visible]:translate-y-0 text-[clamp(2rem,3.5vw,2.8rem)] font-[family-name:var(--font-playfair)] text-[#0b1f26] mb-4">
                Everything Your Business Needs,<br><em class="italic text-[#2AA5BD]">Built Into One Platform</em>
            </h2>
            <p class="reveal opacity-0 translate-y-6 transition-all duration-500 [&.visible]:opacity-100 [&.visible]:translate-y-0 text-[#6a8e99] text-[1.05rem] max-w-[560px] mx-auto">
                From taking orders to tracking expenses, managing sales to following up on client payments — Mangi Digital gives you complete control.
            </p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach([
                ['icon' => 'shopping-cart', 'title' => 'External Order Management', 'desc' => 'Manage all incoming orders from outside suppliers and vendors. Track order status, delivery dates, and payments in one unified view.'],
                ['icon' => 'banknotes', 'title' => 'Expense Tracking', 'desc' => 'Record, categorise and analyse every business expense. Set budgets, get alerts and gain clarity on where your money is going — daily or monthly.'],
                ['icon' => 'chart-bar', 'title' => 'Sales Management', 'desc' => 'Log every sale, monitor top products, and view performance trends with real-time charts. Know your best selling days and plan accordingly.'],
                ['icon' => 'users', 'title' => 'Client Management', 'desc' => 'Maintain a clean client database with contact details, purchase history, and credit status. Never lose track of who owes what.'],
                ['icon' => 'credit-card', 'title' => 'Instalment & Partial Payments', 'desc' => 'Allow clients to pay in small amounts over time. Track each partial payment, auto-calculate balances, and send reminders for outstanding amounts.'],
                ['icon' => 'cube', 'title' => 'Inventory Overview', 'desc' => 'Connect your stock levels to your sales and orders. Get low-stock alerts and ensure you never run out of your best-selling products.'],
            ] as $feat)
                <div class="reveal opacity-0 translate-y-6 transition-all duration-500 [&.visible]:opacity-100 [&.visible]:translate-y-0 bg-white border border-black/5 border-[length:1.5px] rounded-2xl p-8 transition-all relative overflow-hidden group hover:border-[rgba(42,165,189,0.18)] hover:-translate-y-1 hover:shadow-[0_16px_48px_rgba(42,165,189,0.13)] after:content-[''] after:absolute after:bottom-0 after:left-0 after:right-0 after:h-[3px] after:bg-[#2AA5BD] after:scale-x-0 after:origin-left after:transition-transform group-hover:after:scale-x-100">
                    <div class="w-[52px] h-[52px] rounded-xl bg-[rgba(42,165,189,0.10)] border border-[rgba(42,165,189,0.18)] border-[length:1.5px] flex items-center justify-center mb-5 text-[#2AA5BD]">
                        @switch($feat['icon'])
                            @case('shopping-cart')<flux:icon.shopping-cart class="size-6" />@break
                            @case('banknotes')<flux:icon.banknotes class="size-6" />@break
                            @case('chart-bar')<flux:icon.chart-bar class="size-6" />@break
                            @case('users')<flux:icon.users class="size-6" />@break
                            @case('credit-card')<flux:icon.credit-card class="size-6" />@break
                            @case('cube')<flux:icon.cube class="size-6" />@break
                        @endswitch
                    </div>
                    <h3 class="text-[1.05rem] font-bold text-[#0b1f26] mb-2.5">{{ $feat['title'] }}</h3>
                    <p class="text-[#6a8e99] text-[0.88rem] leading-[1.75]">{{ $feat['desc'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>
