@extends('layouts.main')

@section('title', 'تسجيل الدخول')
@vite(['resources/css/global.css', 'resources/css/login.css', 'resources/js/app.js','resources/js/login.js'])
@section('content')
<div class="login-page" style="background-image: url('{{ asset('images/loginbackground.jpg') }}');">
    <div class="login-box">
        <h3>تسجيل الدخول</h3>

        <form id="loginForm" action="{{ route('login') }}" method="POST" dir="rtl" >
            @csrf

            <div id="loginErrors" style="color: red; text-align:center; margin-bottom:10px; display:none;"></div>
            @if($errors->any())
                <div style="color: red; text-align:center; margin-bottom:10px;">
                    @foreach($errors->all() as $err){{ $err }}<br>@endforeach
                </div>
            @endif

            <div class="form-group">
                <label for="nin">رقم التعريف الوطني</label>
                <input 
                    type="text" 
                    id="nin" 
                    name="nin" 
                    placeholder="أدخل رقم التعريف الوطني"
                    value="{{ old('nin') }}" 
                    required 
                    maxlength="18" 
                    pattern="\d{18}" 
                    title="يجب أن يحتوي رقم التعريف الوطني على 18 رقمًا فقط"
                    oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 18);"
                >
            </div>

            <div class="form-group password-wrapper">
                <label for="password">كلمة المرور</label>
                <input type="password" id="password" name="password" placeholder="أدخل كلمة المرور" required>
                <span class="toggle-password" onclick="togglePassword()">
                    <i class="fa-solid fa-eye"></i>
                </span>
            </div>

            <div class="options right">
                <a href="{{ route('password.request') }}">نسيت كلمة المرور؟</a>
            </div>

            <button type="submit" class="login-btn">تسجيل الدخول</button>

            <div class="options center">
                <a href="{{ route('signup') }}">ليس لديك حساب ؟ انشاء حساب جديد</a>
            </div>
        </form>
    </div>
</div>
{{-- Toast Success OUTSIDE of login-page --}}
@if (session('success'))
    <div id="toast-success">
        <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
    </div>
@endif
@endsection



@push('scripts')
<script>
function togglePassword() {
    const passwordField = document.getElementById('password');
    const icon = document.querySelector('.toggle-password i');
    if (passwordField.type === 'password') {
        passwordField.type = 'text';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        passwordField.type = 'password';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
}

document.getElementById('nin').addEventListener('input', function () {
    if (this.value.length === 18) {
        document.getElementById('password').focus();
    }
});

window.addEventListener('DOMContentLoaded', () => {
    const toast = document.getElementById('toast-success');
    if (toast) setTimeout(() => toast.remove(), 3000);

    const form = document.getElementById('loginForm');
    const errorsDiv = document.getElementById('loginErrors');
    if (!form || !errorsDiv) return;

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        errorsDiv.style.display = 'none';
        errorsDiv.innerHTML = '';

        const nin = document.getElementById('nin')?.value?.trim();
        const password = document.getElementById('password')?.value;
        if (!nin || nin.length !== 18 || !password) {
            errorsDiv.textContent = 'الرجاء إدخال رقم وطني صحيح (18 رقمًا) وكلمة المرور';
            errorsDiv.style.display = 'block';
            return;
        }

        const btn = form.querySelector('button[type="submit"]');
        const origText = btn?.innerHTML;
        if (btn) { btn.disabled = true; btn.innerHTML = 'جاري تسجيل الدخول...'; }

        try {
            const url = window.getApiUrl ? window.getApiUrl('/api/auth/tuteur/login') : ('/api/auth/tuteur/login');
            const response = await fetch(url, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify({ nin: nin, password: password })
            });
            const data = await response.json().catch(() => ({}));

            if (response.ok && data.success && data.token) {
                localStorage.setItem('api_token', data.token);
                localStorage.setItem('token_type', data.token_type || 'Bearer');
                window.location.href = '{{ route('dashboard') }}';
                return;
            }

            const msg = data.message || data.errors?.nin?.[0] || data.errors?.password?.[0] || 'فشل تسجيل الدخول';
            errorsDiv.textContent = msg;
            errorsDiv.style.display = 'block';
        } catch (err) {
            errorsDiv.textContent = 'حدث خطأ في الاتصال. تحقق من الشبكة أو من عنوان الخادم (MINHATI_APP_URL).';
            errorsDiv.style.display = 'block';
        } finally {
            if (btn) { btn.disabled = false; btn.innerHTML = origText || 'تسجيل الدخول'; }
        }
    });
});
</script>
@endpush
