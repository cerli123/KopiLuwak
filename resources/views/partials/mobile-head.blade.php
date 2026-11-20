{{-- Tambahan <head> untuk versi HP / Android (PWA). Letakkan SETELAH @include('partials.assets') --}}
@php $mAdminArea = request()->is('admin') || request()->is('admin/*'); @endphp
<meta name="theme-color" content="{{ $mAdminArea ? '#2A160B' : '#1A0E07' }}">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black">
<meta name="apple-mobile-web-app-title" content="{{ $mAdminArea ? 'Admin Kopi' : 'Kopiluwak' }}">
<link rel="manifest" href="{{ asset($mAdminArea ? 'manifest-admin.webmanifest' : 'manifest.webmanifest') }}">
<link rel="apple-touch-icon" href="{{ asset('icons/apple-touch-icon.png') }}">
<link rel="stylesheet" href="{{ asset('css/mobile.css') }}?v={{ @filemtime(public_path('css/mobile.css')) }}">
