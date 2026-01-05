<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Minha Madrassiya')</title>

    {{-- Main layout CSS --}}
    @vite(['resources/css/global.css', 'resources/css/main.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">


    {{-- Page-specific CSS --}}
    @stack('styles')
</head>
<body>
    {{-- Navbar --}}
    <nav class="main-navbar">
        <img src="{{ asset('images/LOGO_ads.png') }}" class="nav-logo left" alt="Logo gauche">
        <div class="nav-title">
            <span>الجمهورية الجزائرية الديمقراطية الشعبية</span>
            <span class="ministry-title">وزارة التضامن الوطني والأسرة وقضايا المرأة</span>
            <span>وكالة التنمية الإجتماعية</span>
        </div>
        <img src="{{ asset('images/ministere1.png') }}" class="nav-logo right" alt="Logo droite">
    </nav>

    {{-- Main content --}}
    <div class="main-content">
        @yield('content')
    </div>

    {{-- Footer --}}
    <footer class="main-footer">
        <span>  © {{ date('Y') }} المنصة الرقمية لطلب المنحة المدرسية الخاصة — جميع الحقوق محفوظة</span>
    </footer>

    {{-- Page-specific JS --}}
    @stack('scripts')


</body>
</html>
