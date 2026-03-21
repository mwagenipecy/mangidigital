@extends('layouts.landing')

@section('title', __('Track your cargo') . ' — Mangi Digital')

@section('content')
    <x-landing-navbar />

    <main class="pt-[100px] pb-20 px-[5%]">
        <div class="max-w-lg mx-auto">
            <h1 class="font-[family-name:var(--font-playfair)] text-3xl md:text-4xl font-black text-[#0b1f26] mb-2">{{ __('Track your cargo') }}</h1>
            <p class="text-[#6a8e99] text-[0.95rem] mb-8 leading-relaxed">
                {{ __('Enter the tracking code from your delivery email. It is a unique ID — keep it private.') }}
            </p>

            @if ($errors->any())
                <div class="mb-6 rounded-[10px] border border-red-200 bg-red-50 text-red-800 px-4 py-3 text-sm" role="alert">
                    {{ $errors->first('flow_token') }}
                </div>
            @endif

            <form method="POST" action="{{ route('cargo.track.lookup') }}" class="space-y-5">
                @csrf
                <div>
                    <label for="flow_token" class="block text-sm font-semibold text-[#1e3a44] mb-2">{{ __('Tracking code') }}</label>
                    <input type="text"
                           id="flow_token"
                           name="flow_token"
                           value="{{ old('flow_token') }}"
                           autocomplete="off"
                           spellcheck="false"
                           placeholder="xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx"
                           class="w-full px-4 py-3.5 rounded-[10px] border border-black/10 bg-white text-[#0b1f26] text-[0.95rem] font-mono tracking-tight placeholder:text-[#94a3b8] focus:outline-none focus:ring-2 focus:ring-[#2AA5BD]/40 focus:border-[#2AA5BD]"
                           required>
                </div>
                <button type="submit"
                        class="w-full md:w-auto inline-flex items-center justify-center bg-[#2AA5BD] text-white py-3.5 px-8 text-[0.95rem] font-semibold rounded-[10px] shadow-[0_4px_14px_rgba(42,165,189,0.28)] border-0 cursor-pointer hover:bg-[#1d8aa0] transition-colors">
                    {{ __('View status') }}
                </button>
            </form>

            <p class="mt-10 text-sm text-[#94a3b8]">
                <a href="{{ url('/') }}" class="text-[#2AA5BD] font-medium no-underline hover:underline">{{ __('← Back to home') }}</a>
            </p>
        </div>
    </main>
@endsection
