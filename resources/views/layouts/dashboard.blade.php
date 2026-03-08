<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>@yield('title', __('Dashboard')) — {{ config('app.name') }}</title>
    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700;900&family=DM+Sans:wght@300;400;500;600&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">
    @vite(['resources/css/dashboard.css', 'resources/js/app.js'])
    @fluxAppearance
</head>
<body class="min-h-screen bg-[#f2f8fa] text-[#1e3a44] font-sans antialiased" style="font-family:'DM Sans',sans-serif">
<div class="dash-overlay" id="dashOverlay" onclick="window.dashCloseAll()"></div>

<x-dashboard.mobile-header />
<x-dashboard.mobile-sidebar />

<div class="dash-shell" id="dashShell">
    <x-dashboard.sidebar />
    <x-dashboard.header />
    <main class="dash-main" id="dashMainContent">
        @yield('content')
    </main>
</div>

<x-dashboard.bottom-nav />
<x-dashboard.toast />

<script>
(function() {
  let collapsed = false;
  window.dashToggleSidebar = function() {
    collapsed = !collapsed;
    document.getElementById('dashShell').classList.toggle('collapsed', collapsed);
    var icon = document.getElementById('dash-toggle-icon');
    if (icon) icon.innerHTML = collapsed ? '<svg class="size-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.06l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" /></svg>' : '<svg class="size-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 01-.02 1.06L8.832 10l3.938 3.71a.75.75 0 11-1.04 1.08l-4.5-4.25a.75.75 0 010-1.06l4.5-4.25a.75.75 0 011.06.02z" clip-rule="evenodd" /></svg>';
  };
  window.dashOpenMobileSidebar = function() {
    document.getElementById('dashMobileSidebar').classList.add('open');
    document.getElementById('dashOverlay').classList.add('show');
    document.body.style.overflow = 'hidden';
  };
  window.dashCloseMobileSidebar = function() {
    document.getElementById('dashMobileSidebar').classList.remove('open');
    document.getElementById('dashOverlay').classList.remove('show');
    document.body.style.overflow = '';
  };
  window.dashToggleNotif = function() {
    var dd = document.getElementById('dash-notif-dropdown');
    var isOpen = dd && dd.classList.contains('open');
    window.dashCloseAll();
    if (dd && !isOpen) dd.classList.add('open');
  };
  window.dashToggleProfile = function() {
    var dd = document.getElementById('dash-profile-dropdown');
    var isOpen = dd && dd.classList.contains('open');
    window.dashCloseAll();
    if (dd && !isOpen) dd.classList.add('open');
  };
  window.dashCloseAll = function() {
    document.querySelectorAll('.dash-dd').forEach(function(d) { d.classList.remove('open'); });
    var o = document.getElementById('dashOverlay'); if (o) o.classList.remove('show');
    window.dashCloseMobileSidebar();
  };
  window.dashMarkAllRead = function() {
    document.querySelectorAll('.dash-notif-item.unread').forEach(function(n) { n.classList.remove('unread'); });
    var b = document.querySelector('.dash-hdr-badge'); if (b) b.style.display = 'none';
    var mb = document.querySelector('.dash-mob-badge'); if (mb) mb.remove();
    window.dashToast('All notifications marked as read');
  };
  window.dashToggleMobSearch = function() {
    var w = document.getElementById('dashMobSearchWrap');
    if (!w) return;
    w.classList.toggle('show');
    if (w.classList.contains('show')) { var i = w.querySelector('input'); if (i) i.focus(); }
  };
  window.dashToast = function(msg) {
    var t = document.getElementById('dash-toast');
    if (!t) return;
    t.textContent = msg;
    t.classList.add('show');
    clearTimeout(window._dashToastTimer);
    window._dashToastTimer = setTimeout(function() { t.classList.remove('show'); }, 2400);
  };
})();
document.addEventListener('click', function(e) {
  if (!e.target.closest('#dash-notif-btn') && !e.target.closest('#dash-notif-dropdown') &&
      !e.target.closest('#dash-profile-btn') && !e.target.closest('#dash-profile-dropdown') &&
      !e.target.closest('.dash-hdr-profile') && !e.target.closest('.dash-sb-footer-profile') &&
      !e.target.closest('.dash-mob-icon-btn')) {
    document.querySelectorAll('.dash-dd').forEach(function(d) { d.classList.remove('open'); });
  }
});
document.addEventListener('keydown', function(e) {
  if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
    e.preventDefault();
    var s = document.querySelector('.dash-hdr-search input');
    if (s) { s.focus(); s.select(); }
  }
  if (e.key === 'Escape') window.dashCloseAll();
});
</script>
@stack('scripts')
@fluxScripts
</body>
</html>
