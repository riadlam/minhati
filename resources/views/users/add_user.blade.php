@extends('layouts.main')

@section('title', 'إضافة مستخدم جديد')

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

.admin-create-user-card {
    background: #fff; border-radius: 16px; padding: 1.25rem;
    border: 1px solid #e5e7eb; box-shadow: 0 8px 28px rgba(15, 3, 58, 0.08);
}
.admin-card-header h3 { color: #0f033a; margin-bottom: 0.4rem; font-weight: 700; }
.admin-card-header p { color: #4b5563; margin-bottom: 1rem; }
.admin-form-grid {
    display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 0.9rem;
}
.admin-form-actions { margin-top: 1rem; display: flex; justify-content: flex-start; }
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
                <li class="sidebar-item active">
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
                <li class="sidebar-item">
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
                    <h2>إضافة مستخدم جديد</h2>
                    <p>إنشاء مستخدم جديد في النظام (تقني بلدية، DAS، لجنة ولاية، ATR، أو مدير)</p>
                </div>
                <a href="{{ route('user.dashboard') }}" class="btn btn-outline-secondary">
                    <i class="fa-solid fa-arrow-right"></i> رجوع للوحة التحكم
                </a>
            </div>

            <div class="admin-create-user-card">
                <div class="admin-card-header">
                    <h3><i class="fa-solid fa-user-plus"></i> إنشاء مستخدم جديد</h3>
                    <p>أدخل البيانات الأساسية، ثم اختر الرتبة. ستظهر الولاية/البلدية تلقائيا حسب نوع الرتبة.</p>
                </div>
                <form id="adminCreateUserForm" class="admin-create-user-form">
                    @csrf
                    <div class="admin-form-grid">
                        <div>
                            <label class="form-label fw-bold required">رقم المستخدم (18 رقم)</label>
                            <input type="text" name="code_user" class="form-control" maxlength="18" minlength="18" pattern="\d{18}" required autocomplete="off" inputmode="numeric">
                        </div>
                        <div>
                            <label class="form-label fw-bold">الاسم (اللقب)</label>
                            <input type="text" name="nom_user" class="form-control" maxlength="50" autocomplete="off" placeholder="اختياري">
                        </div>
                        <div>
                            <label class="form-label fw-bold">الاسم الشخصي</label>
                            <input type="text" name="prenom_user" class="form-control" maxlength="50" autocomplete="off" placeholder="اختياري">
                        </div>
                        <div>
                            <label class="form-label fw-bold required">كلمة المرور</label>
                            <input type="password" name="pass" class="form-control" minlength="6" required autocomplete="new-password">
                        </div>
                        <div>
                            <label class="form-label fw-bold required">الرتبة</label>
                            <select name="role" id="adminRoleSelect" class="form-select" required>
                                <option value="ts_commune" selected>ts_commune</option>
                                <option value="das">das</option>
                                <option value="comite_wilaya">comite_wilaya</option>
                                <option value="antr">antr</option>
                                <option value="admin">admin</option>
                            </select>
                        </div>
                        <div id="adminWilayaWrapper">
                            <label class="form-label fw-bold required" for="adminCodeWilaya">الولاية</label>
                            <select name="code_wilaya" id="adminCodeWilaya" class="form-select">
                                <option value="">اختر الولاية...</option>
                            </select>
                        </div>
                        <div id="adminCommuneWrapper">
                            <label class="form-label fw-bold required" for="adminCodeComm">البلدية</label>
                            <select name="code_comm" id="adminCodeComm" class="form-select" disabled>
                                <option value="">اختر الولاية أولا...</option>
                            </select>
                        </div>
                    </div>
                    <div class="admin-form-actions">
                        <button type="submit" class="btn btn-warning-custom px-4">
                            <i class="fa-solid fa-floppy-disk me-1"></i> إنشاء المستخدم
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

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
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) document.getElementById('logout-form').submit();
    });
}

