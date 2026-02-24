@extends('layouts.main')

@section('title', 'إدارة ATR')

@vite(['resources/css/dashboard.css'])

@push('styles')
<style>
.ts-mgmt-container { padding: 1.5rem; max-width: 1400px; margin: 0 auto; }
.wilaya-grid, .users-grid {
    display: grid;
    grid-template-columns: repeat(6, 1fr);
    gap: 1rem;
}
@media (max-width: 1200px) {
    .wilaya-grid, .users-grid { grid-template-columns: repeat(4, 1fr); }
}
@media (max-width: 768px) {
    .wilaya-grid, .users-grid { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 480px) {
    .wilaya-grid, .users-grid { grid-template-columns: 1fr; }
}
.wilaya-card, .user-card {
    background: linear-gradient(135deg, #fff 0%, #f8fafc 100%);
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 1.25rem;
    text-align: center;
    cursor: pointer;
    transition: all 0.25s ease;
    box-shadow: 0 2px 8px rgba(15, 3, 58, 0.06);
}
.wilaya-card:hover, .user-card:hover { transform: translateY(-4px); box-shadow: 0 8px 24px rgba(15, 3, 58, 0.12); border-color: #c7d2fe; }
.wilaya-card h3 { font-size: 1.1rem; font-weight: 700; color: #0f033a; margin: 0 0 0.5rem 0; }
.user-card .user-name { font-size: 1.1rem; font-weight: 700; color: #0f033a; margin: 0 0 0.5rem 0; }
.user-card .user-code { font-size: 0.8rem; color: #6b7280; margin-bottom: 0.75rem; }
.open-as-btn {
    display: inline-flex; align-items: center; gap: 0.35rem;
    background: linear-gradient(135deg, #0f033a, #1a0f4a);
    color: white; border: none; padding: 0.5rem 1rem; border-radius: 8px;
    font-size: 0.9rem; font-weight: 600; cursor: pointer; transition: all 0.25s ease;
}
.open-as-btn:hover { opacity: 0.95; transform: scale(1.02); color: white; }
.open-as-btn:disabled { opacity: 0.6; cursor: not-allowed; transform: none; }
.breadcrumb-bar { margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap; }
.breadcrumb-bar .back-link { color: #4f46e5; font-weight: 600; text-decoration: none; }
.breadcrumb-bar .back-link:hover { text-decoration: underline; }
#usersSection { display: none; }
#usersSection.visible { display: block; }
#regionsSection.hidden-section { display: none; }
#regionsSection.loading .wilaya-grid { opacity: 0.6; pointer-events: none; }
#usersSection.loading .users-grid { opacity: 0.6; pointer-events: none; }
</style>
@endpush

@section('content')
<div class="dashboard-container" dir="rtl">
    <aside class="dashboard-sidebar">
        <div class="sidebar-header">
            <h3>القائمة</h3>
        </div>
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
                <li class="sidebar-item active">
                    <a href="{{ route('user.admin.antr.management') }}" class="sidebar-link">
                        <i class="fa-solid fa-sitemap"></i>
                        <span>إدارة ATR</span>
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
        <div class="dashboard-content-wrapper ts-mgmt-container">
            <div class="dashboard-header" style="display:flex;justify-content:space-between;align-items:center;gap:1rem;flex-wrap:wrap;">
                <div>
                    <h2>إدارة ATR (الهيئة الجهوية)</h2>
                    <p>اختر الجهة ثم المستخدم لفتح لوحة ATR بعرض فقط (بدون تعديل)</p>
                </div>
                <a href="{{ route('user.dashboard') }}" class="btn btn-outline-secondary">
                    <i class="fa-solid fa-arrow-right"></i> رجوع
                </a>
            </div>

            <div id="regionsSection" class="mb-4">
                <div class="wilaya-grid" id="regionGrid">
                    <span style="color:#6b7280;">جار التحميل...</span>
                </div>
            </div>

            <div id="usersSection" class="mb-4">
                <div class="breadcrumb-bar">
                    <a href="#" class="back-link" id="backToRegions"><i class="fa-solid fa-arrow-right"></i> رجوع للجهات</a>
                    <span id="selectedRegionTitle" style="color:#6b7280;"></span>
                </div>
                <div class="users-grid" id="usersGrid"></div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function confirmLogout() {
    Swal.fire({
        title: 'تأكيد تسجيل الخروج',
        text: 'هل تريد فعلا تسجيل الخروج؟',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'نعم',
        cancelButtonText: 'إلغاء'
    }).then((result) => {
        if (result.isConfirmed) document.getElementById('logout-form').submit();
    });
}

const baseUrl = '{{ url("/") }}';

async function loadRegions() {
    const grid = document.getElementById('regionGrid');
    document.getElementById('regionsSection').classList.add('loading');
    try {
        const r = await fetch(baseUrl + '/user/admin/antennes', { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
        const data = await r.json();
        document.getElementById('regionsSection').classList.remove('loading');
        if (!data.success || !data.antennes) {
            grid.innerHTML = '<span style="color:red;">فشل تحميل الجهات</span>';
            return;
        }
        grid.innerHTML = data.antennes.map(function(a) {
            var name = (a.lib_ar_ar || a.lib_ar_fr || a.code_ar || '');
            return '<div class="wilaya-card" data-code-ar="' + (a.code_ar || '') + '" data-name="' + (name).replace(/"/g, '&quot;') + '"><h3>' + name + '</h3></div>';
        }).join('');
        grid.querySelectorAll('.wilaya-card').forEach(function(card) {
            card.addEventListener('click', function() {
                loadAntrUsers(this.getAttribute('data-code-ar'), this.getAttribute('data-name'));
            });
        });
    } catch (e) {
        document.getElementById('regionsSection').classList.remove('loading');
        grid.innerHTML = '<span style="color:red;">خطأ في التحميل</span>';
    }
}

async function loadAntrUsers(codeAr, regionName) {
    const section = document.getElementById('usersSection');
    const regionsSection = document.getElementById('regionsSection');
    const grid = document.getElementById('usersGrid');
    const titleEl = document.getElementById('selectedRegionTitle');
    regionsSection.classList.add('hidden-section');
    section.classList.add('visible', 'loading');
    titleEl.textContent = 'الجهة: ' + regionName;
    grid.innerHTML = '<span style="color:#6b7280;">جار التحميل...</span>';
    try {
        const r = await fetch(baseUrl + '/user/admin/antr-users?code_ar=' + encodeURIComponent(codeAr), { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
        const data = await r.json();
        section.classList.remove('loading');
        if (!data.success || !data.users) {
            grid.innerHTML = '<span style="color:red;">فشل تحميل المستخدمين</span>';
            return;
        }
        if (data.users.length === 0) {
            grid.innerHTML = '<span style="color:#6b7280;">لا يوجد مستخدمون ATR لهذه الجهة</span>';
            return;
        }
        grid.innerHTML = data.users.map(function(u) {
            var fullName = ((u.nom_user || '') + ' ' + (u.prenom_user || '')).trim() || '—';
            return '<div class="user-card">' +
                '<div class="user-name">' + fullName + '</div>' +
                '<div class="user-code">' + (u.code_user || '') + '</div>' +
                '<button type="button" class="open-as-btn" data-code-user="' + (u.code_user || '') + '" data-code-wilaya="' + (u.code_wilaya || '') + '">' +
                '<i class="fa-solid fa-user-secret"></i> فتح كـ ATR' +
                '</button>' +
                '</div>';
        }).join('');
        grid.querySelectorAll('.user-card .open-as-btn').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                openAsAntr(btn.getAttribute('data-code-wilaya'), btn.getAttribute('data-code-user'), btn);
            });
        });
    } catch (e) {
        section.classList.remove('loading');
        grid.innerHTML = '<span style="color:red;">خطأ في التحميل</span>';
    }
}

async function openAsAntr(codeWilaya, codeUser, btn) {
    if (btn) btn.disabled = true;
    var url = baseUrl + '/user/admin/impersonate-antr?code_wilaya=' + encodeURIComponent(codeWilaya) + '&code_user=' + encodeURIComponent(codeUser);
    try {
        const r = await fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
        const data = await r.json();
        if (data.success && data.url) {
            window.location.href = data.url;
        } else {
            Swal.fire({ icon: 'warning', title: 'تنبيه', text: data.message || 'فشل إنشاء رابط الدخول' });
        }
    } catch (e) {
        Swal.fire({ icon: 'error', title: 'خطأ', text: 'فشل إنشاء رابط الدخول' });
    }
    if (btn) btn.disabled = false;
}

document.getElementById('backToRegions').addEventListener('click', function(e) {
    e.preventDefault();
    document.getElementById('usersSection').classList.remove('visible');
    document.getElementById('regionsSection').classList.remove('hidden-section');
});

loadRegions();
</script>
@endsection
