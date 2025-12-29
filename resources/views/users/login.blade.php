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

        <form id="userLoginForm" dir="rtl">
            @csrf

            <div id="userLoginErrors" style="color: red; text-align:center; margin-bottom:10px; display:none;"></div>

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

    // Handle user login form submission via API
    const userLoginForm = document.getElementById('userLoginForm');
    const errorDiv = document.getElementById('userLoginErrors');
    
    if (userLoginForm) {
        userLoginForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            errorDiv.style.display = 'none';
            errorDiv.innerHTML = '';

            const formData = new FormData(userLoginForm);
            const data = {
                code_user: formData.get('code_user'),
                password: formData.get('password')
            };

            try {
                const response = await fetch('/api/auth/user/login', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();

                if (!response.ok || !result.success) {
                    const errorMsg = result.message || 'حدث خطأ أثناء تسجيل الدخول';
                    const errors = result.errors || {};
                    let errorHtml = errorMsg;
                    
                    if (Object.keys(errors).length > 0) {
                        errorHtml = Object.values(errors).flat().join('<br>');
                    }
                    
                    errorDiv.innerHTML = errorHtml;
                    errorDiv.style.display = 'block';
                    return;
                }

                // Store token in localStorage
                if (result.token) {
                    localStorage.setItem('api_token', result.token);
                    localStorage.setItem('token_type', result.token_type || 'Bearer');
                }

                // Success - redirect to dashboard
                window.location.href = '/user/dashboard';
            } catch (error) {
                console.error('Login error:', error);
                errorDiv.innerHTML = 'حدث خطأ في الاتصال بالخادم';
                errorDiv.style.display = 'block';
            }
        });
    }
});
</script>
@endpush
