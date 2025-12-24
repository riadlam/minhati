@extends('layouts.main')

@section('title', 'تسجيل دخول المستخدم')
@vite(['resources/css/login.css', 'resources/js/app.js'])

@section('content')
<div class="login-page">
    <div class="login-image">
        <img src="{{ asset('images/user_image.png') }}" alt="Child Image">
    </div>

    <div class="login-box">
        <h3>تسجيل دخول المستخدم</h3>

        <form action="{{ route('user.login.submit') }}" method="POST" dir="rtl">
            @csrf

            @if ($errors->any())
                <div style="color: red; text-align:center; margin-bottom:10px;">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <div class="form-group">
                <label for="code_user">رمز المستخدم</label>
                <input 
                    type="text" 
                    id="code_user" 
                    name="code_user" 
                    placeholder="أدخل رمز المستخدم"
                    value="{{ old('code_user') }}" 
                    required 
                    minlength="18"
                    maxlength="18"
                    pattern="\d{18}"
                    title="يجب أن يحتوي رمز المستخدم على 18 رقمًا"
                />
            </div>

            <div class="form-group password-wrapper">
                <label for="password">كلمة المرور</label>
                <input type="password" id="password" name="password" placeholder="أدخل كلمة المرور" required>
                <span class="toggle-password" onclick="togglePassword()">
                    <i class="fa-solid fa-eye"></i>
                </span>
            </div>

            <button type="submit" class="login-btn">تسجيل الدخول</button>
        </form>
    </div>
</div>

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

window.addEventListener('DOMContentLoaded', () => {
    const toast = document.getElementById('toast-success');
    if (toast) setTimeout(() => toast.remove(), 3000);
});
</script>
@endpush
