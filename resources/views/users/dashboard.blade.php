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

/* === Comment Modal Styles === */
.swal2-popup.swal-comment-modal {
    border-radius: 16px !important;
    max-width: 700px !important;
    padding: 0 !important;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3) !important;
    overflow: hidden;
}

.swal-comment-title {
    background: linear-gradient(135deg, #0f033a 0%, #1a0f4a 100%);
    color: white !important;
    padding: 1.5rem 2rem;
    margin: 0 !important;
    font-size: 1.5rem;
    font-weight: 700;
    text-align: center;
    border-radius: 16px 16px 0 0;
    border-bottom: 3px solid #fdae4b;
}

.swal-comment-content {
    padding: 2rem !important;
    margin: 0 !important;
    text-align: right;
    background: white;
}

.swal-comment-content::-webkit-scrollbar {
    width: 10px;
}

.swal-comment-content::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 8px;
}

.swal-comment-content::-webkit-scrollbar-thumb {
    background: linear-gradient(135deg, #2563eb, #3b82f6);
    border-radius: 8px;
}

.swal-comment-content::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(135deg, #1d4ed8, #2563eb);
}

.swal-comment-confirm {
    background: linear-gradient(135deg, #2563eb, #1d4ed8) !important;
    border: none !important;
    border-radius: 8px !important;
    padding: 0.75rem 2rem !important;
    font-weight: 600 !important;
    font-size: 1rem !important;
    box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3) !important;
    transition: all 0.3s ease !important;
}

.swal-comment-confirm:hover {
    transform: translateY(-2px) !important;
    box-shadow: 0 6px 20px rgba(37, 99, 235, 0.4) !important;
}

.swal-comment-cancel {
    background: linear-gradient(135deg, #6b7280, #4b5563) !important;
    border: none !important;
    border-radius: 8px !important;
    padding: 0.75rem 2rem !important;
    font-weight: 600 !important;
    font-size: 1rem !important;
    box-shadow: 0 4px 12px rgba(107, 114, 128, 0.3) !important;
    transition: all 0.3s ease !important;
}

.swal-comment-cancel:hover {
    transform: translateY(-2px) !important;
    box-shadow: 0 6px 20px rgba(107, 114, 128, 0.4) !important;
}

.comments-container::-webkit-scrollbar {
    width: 8px;
}

.comments-container::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 8px;
}

.comments-container::-webkit-scrollbar-thumb {
    background: linear-gradient(135deg, #2563eb, #3b82f6);
    border-radius: 8px;
}

.comments-container::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(135deg, #1d4ed8, #2563eb);
}

.comment-card:hover {
    transform: translateX(-5px);
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12) !important;
}

#commentText:focus {
    outline: none;
    border-color: #2563eb !important;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1) !important;
}

.admin-create-user-card {
    background: #fff;
    border-radius: 16px;
    padding: 1.25rem;
    border: 1px solid #e5e7eb;
    box-shadow: 0 8px 28px rgba(15, 3, 58, 0.08);
}

.admin-card-header h3 {
    color: #0f033a;
    margin-bottom: 0.4rem;
    font-weight: 700;
}

.admin-card-header p {
    color: #4b5563;
    margin-bottom: 1rem;
}

.admin-form-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 0.9rem;
}

