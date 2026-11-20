<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Kopiluwak Lereng Muria')</title>
    <meta name="description" content="@yield('description', 'Kopi luwak dan kopi premium dari lereng Gunung Muria. Pesan online, bayar mudah, dikirim ke rumah Anda.')">
    @include('partials.assets')
    @include('partials.mobile-head')
    @stack('styles')
</head>

<body>
    @include('partials.public-nav')

    <main>
        @if (session('success'))
            <div class="toast toast-success" id="toast-msg"><i class="fa-solid fa-circle-check"></i>{{ session('success') }}</div>
        @elseif (session('error'))
            <div class="toast toast-error" id="toast-msg"><i class="fa-solid fa-circle-xmark"></i>{{ session('error') }}</div>
        @elseif (session('info'))
            <div class="toast toast-info" id="toast-msg"><i class="fa-solid fa-circle-info"></i>{{ session('info') }}</div>
        @endif
        @yield('content')
    </main>

    @include('partials.footer')

    @include('partials.mobile-nav-user')

    <script src="{{ asset('js/app.js') }}"></script>
    @stack('scripts')
</body>

</html>
