@extends('layouts.main')

@section('title', 'قائمة المستخدمين')

@vite(['resources/css/dashboard.css'])

@push('styles')
<style>
.filters-row {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
    margin-bottom: 1.25rem;
    align-items: flex-end;
}

.filters-row .filter-group {
    display: flex;
    flex-direction: column;
    gap: 0.35rem;
    flex: 1;
    min-width: 200px;
}

.filters-row .filter-group label {
    font-weight: 700;
    font-size: 0.9rem;
    color: #0f033a;
}

.filters-row .filter-group .filter-control {
    padding: 0.62rem 0.75rem;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    font-size: 0.95rem;
    background: #fff;
    color: #111827;
}

.filters-row .filter-group .filter-control:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.18);
}

.filters-row .filter-actions {
    display: flex;
    align-items: flex-end;
}

.swal-users-popup {
    border-radius: 16px !important;
    width: 95vw !important;
    max-width: 1200px !important;
    min-width: 980px !important;
}

.users-modal-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(180px, 1fr));
    gap: 0.75rem;
}

.users-modal-field {
    background: #fff;
    border-radius: 10px;
    border: 1px solid #e5e7eb;
    padding: 0.7rem;
    text-align: right;
}

.users-modal-field label {
    display: block;
    font-weight: 700;
    color: #0f033a;
    font-size: 0.85rem;
    margin-bottom: 0.35rem;
}

.users-modal-input,
.users-modal-select {
    width: 100%;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    padding: 0.55rem 0.65rem;
    background: #fff;
    color: #111827;
    font-size: 0.95rem;
}

.users-modal-value {
    background: #f8fafc;
    border-radius: 8px;
    border: 1px solid #e5e7eb;
    padding: 0.55rem 0.65rem;
    min-height: 38px;
    color: #111827;
    font-size: 0.95rem;
}

@media (max-width: 900px) {
    .swal-users-popup {
        min-width: auto !important;
        width: 96vw !important;
        max-width: 96vw !important;
    }

    .users-modal-grid {
        grid-template-columns: repeat(2, minmax(160px, 1fr));
    }
}

@media (max-width: 600px) {
    .users-modal-grid {
        grid-template-columns: 1fr;
    }
}
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
                <li class="sidebar-item active">
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
                <li class="sidebar-item">
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
        <div class="dashboard-content-wrapper">
            <div class="dashboard-header" style="display:flex;justify-content:space-between;align-items:center;gap:1rem;flex-wrap:wrap;">
                <div>
                    <h2>قائمة المستخدمين</h2>
                    <p>عرض وتعديل مستخدمي النظام من جدول users</p>
                </div>
                <a href="{{ route('user.dashboard') }}" class="btn btn-outline-secondary">
                    <i class="fa-solid fa-arrow-right"></i> رجوع
                </a>
            </div>

            <div class="children-table-section">
                <div class="filters-row">
                    <div class="filter-group search-filter">
                        <label for="usersSearch">بحث</label>
                        <input type="text" id="usersSearch" class="filter-control" placeholder="code_user / الاسم / اللقب">
                    </div>
                    <div class="filter-group">
                        <label for="usersRoleFilter">الرتبة</label>
                        <select id="usersRoleFilter" class="filter-control">
                            <option value="">الكل</option>
                            <option value="ts_commune">ts_commune</option>
                            <option value="das">das</option>
                            <option value="comite_wilaya">comite_wilaya</option>
                            <option value="antr">antr</option>
                            <option value="admin">admin</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="usersWilayaFilter">الولاية</label>
                        <select id="usersWilayaFilter" class="filter-control">
                            <option value="">الكل</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="usersCommuneFilter">البلدية</label>
                        <select id="usersCommuneFilter" class="filter-control">
                            <option value="">الكل</option>
                        </select>
                    </div>
                    <div class="filter-actions">
                        <button id="clearUsersFilters" type="button" style="padding: 0.5rem 1.5rem; background: #6b7280; color: white; border: none; border-radius: 8px; cursor: pointer; font-family: 'Cairo', sans-serif; font-weight: 600; display: none; transition: all 0.3s ease; white-space: nowrap;">
                            <i class="fa-solid fa-times"></i> مسح الفلاتر
                        </button>
                    </div>
                </div>
                <div class="children-table-wrapper">
                    <table class="children-table" id="main-table">
                    <thead id="table-head">
                        <tr>
                            <th>code_user</th>
                            <th>الاسم</th>
                            <th>اللقب</th>
                            <th>الرتبة</th>
                            <th>الولاية</th>
                            <th>البلدية</th>
                            <th>الحالة</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody id="table-body">
                        <tr><td colspan="8" style="text-align:center;">جار التحميل...</td></tr>
                    </tbody>
                </table>
                </div>

                <div id="pagination-container" style="display: flex; justify-content: center; align-items: center; gap: 0.5rem; margin-top: 2rem; padding: 1rem;"></div>
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
        if (result.isConfirmed) {
            document.getElementById('logout-form').submit();
        }
    });
}

