@extends('layouts.main')

@section('title', 'الإعدادات')

@vite(['resources/css/dashboard.css'])

@push('styles')
<style>
.logged-in-as-badge {
    position: sticky; top: 0; z-index: 100;
    display: flex; align-items: center; gap: 0.5rem;
    padding: 0.5rem 1rem; margin-bottom: 0.75rem;
    background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
    border: 1px solid #f59e0b; border-radius: 8px;
    font-size: 0.9rem; font-weight: 600; color: #92400e;
    box-shadow: 0 2px 8px rgba(245, 158, 11, 0.2);
}
.end-impersonate-link { margin-right: auto; color: #b45309; text-decoration: underline; font-weight: 600; }
.end-impersonate-link:hover { color: #92400e; }
.settings-card {
    background: #fff; border-radius: 16px; padding: 1.25rem;
    border: 1px solid #e5e7eb; box-shadow: 0 8px 28px rgba(15, 3, 58, 0.08);
}
.settings-card h3 { color: #0f033a; margin-bottom: 0.4rem; font-weight: 700; }
.settings-card p { color: #4b5563; margin-bottom: 1rem; }
.settings-form-actions { margin-top: 1rem; }
</style>
@endpush

@section('content')
<div class="dashboard-container" dir="rtl">
    <aside class="dashboard-sidebar">
        <div class="sidebar-header"><h3>القائمة</h3></div>
        <nav class="sidebar-nav">
            <ul class="sidebar-menu">
                <li class="sidebar-item">
                    <a href="{{ route('user.dashboard') }}" class="sidebar-link">
                        <i class="fa-solid fa-home"></i>
                        <span>الرئيسية</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="{{ route('user.users.list') }}" class="sidebar-link">
                        <i class="fa-solid fa-users-gear"></i>
                        <span>المستخدمون</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="{{ route('user.add.user') }}" class="sidebar-link">
                        <i class="fa-solid fa-user-plus"></i>
                        <span>إضافة مستخدم جديد</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="{{ route('user.admin.ts_commune.management') }}" class="sidebar-link">
                        <i class="fa-solid fa-building-user"></i>
                        <span>إدارة تقني البلدية</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="{{ route('user.admin.das.management') }}" class="sidebar-link">
                        <i class="fa-solid fa-user-tie"></i>
                        <span>إدارة DAS</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="{{ route('user.admin.comite_wilaya.management') }}" class="sidebar-link">
                        <i class="fa-solid fa-landmark"></i>
                        <span>إدارة لجنة الولاية</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="{{ route('user.admin.antr.management') }}" class="sidebar-link">
                        <i class="fa-solid fa-sitemap"></i>
                        <span>إدارة ATR</span>
                    </a>
                </li>
                <li class="sidebar-item active">
                    <a href="{{ route('user.settings') }}" class="sidebar-link">
                        <i class="fa-solid fa-gear"></i>
                        <span>الإعدادات</span>
                    </a>
                </li>
            </ul>
        </nav>
        <div class="sidebar-footer">
            <form id="logout-form" action="{{ route('user.logout') }}" method="POST" style="display: none;">@csrf</form>
            <button class="sidebar-logout-btn" onclick="confirmLogout()">
                <i class="fa-solid fa-right-from-bracket"></i>
                <span>تسجيل الخروج</span>
            </button>
        </div>
    </aside>

    <div class="dashboard-main-content">
        @if(!empty($impersonating) && !empty($loggedInAsName))
        <div class="logged-in-as-badge" role="status">
            <i class="fa-solid fa-user-secret"></i>
            <span>تم الدخول باسم {{ $loggedInAsName }}</span>
            <a href="{{ route('user.impersonate.end') }}" class="end-impersonate-link">إنهاء العرض فقط</a>
        </div>
        @endif
        <div class="dashboard-content-wrapper">
            <div class="dashboard-header" style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;">
                <div>
                    <h2>الإعدادات</h2>
                    <p>تعديل الإعدادات العامة للنظام (مثل CCP المستخدم في ملف المخالصة)</p>
                </div>
                <a href="{{ route('user.dashboard') }}" class="btn btn-outline-secondary">
                    <i class="fa-solid fa-arrow-right"></i> رجوع للوحة التحكم
                </a>
            </div>

            @if(session('success'))
            <div class="alert alert-success" role="alert">{{ session('success') }}</div>
            @endif
            @if(session('error'))
            <div class="alert alert-danger" role="alert">{{ session('error') }}</div>
            @endif

            <div class="settings-card">
                <div class="admin-card-header">
                    <h3><i class="fa-solid fa-credit-card"></i> CCP المرسل (ملف المخالصة)</h3>
                    <p>الحساب البريدي للمرسل يُستخدم كقيمة المستلم (recipient_ccp) عند إنشاء ملف المخالصة. إذا تركت الحقل فارغاً سيُستخدم القيمة الافتراضية.</p>
                </div>
                <form action="{{ route('user.settings.update') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="sender_ccp" class="form-label">CCP المرسل</label>
                        <input type="text" id="sender_ccp" name="sender_ccp" class="form-control" value="{{ old('sender_ccp', $sender_ccp ?? '') }}" placeholder="مثال: 1701517558" maxlength="50" dir="ltr">
                        @error('sender_ccp')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="settings-form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa-solid fa-save"></i> حفظ
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
function confirmLogout() {
    if (confirm('هل تريد تسجيل الخروج؟')) document.getElementById('logout-form').submit();
}
</script>
@endsection
