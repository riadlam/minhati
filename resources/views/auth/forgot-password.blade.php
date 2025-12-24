@extends('layouts.main')

@section('title', 'إعادة تعيين كلمة المرور')

@vite(['resources/css/login.css', 'resources/js/login.js'])

@section('content')
<div class="login-page">
    <div class="login-image">
        <img src="{{ asset('images/child2.png') }}" alt="Child Image">
    </div>

    <div class="login-box">
        <!-- 🔙 Bouton retour -->
        <a href="{{ route('login.form') }}" class="back-btn rtl">
            العودة إلى تسجيل الدخول <i class="fas fa-arrow-left"></i>
        </a>

        <h3>إعادة تعيين كلمة المرور</h3>

        <form method="POST" action="{{ route('password.email') }}" id="resetForm" dir="rtl">
            @csrf
            <div class="form-group">
                <label for="email">البريد الإلكتروني</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    required 
                    placeholder="example@email.com"
                    value="{{ old('email') }}"
                >
                @error('email')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <button type="submit" class="login-btn">إرسال الرابط</button>
        </form>
    </div>
</div>
<!-- ✅ Message de succès -->
        @if (session('success'))
        <div id="successModal" class="modal-overlay">
            <div class="modal-box">
                <h4>تم إرسال الرابط!</h4>
                <p>لقد تم إرسال رابط إعادة التعيين إلى البريد الإلكتروني الذي أدخلته.</p>
                <button id="closeModal" class="login-btn">حسناً</button>
            </div>
        </div>
        @endif
@endsection
