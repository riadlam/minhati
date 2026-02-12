<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="minhati-api-url" content="{{ config('app.minhati_api_url') }}">
    <title>@yield('title', 'Minha Madrassiya')</title>

    {{-- Main layout CSS --}}
    @vite(['resources/css/global.css', 'resources/css/main.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">


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



    {{-- Page-specific JS --}}
    @stack('scripts')
    <!-- Bootstrap JS (with Popper) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // API base URL: when MINHATI_APP_URL is set, all API requests go to that host (two-host load balancing).
    window.MINHATI_API_URL = document.querySelector('meta[name="minhati-api-url"]')?.getAttribute('content') || (window.location.origin + '/api');
    window.getApiUrl = function(path) {
        var base = (window.MINHATI_API_URL || (window.location.origin + '/api')).replace(/\/$/, '');
        if (typeof path !== 'string') return base;
        if (path.indexOf('http') === 0) return path;
        var p = path.indexOf('/api') === 0 ? path.substring(4) : (path.indexOf('/') === 0 ? path : '/' + path);
        return base + (p.indexOf('/') === 0 ? p : '/' + p);
    };

    // Bootstrap API token from session so /api/user/* calls work (e.g. after web login or two-host).
    // Always sync when server has a token so we never use a stale/invalid token.
    @if(session('user_logged') && session('api_token'))
    (function(){
        try {
            var t = @json(session('api_token'));
            if (t && typeof localStorage !== 'undefined') {
                localStorage.setItem('api_token', t);
                localStorage.setItem('token_type', 'Bearer');
            }
        } catch (e) {}
    })();
    @endif

    // Auto-attach API auth headers for requests targeting MINHATI_API_URL.
    (function() {
        if (!window.fetch) return;
        var nativeFetch = window.fetch.bind(window);
        var apiBase = (window.MINHATI_API_URL || (window.location.origin + '/api')).replace(/\/$/, '');
        window.fetch = function(input, init) {
            try {
                var reqUrl = typeof input === 'string' ? input : (input && input.url ? input.url : '');
                if (reqUrl) {
                    var absoluteUrl = reqUrl.indexOf('http') === 0 ? reqUrl : new URL(reqUrl, window.location.origin).toString();
                    if (absoluteUrl.indexOf(apiBase + '/') === 0) {
                        init = init || {};
                        var headers = new Headers(init.headers || (input instanceof Request ? input.headers : undefined));
                        var csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                        var token = localStorage.getItem('api_token');
                        var tokenType = localStorage.getItem('token_type') || 'Bearer';
                        if (csrf && !headers.has('X-CSRF-TOKEN')) headers.set('X-CSRF-TOKEN', csrf);
                        if (!headers.has('Accept')) headers.set('Accept', 'application/json');
                        if (token && !headers.has('Authorization')) headers.set('Authorization', tokenType + ' ' + token);
                        init.headers = headers;
                    }
                }
            } catch (_) {}
            return nativeFetch(input, init);
        };
    })();
</script>

</body>
</html>