.admin-form-actions {
    margin-top: 1rem;
    display: flex;
    justify-content: flex-start;
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
                @if(session('user_role') !== 'admin')
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
                    @if(session('user_role') !== 'das' && session('user_role') !== 'comite_wilaya')
                    <li class="sidebar-item">
                        <a href="{{ route('user.add.student') }}" class="sidebar-link">
                            <i class="fa-solid fa-user-plus"></i>
                            <span>إضافة تلميذ جديد</span>
                        </a>
                    </li>
                    @endif
                    @if(session('user_role') !== 'das' && session('user_role') !== 'comite_wilaya')
                    <li class="sidebar-item">
                        <a href="{{ route('user.pending.requests') }}" class="sidebar-link">
                            <i class="fa-solid fa-file-check"></i>
                            <span>الطلبات قيد التأكيد</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a href="{{ route('user.approved.requests') }}" class="sidebar-link">
                            <i class="fa-solid fa-file-circle-check"></i>
                            <span>الطلبات المؤكدة</span>
                        </a>
                    </li>
                    @endif
                @endif
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
        <p id="user-role">الوظيفة: {{ session('user_role') === 'das' ? 'DAS' : (session('user_role') === 'comite_wilaya' ? 'اللجنة الولائية' : (session('user_role') ?? '-')) }}</p>
        <p class="dashboard-header-commune" id="user-commune">
            @if(session('user_role') === 'das' || session('user_role') === 'comite_wilaya')
                ولاية: {{ $wilayaName ?? session('user_wilaya') ?? 'غير محددة' }}
            @else
                بلدية: {{ session('user_commune') ?? 'غير محددة' }}
            @endif
        </p>
    </div>

    @if(session('user_role') === 'admin')
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
                    <label class="form-label fw-bold required">كلمة المرور</label>
                    <input type="password" name="pass" class="form-control" minlength="6" required autocomplete="new-password">
                </div>
                <div>
                    <label class="form-label fw-bold required">الرتبة</label>
                    <select name="role" id="adminRoleSelect" class="form-select" required>
                        <option value="ts_commune" selected>ts_commune</option>
                        <option value="das">das</option>
                        <option value="comite_wilaya">comite_wilaya</option>
                        <option value="anten">anten</option>
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
    @else
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

        @if(session('user_role') !== 'das' && session('user_role') !== 'comite_wilaya')
        <a href="{{ route('user.pending.requests') }}" class="dashboard-action-card">
            <div class="action-card-icon warning">
                <i class="fa-solid fa-clock"></i>
            </div>
            <div class="action-card-content">
                <h3>الطلبات قيد التأكيد</h3>
                <p>مراجعة الطلبات التي في انتظار الموافقة</p>
            </div>
            <div class="action-card-arrow">
                <i class="fa-solid fa-chevron-left"></i>
            </div>
        </a>

        <a href="{{ route('user.approved.requests') }}" class="dashboard-action-card">
            <div class="action-card-icon success">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div class="action-card-content">
                <h3>الطلبات المؤكدة</h3>
                <p>عرض جميع الطلبات التي تمت الموافقة عليها</p>
            </div>
            <div class="action-card-arrow">
                <i class="fa-solid fa-chevron-left"></i>
            </div>
        </a>
        @endif
    </div>
    @endif

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

document.addEventListener('DOMContentLoaded', () => {
    const adminCreateUserForm = document.getElementById('adminCreateUserForm');
    if (!adminCreateUserForm) return;

    const API_TOKEN = @json(session('api_token'));
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
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
        } else if (role === 'das' || role === 'comite_wilaya' || role === 'anten') {
            if (wilayaWrapper) wilayaWrapper.style.display = 'block';
            if (communeWrapper) communeWrapper.style.display = 'none';
            if (codeWilayaSelect) codeWilayaSelect.required = true;
            if (codeCommSelect) {
                codeCommSelect.required = false;
                codeCommSelect.value = '';
            }
        } else {
            if (wilayaWrapper) wilayaWrapper.style.display = 'none';
            if (communeWrapper) communeWrapper.style.display = 'none';
            if (codeWilayaSelect) {
                codeWilayaSelect.required = false;
                codeWilayaSelect.value = '';
            }
            if (codeCommSelect) {
                codeCommSelect.required = false;
                codeCommSelect.value = '';
            }
        }
    };

    const loadWilayas = async () => {
        if (!codeWilayaSelect) return;
        try {
            const response = await fetch('/api/wilayas', {
                method: 'GET',
                headers: { 'Accept': 'application/json' }
            });
            const data = await response.json().catch(() => []);
            const wilayas = Array.isArray(data) ? data : (data.data || []);

            codeWilayaSelect.innerHTML = '<option value="">اختر الولاية...</option>';
            wilayas.forEach((w) => {
                const option = document.createElement('option');
                option.value = w.code_wil || w.id || '';
                option.textContent = w.lib_wil_ar || w.nom_wilaya || w.code_wil || 'ولاية';
                codeWilayaSelect.appendChild(option);
            });
        } catch (_) {
            codeWilayaSelect.innerHTML = '<option value="">تعذر تحميل الولايات</option>';
        }
    };

    const loadCommunesByWilaya = async (wilayaCode) => {
        if (!codeCommSelect) return;
        if (!wilayaCode) {
            codeCommSelect.innerHTML = '<option value="">اختر الولاية أولا...</option>';
            codeCommSelect.disabled = true;
            return;
        }
        try {
            const response = await fetch(`/api/communes/by-wilaya/${encodeURIComponent(wilayaCode)}`, {
                method: 'GET',
                headers: { 'Accept': 'application/json' }
            });
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
        } catch (_) {
            codeCommSelect.innerHTML = '<option value="">تعذر تحميل البلديات</option>';
            codeCommSelect.disabled = true;
        }
    };

    const checkCodeUserExists = async (codeUser) => {
        try {
            const response = await fetch(`/api/users/${encodeURIComponent(codeUser)}`, {
                method: 'GET',
                headers: { 'Accept': 'application/json' }
            });
            return response.ok;
        } catch (_) {
            return false;
        }
    };

    if (roleSelect) {
        roleSelect.addEventListener('change', () => {
            setRoleVisibility();
            if (codeWilayaSelect && codeWilayaSelect.value) {
                loadCommunesByWilaya(codeWilayaSelect.value);
            }
        });
    }

    if (codeWilayaSelect) {
        codeWilayaSelect.addEventListener('change', () => {
            loadCommunesByWilaya(codeWilayaSelect.value);
        });
    }

    loadWilayas();
    setRoleVisibility();
    if (codeCommSelect) {
        codeCommSelect.innerHTML = '<option value="">اختر الولاية أولا...</option>';
        codeCommSelect.disabled = true;
    }

    adminCreateUserForm.addEventListener('submit', async (e) => {
        e.preventDefault();

        const formData = new FormData(adminCreateUserForm);
        const payload = Object.fromEntries(formData.entries());
        payload.code_user = (payload.code_user || '').trim();

        const roleValue = payload.role || '';
        if (roleValue === 'ts_commune') {
            if (!payload.code_wilaya || !payload.code_comm) {
                Swal.fire({ icon: 'warning', title: 'تنبيه', text: 'يرجى اختيار الولاية والبلدية.' });
                return;
            }
        } else if (roleValue === 'das' || roleValue === 'comite_wilaya' || roleValue === 'anten') {
            if (!payload.code_wilaya) {
                Swal.fire({ icon: 'warning', title: 'تنبيه', text: 'يرجى اختيار الولاية.' });
                return;
            }
            payload.code_comm = null;
        } else {
            payload.code_comm = null;
            payload.code_wilaya = null;
        }

        if (!/^\d{18}$/.test(payload.code_user || '')) {
            Swal.fire({ icon: 'warning', title: 'تنبيه', text: 'رقم المستخدم يجب أن يكون 18 رقمًا.' });
            return;
        }

        const exists = await checkCodeUserExists(payload.code_user);
        if (exists) {
            Swal.fire({
                icon: 'error',
                title: 'موجود مسبقًا',
                text: 'رقم المستخدم موجود مسبقًا، يرجى إدخال رقم آخر.',
                confirmButtonText: 'حسنًا'
            });
            return;
        }

        try {
            Swal.fire({
                title: 'جارٍ إنشاء المستخدم...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            const headers = {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            };

            if (API_TOKEN) {
                headers['Authorization'] = `Bearer ${API_TOKEN}`;
            }

            const response = await fetch('/api/admin/users', {
                method: 'POST',
                headers,
                credentials: 'same-origin',
                body: JSON.stringify(payload)
            });

            const data = await response.json().catch(() => ({}));
            if (!response.ok) {
                let message = data.message || 'فشل إنشاء المستخدم';
                if (data.errors) {
                    const firstError = Object.values(data.errors)[0];
                    if (Array.isArray(firstError) && firstError.length > 0) {
                        message = firstError[0];
                    }
                }
                throw new Error(message);
            }

            await Swal.fire({
                icon: 'success',
                title: 'تم بنجاح',
                text: data.message || 'تم إنشاء المستخدم بنجاح',
                confirmButtonText: 'حسنًا'
            });

            adminCreateUserForm.reset();
            if (roleSelect) roleSelect.value = 'ts_commune';
            setRoleVisibility();
            if (codeCommSelect) {
                codeCommSelect.innerHTML = '<option value="">اختر الولاية أولا...</option>';
                codeCommSelect.disabled = true;
            }
        } catch (error) {
            Swal.fire({
                icon: 'error',
                title: 'خطأ',
                text: error.message || 'حدث خطأ أثناء إنشاء المستخدم',
                confirmButtonText: 'حسنًا'
            });
        }
    });
});

// Comment eleve - Enhanced with rich styling (same as tuteurs_list)
async function commentEleve(num_scolaire) {
    // Show loading
    Swal.fire({
        title: 'جارٍ التحميل...',
        html: '<div class="spinner-border text-primary" role="status"></div>',
        allowOutsideClick: false,
        showConfirmButton: false,
        didOpen: () => { Swal.showLoading(); }
    });

    // First, get existing comments
    let existingComments = [];
    try {
        const response = await fetch(`/user/eleves/${num_scolaire}/comments`, {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        });
        const data = await response.json();
        if (data.success) {
            existingComments = data.comments || [];
        }
    } catch (error) {
        console.error('Error loading comments:', error);
    }

    // Build HTML for existing comments with rich styling
    let commentsHTML = '';
    if (existingComments.length > 0) {
        commentsHTML = `
            <div class="comments-container" style="max-height: 400px; overflow-y: auto; margin-bottom: 2rem; padding: 1.5rem; background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%); border-radius: 12px; border: 1px solid rgba(15, 3, 58, 0.1);">
                <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 3px solid #fdae4b;">
                    <i class="fa-solid fa-comments" style="color: #fdae4b; font-size: 1.25rem;"></i>
                    <h6 style="margin: 0; color: #0f033a; font-weight: 700; font-size: 1.1rem;">التعليقات السابقة (${existingComments.length})</h6>
                </div>
        `;
        
        existingComments.forEach((comment, index) => {
            const dateObj = new Date(comment.created_at);
            const date = dateObj.toLocaleDateString('ar-DZ', { year: 'numeric', month: 'long', day: 'numeric' });
            const time = dateObj.toLocaleTimeString('ar-DZ', { hour: '2-digit', minute: '2-digit' });
            
            commentsHTML += `
                <div class="comment-card" style="background: white; padding: 1.25rem; margin-bottom: 1rem; border-radius: 12px; border-right: 4px solid #2563eb; box-shadow: 0 2px 8px rgba(0,0,0,0.08); transition: all 0.3s ease; position: relative; overflow: hidden;">
                    <div style="position: absolute; top: 0; right: 0; width: 100%; height: 3px; background: linear-gradient(90deg, #2563eb 0%, #3b82f6 100%);"></div>
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.75rem; gap: 1rem;">
                        <div style="display: flex; align-items: center; gap: 0.75rem; flex: 1;">
                            <div style="width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(135deg, #2563eb, #3b82f6); display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 1rem; box-shadow: 0 2px 8px rgba(37, 99, 235, 0.3);">
                                <i class="fa-solid fa-user"></i>
                    </div>
                            <div>
                                <strong style="color: #0f033a; font-size: 0.95rem; font-weight: 700; display: block; margin-bottom: 0.25rem;">
                                    ${(comment.user && comment.user.nom_user) ? comment.user.nom_user + ' ' + (comment.user.prenom_user || '') : 'مستخدم'}
                                </strong>
                                <div style="display: flex; align-items: center; gap: 0.5rem; color: #6b7280; font-size: 0.8rem;">
                                    <i class="fa-solid fa-calendar" style="color: #9ca3af;"></i>
                                    <span>${date}</span>
                                    <span style="margin: 0 0.25rem;">•</span>
                                    <i class="fa-solid fa-clock" style="color: #9ca3af;"></i>
                                    <span>${time}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div style="background: #f8fafc; padding: 1rem; border-radius: 8px; border-right: 2px solid #e5e7eb;">
                        <p style="margin: 0; color: #374151; line-height: 1.8; font-size: 0.95rem; white-space: pre-wrap; word-break: break-word;">${comment.text}</p>
                    </div>
                </div>
            `;
        });
        commentsHTML += '</div>';
    } else {
        commentsHTML = `
            <div class="empty-comments" style="text-align: center; padding: 3rem 2rem; background: linear-gradient(135deg, #dbeafe, #bfdbfe); border-radius: 12px; margin-bottom: 2rem; border: 2px dashed #3b82f6;">
                <i class="fa-solid fa-comment-slash" style="font-size: 3rem; color: #3b82f6; margin-bottom: 1rem; opacity: 0.6; display: block;"></i>
                <div style="color: #1e40af; font-weight: 600; font-size: 1rem;">لا توجد تعليقات سابقة</div>
            </div>
        `;
    }

    const result = await Swal.fire({
        title: '<div style="display: flex; align-items: center; gap: 0.75rem; justify-content: center;"><i class="fa-solid fa-comments" style="color: #fdae4b;"></i><span>التعليقات</span></div>',
        html: `
            <div style="direction: rtl; text-align: right;">
            ${commentsHTML}
                <div class="new-comment-section" style="margin-top: 2rem; padding-top: 2rem; border-top: 3px solid #fdae4b;">
                    <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1rem;">
                        <i class="fa-solid fa-plus-circle" style="color: #2563eb; font-size: 1.25rem;"></i>
                        <label style="color: #0f033a; font-weight: 700; font-size: 1.1rem; margin: 0;">إضافة تعليق جديد</label>
                    </div>
                    <div style="position: relative;">
                        <textarea id="commentText" rows="5" style="width: 100%; padding: 1rem 1.25rem; border: 2px solid #e5e7eb; border-radius: 12px; font-family: 'Cairo', sans-serif; font-size: 1rem; resize: vertical; transition: all 0.3s ease; background: white; color: #374151; line-height: 1.6;" placeholder="اكتب تعليقك هنا... (الحد الأقصى 1000 حرف)" oninput="updateCommentCounter()"></textarea>
                        <div id="commentCounter" style="position: absolute; bottom: 0.75rem; left: 1rem; color: #9ca3af; font-size: 0.85rem; background: white; padding: 0.25rem 0.5rem; border-radius: 4px;">0 / 1000</div>
                    </div>
                </div>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: '<i class="fa-solid fa-paper-plane"></i> إضافة التعليق',
        cancelButtonText: '<i class="fa-solid fa-times"></i> إلغاء',
        reverseButtons: true,
        confirmButtonColor: '#2563eb',
        cancelButtonColor: '#6b7280',
        width: '700px',
        customClass: {
            popup: 'swal-comment-modal',
            title: 'swal-comment-title',
            htmlContainer: 'swal-comment-content',
            confirmButton: 'swal-comment-confirm',
            cancelButton: 'swal-comment-cancel'
        },
        didOpen: () => {
            // Add scrollbar styling to comments container
            const commentsContainer = document.querySelector('.comments-container');
            if (commentsContainer) {
                commentsContainer.style.scrollbarWidth = 'thin';
                commentsContainer.style.scrollbarColor = '#2563eb #f1f5f9';
            }
            
            // Focus textarea
            const textarea = document.getElementById('commentText');
            if (textarea) {
                textarea.focus();
                textarea.addEventListener('input', function() {
                    if (this.value.length > 1000) {
                        this.value = this.value.substring(0, 1000);
                    }
                    updateCommentCounter();
                });
            }
        },
        preConfirm: async () => {
            const text = document.getElementById('commentText').value.trim();
            if (!text) {
                Swal.showValidationMessage('<i class="fa-solid fa-exclamation-circle"></i> يرجى إدخال نص التعليق');
                return false;
            }
            if (text.length > 1000) {
                Swal.showValidationMessage('<i class="fa-solid fa-exclamation-circle"></i> التعليق طويل جداً (الحد الأقصى 1000 حرف)');
                return false;
            }
            return text;
        }
    });
    
        if (result.isConfirmed && result.value) {
        // Show loading
        Swal.fire({
            title: 'جارٍ الإضافة...',
            html: '<div class="spinner-border text-primary" role="status"></div>',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => { Swal.showLoading(); }
        });

            try {
                const response = await fetch(`/user/eleves/${num_scolaire}/comments`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ text: result.value })
                });

                const data = await response.json();

                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                    title: '<div style="display: flex; align-items: center; gap: 0.5rem; justify-content: center;"><i class="fa-solid fa-check-circle"></i><span>تمت الإضافة</span></div>',
                    html: '<div style="color: #059669; font-weight: 600;">تمت إضافة التعليق بنجاح</div>',
                        confirmButtonText: 'حسنًا',
                    confirmButtonColor: '#10b981',
                    timer: 2000,
                    timerProgressBar: true
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                    title: '<div style="display: flex; align-items: center; gap: 0.5rem; justify-content: center;"><i class="fa-solid fa-exclamation-triangle"></i><span>خطأ</span></div>',
                        text: data.message || 'فشلت إضافة التعليق',
                    confirmButtonText: 'حسنًا',
                    confirmButtonColor: '#ef4444'
                    });
                }
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                title: '<div style="display: flex; align-items: center; gap: 0.5rem; justify-content: center;"><i class="fa-solid fa-exclamation-triangle"></i><span>خطأ</span></div>',
                    text: 'حدث خطأ أثناء إضافة التعليق',
                confirmButtonText: 'حسنًا',
                confirmButtonColor: '#ef4444'
                });
            }
        }
}

// Update comment counter
function updateCommentCounter() {
    const textarea = document.getElementById('commentText');
    const counter = document.getElementById('commentCounter');
    if (textarea && counter) {
        const length = textarea.value.length;
        counter.textContent = `${length} / 1000`;
        if (length > 900) {
            counter.style.color = '#ef4444';
            counter.style.fontWeight = '700';
        } else if (length > 700) {
            counter.style.color = '#f59e0b';
            counter.style.fontWeight = '600';
        } else {
            counter.style.color = '#9ca3af';
            counter.style.fontWeight = '400';
        }
                    }
                }
</script>

@endsection
