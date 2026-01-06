@extends('layouts.main')

@section('title', 'لوحة التحكم - المستخدم')

@vite(['resources/css/dashboard.css'])

@push('styles')
<style>
.badge {
    padding: 6px 12px;
    border-radius: 4px;
    font-size: 0.875rem;
    font-weight: 500;
}
.bg-success {
    background-color: #10b981 !important;
    color: white;
}
.bg-warning {
    background-color: #f59e0b !important;
    color: white;
}
.bg-secondary {
    background-color: #6b7280 !important;
    color: white;
}
</style>
@endpush

@section('content')
<div class="dashboard-container" dir="rtl">
    <!-- Sidebar -->
    <aside class="dashboard-sidebar">
        <div class="sidebar-header">
            <h3>القائمة</h3>
        </div>
        <nav class="sidebar-nav">
            <ul class="sidebar-menu">
                <li class="sidebar-item active">
                    <a href="{{ route('user.dashboard') }}" class="sidebar-link">
                        <i class="fa-solid fa-home"></i>
                        <span>الرئيسية</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="{{ route('user.tuteurs.list') }}" class="sidebar-link">
                        <i class="fa-solid fa-users"></i>
                        <span>الأوصياء والأولياء</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="{{ route('user.students.list') }}" class="sidebar-link">
                        <i class="fa-solid fa-user-graduate"></i>
                        <span>التلاميذ</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="#" class="sidebar-link">
                        <i class="fa-solid fa-file-check"></i>
                        <span>الطلبات المعلقة</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="#" class="sidebar-link">
                        <i class="fa-solid fa-file-circle-check"></i>
                        <span>الطلبات المعتمدة</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="#" class="sidebar-link">
                        <i class="fa-solid fa-chart-bar"></i>
                        <span>الإحصائيات</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="#" class="sidebar-link">
                        <i class="fa-solid fa-gear"></i>
                        <span>الإعدادات</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="#" class="sidebar-link">
                        <i class="fa-solid fa-book"></i>
                        <span>الدليل</span>
                    </a>
                </li>
            </ul>
        </nav>
        <div class="sidebar-footer">
            <form id="logout-form" action="{{ route('user.logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
            <button class="sidebar-logout-btn" onclick="confirmLogout()">
                <i class="fa-solid fa-right-from-bracket"></i>
                <span>تسجيل الخروج</span>
            </button>
    </div>
    </aside>

    <div class="dashboard-main-content">
        <!-- Main Content Wrapper -->
        <div class="dashboard-content-wrapper">
    <!-- Welcome header -->
    <div class="dashboard-header">
        <h2 id="user-name">مرحباً، {{ session('user_name') ?? 'المستخدم' }}</h2>
        <p id="user-role">الوظيفة: {{ session('user_role') ?? '-' }}</p>
        <p class="dashboard-header-commune" id="user-commune">بلدية: {{ session('user_commune') ?? 'غير محددة' }}</p>
    </div>

    <!-- Action Cards Section -->
    <div class="dashboard-actions-grid">
        <a href="{{ route('user.tuteurs.list') }}" class="dashboard-action-card">
            <div class="action-card-icon primary">
                <i class="fa-solid fa-users"></i>
            </div>
            <div class="action-card-content">
                <h3>الأوصياء والأولياء</h3>
                <p>عرض وإدارة جميع الأوصياء والأولياء المسجلين</p>
            </div>
            <div class="action-card-arrow">
                <i class="fa-solid fa-chevron-left"></i>
            </div>
        </a>

        <a href="{{ route('user.students.list') }}" class="dashboard-action-card">
            <div class="action-card-icon info">
                <i class="fa-solid fa-user-graduate"></i>
            </div>
            <div class="action-card-content">
                <h3>التلاميذ</h3>
                <p>عرض وإدارة جميع التلاميذ المسجلين</p>
            </div>
            <div class="action-card-arrow">
                <i class="fa-solid fa-chevron-left"></i>
            </div>
        </a>

        <a href="#" class="dashboard-action-card">
            <div class="action-card-icon warning">
                <i class="fa-solid fa-clock"></i>
            </div>
            <div class="action-card-content">
                <h3>الطلبات المعلقة</h3>
                <p>مراجعة الطلبات التي في انتظار الموافقة</p>
            </div>
            <div class="action-card-arrow">
                <i class="fa-solid fa-chevron-left"></i>
            </div>
        </a>

        <a href="#" class="dashboard-action-card">
            <div class="action-card-icon success">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div class="action-card-content">
                <h3>الطلبات المعتمدة</h3>
                <p>عرض جميع الطلبات التي تمت الموافقة عليها</p>
            </div>
            <div class="action-card-arrow">
                <i class="fa-solid fa-chevron-left"></i>
            </div>
        </a>

        <a href="#" class="dashboard-action-card">
            <div class="action-card-icon stats">
                <i class="fa-solid fa-chart-bar"></i>
            </div>
            <div class="action-card-content">
                <h3>الإحصائيات</h3>
                <p>عرض إحصائيات مفصلة عن المنصة</p>
            </div>
            <div class="action-card-arrow">
                <i class="fa-solid fa-chevron-left"></i>
            </div>
        </a>

        <a href="#" class="dashboard-action-card">
            <div class="action-card-icon settings">
                <i class="fa-solid fa-gear"></i>
            </div>
            <div class="action-card-content">
                <h3>الإعدادات</h3>
                <p>تعديل إعدادات الحساب والنظام</p>
            </div>
            <div class="action-card-arrow">
                <i class="fa-solid fa-chevron-left"></i>
            </div>
        </a>

        <a href="#" class="dashboard-action-card">
            <div class="action-card-icon guide">
                <i class="fa-solid fa-book"></i>
            </div>
            <div class="action-card-content">
                <h3>دليل الاستخدام</h3>
                <p>تعليمات وإرشادات استخدام المنصة</p>
            </div>
            <div class="action-card-arrow">
                <i class="fa-solid fa-chevron-left"></i>
            </div>
        </a>

        <a href="#" class="dashboard-action-card">
            <div class="action-card-icon export">
                <i class="fa-solid fa-file-export"></i>
            </div>
            <div class="action-card-content">
                <h3>تصدير البيانات</h3>
                <p>تصدير التقارير والبيانات بصيغة CSV/Excel</p>
            </div>
            <div class="action-card-arrow">
                <i class="fa-solid fa-chevron-left"></i>
            </div>
        </a>
    </div>
        </div>
    </div>
</div>


<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
function confirmLogout() {
    Swal.fire({
        title: 'تأكيد تسجيل الخروج',
        text: "هل تريد فعلاً تسجيل الخروج؟",
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'نعم، تسجيل الخروج',
        cancelButtonText: 'إلغاء',
        reverseButtons: true,
        customClass: {
            popup: 'logout-popup',
            title: 'logout-title',
            confirmButton: 'swal-confirm-btn',
            cancelButton: 'swal-cancel-btn'
        },
        buttonsStyling: false
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('logout-form').submit();
        }
    });
}
</script>

@endsection
