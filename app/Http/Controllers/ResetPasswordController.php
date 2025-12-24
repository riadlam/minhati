@extends('layouts.app')

@section('title', 'إعادة تعيين كلمة المرور')

@section('content')
<div class="login-box">
    <h3>إعادة تعيين كلمة المرور</h3>

    <form method="POST" action="{{ route('password.email') }}" id="resetForm">
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

<!-- ✅ Success modal -->
@if (session('success'))
<div id="successModal" class="modal-overlay">
    <div class="modal-box">
        <h4>تم إرسال الرابط!</h4>
        <p>لقد تم إرسال رابط إعادة التعيين إلى البريد الإلكتروني الذي أدخلته.</p>
        <button id="closeModal" class="modal-btn">حسناً</button>
    </div>
</div>
@endif

<style>
    /* Simple modal styling */
    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        animation: fadeIn 0.3s ease-in-out;
    }

    .modal-box {
        background: #fff;
        padding: 30px;
        border-radius: 10px;
        width: 90%;
        max-width: 400px;
        text-align: center;
        box-shadow: 0 5px 20px rgba(0,0,0,0.2);
    }

    .modal-box h4 {
        margin-bottom: 10px;
        color: #2c3e50;
    }

    .modal-box p {
        font-size: 14px;
        margin-bottom: 20px;
    }

    .modal-btn {
        background: #3498db;
        color: white;
        border: none;
        padding: 8px 20px;
        border-radius: 6px;
        cursor: pointer;
    }

    .modal-btn:hover {
        background: #2980b9;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modal = document.getElementById('successModal');
        const closeBtn = document.getElementById('closeModal');

        if (modal && closeBtn) {
            closeBtn.addEventListener('click', () => {
                modal.remove();
            });
        }
    });
</script>
@endsection
