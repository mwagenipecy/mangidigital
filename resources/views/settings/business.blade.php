@extends('layouts.dashboard')

@section('title', __('Business Settings'))

@section('content')
<div class="dash-page-header">
    <div>
        <h1 class="dash-page-title">{{ __('Business Settings') }}</h1>
        <p class="dash-page-subtitle">{{ __('Your organization and business details') }}</p>
    </div>
</div>

<div class="dash-card" style="max-width: 32rem;">
    @if(auth()->user()->organization)
        <div class="dash-card-header">
            <div>
                <div class="dash-card-title">{{ auth()->user()->organization->name }}</div>
                <div class="dash-card-subtitle">{{ __('Organization') }}</div>
            </div>
        </div>
        <div style="padding: 0 20px 20px;">
            @if(session('error'))
                <div class="dash-card" style="margin:16px 0;background:rgba(239,68,68,.08);border-color:var(--dash-danger);">
                    <p style="margin:0;font-size:.9rem;color:var(--dash-danger);">{{ session('error') }}</p>
                </div>
            @endif
            @if(session('success'))
                <div class="dash-card" style="margin:16px 0;background:var(--dash-brand-10);border-color:var(--dash-brand);">
                    <p style="margin:0;font-size:.9rem;color:var(--dash-ink);">{{ session('success') }}</p>
                </div>
            @endif

            <p style="margin: 0 0 12px; font-size: .9rem; color: var(--dash-text);">
                <strong>{{ __('Status') }}:</strong> {{ auth()->user()->organization->status ?? '—' }}
            </p>
            @if(auth()->user()->organization->subscription_start)
                <p style="margin: 0 0 12px; font-size: .9rem; color: var(--dash-text);">
                    <strong>{{ __('Subscription') }}:</strong>
                    {{ auth()->user()->organization->subscription_start->format('d M Y') }}
                    @if(auth()->user()->organization->subscription_end)
                        – {{ auth()->user()->organization->subscription_end->format('d M Y') }}
                    @endif
                </p>
            @endif

            @php
                $org = auth()->user()->organization;
                $logoFile = $org?->logo_path ? public_path($org->logo_path) : null;
            @endphp

            <form method="POST" action="{{ route('settings.business.update') }}" enctype="multipart/form-data" style="margin-top:16px;">
                @csrf
                @method('PUT')

                <div style="margin-bottom:14px;">
                    <div style="font-size:.8rem;font-weight:700;text-transform:uppercase;color:var(--dash-muted);margin-bottom:6px;">{{ __('Organization Logo') }}</div>
                    <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
                        @if($logoFile && file_exists($logoFile))
                            <img src="/{{ $org->logo_path }}" alt="Logo" style="width:56px;height:56px;border-radius:12px;border:1px solid var(--dash-border);object-fit:contain;background:transparent;">
                        @else
                            <div style="width:56px;height:56px;border-radius:12px;border:1px dashed var(--dash-border);display:flex;align-items:center;justify-content:center;color:var(--dash-muted);font-weight:700;">
                                {{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::limit($org->name ?? 'ORG', 2, '')) }}
                            </div>
                        @endif
                        <div style="flex:1;min-width:220px;">
                            <input type="file" name="logo" accept="image/*" style="display:block;width:100%;padding:10px 12px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);font-size:.9rem;">
                            @error('logo')
                                <div style="margin-top:6px;color:var(--dash-danger);font-size:.85rem;">{{ $message }}</div>
                            @enderror
                            <div style="margin-top:6px;color:var(--dash-muted);font-size:.8rem;">
                                {{ __('We automatically remove a plain white background (saved as PNG).') }}
                            </div>
                        </div>
                    </div>
                </div>

                <div style="margin-bottom:14px;">
                    <div style="font-size:.8rem;font-weight:700;text-transform:uppercase;color:var(--dash-muted);margin-bottom:6px;">{{ __('Address') }}</div>
                    <textarea name="address" rows="3" style="width:100%;padding:10px 12px;border:1.5px solid var(--dash-border);border-radius:var(--dash-r-sm);font-size:.9rem;">{{ old('address', $org->address) }}</textarea>
                    @error('address')
                        <div style="margin-top:6px;color:var(--dash-danger);font-size:.85rem;">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="dash-btn dash-btn-brand">{{ __('Save changes') }}</button>
            </form>
        </div>
    @else
        <p style="margin: 0; padding: 20px; font-size: .9rem; color: var(--dash-muted);">{{ __('No organization linked to your account.') }}</p>
    @endif
</div>
@endsection
