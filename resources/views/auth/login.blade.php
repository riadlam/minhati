@extends('layouts.main')

@section('title', 'تسجيل الدخول')
@vite(['resources/css/global.css', 'resources/css/login.css', 'resources/js/app.js','resources/js/login.js'])
@section('content')
<div class="login-page">
    <div class="login-image">
        <img src="{{ asset('images/child2.png') }}" alt="Child Image">
    </div>

    <div class="login-box">
        <h3>تسجيل الدخول</h3>

        <form action="{{ route('login') }}" method="POST" dir="rtl">
            @csrf

            @if ($errors->any())
                <div style="color: red; text-align:center; margin-bottom:10px;">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
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
});
</script>
@endpush
