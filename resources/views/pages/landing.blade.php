{{--
    Flow: Route (web) → LandingController::index() → this blade (pages.landing)
    This view then renders each section via Livewire components.
--}}
@extends('layouts.landing')

@section('title', 'Mangi Digital — Unified Business Management')

@section('content')
    @livewire('landing.navbar')
    @livewire('landing.hero-section')
    @livewire('landing.marquee-section')
    @livewire('landing.about-section')
    <div class="h-px bg-gradient-to-r from-transparent via-[rgba(42,165,189,0.18)] to-transparent"></div>
    @livewire('landing.how-it-works-section')
    <div class="h-px bg-gradient-to-r from-transparent via-[rgba(42,165,189,0.18)] to-transparent"></div>
    @livewire('landing.pricing-section')
    @livewire('landing.testimonials-section')
    @livewire('landing.cta-section')
    @livewire('landing.footer-section')
@endsection
