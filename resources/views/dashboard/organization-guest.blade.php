@extends('layouts.dashboard')

@section('title', __('Dashboard'))

@section('content')
<div class="dash-page-header">
    <div>
        <h1 class="dash-page-title">Dashboard</h1>
        <p class="dash-page-subtitle">You need an organization to use the dashboard.</p>
    </div>
</div>

<div class="dash-card" style="max-width:480px;">
    <p style="margin:0 0 12px;color:var(--dash-ink);">Your account is not linked to an organization yet. Please contact support or your administrator to get access.</p>
    <p style="margin:0;font-size:.9rem;color:var(--dash-muted);">If you just registered, your organization may be pending approval.</p>
</div>
@endsection
