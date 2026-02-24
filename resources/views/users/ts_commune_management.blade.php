@extends('layouts.main')

@section('title', 'إدارة تقني البلدية')

@vite(['resources/css/dashboard.css'])

@push('styles')
<style>
.ts-mgmt-container { padding: 1.5rem; max-width: 1400px; margin: 0 auto; }
.ts-mgmt-header { margin-bottom: 1.5rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; }
.ts-mgmt-header h2 { color: #0f033a; font-weight: 700; margin: 0; }
.ts-mgmt-header p { color: #6b7280; margin: 0.25rem 0 0 0; }
.wilaya-grid, .commune-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 1rem; }
.wilaya-card, .commune-card {
    background: linear-gradient(135deg, #fff 0%, #f8fafc 100%);
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 1.25rem;
    text-align: center;
    cursor: pointer;
    transition: all 0.25s ease;
    box-shadow: 0 2px 8px rgba(15, 3, 58, 0.06);
}
.wilaya-card:hover, .commune-card:hover { transform: translateY(-4px); box-shadow: 0 8px 24px rgba(15, 3, 58, 0.12); border-color: #c7d2fe; }
.wilaya-card h3, .commune-card .commune-name { font-size: 1.1rem; font-weight: 700; color: #0f033a; margin: 0 0 0.5rem 0; }
.commune-card .commune-name { margin-bottom: 0.75rem; }
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
#communesSection { display: none; }
#communesSection.visible { display: block; }
#wilayasSection.loading .wilaya-grid { opacity: 0.6; pointer-events: none; }
#communesSection.loading .commune-grid { opacity: 0.6; pointer-events: none; }
.no-user-badge { font-size: 0.75rem; color: #94a3b8; margin-top: 0.5rem; }
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
                <li class="sidebar-item active">
                    <a href="{{ route('user.admin.ts_commune.management') }}" class="sidebar-link">
                        <i class="fa-solid fa-building-user"></i>
                        <span>إدارة تقني البلدية</span>
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
            <div class="ts-mgmt-header">
                <div>
                    <h2>إدارة تقني البلدية</h2>
                    <p>اختر الولاية ثم البلدية لفتح لوحة التقني بعرض فقط (بدون تعديل)</p>
                </div>
                <a href="{{ route('user.dashboard') }}" class="btn btn-outline-secondary">
                    <i class="fa-solid fa-arrow-right"></i> رجوع
                </a>
            </div>

            <div id="wilayasSection" class="mb-4">
                <div class="wilaya-grid" id="wilayaGrid">
                    <span style="color:#6b7280;">جار التحميل...</span>
                </div>
            </div>

            <div id="communesSection" class="mb-4">
                <div class="breadcrumb-bar">
                    <a href="#" class="back-link" id="backToWilayas"><i class="fa-solid fa-arrow-right"></i> رجوع للولايات</a>
                    <span id="selectedWilayaTitle" style="color:#6b7280;"></span>
                </div>
                <div class="commune-grid" id="communeGrid"></div>
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
const csrfToken = '{{ csrf_token() }}';

async function loadWilayas() {
    const grid = document.getElementById('wilayaGrid');
    document.getElementById('wilayasSection').classList.add('loading');
    try {
        const r = await fetch(baseUrl + '/user/admin/wilayas', { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
        const data = await r.json();
        document.getElementById('wilayasSection').classList.remove('loading');
        if (!data.success || !data.wilayas) {
            grid.innerHTML = '<span style="color:red;">فشل تحميل الولايات</span>';
            return;
        }
        grid.innerHTML = data.wilayas.map(function(w) {
            return '<div class="wilaya-card" data-code="' + (w.code_wil || '') + '" data-name="' + (w.lib_wil_ar || w.lib_wil_fr || w.code_wil || '').replace(/"/g, '&quot;') + '"><h3>' + (w.lib_wil_ar || w.lib_wil_fr || w.code_wil || '') + '</h3></div>';
        }).join('');
        grid.querySelectorAll('.wilaya-card').forEach(function(card) {
            card.addEventListener('click', function() {
                const code = this.getAttribute('data-code');
                const name = this.getAttribute('data-name');
                loadCommunes(code, name);
            });
        });
    } catch (e) {
        document.getElementById('wilayasSection').classList.remove('loading');
        grid.innerHTML = '<span style="color:red;">خطأ في التحميل</span>';
    }
}

async function loadCommunes(codeWilaya, wilayaName) {
    const section = document.getElementById('communesSection');
    const grid = document.getElementById('communeGrid');
    const titleEl = document.getElementById('selectedWilayaTitle');
    section.classList.add('visible', 'loading');
    titleEl.textContent = 'ولاية: ' + wilayaName;
    grid.innerHTML = '<span style="color:#6b7280;">جار التحميل...</span>';
    try {
        const r = await fetch(baseUrl + '/user/admin/communes?code_wilaya=' + encodeURIComponent(codeWilaya), { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
        const data = await r.json();
        section.classList.remove('loading');
        if (!data.success || !data.communes) {
            grid.innerHTML = '<span style="color:red;">فشل تحميل البلديات</span>';
            return;
        }
        grid.innerHTML = data.communes.map(function(c) {
            return '<div class="commune-card">' +
                '<div class="commune-name">' + (c.lib_comm_ar || c.lib_comm_fr || c.code_comm || '') + '</div>' +
                '<button type="button" class="open-as-btn" data-code-comm="' + (c.code_comm || '') + '" data-code-wilaya="' + (c.code_wilaya || codeWilaya) + '">' +
                '<i class="fa-solid fa-user-secret"></i> فتح كتقني البلدية' +
                '</button>' +
                '</div>';
        }).join('');
        grid.querySelectorAll('.open-as-btn').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                openAsTsCommune(this.getAttribute('data-code-comm'), this.getAttribute('data-code-wilaya'), btn);
            });
        });
    } catch (e) {
        section.classList.remove('loading');
        grid.innerHTML = '<span style="color:red;">خطأ في التحميل</span>';
    }
}

async function openAsTsCommune(codeComm, codeWilaya, btn) {
    btn.disabled = true;
    try {
        const r = await fetch(baseUrl + '/user/admin/impersonate-ts-commune?code_comm=' + encodeURIComponent(codeComm) + '&code_wilaya=' + encodeURIComponent(codeWilaya), { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
        const data = await r.json();
        if (data.success && data.url) {
            window.open(data.url, '_blank', 'noopener,noreferrer');
        } else {
            Swal.fire({ icon: 'warning', title: 'تنبيه', text: data.message || 'لا يوجد مستخدم تقني لهذه البلدية' });
        }
    } catch (e) {
        Swal.fire({ icon: 'error', title: 'خطأ', text: 'فشل إنشاء رابط الدخول' });
    }
    btn.disabled = false;
}

document.getElementById('backToWilayas').addEventListener('click', function(e) {
    e.preventDefault();
    document.getElementById('communesSection').classList.remove('visible');
});

loadWilayas();
</script>
@endsection
