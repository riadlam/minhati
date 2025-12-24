@extends('layouts.app')

@section('title', 'كلمة مرور جديدة')

@section('content')
<div class="login-box">
    <h3>كلمة مرور جديدة</h3>

    <form method="POST" action="{{ route('password.update') }}" dir="rtl">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <div class="form-group">
            <label for="email">البريد الإلكتروني</label>
            <input type="email" id="email" name="email" required value="{{ old('email') }}">
            @error('email') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="form-group">
            <label for="password">كلمة المرور الجديدة</label>
            <input type="password" id="password" name="password" required>
            @error('password') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="form-group">
            <label for="password_confirmation">تأكيد كلمة المرور</label>
            <input type="password" id="password_confirmation" name="password_confirmation" required>
        </div>

        <button type="submit" class="login-btn">تحديث كلمة المرور</button>
    </form>
</div>
@endsection
