@extends('layouts.main')

@section('title', 'قائمة المستخدمين')

@vite(['resources/css/dashboard.css'])

@push('styles')
<style>
.users-toolbar {
    background: #fff;
    border-radius: 12px;
    border: 1px solid #e5e7eb;
    padding: 1rem;
    margin-bottom: 1rem;
}

.users-filters-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 0.75rem;
}

.users-table-wrap {
    background: #fff;
    border-radius: 12px;
    border: 1px solid #e5e7eb;
    overflow-x: auto;
}

.users-table {
    width: 100%;
    border-collapse: collapse;
}

.users-table th,
.users-table td {
    padding: 0.75rem;
    border-bottom: 1px solid #f1f5f9;
    text-align: right;
    white-space: nowrap;
}

.users-table thead th {
    background: #f8fafc;
    color: #0f033a;
    font-weight: 700;
}

.users-pagination {
    margin-top: 1rem;
    display: flex;
    gap: 0.5rem;
    justify-content: center;
    align-items: center;
}

.users-pagination button {
    border: 1px solid #cbd5e1;
    background: #fff;
    padding: 0.4rem 0.8rem;
    border-radius: 8px;
}

.users-pagination button:disabled {
    opacity: 0.5;
    cursor: not-allowed;
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

            <div class="users-toolbar">
                <div class="users-filters-grid">
                    <div>
                        <label class="form-label fw-bold">بحث</label>
                        <input type="text" id="usersSearch" class="form-control" placeholder="code_user / الاسم / اللقب">
                    </div>
                    <div>
                        <label class="form-label fw-bold">الرتبة</label>
                        <select id="usersRoleFilter" class="form-select">
                            <option value="">الكل</option>
                            <option value="ts_commune">ts_commune</option>
                            <option value="das">das</option>
                            <option value="comite_wilaya">comite_wilaya</option>
                            <option value="anten">anten</option>
                            <option value="admin">admin</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label fw-bold">الولاية</label>
                        <select id="usersWilayaFilter" class="form-select">
                            <option value="">الكل</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label fw-bold">البلدية</label>
                        <select id="usersCommuneFilter" class="form-select">
                            <option value="">الكل</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="users-table-wrap">
                <table class="users-table">
                    <thead>
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
                    <tbody id="usersTableBody">
                        <tr><td colspan="8" style="text-align:center;">جار التحميل...</td></tr>
                    </tbody>
                </table>
            </div>

            <div class="users-pagination">
                <button id="usersPrevBtn" type="button">السابق</button>
                <span id="usersPageInfo">صفحة 1</span>
                <button id="usersNextBtn" type="button">التالي</button>
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
    const API_TOKEN = @json(session('api_token'));
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    const usersTableBody = document.getElementById('usersTableBody');
    const searchInput = document.getElementById('usersSearch');
    const roleFilter = document.getElementById('usersRoleFilter');
    const wilayaFilter = document.getElementById('usersWilayaFilter');
    const communeFilter = document.getElementById('usersCommuneFilter');
    const prevBtn = document.getElementById('usersPrevBtn');
    const nextBtn = document.getElementById('usersNextBtn');
    const pageInfo = document.getElementById('usersPageInfo');

    let currentPage = 1;
    let lastPage = 1;
    let searchTimer = null;

    const apiHeaders = () => {
        const h = {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        };
        if (API_TOKEN) {
            h['Authorization'] = `Bearer ${API_TOKEN}`;
        }
        return h;
    };

    const loadWilayas = async () => {
        try {
            const response = await fetch('/api/wilayas', { headers: { 'Accept': 'application/json' } });
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
            const response = await fetch(`/api/communes/by-wilaya/${encodeURIComponent(wilayaCode)}`, { headers: { 'Accept': 'application/json' } });
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
            const response = await fetch(`/api/admin/users?${buildQuery()}`, {
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
                            <td>${u.statut || '-'}</td>
                            <td>
                                <button class="btn btn-sm btn-info" data-action="show" data-id="${u.code_user}">عرض</button>
                                <button class="btn btn-sm btn-warning" data-action="edit" data-id="${u.code_user}">تعديل</button>
                            </td>
                        </tr>
                    `;
                }).join('');
            }

            pageInfo.textContent = `صفحة ${currentPage} من ${lastPage}`;
            prevBtn.disabled = currentPage <= 1;
            nextBtn.disabled = currentPage >= lastPage;
        } catch (error) {
            usersTableBody.innerHTML = '<tr><td colspan="8" style="text-align:center;color:#ef4444;">حدث خطأ أثناء التحميل</td></tr>';
        }
    };

    const showUser = async (codeUser) => {
        try {
            const response = await fetch(`/api/admin/users/${encodeURIComponent(codeUser)}`, {
                headers: apiHeaders(),
                credentials: 'same-origin'
            });
            const data = await response.json().catch(() => ({}));
            if (!response.ok || !data.success) throw new Error(data.message || 'فشل جلب بيانات المستخدم');
            const u = data.data;
            await Swal.fire({
                title: 'تفاصيل المستخدم',
                html: `
                    <div style="text-align:right;line-height:1.9">
                        <div><strong>code_user:</strong> ${u.code_user || '-'}</div>
                        <div><strong>الاسم:</strong> ${u.nom_user || '-'}</div>
                        <div><strong>اللقب:</strong> ${u.prenom_user || '-'}</div>
                        <div><strong>الرتبة:</strong> ${u.role || '-'}</div>
                        <div><strong>الولاية:</strong> ${(u.wilaya && u.wilaya.lib_wil_ar) ? u.wilaya.lib_wil_ar : (u.code_wilaya || '-')}</div>
                        <div><strong>البلدية:</strong> ${(u.commune && u.commune.lib_comm_ar) ? u.commune.lib_comm_ar : (u.code_comm || '-')}</div>
                        <div><strong>الحالة:</strong> ${u.statut || '-'}</div>
                    </div>
                `,
                confirmButtonText: 'إغلاق'
            });
        } catch (error) {
            Swal.fire('خطأ', error.message || 'فشل العرض', 'error');
        }
    };

    const editUser = async (codeUser) => {
        try {
            const response = await fetch(`/api/admin/users/${encodeURIComponent(codeUser)}`, {
                headers: apiHeaders(),
                credentials: 'same-origin'
            });
            const data = await response.json().catch(() => ({}));
            if (!response.ok || !data.success) throw new Error(data.message || 'فشل جلب بيانات المستخدم');
            const u = data.data;

            const result = await Swal.fire({
                title: 'تعديل المستخدم',
                html: `
                    <div style="text-align:right;display:grid;gap:0.6rem">
                        <label>code_user</label>
                        <input id="edit_code_user" class="swal2-input" value="${u.code_user || ''}" readonly>
                        <label>الاسم</label>
                        <input id="edit_nom_user" class="swal2-input" value="${u.nom_user || ''}">
                        <label>اللقب</label>
                        <input id="edit_prenom_user" class="swal2-input" value="${u.prenom_user || ''}">
                        <label>كلمة المرور (اختياري)</label>
                        <input id="edit_pass" type="password" class="swal2-input" placeholder="اتركه فارغا إذا لا تريد تغييرها">
                        <label>الرتبة</label>
                        <select id="edit_role" class="swal2-input">
                            <option value="ts_commune" ${u.role === 'ts_commune' ? 'selected' : ''}>ts_commune</option>
                            <option value="das" ${u.role === 'das' ? 'selected' : ''}>das</option>
                            <option value="comite_wilaya" ${u.role === 'comite_wilaya' ? 'selected' : ''}>comite_wilaya</option>
                            <option value="anten" ${u.role === 'anten' ? 'selected' : ''}>anten</option>
                            <option value="admin" ${u.role === 'admin' ? 'selected' : ''}>admin</option>
                        </select>
                        <label>code_wilaya</label>
                        <input id="edit_code_wilaya" class="swal2-input" value="${u.code_wilaya || ''}">
                        <label>code_comm</label>
                        <input id="edit_code_comm" class="swal2-input" value="${u.code_comm || ''}">
                        <label>الحالة (1/0)</label>
                        <input id="edit_statut" class="swal2-input" value="${u.statut || '1'}">
                    </div>
                `,
                focusConfirm: false,
                showCancelButton: true,
                confirmButtonText: 'حفظ',
                cancelButtonText: 'إلغاء',
                preConfirm: () => {
                    const payload = {
                        nom_user: document.getElementById('edit_nom_user').value.trim(),
                        prenom_user: document.getElementById('edit_prenom_user').value.trim(),
                        pass: document.getElementById('edit_pass').value.trim(),
                        role: document.getElementById('edit_role').value,
                        code_wilaya: document.getElementById('edit_code_wilaya').value.trim() || null,
                        code_comm: document.getElementById('edit_code_comm').value.trim() || null,
                        statut: document.getElementById('edit_statut').value.trim() || '1',
                    };
                    return payload;
                }
            });

            if (!result.isConfirmed) return;

            const saveResponse = await fetch(`/api/admin/users/${encodeURIComponent(codeUser)}`, {
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

    prevBtn.addEventListener('click', () => {
        if (currentPage > 1) {
            currentPage -= 1;
            loadUsers();
        }
    });
    nextBtn.addEventListener('click', () => {
        if (currentPage < lastPage) {
            currentPage += 1;
            loadUsers();
        }
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

    loadWilayas();
    loadUsers();
});
</script>
@endsection