document.addEventListener('DOMContentLoaded', () => {
    const adminCreateUserForm = document.getElementById('adminCreateUserForm');
    if (!adminCreateUserForm) return;

    let API_TOKEN = @json(session('api_token'));
    if (!API_TOKEN && typeof localStorage !== 'undefined') API_TOKEN = localStorage.getItem('api_token');
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    const getUrl = (path) => (typeof window.getApiUrl === 'function' ? window.getApiUrl(path) : path);
    const roleSelect = document.getElementById('adminRoleSelect');
    const wilayaWrapper = document.getElementById('adminWilayaWrapper');
    const communeWrapper = document.getElementById('adminCommuneWrapper');
    const codeWilayaSelect = document.getElementById('adminCodeWilaya');
    const codeCommSelect = document.getElementById('adminCodeComm');

    const setRoleVisibility = () => {
        const role = roleSelect ? roleSelect.value : '';
        if (role === 'ts_commune') {
            if (wilayaWrapper) wilayaWrapper.style.display = 'block';
            if (communeWrapper) communeWrapper.style.display = 'block';
            if (codeWilayaSelect) codeWilayaSelect.required = true;
            if (codeCommSelect) codeCommSelect.required = true;
        } else if (role === 'das' || role === 'comite_wilaya' || role === 'antr') {
            if (wilayaWrapper) wilayaWrapper.style.display = 'block';
            if (communeWrapper) communeWrapper.style.display = 'none';
            if (codeWilayaSelect) codeWilayaSelect.required = true;
            if (codeCommSelect) { codeCommSelect.required = false; codeCommSelect.value = ''; }
        } else {
            if (wilayaWrapper) wilayaWrapper.style.display = 'none';
            if (communeWrapper) communeWrapper.style.display = 'none';
            if (codeWilayaSelect) { codeWilayaSelect.required = false; codeWilayaSelect.value = ''; }
            if (codeCommSelect) { codeCommSelect.required = false; codeCommSelect.value = ''; }
        }
    };

    const loadWilayas = async () => {
        if (!codeWilayaSelect) return;
        try {
            const response = await fetch(getUrl('/api/wilayas'), { method: 'GET', headers: { 'Accept': 'application/json' } });
            const data = await response.json().catch(() => []);
            const wilayas = Array.isArray(data) ? data : (data.data || []);
            codeWilayaSelect.innerHTML = '<option value="">اختر الولاية...</option>';
            wilayas.forEach((w) => {
                const option = document.createElement('option');
                option.value = w.code_wil || w.id || '';
                option.textContent = w.lib_wil_ar || w.nom_wilaya || w.code_wil || 'ولاية';
                codeWilayaSelect.appendChild(option);
            });
        } catch (_) { codeWilayaSelect.innerHTML = '<option value="">تعذر تحميل الولايات</option>'; }
    };

    const loadCommunesByWilaya = async (wilayaCode) => {
        if (!codeCommSelect) return;
        if (!wilayaCode) {
            codeCommSelect.innerHTML = '<option value="">اختر الولاية أولا...</option>';
            codeCommSelect.disabled = true;
            return;
        }
        try {
            const response = await fetch(getUrl(`/api/communes/by-wilaya/${encodeURIComponent(wilayaCode)}`), { method: 'GET', headers: { 'Accept': 'application/json' } });
            const data = await response.json().catch(() => []);
            const communes = Array.isArray(data) ? data : (data.data || []);
            codeCommSelect.innerHTML = '<option value="">اختر البلدية...</option>';
            communes.forEach((c) => {
                const option = document.createElement('option');
                option.value = c.code_comm || c.id || '';
                option.textContent = c.lib_comm_ar || c.nom_commune || c.code_comm || 'بلدية';
                codeCommSelect.appendChild(option);
            });
            codeCommSelect.disabled = false;
        } catch (_) { codeCommSelect.innerHTML = '<option value="">تعذر تحميل البلديات</option>'; codeCommSelect.disabled = true; }
    };

    const checkCodeUserExists = async (codeUser) => {
        try {
            const response = await fetch(getUrl(`/api/users/${encodeURIComponent(codeUser)}`), { method: 'GET', headers: { 'Accept': 'application/json' } });
            return response.ok;
        } catch (_) { return false; }
    };

    if (roleSelect) roleSelect.addEventListener('change', () => { setRoleVisibility(); if (codeWilayaSelect && codeWilayaSelect.value) loadCommunesByWilaya(codeWilayaSelect.value); });
    if (codeWilayaSelect) codeWilayaSelect.addEventListener('change', () => loadCommunesByWilaya(codeWilayaSelect.value));

    loadWilayas();
    setRoleVisibility();
    if (codeCommSelect) { codeCommSelect.innerHTML = '<option value="">اختر الولاية أولا...</option>'; codeCommSelect.disabled = true; }

    adminCreateUserForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(adminCreateUserForm);
        const payload = Object.fromEntries(formData.entries());
        payload.code_user = (payload.code_user || '').trim();
        const roleValue = payload.role || '';
        if (roleValue === 'ts_commune') {
            if (!payload.code_wilaya || !payload.code_comm) { Swal.fire({ icon: 'warning', title: 'تنبيه', text: 'يرجى اختيار الولاية والبلدية.' }); return; }
        } else if (roleValue === 'das' || roleValue === 'comite_wilaya' || roleValue === 'antr') {
            if (!payload.code_wilaya) { Swal.fire({ icon: 'warning', title: 'تنبيه', text: 'يرجى اختيار الولاية.' }); return; }
            payload.code_comm = null;
        } else { payload.code_comm = null; payload.code_wilaya = null; }
        if (!/^\d{18}$/.test(payload.code_user || '')) { Swal.fire({ icon: 'warning', title: 'تنبيه', text: 'رقم المستخدم يجب أن يكون 18 رقمًا.' }); return; }
        const exists = await checkCodeUserExists(payload.code_user);
        if (exists) { Swal.fire({ icon: 'error', title: 'موجود مسبقًا', text: 'رقم المستخدم موجود مسبقًا، يرجى إدخال رقم آخر.', confirmButtonText: 'حسنًا' }); return; }
        try {
            Swal.fire({ title: 'جارٍ إنشاء المستخدم...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
            const headers = { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken };
            if (API_TOKEN) headers['Authorization'] = `Bearer ${API_TOKEN}`;
            const response = await fetch(getUrl('/api/admin/users'), { method: 'POST', headers, credentials: 'same-origin', body: JSON.stringify(payload) });
            const data = await response.json().catch(() => ({}));
            if (!response.ok) {
                let message = data.message || 'فشل إنشاء المستخدم';
                if (data.errors) { const firstError = Object.values(data.errors)[0]; if (Array.isArray(firstError) && firstError.length > 0) message = firstError[0]; }
                throw new Error(message);
            }
            await Swal.fire({ icon: 'success', title: 'تم بنجاح', text: data.message || 'تم إنشاء المستخدم بنجاح', confirmButtonText: 'حسنًا' });
            adminCreateUserForm.reset();
            if (roleSelect) roleSelect.value = 'ts_commune';
            setRoleVisibility();
            if (codeCommSelect) { codeCommSelect.innerHTML = '<option value="">اختر الولاية أولا...</option>'; codeCommSelect.disabled = true; }
        } catch (error) {
            Swal.fire({ icon: 'error', title: 'خطأ', text: error.message || 'حدث خطأ أثناء إنشاء المستخدم', confirmButtonText: 'حسنًا' });
        }
    });
});
</script>
@endsection