document.addEventListener('DOMContentLoaded', () => {
    let API_TOKEN = @json(session('api_token'));
    if (!API_TOKEN && typeof localStorage !== 'undefined') API_TOKEN = localStorage.getItem('api_token');
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    const usersTableBody = document.getElementById('table-body');
    const searchInput = document.getElementById('usersSearch');
    const roleFilter = document.getElementById('usersRoleFilter');
    const wilayaFilter = document.getElementById('usersWilayaFilter');
    const communeFilter = document.getElementById('usersCommuneFilter');
    const clearFilters = document.getElementById('clearUsersFilters');
    const paginationContainer = document.getElementById('pagination-container');

    let currentPage = 1;
    let lastPage = 1;
    let searchTimer = null;

    const updateClearButton = () => {
        if (!clearFilters) return;
        const hasFilters = !!(searchInput.value.trim() || roleFilter.value || wilayaFilter.value || communeFilter.value);
        clearFilters.style.display = hasFilters ? 'block' : 'none';
    };

    const apiHeaders = () => {
        const h = {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        };
        const token = API_TOKEN || (typeof localStorage !== 'undefined' && localStorage.getItem('api_token'));
        if (token) {
            h['Authorization'] = ((typeof localStorage !== 'undefined' && localStorage.getItem('token_type')) || 'Bearer') + ' ' + token;
        }
        return h;
    };
    const getUrl = (path) => (typeof window.getApiUrl === 'function' ? window.getApiUrl(path) : path);

    const loadWilayas = async () => {
        try {
            const response = await fetch(getUrl('/api/wilayas'), { headers: { 'Accept': 'application/json' } });
            const data = await response.json().catch(() => []);
            const items = Array.isArray(data) ? data : (data.data || []);
            wilayaFilter.innerHTML = '<option value="">الكل</option>';
            items.forEach((w) => {
                const op = document.createElement('option');
                op.value = w.code_wil || '';
                op.textContent = w.lib_wil_ar || w.code_wil || 'ولاية';
                wilayaFilter.appendChild(op);
            });
        } catch (_) {}
    };

    const loadCommunes = async (wilayaCode = '') => {
        try {
            if (!wilayaCode) {
                communeFilter.innerHTML = '<option value="">الكل</option>';
                return;
            }
            const response = await fetch(getUrl(`/api/communes/by-wilaya/${encodeURIComponent(wilayaCode)}`), { headers: { 'Accept': 'application/json' } });
            const data = await response.json().catch(() => []);
            const items = Array.isArray(data) ? data : (data.data || []);
            communeFilter.innerHTML = '<option value="">الكل</option>';
            items.forEach((c) => {
                const op = document.createElement('option');
                op.value = c.code_comm || '';
                op.textContent = c.lib_comm_ar || c.code_comm || 'بلدية';
                communeFilter.appendChild(op);
            });
        } catch (_) {}
    };

    const buildQuery = () => {
        const params = new URLSearchParams();
        params.set('page', String(currentPage));
        params.set('per_page', '15');
        if (searchInput.value.trim()) params.set('search', searchInput.value.trim());
        if (roleFilter.value) params.set('role', roleFilter.value);
        if (wilayaFilter.value) params.set('code_wilaya', wilayaFilter.value);
        if (communeFilter.value) params.set('code_comm', communeFilter.value);
        return params.toString();
    };

    const loadUsers = async () => {
        usersTableBody.innerHTML = '<tr><td colspan="8" style="text-align:center;">جار التحميل...</td></tr>';
        try {
            const response = await fetch(getUrl(`/api/admin/users?${buildQuery()}`), {
                headers: apiHeaders(),
                credentials: 'same-origin'
            });
            const data = await response.json().catch(() => ({}));
            if (!response.ok || !data.success) {
                throw new Error(data.message || 'فشل تحميل المستخدمين');
            }

            const users = data.data || [];
            const pagination = data.pagination || {};
            currentPage = pagination.current_page || 1;
            lastPage = pagination.last_page || 1;

            if (!users.length) {
                usersTableBody.innerHTML = '<tr><td colspan="8" style="text-align:center;">لا توجد بيانات</td></tr>';
            } else {
                usersTableBody.innerHTML = users.map((u) => {
                    const name = u.nom_user || '-';
                    const lastName = u.prenom_user || '-';
                    const wil = (u.wilaya && u.wilaya.lib_wil_ar) ? u.wilaya.lib_wil_ar : (u.code_wilaya || '-');
                    const comm = (u.commune && u.commune.lib_comm_ar) ? u.commune.lib_comm_ar : (u.code_comm || '-');
                    return `
                        <tr>
                            <td>${u.code_user || ''}</td>
                            <td>${name}</td>
                            <td>${lastName}</td>
                            <td>${u.role || '-'}</td>
                            <td>${wil}</td>
                            <td>${comm}</td>
                            <td>${(u.statut === 1 || u.statut === '1') ? 'نشط' : 'غير نشط'}</td>
                            <td>
                                <button class="btn btn-sm btn-info" data-action="show" data-id="${u.code_user}" title="عرض التفاصيل" style="background: linear-gradient(135deg, #3b82f6, #2563eb); border: none; padding: 0.4rem 0.6rem; border-radius: 6px; color: white; display: inline-flex; align-items: center; gap: 0.25rem; transition: all 0.3s ease; box-shadow: 0 2px 4px rgba(0,0,0,0.1); white-space: nowrap;">
                                    <i class="fa-solid fa-eye"></i>
                                    <span style="font-size: 0.85rem;">عرض</span>
                                </button>
                                <button class="btn btn-sm btn-warning" data-action="edit" data-id="${u.code_user}" title="تعديل" style="background: linear-gradient(135deg, #f59e0b, #d97706); border: none; padding: 0.4rem 0.6rem; border-radius: 6px; color: white; display: inline-flex; align-items: center; gap: 0.25rem; transition: all 0.3s ease; box-shadow: 0 2px 4px rgba(0,0,0,0.1); white-space: nowrap;">
                                    <i class="fa-solid fa-pen"></i>
                                    <span style="font-size: 0.85rem;">تعديل</span>
                                </button>
                            </td>
                        </tr>
                    `;
                }).join('');
            }

            let paginationHTML = '';
            if (lastPage > 1) {
                paginationHTML = '<div style="display: flex; gap: 0.5rem; align-items: center; flex-wrap: wrap;">';

                if (currentPage > 1) {
                    paginationHTML += `<button onclick="loadUsersPage(${currentPage - 1})" style="padding: 0.5rem 1rem; background: #2563eb; color: white; border: none; border-radius: 6px; cursor: pointer;">◀ السابق</button>`;
                }

                for (let i = 1; i <= lastPage; i++) {
                    if (i === 1 || i === lastPage || (i >= currentPage - 2 && i <= currentPage + 2)) {
                        paginationHTML += `<button onclick="loadUsersPage(${i})" style="padding: 0.5rem 1rem; background: ${i === currentPage ? '#0f033a' : '#e5e7eb'}; color: ${i === currentPage ? 'white' : '#374151'}; border: none; border-radius: 6px; cursor: pointer; font-weight: ${i === currentPage ? '600' : '400'};" ${i === currentPage ? 'disabled' : ''}>${i}</button>`;
                    } else if (i === currentPage - 3 || i === currentPage + 3) {
                        paginationHTML += '<span style="padding: 0.5rem;">...</span>';
                    }
                }

                if (currentPage < lastPage) {
                    paginationHTML += `<button onclick="loadUsersPage(${currentPage + 1})" style="padding: 0.5rem 1rem; background: #2563eb; color: white; border: none; border-radius: 6px; cursor: pointer;">التالي ▶</button>`;
                }

                paginationHTML += '</div>';
            }
            if (paginationContainer) paginationContainer.innerHTML = paginationHTML;
            updateClearButton();
        } catch (error) {
            usersTableBody.innerHTML = '<tr><td colspan="8" style="text-align:center;color:#ef4444;">حدث خطأ أثناء التحميل</td></tr>';
            if (paginationContainer) paginationContainer.innerHTML = '';
        }
    };

    const showUser = async (codeUser) => {
        try {
            const response = await fetch(getUrl(`/api/admin/users/${encodeURIComponent(codeUser)}`), {
                headers: apiHeaders(),
                credentials: 'same-origin'
            });
            const data = await response.json().catch(() => ({}));
            if (!response.ok || !data.success) throw new Error(data.message || 'فشل جلب بيانات المستخدم');
            const u = data.data;
            await Swal.fire({
                title: 'تفاصيل المستخدم',
                width: 1200,
                html: `
                    <div style="text-align:right;">
                        <div style="background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%); padding: 1rem; border-radius: 10px;">
                            <div class="users-modal-grid">
                                <div class="users-modal-field"><label>code_user</label><div class="users-modal-value">${u.code_user || '-'}</div></div>
                                <div class="users-modal-field"><label>الاسم</label><div class="users-modal-value">${u.nom_user || '-'}</div></div>
                                <div class="users-modal-field"><label>اللقب</label><div class="users-modal-value">${u.prenom_user || '-'}</div></div>
                                <div class="users-modal-field"><label>الرتبة</label><div class="users-modal-value">${u.role || '-'}</div></div>
                                <div class="users-modal-field"><label>الولاية</label><div class="users-modal-value">${(u.wilaya && u.wilaya.lib_wil_ar) ? u.wilaya.lib_wil_ar : (u.code_wilaya || '-')}</div></div>
                                <div class="users-modal-field"><label>البلدية</label><div class="users-modal-value">${(u.commune && u.commune.lib_comm_ar) ? u.commune.lib_comm_ar : (u.code_comm || '-')}</div></div>
                                <div class="users-modal-field"><label>الحالة</label><div class="users-modal-value">${(u.statut === 1 || u.statut === '1') ? 'نشط' : 'غير نشط'}</div></div>
                            </div>
                        </div>
                    </div>
                `,
                confirmButtonText: 'إغلاق',
                customClass: { popup: 'swal-users-popup' },
                heightAuto: false
            });
        } catch (error) {
            Swal.fire('خطأ', error.message || 'فشل العرض', 'error');
        }
    };

    const editUser = async (codeUser) => {
        try {
            const response = await fetch(getUrl(`/api/admin/users/${encodeURIComponent(codeUser)}`), {
                headers: apiHeaders(),
                credentials: 'same-origin'
            });
            const data = await response.json().catch(() => ({}));
            if (!response.ok || !data.success) throw new Error(data.message || 'فشل جلب بيانات المستخدم');
            const u = data.data;

            const result = await Swal.fire({
                title: 'تعديل المستخدم',
                width: 1200,
                html: `
                    <div style="text-align:right;">
                        <div style="background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%); padding: 1rem; border-radius: 10px;">
                            <div class="users-modal-grid">
                                <div class="users-modal-field">
                                    <label>code_user</label>
                                    <input id="edit_code_user" class="users-modal-input" value="${u.code_user || ''}" readonly style="background:#f3f4f6;">
                                </div>
                                <div class="users-modal-field">
                                    <label>الاسم</label>
                                    <input id="edit_nom_user" class="users-modal-input" value="${u.nom_user || ''}">
                                </div>
                                <div class="users-modal-field">
                                    <label>اللقب</label>
                                    <input id="edit_prenom_user" class="users-modal-input" value="${u.prenom_user || ''}">
                                </div>
                                <div class="users-modal-field">
                                    <label>كلمة المرور</label>
                                    <div class="password-input-wrapper" style="position:relative; display:flex; align-items:center;">
                                        <input id="edit_pass" type="password" class="users-modal-input" placeholder="اتركه فارغا إذا لا تريد تغييرها" style="padding-inline-end: 2.5rem;">
                                        <button type="button" id="edit_pass_toggle" class="password-toggle" aria-label="إظهار/إخفاء كلمة المرور" style="position:absolute; inset-inline-end:0.5rem; background:none; border:none; padding:0.25rem; cursor:pointer; color:#6b7280;">
                                            <i class="fa-solid fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="users-modal-field">
                                    <label>الرتبة</label>
                                    <select id="edit_role" class="users-modal-select">
                                        <option value="ts_commune" ${u.role === 'ts_commune' ? 'selected' : ''}>ts_commune</option>
                                        <option value="das" ${u.role === 'das' ? 'selected' : ''}>das</option>
                                        <option value="comite_wilaya" ${u.role === 'comite_wilaya' ? 'selected' : ''}>comite_wilaya</option>
                                        <option value="antr" ${u.role === 'antr' ? 'selected' : ''}>antr</option>
                                        <option value="admin" ${u.role === 'admin' ? 'selected' : ''}>admin</option>
                                    </select>
                                </div>
                                <div class="users-modal-field" id="edit_wilaya_field">
                                    <label>الولاية</label>
                                    <select id="edit_code_wilaya" class="users-modal-select">
                                        <option value="">اختر الولاية...</option>
                                    </select>
                                </div>
                                <div class="users-modal-field" id="edit_comm_field">
                                    <label>البلدية</label>
                                    <select id="edit_code_comm" class="users-modal-select">
                                        <option value="">اختر البلدية...</option>
                                    </select>
                                </div>
                                <div class="users-modal-field">
                                    <label>الحالة</label>
                                    <select id="edit_statut" class="users-modal-select">
                                        <option value="1" ${(u.statut === 1 || u.statut === '1') ? 'selected' : ''}>نشط</option>
                                        <option value="0" ${(u.statut !== 1 && u.statut !== '1') ? 'selected' : ''}>غير نشط</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                `,
                focusConfirm: false,
                showCancelButton: true,
                confirmButtonText: 'حفظ',
                cancelButtonText: 'إلغاء',
                customClass: { popup: 'swal-users-popup' },
                heightAuto: false,
                didOpen: async () => {
                    const roleEl = document.getElementById('edit_role');
                    const wilayaEl = document.getElementById('edit_code_wilaya');
                    const commEl = document.getElementById('edit_code_comm');
                    const wilayaField = document.getElementById('edit_wilaya_field');
                    const commField = document.getElementById('edit_comm_field');

                    const passInput = document.getElementById('edit_pass');
                    const passToggle = document.getElementById('edit_pass_toggle');
                    if (passInput && passToggle) {
                        passToggle.addEventListener('click', () => {
                            const icon = passToggle.querySelector('i');
                            if (passInput.type === 'password') {
                                passInput.type = 'text';
                                if (icon) { icon.classList.remove('fa-eye'); icon.classList.add('fa-eye-slash'); }
                            } else {
                                passInput.type = 'password';
                                if (icon) { icon.classList.remove('fa-eye-slash'); icon.classList.add('fa-eye'); }
                            }
                        });
                    }

                    const fillWilayas = async (selectedCode = '') => {
                        try {
                            const response = await fetch(getUrl('/api/wilayas'), { headers: { 'Accept': 'application/json' } });
                            const data = await response.json().catch(() => []);
                            const items = Array.isArray(data) ? data : (data.data || []);
                            wilayaEl.innerHTML = '<option value="">اختر الولاية...</option>';
                            items.forEach((w) => {
                                const option = document.createElement('option');
                                option.value = w.code_wil || '';
                                option.textContent = w.lib_wil_ar || w.code_wil || 'ولاية';
                                if (String(option.value) === String(selectedCode || '')) {
                                    option.selected = true;
                                }
                                wilayaEl.appendChild(option);
                            });
                        } catch (_) {
                            wilayaEl.innerHTML = '<option value="">تعذر تحميل الولايات</option>';
                        }
                    };

                    const fillCommunes = async (wilayaCode = '', selectedComm = '') => {
                        if (!wilayaCode) {
                            commEl.innerHTML = '<option value="">اختر الولاية أولا...</option>';
                            commEl.disabled = true;
                            return;
                        }
                        try {
                            const response = await fetch(getUrl(`/api/communes/by-wilaya/${encodeURIComponent(wilayaCode)}`), { headers: { 'Accept': 'application/json' } });
                            const data = await response.json().catch(() => []);
                            const items = Array.isArray(data) ? data : (data.data || []);
                            commEl.innerHTML = '<option value="">اختر البلدية...</option>';
                            items.forEach((c) => {
                                const option = document.createElement('option');
                                option.value = c.code_comm || '';
                                option.textContent = c.lib_comm_ar || c.code_comm || 'بلدية';
                                if (String(option.value) === String(selectedComm || '')) {
                                    option.selected = true;
                                }
                                commEl.appendChild(option);
                            });
                            commEl.disabled = false;
                        } catch (_) {
                            commEl.innerHTML = '<option value="">تعذر تحميل البلديات</option>';
                            commEl.disabled = true;
                        }
                    };

                    const applyRoleVisibility = () => {
                        const role = roleEl.value;
                        if (role === 'ts_commune') {
                            wilayaField.style.display = '';
                            commField.style.display = '';
                            wilayaEl.disabled = false;
                            commEl.disabled = !wilayaEl.value;
                        } else if (role === 'das' || role === 'comite_wilaya' || role === 'antr') {
                            wilayaField.style.display = '';
                            commField.style.display = 'none';
                            wilayaEl.disabled = false;
                            commEl.value = '';
                        } else {
                            wilayaField.style.display = 'none';
                            commField.style.display = 'none';
                            wilayaEl.value = '';
                            commEl.value = '';
                        }
                    };

                    await fillWilayas(u.code_wilaya || '');
                    await fillCommunes(u.code_wilaya || '', u.code_comm || '');
                    applyRoleVisibility();

                    roleEl.addEventListener('change', async () => {
                        applyRoleVisibility();
                        if (roleEl.value === 'ts_commune' || roleEl.value === 'das' || roleEl.value === 'comite_wilaya' || roleEl.value === 'antr') {
                            if (roleEl.value !== 'ts_commune') {
                                commEl.value = '';
                            }
                            if (wilayaEl.value) {
                                await fillCommunes(wilayaEl.value, '');
                            } else if (roleEl.value === 'ts_commune') {
                                commEl.innerHTML = '<option value="">اختر الولاية أولا...</option>';
                                commEl.disabled = true;
                            }
                        }
                    });

                    wilayaEl.addEventListener('change', async () => {
                        await fillCommunes(wilayaEl.value, '');
                    });
                },
                preConfirm: () => {
                    const selectedRole = document.getElementById('edit_role').value;
                    const selectedWilaya = document.getElementById('edit_code_wilaya').value;
                    const selectedComm = document.getElementById('edit_code_comm').value;

                    if (selectedRole === 'ts_commune' && (!selectedWilaya || !selectedComm)) {
                        Swal.showValidationMessage('يرجى اختيار الولاية والبلدية لرتبة ts_commune');
                        return false;
                    }
                    if ((selectedRole === 'das' || selectedRole === 'comite_wilaya' || selectedRole === 'antr') && !selectedWilaya) {
                        Swal.showValidationMessage('يرجى اختيار الولاية لهذه الرتبة');
                        return false;
                    }

                    const payload = {
                        nom_user: document.getElementById('edit_nom_user').value.trim(),
                        prenom_user: document.getElementById('edit_prenom_user').value.trim(),
                        pass: document.getElementById('edit_pass').value.trim(),
                        role: selectedRole,
                        code_wilaya: selectedWilaya || null,
                        code_comm: selectedRole === 'ts_commune' ? (selectedComm || null) : null,
                        statut: document.getElementById('edit_statut').value.trim() || '1',
                    };
                    return payload;
                }
            });

            if (!result.isConfirmed) return;

            const saveResponse = await fetch(getUrl(`/api/admin/users/${encodeURIComponent(codeUser)}`), {
                method: 'PUT',
                headers: {
                    ...apiHeaders(),
                    'Content-Type': 'application/json'
                },
                credentials: 'same-origin',
                body: JSON.stringify(result.value)
            });
            const saveData = await saveResponse.json().catch(() => ({}));
            if (!saveResponse.ok || !saveData.success) {
                throw new Error(saveData.message || 'فشل التحديث');
            }

            await Swal.fire('تم', saveData.message || 'تم التحديث بنجاح', 'success');
            loadUsers();
        } catch (error) {
            Swal.fire('خطأ', error.message || 'فشل التعديل', 'error');
        }
    };

    usersTableBody.addEventListener('click', (e) => {
        const btn = e.target.closest('button[data-action]');
        if (!btn) return;
        const action = btn.getAttribute('data-action');
        const id = btn.getAttribute('data-id');
        if (!id) return;
        if (action === 'show') showUser(id);
        if (action === 'edit') editUser(id);
    });

    const triggerReload = () => {
        currentPage = 1;
        loadUsers();
    };

    searchInput.addEventListener('input', () => {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(triggerReload, 350);
    });
    roleFilter.addEventListener('change', triggerReload);
    wilayaFilter.addEventListener('change', async () => {
        await loadCommunes(wilayaFilter.value);
        triggerReload();
    });
    communeFilter.addEventListener('change', triggerReload);
    if (clearFilters) {
        clearFilters.addEventListener('click', () => {
            searchInput.value = '';
            roleFilter.value = '';
            wilayaFilter.value = '';
            communeFilter.innerHTML = '<option value="">الكل</option>';
            currentPage = 1;
            loadUsers();
        });
    }

    window.loadUsersPage = function(page) {
        currentPage = page;
        loadUsers();
    };

    loadWilayas();
    loadUsers();
});
</script>
@endsection
