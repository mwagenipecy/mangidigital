<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Mangi Digital — Unified Business Management')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700;900&family=DM+Sans:wght@300;400;500;600&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>
        html { scroll-behavior: smooth; }
        body { font-family: 'DM Sans', sans-serif; }
        h1, h2, h3, h4 { font-family: 'Playfair Display', serif; }
    </style>
</head>
<body class="bg-white text-[#1e3a44] text-base leading-[1.65] overflow-x-hidden antialiased">
    @yield('content')
    @livewireScripts
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const navbar = document.getElementById('navbar');
            if (navbar) {
                window.addEventListener('scroll', () => navbar.classList.toggle('shadow-[0_4px_28px_rgba(42,165,189,.14)]', window.scrollY > 60));
            }
            const reveals = document.querySelectorAll('.reveal');
            const obs = new IntersectionObserver((entries) => {
                entries.forEach((e, i) => {
                    if (e.isIntersecting) {
                        setTimeout(() => e.target.classList.add('visible'), i * 80);
                        obs.unobserve(e.target);
                    }
                });
            }, { threshold: 0.1 });
            reveals.forEach(el => obs.observe(el));
        });
    </script>
</body>
</html>
