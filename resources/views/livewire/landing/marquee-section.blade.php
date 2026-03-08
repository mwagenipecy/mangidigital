<div class="py-5 overflow-hidden bg-[rgba(42,165,189,0.10)] border-y border-[length:1.5px] border-[rgba(42,165,189,0.18)]">
    <div class="flex gap-12 animate-[marquee_28s_linear_infinite] w-max hover:[animation-play-state:paused]">
        @foreach([
            ['label' => 'Order Management'],
            ['label' => 'Expense Tracking'],
            ['label' => 'Sales Analytics'],
            ['label' => 'Client Management'],
            ['label' => 'Instalment Payments'],
            ['label' => 'Inventory Control'],
            ['label' => 'Revenue Reports'],
            ['label' => 'Payment Reminders'],
        ] as $item)
            <div class="flex items-center gap-2 text-[#2AA5BD] text-[0.84rem] font-semibold whitespace-nowrap">
                @switch($item['label'])
                    @case('Order Management')<flux:icon.shopping-cart class="size-5 shrink-0 text-[#2AA5BD]" />@break
                    @case('Expense Tracking')<flux:icon.banknotes class="size-5 shrink-0 text-[#2AA5BD]" />@break
                    @case('Sales Analytics')<flux:icon.chart-bar class="size-5 shrink-0 text-[#2AA5BD]" />@break
                    @case('Client Management')<flux:icon.users class="size-5 shrink-0 text-[#2AA5BD]" />@break
                    @case('Instalment Payments')<flux:icon.credit-card class="size-5 shrink-0 text-[#2AA5BD]" />@break
                    @case('Inventory Control')<flux:icon.cube class="size-5 shrink-0 text-[#2AA5BD]" />@break
                    @case('Revenue Reports')<flux:icon.presentation-chart-bar class="size-5 shrink-0 text-[#2AA5BD]" />@break
                    @case('Payment Reminders')<flux:icon.bell class="size-5 shrink-0 text-[#2AA5BD]" />@break
                @endswitch
                <span>{{ $item['label'] }}</span>
            </div>
        @endforeach
        @foreach([
            ['label' => 'Order Management'],
            ['label' => 'Expense Tracking'],
            ['label' => 'Sales Analytics'],
            ['label' => 'Client Management'],
            ['label' => 'Instalment Payments'],
            ['label' => 'Inventory Control'],
            ['label' => 'Revenue Reports'],
            ['label' => 'Payment Reminders'],
        ] as $item)
            <div class="flex items-center gap-2 text-[#2AA5BD] text-[0.84rem] font-semibold whitespace-nowrap">
                @switch($item['label'])
                    @case('Order Management')<flux:icon.shopping-cart class="size-5 shrink-0 text-[#2AA5BD]" />@break
                    @case('Expense Tracking')<flux:icon.banknotes class="size-5 shrink-0 text-[#2AA5BD]" />@break
                    @case('Sales Analytics')<flux:icon.chart-bar class="size-5 shrink-0 text-[#2AA5BD]" />@break
                    @case('Client Management')<flux:icon.users class="size-5 shrink-0 text-[#2AA5BD]" />@break
                    @case('Instalment Payments')<flux:icon.credit-card class="size-5 shrink-0 text-[#2AA5BD]" />@break
                    @case('Inventory Control')<flux:icon.cube class="size-5 shrink-0 text-[#2AA5BD]" />@break
                    @case('Revenue Reports')<flux:icon.presentation-chart-bar class="size-5 shrink-0 text-[#2AA5BD]" />@break
                    @case('Payment Reminders')<flux:icon.bell class="size-5 shrink-0 text-[#2AA5BD]" />@break
                @endswitch
                <span>{{ $item['label'] }}</span>
            </div>
        @endforeach
    </div>
</div>
<style>
@keyframes marquee { from { transform: translateX(0); } to { transform: translateX(-50%); } }
</style>
