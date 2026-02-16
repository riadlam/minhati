@extends('layouts.main')

@section('title', 'قائمة التلاميذ')

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

/* Table Styles */
.children-table-wrapper {
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    overflow-x: auto;
    overflow-y: visible;
    border: 1px solid rgba(15, 3, 58, 0.1);
}

.children-table-wrapper::-webkit-scrollbar {
    height: 8px;
}

.children-table-wrapper::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 8px;
}

.children-table-wrapper::-webkit-scrollbar-thumb {
    background: linear-gradient(135deg, #0f033a, #fdae4b);
    border-radius: 8px;
}

.children-table-wrapper::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(135deg, #1a0f4a, #f59e0b);
}

.children-table {
    width: 100%;
    border-collapse: collapse;
    background: white;
    min-width: 1000px;
}

.children-table thead {
    background: linear-gradient(135deg, #0f033a 0%, #1a0f4a 100%);
    color: white;
}

.children-table thead th {
    padding: 1rem 0.75rem;
    text-align: center;
    font-weight: 600;
    font-size: 0.9rem;
    border: none;
    white-space: nowrap;
}

.children-table tbody tr {
    border-bottom: 1px solid #e2e8f0;
    transition: all 0.2s ease;
}

.children-table tbody tr:hover {
    background: linear-gradient(90deg, rgba(253, 174, 75, 0.05) 0%, rgba(253, 174, 75, 0.1) 50%, rgba(253, 174, 75, 0.05) 100%);
}

.children-table tbody td {
    padding: 1rem 0.75rem;
    text-align: center;
    color: #0f1419;
    font-size: 0.9rem;
    border: none;
    vertical-align: middle;
}

/* Filter section spacing - prevent overlap */
.filters-row {
    display: flex;
    flex-wrap: wrap;
    align-items: flex-end;
    gap: 1.5rem 2rem;
    margin-bottom: 1.5rem;
    padding: 1.25rem 1.5rem;
    background: #f8fafc;
    border-radius: 12px;
    border: 1px solid #e5e7eb;
}
.filters-row .filter-group {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    min-width: 0;
}
.filters-row .filter-group label {
    color: #374151;
    font-weight: 600;
    font-size: 0.9rem;
    white-space: nowrap;
    margin: 0;
}
.filters-row .filter-group.status-filter { min-width: 180px; }
.filters-row .filter-group.search-filter { flex: 1; min-width: 220px; }
.filters-row .filter-group.school-filter { flex: 1; min-width: 220px; }
.filters-row .filter-group .filter-control {
    padding: 0.5rem 1rem;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    font-family: 'Cairo', sans-serif;
    font-size: 0.95rem;
    background: white;
    cursor: pointer;
    width: 100%;
    min-width: 0;
    transition: all 0.3s ease;
}
.filters-row .filter-group .filter-control:focus {
    outline: none;
    border-color: #fdae4b;
    box-shadow: 0 0 0 3px rgba(253, 174, 75, 0.2);
}
.filters-row .filter-actions {
    flex-shrink: 0;
    margin-right: auto;
}

/* Action Buttons */
.action-buttons {
    display: flex;
    gap: 5px;
    justify-content: center;
    flex-wrap: wrap;
}

.action-buttons button {
    transition: all 0.3s ease;
}

.action-buttons button:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.2) !important;
}

.action-buttons button:active {
    transform: translateY(0);
}

/* SweetAlert2 Modal Overrides */
.swal2-popup.swal-tuteur-modal {
    border-radius: 16px !important;
    max-width: 90% !important;
    padding: 0 !important;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3) !important;
    overflow: hidden;
}

.swal2-popup.swal-tuteur-modal .swal2-title {
    background: linear-gradient(135deg, #0f033a 0%, #1a0f4a 100%);
    color: white !important;
    padding: 1.5rem 2rem;
    margin: 0 !important;
    font-size: 1.5rem;
    font-weight: 700;
    text-align: right;
    border-radius: 16px 16px 0 0;
    border-bottom: 3px solid #fdae4b;
}

.swal2-popup.swal-tuteur-modal .swal2-html-container {
    padding: 2rem !important;
    margin: 0 !important;
    text-align: right;
    max-height: 65vh;
    overflow-y: auto;
    background: white;
}

.swal2-popup.swal-tuteur-modal .swal2-html-container::-webkit-scrollbar {
    width: 10px;
}

.swal2-popup.swal-tuteur-modal .swal2-html-container::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 8px;
}

.swal2-popup.swal-tuteur-modal .swal2-html-container::-webkit-scrollbar-thumb {
    background: linear-gradient(135deg, #0f033a, #fdae4b);
    border-radius: 8px;
}

.swal2-popup.swal-tuteur-modal .swal2-actions {
    padding: 1.5rem 2rem;
    margin: 0 !important;
    background: #f8fafc;
    border-top: 1px solid #e2e8f0;
    border-radius: 0 0 16px 16px;
}

.swal2-popup.swal-tuteur-modal .swal2-confirm {
    background: linear-gradient(135deg, #0f033a 0%, #1a0f4a 100%) !important;
    border: none !important;
    border-radius: 8px !important;
    padding: 0.75rem 2rem !important;
    font-weight: 600 !important;
    font-size: 1rem !important;
    box-shadow: 0 4px 12px rgba(15, 3, 58, 0.3) !important;
}

.swal2-popup.swal-tuteur-modal .swal2-close {
    color: white !important;
    font-size: 2rem !important;
    opacity: 0.9 !important;
}

.empty-state {
    background: linear-gradient(135deg, #dbeafe, #bfdbfe);
    padding: 2.5rem;
    border-radius: 12px;
    text-align: center;
    color: #1e40af;
    font-weight: 500;
    border: 2px dashed #3b82f6;
    margin-top: 1rem;
}

.empty-state i {
    font-size: 3rem;
    margin-bottom: 1rem;
    display: block;
    opacity: 0.6;
}

/* Decline modal (DAS) - modern style */
.swal-decline-popup {
    border-radius: 16px !important;
    overflow: hidden;
    box-shadow: 0 24px 48px rgba(15, 3, 58, 0.15), 0 12px 24px rgba(0, 0, 0, 0.1) !important;
    border: 1px solid rgba(15, 3, 58, 0.08);
}
.swal-decline-form {
    text-align: right;
    padding: 0.5rem 0;
}
.swal-decline-label {
    display: block;
    font-weight: 600;
    color: #0f033a;
    margin-bottom: 0.5rem;
    font-size: 0.95rem;
}
.swal-decline-textarea {
    width: 100%;
    min-height: 100px;
    padding: 0.875rem 1rem;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    font-size: 0.95rem;
    font-family: inherit;
    resize: vertical;
    transition: border-color 0.2s, box-shadow 0.2s;
}
.swal-decline-textarea:focus {
    outline: none;
    border-color: #0f033a;
    box-shadow: 0 0 0 3px rgba(15, 3, 58, 0.08);
}
.swal-decline-textarea::placeholder {
    color: #94a3b8;
}
.swal-decline-checkboxes {
    display: flex;
    gap: 1.5rem;
    margin-top: 1.25rem;
    flex-wrap: wrap;
}
.swal-decline-check {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    cursor: pointer;
    padding: 0.5rem 1rem;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    transition: background 0.2s, border-color 0.2s;
}
.swal-decline-check:hover {
    background: #f1f5f9;
    border-color: #cbd5e1;
}
.swal-decline-checkbox {
    width: 1.125rem;
    height: 1.125rem;
    accent-color: #0f033a;
    cursor: pointer;
}
.swal-decline-check span {
    font-weight: 500;
    color: #334155;
    font-size: 0.9rem;
}
.swal-decline-readonly-text {
    width: 100%;
    min-height: 80px;
    padding: 0.875rem 1rem;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    background: #f8fafc;
    font-size: 0.95rem;
    color: #334155;
    text-align: right;
    line-height: 1.6;
}
.swal-decline-readonly-checks .swal-decline-check.readonly {
    cursor: default;
    background: #f1f5f9;
}
.swal-decline-readonly-checks .swal-decline-checkbox:disabled {
    cursor: default;
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
                <li class="sidebar-item">
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
                <li class="sidebar-item active">
                    <a href="{{ route('user.students.list') }}" class="sidebar-link">
                        <i class="fa-solid fa-user-graduate"></i>
                        <span>التلاميذ</span>
                    </a>
                </li>
                @if(!in_array(session('user_role'), ['das', 'comite_wilaya', 'antr']))
                <li class="sidebar-item">
                    <a href="{{ route('user.add.student') }}" class="sidebar-link">
                        <i class="fa-solid fa-user-plus"></i>
                        <span>إضافة تلميذ جديد</span>
                    </a>
                </li>
                @endif
                @if(!in_array(session('user_role'), ['das', 'comite_wilaya', 'antr']))
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
            <div class="dashboard-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                <div style="flex: 1;">
                    <h2>قائمة التلاميذ</h2>
                    <p>عرض وإدارة جميع التلاميذ المسجلين في المنصة</p>
                </div>
                <a href="{{ route('user.eleves.export.excel') }}" class="btn btn-success" style="background: linear-gradient(135deg, #10b981, #059669); border: none; padding: 0.75rem 1.5rem; border-radius: 8px; color: white; display: inline-flex; align-items: center; gap: 0.5rem; text-decoration: none; font-weight: 600; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3); transition: all 0.3s ease; white-space: nowrap;">
                    <i class="fa-solid fa-file-excel"></i>
                    <span>تصدير Excel</span>
                </a>
            </div>

            <!-- Table Section -->
            <div class="children-table-section">
                <!-- Filters Row -->
                <div class="filters-row">
                    @if(in_array(session('user_role'), ['das', 'comite_wilaya', 'antr']))
                    <div class="filter-group status-filter">
                        <label for="statusFilter">حالة الطلب:</label>
                        <select id="statusFilter" class="filter-control">
                            <option value="">الكل</option>
                            <option value="accepte">مقبول</option>
                            <option value="refuse">مرفوض</option>
                            <option value="pending">قيد المراجعة</option>
                        </select>
                    </div>
                    @endif
                    <div class="filter-group search-filter">
                        <label for="num_scolaire_search">البحث برقم التعريف المدرسي:</label>
                        <input type="text" id="num_scolaire_search" class="filter-control" placeholder="ابحث برقم التعريف المدرسي...">
                    </div>
                    <div class="filter-group school-filter">
                        <label for="schoolFilter">فلترة حسب مؤسسة التربية والتعليم:</label>
                        <select id="schoolFilter" class="filter-control">
                            <option value="">جميع المدارس</option>
                            @php
                                $schoolsByLevel = [];
                                foreach($schools as $school) {
                                    $levels = $school->levels ?? [];
                                    foreach($levels as $level) {
                                        if (!isset($schoolsByLevel[$level])) {
                                            $schoolsByLevel[$level] = [];
                                        }
                                        $schoolsByLevel[$level][] = $school;
                                    }
                                }
                                $levelOrder = ['ابتدائي', 'متوسط', 'ثانوي', 'أخرى'];
                            @endphp
                            @foreach($levelOrder as $level)
                                @if(isset($schoolsByLevel[$level]) && count($schoolsByLevel[$level]) > 0)
                                    <optgroup label="{{ $level }}">
                                        @foreach($schoolsByLevel[$level] as $school)
                                            <option value="{{ $school->code_etabliss }}">{{ $school->nom_etabliss }}</option>
                                        @endforeach
                                    </optgroup>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="filter-actions">
                        <button id="clearFilters" type="button" style="padding: 0.5rem 1.5rem; background: #6b7280; color: white; border: none; border-radius: 8px; cursor: pointer; font-family: 'Cairo', sans-serif; font-weight: 600; display: none; transition: all 0.3s ease; white-space: nowrap;">
                            <i class="fa-solid fa-times"></i> مسح الفلاتر
                        </button>
                    </div>
                </div>
                <div class="children-table-wrapper">
                    <table class="children-table" id="main-table">
                        <thead id="table-head">
                            <tr>
                                <th>رقم التعريف المدرسي</th>
                                <th>الاسم الكامل</th>
                                <th>تاريخ الميلاد</th>
                                <th>المستوى/القسم</th>
                                <th>مؤسسة التربية والتعليم</th>
                                @if(!in_array(session('user_role'), ['das', 'comite_wilaya', 'antr']))
                                <th>حالة الملف</th>
                                <th style="min-width: 280px; width: 280px;">الإجراءات</th>
                                @elseif(session('user_role') === 'das')
                                <th>الحالة</th>
                                <th>سبب الرفض</th>
                                <th style="min-width: 100px; width: 100px;">الإجراءات</th>
                                @elseif(session('user_role') === 'antr')
                                <th>حالة DAS</th>
                                <th>حالة اللجنة</th>
                                <th>القرار النهائي</th>
                                <th style="min-width: 200px; width: 200px;">الإجراءات</th>
                                @else
                                <th>حالة DAS</th>
                                <th>حالة اللجنة الولائية</th>
                                <th>سبب الرفض</th>
                                <th style="min-width: 100px; width: 100px;">الإجراءات</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody id="table-body">
                            <tr>
                                <td colspan="{{ session('user_role') === 'antr' ? '10' : (session('user_role') === 'comite_wilaya' ? '9' : (session('user_role') === 'das' ? '8' : '7')) }}" style="text-align: center; padding: 20px;">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">جارٍ التحميل...</span>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <!-- Pagination -->
                <div id="pagination-container" style="display: flex; justify-content: center; align-items: center; gap: 0.5rem; margin-top: 2rem; padding: 1rem;"></div>
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

// Variables
// Store API token from session or localStorage (two-host: token from login on API host)
let API_TOKEN = '{{ session("api_token") }}';
if (!API_TOKEN && typeof localStorage !== 'undefined') API_TOKEN = localStorage.getItem('api_token');
const getApiUrlPath = (path) => (typeof window.getApiUrl === 'function' ? window.getApiUrl(path) : path);

let currentPage = 1;
let currentFilter = '';
let currentNumScolaireSearch = '';
let currentStatusFilter = '';
let searchTimeout = null;
let allSchools = [];
const isDasOrComite = ['das', 'comite_wilaya', 'antr'].includes('{{ session("user_role") }}');
const isAntrRole = '{{ session("user_role") }}' === 'antr';

// Load students with pagination - GLOBAL FUNCTION
async function loadStudents(page = 1, code_etabliss = '', num_scolaire_search = '', status_filter = '') {
    const tableBody = document.getElementById('table-body');
    const paginationContainer = document.getElementById('pagination-container');
    
    if (!tableBody) return; // Guard clause if elements not ready yet
    
    const colSpan = isAntrRole ? 10 : ('{{ session("user_role") }}' === 'comite_wilaya' ? 9 : ('{{ session("user_role") }}' === 'das' ? 8 : 7));
    tableBody.innerHTML = `
        <tr>
            <td colspan="${colSpan}" style="text-align: center; padding: 20px;">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">جارٍ التحميل...</span>
                </div>
            </td>
        </tr>
    `;

    try {
        const url = new URL(getApiUrlPath('/api/user/eleves'));
        url.searchParams.append('page', page);
        if (code_etabliss) {
            url.searchParams.append('code_etabliss', code_etabliss);
        }
        if (num_scolaire_search) {
            url.searchParams.append('num_scolaire_search', num_scolaire_search);
        }
        if (isDasOrComite && status_filter) {
            url.searchParams.append('status_filter', status_filter);
        }

        const response = await fetch(url, {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        });

        const result = await response.json();

        if (!result.success) {
            tableBody.innerHTML = `<tr><td colspan="${colSpan}" style="text-align: center; padding: 20px; color: red;">حدث خطأ أثناء تحميل البيانات</td></tr>`;
            return;
        }

        const eleves = result.data || [];
        const total = result.total || 0;
        currentPage = result.current_page || 1;
        const lastPage = result.last_page || 1;

        if (eleves.length === 0) {
            tableBody.innerHTML = `<tr><td colspan="${colSpan}" style="text-align: center; padding: 20px; color: #6b7280;">لا توجد بيانات</td></tr>`;
            if (paginationContainer) paginationContainer.innerHTML = '';
            return;
        }

        const isDasRole = '{{ session("user_role") }}' === 'das';
        const isComiteRole = '{{ session("user_role") }}' === 'comite_wilaya';
        const isAntr = '{{ session("user_role") }}' === 'antr';
        const escapeAttr = (s) => (s || '').replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/\r?\n/g, ' ');
        
        let html = '';
        eleves.forEach((eleve, index) => {
            const dossierBadge = eleve.dossier_depose === 'oui' 
                ? `<span class="badge bg-success">مودع</span>`
                : `<span class="badge bg-warning">غير مودع</span>`;
            
            const isApproved = eleve.dossier_depose === 'oui';

            // Status badges
            let statusBadge = '';
            let showActionButtons = true;
            let statusDasBadge = '';
            let statusComiteBadge = '';
            let statusFinalBadge = '';
            let showComiteActionButtons = true;
            let showAntrActionButtons = true;
            
            if (isDasRole) {
                const etatDas = (eleve.etat_das || '').toLowerCase();
                if (etatDas === 'accepte') { statusBadge = '<span class="badge bg-success">مقبول</span>'; showActionButtons = false; }
                else if (etatDas === 'refuse') { statusBadge = '<span class="badge bg-danger">مرفوض</span>'; showActionButtons = false; }
                else { statusBadge = '<span class="badge bg-secondary">قيد المراجعة</span>'; showActionButtons = true; }
            }
            if (isComiteRole) {
                const etatDas = (eleve.etat_das || '').toLowerCase();
                if (etatDas === 'accepte') statusDasBadge = '<span class="badge bg-success">مقبول</span>';
                else if (etatDas === 'refuse') statusDasBadge = '<span class="badge bg-danger">مرفوض</span>';
                else statusDasBadge = '<span class="badge bg-secondary">قيد المراجعة</span>';
                const etatComite = (eleve.etat_comite_wilaya || '').toLowerCase();
                if (etatComite === 'accepte') { statusComiteBadge = '<span class="badge bg-success">مقبول</span>'; showComiteActionButtons = false; }
                else if (etatComite === 'refuse') { statusComiteBadge = '<span class="badge bg-danger">مرفوض</span>'; showComiteActionButtons = false; }
                else { statusComiteBadge = '<span class="badge bg-secondary">قيد المراجعة</span>'; showComiteActionButtons = true; }
            }
            if (isAntr) {
                statusDasBadge = '<span class="badge bg-success">مقبول</span>';
                statusComiteBadge = '<span class="badge bg-success">مقبول</span>';
                const etatFinal = (eleve.etat_final || '').toLowerCase();
                if (etatFinal === 'accepte') { statusFinalBadge = '<span class="badge bg-success">مقبول نهائي</span>'; showAntrActionButtons = false; }
                else if (etatFinal === 'refuse') { statusFinalBadge = '<span class="badge bg-danger">مرفوض</span>'; showAntrActionButtons = false; }
                else { statusFinalBadge = '<span class="badge bg-warning">قيد الدراسة</span>'; showAntrActionButtons = true; }
            }

            const etatDasRefuse = (eleve.etat_das || '').toLowerCase() === 'refuse';
            const etatComiteRefuse = (eleve.etat_comite_wilaya || '').toLowerCase() === 'refuse';
            const isRefused = (isDasRole && etatDasRefuse) || (isComiteRole && (etatDasRefuse || etatComiteRefuse));
            const motifEscaped = escapeAttr(eleve.motif || '');
            const causeShowForComite = isComiteRole && (etatDasRefuse || etatComiteRefuse);
            html += `
                    <tr ${isRefused ? `data-motif="${motifEscaped}" data-cnas="${eleve.cnas_refuse || 0}" data-casnos="${eleve.casnos_refuse || 0}" data-num-scolaire="${eleve.num_scolaire || ''}"` : ''}>
                        <td>${eleve.num_scolaire || '—'}</td>
                        <td>${eleve.nom || '—'} ${eleve.prenom || '—'}</td>
                        <td>${eleve.date_naiss || '—'}</td>
                        <td>${eleve.classe_scol || eleve.niv_scol || '—'}</td>
                        <td>${eleve.etablissement_nom || '—'}</td>
                        ${!isDasRole && !isComiteRole && !isAntr ? `<td>${dossierBadge}</td>` : ''}
                        ${isDasRole ? `<td>${statusBadge}</td><td>${isRefused ? `<button type="button" class="btn btn-sm btn-outline-secondary" onclick="showRefuseModalFromRow(this)" title="عرض سبب الرفض" style="padding: 0.35rem 0.6rem; border-radius: 6px; font-size: 0.85rem;"><i class="fa-solid fa-eye me-1"></i>عرض</button>` : '—'}</td>` : ''}
                        ${isComiteRole ? `<td>${statusDasBadge}</td><td>${statusComiteBadge}</td><td>${causeShowForComite ? `<button type="button" class="btn btn-sm btn-outline-secondary" onclick="showRefuseModalFromRowComite(this)" title="عرض/تعديل سبب الرفض" style="padding: 0.35rem 0.6rem; border-radius: 6px; font-size: 0.85rem;"><i class="fa-solid fa-eye me-1"></i>عرض</button>` : '—'}</td>` : ''}
                        ${isAntr ? `<td>${statusDasBadge}</td><td>${statusComiteBadge}</td><td>${statusFinalBadge}</td>` : ''}
                        <td>
                            <div class="action-buttons" style="display: flex; gap: 5px; justify-content: center; flex-wrap: nowrap;">
                                <button class="btn btn-sm btn-info" onclick="viewEleveFromModal('${eleve.num_scolaire}')" title="عرض التفاصيل" style="background: linear-gradient(135deg, #3b82f6, #2563eb); border: none; padding: 0.4rem 0.6rem; border-radius: 6px; color: white; display: inline-flex; align-items: center; gap: 0.25rem; transition: all 0.3s ease; box-shadow: 0 2px 4px rgba(0,0,0,0.1); white-space: nowrap;">
                                    <i class="fa-solid fa-eye"></i>
                                    <span style="font-size: 0.85rem;">عرض</span>
                                </button>
                                ${eleve.appeal_status === 'pending' ? `
                                <button class="btn btn-sm" onclick="showAdminAppeal('${eleve.num_scolaire}')" title="طعن قيد المراجعة" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed); border: none; padding: 0.4rem 0.6rem; border-radius: 6px; color: white; display: inline-flex; align-items: center; gap: 0.25rem; transition: all 0.3s ease; box-shadow: 0 2px 4px rgba(0,0,0,0.1); white-space: nowrap; position: relative;">
                                    <i class="fa-solid fa-gavel"></i>
                                    <span style="font-size: 0.85rem;">طعن</span>
                                    <span style="position:absolute;top:-6px;right:-6px;background:#ef4444;color:#fff;border-radius:50%;width:18px;height:18px;font-size:10px;line-height:18px;text-align:center;font-weight:700;">!</span>
                                </button>
                                ` : ''}
                                ${isDasRole && showActionButtons ? `
                                <button class="btn btn-sm btn-success" onclick="dasAcceptEleve('${eleve.num_scolaire}')" title="قبول" style="background: linear-gradient(135deg, #10b981, #059669); border: none; padding: 0.4rem 0.6rem; border-radius: 6px; color: white; display: inline-flex; align-items: center; gap: 0.25rem; transition: all 0.3s ease; box-shadow: 0 2px 4px rgba(0,0,0,0.1); white-space: nowrap;">
                                    <i class="fa-solid fa-check"></i>
                                    <span style="font-size: 0.85rem;">قبول</span>
                                </button>
                                <button class="btn btn-sm btn-danger" onclick="dasDeclineEleve('${eleve.num_scolaire}')" title="رفض" style="background: linear-gradient(135deg, #ef4444, #dc2626); border: none; padding: 0.4rem 0.6rem; border-radius: 6px; color: white; display: inline-flex; align-items: center; gap: 0.25rem; transition: all 0.3s ease; box-shadow: 0 2px 4px rgba(0,0,0,0.1); white-space: nowrap;">
                                    <i class="fa-solid fa-times"></i>
                                    <span style="font-size: 0.85rem;">رفض</span>
                                </button>
                                ` : ''}
                                ${isComiteRole && showComiteActionButtons ? `
                                <button class="btn btn-sm btn-success" onclick="comiteAcceptEleve('${eleve.num_scolaire}')" title="قبول" style="background: linear-gradient(135deg, #10b981, #059669); border: none; padding: 0.4rem 0.6rem; border-radius: 6px; color: white; display: inline-flex; align-items: center; gap: 0.25rem; transition: all 0.3s ease; box-shadow: 0 2px 4px rgba(0,0,0,0.1); white-space: nowrap;">
                                    <i class="fa-solid fa-check"></i>
                                    <span style="font-size: 0.85rem;">قبول</span>
                                </button>
                                <button class="btn btn-sm btn-danger" onclick="comiteDeclineEleve('${eleve.num_scolaire}', this)" title="رفض" style="background: linear-gradient(135deg, #ef4444, #dc2626); border: none; padding: 0.4rem 0.6rem; border-radius: 6px; color: white; display: inline-flex; align-items: center; gap: 0.25rem; transition: all 0.3s ease; box-shadow: 0 2px 4px rgba(0,0,0,0.1); white-space: nowrap;">
                                    <i class="fa-solid fa-times"></i>
                                    <span style="font-size: 0.85rem;">رفض</span>
                                </button>
                                ` : ''}
                                ${isAntr && showAntrActionButtons ? `
                                <button class="btn btn-sm btn-success" onclick="antrAcceptEleve('${eleve.num_scolaire}')" title="قبول نهائي" style="background: linear-gradient(135deg, #10b981, #059669); border: none; padding: 0.4rem 0.6rem; border-radius: 6px; color: white; display: inline-flex; align-items: center; gap: 0.25rem; transition: all 0.3s ease; box-shadow: 0 2px 4px rgba(0,0,0,0.1); white-space: nowrap;">
                                    <i class="fa-solid fa-check-double"></i>
                                    <span style="font-size: 0.85rem;">قبول نهائي</span>
                                </button>
                                <button class="btn btn-sm btn-danger" onclick="antrDeclineEleve('${eleve.num_scolaire}')" title="رفض" style="background: linear-gradient(135deg, #ef4444, #dc2626); border: none; padding: 0.4rem 0.6rem; border-radius: 6px; color: white; display: inline-flex; align-items: center; gap: 0.25rem; transition: all 0.3s ease; box-shadow: 0 2px 4px rgba(0,0,0,0.1); white-space: nowrap;">
                                    <i class="fa-solid fa-times"></i>
                                    <span style="font-size: 0.85rem;">رفض</span>
                                </button>
                                ` : ''}
                                ${!isDasRole && !isComiteRole && !isAntr ? `
                                <button class="btn btn-sm btn-danger" onclick="generateIstimaraPDF('${eleve.num_scolaire}')" title="PDF" style="background: linear-gradient(135deg, #ef4444, #dc2626); border: none; padding: 0.4rem 0.6rem; border-radius: 6px; color: white; display: inline-flex; align-items: center; gap: 0.25rem; transition: all 0.3s ease; box-shadow: 0 2px 4px rgba(0,0,0,0.1); white-space: nowrap;">
                                    <i class="fa-solid fa-file-pdf"></i>
                                    <span style="font-size: 0.85rem;">PDF</span>
                                </button>
                                ${!isApproved ? `<button class="btn btn-sm btn-success" onclick="approveEleveFromModal('${eleve.num_scolaire}')" title="موافقة" style="background: linear-gradient(135deg, #10b981, #059669); border: none; padding: 0.4rem 0.6rem; border-radius: 6px; color: white; display: inline-flex; align-items: center; gap: 0.25rem; transition: all 0.3s ease; box-shadow: 0 2px 4px rgba(0,0,0,0.1); white-space: nowrap;">
                                    <i class="fa-solid fa-check"></i>
                                    <span style="font-size: 0.85rem;">موافقة</span>
                                </button>` : ''}
                                <button class="btn btn-sm btn-warning" onclick="commentEleve('${eleve.num_scolaire}')" title="تعليق" style="background: linear-gradient(135deg, #f59e0b, #d97706); border: none; padding: 0.4rem 0.6rem; border-radius: 6px; color: white; display: inline-flex; align-items: center; gap: 0.25rem; transition: all 0.3s ease; box-shadow: 0 2px 4px rgba(0,0,0,0.1); white-space: nowrap;">
                                    <i class="fa-solid fa-comment"></i>
                                    <span style="font-size: 0.85rem;">تعليق</span>
                                </button>
                                <button class="btn btn-sm btn-danger" onclick="deleteEleveFromModal('${eleve.num_scolaire}')" title="حذف" style="background: linear-gradient(135deg, #ef4444, #dc2626); border: none; padding: 0.4rem 0.6rem; border-radius: 6px; color: white; display: inline-flex; align-items: center; gap: 0.25rem; transition: all 0.3s ease; box-shadow: 0 2px 4px rgba(0,0,0,0.1); white-space: nowrap;">
                                    <i class="fa-solid fa-trash"></i>
                                    <span style="font-size: 0.85rem;">حذف</span>
                                </button>
                                ` : ''}
                            </div>
                        </td>
                    </tr>
                `;
            });

            tableBody.innerHTML = html;

            // Build pagination
            let paginationHTML = '';
            if (lastPage > 1) {
                paginationHTML = '<div style="display: flex; gap: 0.5rem; align-items: center; flex-wrap: wrap;">';
                
                // Previous button
                if (currentPage > 1) {
                    paginationHTML += `<button onclick="loadStudentsPage(${currentPage - 1})" style="padding: 0.5rem 1rem; background: #2563eb; color: white; border: none; border-radius: 6px; cursor: pointer;">◀ السابق</button>`;
                }

                // Page numbers
                for (let i = 1; i <= lastPage; i++) {
                    if (i === 1 || i === lastPage || (i >= currentPage - 2 && i <= currentPage + 2)) {
                        paginationHTML += `<button onclick="loadStudentsPage(${i})" style="padding: 0.5rem 1rem; background: ${i === currentPage ? '#0f033a' : '#e5e7eb'}; color: ${i === currentPage ? 'white' : '#374151'}; border: none; border-radius: 6px; cursor: pointer; font-weight: ${i === currentPage ? '600' : '400'};" ${i === currentPage ? 'disabled' : ''}>${i}</button>`;
                    } else if (i === currentPage - 3 || i === currentPage + 3) {
                        paginationHTML += '<span style="padding: 0.5rem;">...</span>';
                    }
                }

                // Next button
                if (currentPage < lastPage) {
                    paginationHTML += `<button onclick="loadStudentsPage(${currentPage + 1})" style="padding: 0.5rem 1rem; background: #2563eb; color: white; border: none; border-radius: 6px; cursor: pointer;">التالي ▶</button>`;
                }

                paginationHTML += '</div>';
            }
            if (paginationContainer) {
                paginationContainer.innerHTML = paginationHTML;
            }

    } catch (error) {
        console.error('Error loading students:', error);
        if (tableBody) {
            tableBody.innerHTML = '<tr><td colspan="7" style="text-align: center; padding: 20px; color: red;">حدث خطأ أثناء تحميل البيانات</td></tr>';
        }
    }
}

// Make loadStudentsPage available globally
window.loadStudentsPage = function(page) {
    loadStudents(page, currentFilter, currentNumScolaireSearch, currentStatusFilter);
};

// Initialize event listeners when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    const schoolFilter = document.getElementById('schoolFilter');
    const numScolaireSearch = document.getElementById('num_scolaire_search');
    const clearFilters = document.getElementById('clearFilters');
    const schoolSearch = document.getElementById('schoolSearch');
    const selectedSchool = document.getElementById('selectedSchool');
    const schoolDropdown = document.getElementById('schoolDropdown');

    async function loadSchoolsFilter() {
        if (!schoolFilter) return;
        try {
            const response = await fetch(getApiUrlPath('/api/user/schools'));
            const result = await response.json().catch(() => ({}));
            const schools = Array.isArray(result.data) ? result.data : [];
            schoolFilter.innerHTML = '<option value="">جميع المدارس</option>';
            const levelsOrder = ['ابتدائي', 'متوسط', 'ثانوي', 'أخرى'];
            const byLevel = {};
            levelsOrder.forEach((level) => byLevel[level] = []);
            schools.forEach((s) => {
                const levels = Array.isArray(s.levels) && s.levels.length ? s.levels : ['أخرى'];
                levels.forEach((level) => {
                    if (!byLevel[level]) byLevel[level] = [];
                    byLevel[level].push(s);
                });
            });
            levelsOrder.forEach((level) => {
                if (!byLevel[level] || !byLevel[level].length) return;
                const og = document.createElement('optgroup');
                og.label = level;
                byLevel[level].forEach((s) => {
                    const op = document.createElement('option');
                    op.value = s.code_etabliss || '';
                    op.textContent = s.nom_etabliss || s.code_etabliss || '—';
                    og.appendChild(op);
                });
                schoolFilter.appendChild(og);
            });
        } catch (_) {}
    }

    // Function to update clear button visibility
    function updateClearButton() {
        if (currentFilter || currentNumScolaireSearch || currentStatusFilter) {
            clearFilters.style.display = 'block';
        } else {
            clearFilters.style.display = 'none';
        }
    }

    // School filter change handler
    if (schoolFilter) {
        schoolFilter.addEventListener('change', () => {
            currentFilter = schoolFilter.value;
            updateClearButton();
            loadStudents(1, currentFilter, currentNumScolaireSearch, currentStatusFilter);
        });
    }

    // Status filter (DAS / comite_wilaya)
    const statusFilterEl = document.getElementById('statusFilter');
    if (statusFilterEl) {
        statusFilterEl.addEventListener('change', function() {
            currentStatusFilter = this.value;
            updateClearButton();
            loadStudents(1, currentFilter, currentNumScolaireSearch, currentStatusFilter);
        });
    }

    // Student ID search with debounce
    if (numScolaireSearch) {
        numScolaireSearch.addEventListener('input', (e) => {
            currentNumScolaireSearch = e.target.value.trim();
            updateClearButton();
            
            // Clear previous timeout
            if (searchTimeout) {
                clearTimeout(searchTimeout);
            }

            // Set new timeout for real-time search
            searchTimeout = setTimeout(() => {
                loadStudents(1, currentFilter, currentNumScolaireSearch, currentStatusFilter);
            }, 500);
        });
    }

    // Clear filters button
    if (clearFilters) {
        clearFilters.addEventListener('click', () => {
            currentFilter = '';
            currentNumScolaireSearch = '';
            currentStatusFilter = '';
            const statusFilterEl = document.getElementById('statusFilter');
            if (statusFilterEl) statusFilterEl.value = '';
            if (schoolFilter) schoolFilter.value = '';
            if (schoolSearch) schoolSearch.value = '';
            if (selectedSchool) selectedSchool.textContent = 'اختر...';
            if (numScolaireSearch) numScolaireSearch.value = '';
            if (schoolDropdown) schoolDropdown.style.display = 'none';
            updateClearButton();
            loadStudents(1);
        });
    }

    // Initial load
    loadSchoolsFilter();
    loadStudents(1);
});

// View eleve from modal - reuse the function from tuteurs_list
async function viewEleveFromModal(num_scolaire) {
    Swal.fire({
        title: 'جارٍ التحميل...',
        html: '<div class="spinner-border text-primary" role="status"></div>',
        allowOutsideClick: false,
        showConfirmButton: false,
        didOpen: () => { Swal.showLoading(); }
    });
    
    try {
        const response = await fetch(getApiUrlPath(`/api/user/eleves/${num_scolaire}`), {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        });
        
        const data = await response.json();
        
        if (!data.success || !data.eleve) {
            Swal.fire({
                icon: 'error',
                title: 'خطأ',
                text: data.message || 'فشل تحميل البيانات',
                confirmButtonText: 'حسنًا'
            });
            return;
        }
        
        const e = data.eleve;
        
        // Get father name
        let fatherName = '-';
        if (e.father) {
            fatherName = `${e.father.prenom_ar || ''} ${e.father.nom_ar || ''}`.trim() || '-';
        }
        
        // Get mother name
        let motherName = '-';
        if (e.mother) {
            motherName = `${e.mother.prenom_ar || ''} ${e.mother.nom_ar || ''}`.trim() || '-';
        }
        
        // Build modal content HTML
        let html = `
            <div class="eleve-details-modal" style="text-align: right;">
                <div class="eleve-info-section" style="background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%); padding: 1.5rem; border-radius: 12px; margin-bottom: 2rem; box-shadow: 0 4px 12px rgba(0,0,0,0.08);">
                    <h6 style="color: #0f033a; font-weight: 700; font-size: 1.25rem; margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 3px solid #fdae4b;">معلومات التلميذ</h6>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem;">
                        <div style="background: white; padding: 1rem 1.25rem; border-radius: 8px; border-right: 4px solid #fdae4b;">
                            <strong style="color: #64748b; font-weight: 600; font-size: 0.85rem; display: block; margin-bottom: 0.5rem;">الاسم الكامل</strong>
                            <p style="margin: 0; color: #0f1419; font-size: 1rem; font-weight: 600;">${(e.prenom || '') + ' ' + (e.nom || '')}</p>
                        </div>
                        <div style="background: white; padding: 1rem 1.25rem; border-radius: 8px; border-right: 4px solid #fdae4b;">
                            <strong style="color: #64748b; font-weight: 600; font-size: 0.85rem; display: block; margin-bottom: 0.5rem;">رقم التعريف المدرسي</strong>
                            <p style="margin: 0; color: #0f1419; font-size: 1rem; font-weight: 600;">${e.num_scolaire || '-'}</p>
                        </div>
                        <div style="background: white; padding: 1rem 1.25rem; border-radius: 8px; border-right: 4px solid #fdae4b;">
                            <strong style="color: #64748b; font-weight: 600; font-size: 0.85rem; display: block; margin-bottom: 0.5rem;">تاريخ الميلاد</strong>
                            <p style="margin: 0; color: #0f1419; font-size: 1rem; font-weight: 600;">${e.date_naiss || '-'}</p>
                        </div>
                        <div style="background: white; padding: 1rem 1.25rem; border-radius: 8px; border-right: 4px solid #fdae4b;">
                            <strong style="color: #64748b; font-weight: 600; font-size: 0.85rem; display: block; margin-bottom: 0.5rem;">الجنس</strong>
                            <p style="margin: 0; color: #0f1419; font-size: 1rem; font-weight: 600;">${e.sexe || '-'}</p>
                        </div>
                        <div style="background: white; padding: 1rem 1.25rem; border-radius: 8px; border-right: 4px solid #fdae4b;">
                            <strong style="color: #64748b; font-weight: 600; font-size: 0.85rem; display: block; margin-bottom: 0.5rem;">المستوى الدراسي</strong>
                            <p style="margin: 0; color: #0f1419; font-size: 1rem; font-weight: 600;">${e.classe_scol || e.niv_scol || '-'}</p>
                        </div>
                        <div style="background: white; padding: 1rem 1.25rem; border-radius: 8px; border-right: 4px solid #fdae4b;">
                            <strong style="color: #64748b; font-weight: 600; font-size: 0.85rem; display: block; margin-bottom: 0.5rem;">مؤسسة التربية والتعليم</strong>
                            <p style="margin: 0; color: #0f1419; font-size: 1rem; font-weight: 600;">${(e.etablissement && e.etablissement.nom_etabliss) ? e.etablissement.nom_etabliss : '-'}</p>
                        </div>
                        <div style="background: white; padding: 1rem 1.25rem; border-radius: 8px; border-right: 4px solid #fdae4b;">
                            <strong style="color: #64748b; font-weight: 600; font-size: 0.85rem; display: block; margin-bottom: 0.5rem;">اسم الأب</strong>
                            <p style="margin: 0; color: #0f1419; font-size: 1rem; font-weight: 600;">${fatherName}</p>
                        </div>
                        <div style="background: white; padding: 1rem 1.25rem; border-radius: 8px; border-right: 4px solid #fdae4b;">
                            <strong style="color: #64748b; font-weight: 600; font-size: 0.85rem; display: block; margin-bottom: 0.5rem;">اسم الأم</strong>
                            <p style="margin: 0; color: #0f1419; font-size: 1rem; font-weight: 600;">${motherName}</p>
                        </div>
                        ${'{{ session("user_role") }}' !== 'das' ? `
                        <div style="background: white; padding: 1rem 1.25rem; border-radius: 8px; border-right: 4px solid #fdae4b;">
                            <strong style="color: #64748b; font-weight: 600; font-size: 0.85rem; display: block; margin-bottom: 0.5rem;">حالة الموافقة</strong>
                            <p style="margin: 0;">
                                <span style="background: ${e.dossier_depose === 'oui' ? 'linear-gradient(135deg, #10b981, #059669)' : 'linear-gradient(135deg, #6b7280, #4b5563)'}; color: white; padding: 0.5rem 1rem; border-radius: 20px; font-size: 0.85rem; font-weight: 600;">
                                    ${e.dossier_depose === 'oui' ? 'موافق عليه' : 'قيد المراجعة'}
                                </span>
                            </p>
                        </div>
                        ` : ''}
                    </div>
                </div>
        `;
        
        // Add Father Info Section (collapsible)
        if (e.father_id && e.father) {
            const f = e.father;
            html += `
                <div class="parent-info-section" style="background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); padding: 1.5rem; border-radius: 12px; margin-bottom: 1.5rem; box-shadow: 0 4px 12px rgba(0,0,0,0.08);">
                    <div style="display: flex; align-items: center; justify-content: space-between; cursor: pointer; margin-bottom: 1rem;" onclick="toggleParentInfo('fatherInfo')">
                        <h6 style="color: #0f033a; font-weight: 700; font-size: 1.25rem; margin: 0; padding-bottom: 1rem; border-bottom: 3px solid #fdae4b; display: flex; align-items: center; gap: 0.75rem;">
                            <i class="fa-solid fa-mars" style="color: #fdae4b;"></i>
                            معلومات الأب
                        </h6>
                        <i class="fa-solid fa-chevron-down" id="fatherInfoIcon" style="color: #0f033a; font-size: 1.25rem; transition: transform 0.3s ease;"></i>
                    </div>
                    <div id="fatherInfo" style="display: none;">
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem;">
                            <div style="background: white; padding: 1rem 1.25rem; border-radius: 8px; border-right: 4px solid #fdae4b;">
                                <strong style="color: #64748b; font-weight: 600; font-size: 0.85rem; display: block; margin-bottom: 0.5rem;">الرقم الوطني (NIN)</strong>
                                <p style="margin: 0; color: #0f1419; font-size: 1rem; font-weight: 600;">${f.nin || '—'}</p>
                            </div>
                            <div style="background: white; padding: 1rem 1.25rem; border-radius: 8px; border-right: 4px solid #fdae4b;">
                                <strong style="color: #64748b; font-weight: 600; font-size: 0.85rem; display: block; margin-bottom: 0.5rem;">رقم الضمان الاجتماعي (NSS)</strong>
                                <p style="margin: 0; color: #0f1419; font-size: 1rem; font-weight: 600;">${f.nss || '—'}</p>
                            </div>
                            <div style="background: white; padding: 1rem 1.25rem; border-radius: 8px; border-right: 4px solid #fdae4b;">
                                <strong style="color: #64748b; font-weight: 600; font-size: 0.85rem; display: block; margin-bottom: 0.5rem;">لقب الأب بالعربية</strong>
                                <p style="margin: 0; color: #0f1419; font-size: 1rem; font-weight: 600;">${f.nom_ar || '—'}</p>
                            </div>
                            <div style="background: white; padding: 1rem 1.25rem; border-radius: 8px; border-right: 4px solid #fdae4b;">
                                <strong style="color: #64748b; font-weight: 600; font-size: 0.85rem; display: block; margin-bottom: 0.5rem;">اسم الأب بالعربية</strong>
                                <p style="margin: 0; color: #0f1419; font-size: 1rem; font-weight: 600;">${f.prenom_ar || '—'}</p>
                            </div>
                            <div style="background: white; padding: 1rem 1.25rem; border-radius: 8px; border-right: 4px solid #fdae4b;">
                                <strong style="color: #64748b; font-weight: 600; font-size: 0.85rem; display: block; margin-bottom: 0.5rem;">الفئة الاجتماعية</strong>
                                <p style="margin: 0; color: #0f1419; font-size: 1rem; font-weight: 600;">${f.categorie_sociale || 'غير محدد'}</p>
                            </div>
                        </div>
                        
                        ${(() => {
                            const getFileIcon = (filePath) => {
                                if (!filePath) return 'fa-file';
                                const ext = filePath.split('.').pop().toLowerCase();
                                if (ext === 'pdf') return 'fa-file-pdf';
                                if (['jpg', 'jpeg', 'png', 'gif'].includes(ext)) return 'fa-file-image';
                                return 'fa-file';
                            };
                            
                            const renderDocCard = (title, filePath) => {
                                if (!filePath) return '';
                                const icon = getFileIcon(filePath);
                                const safePath = filePath.replace(/'/g, "&#39;").replace(/"/g, "&quot;");
                                return '<div style="background: white; padding: 1rem; border-radius: 8px; border: 2px solid #e5e7eb; transition: all 0.3s ease; cursor: pointer; margin-bottom: 0.75rem;" onclick="openFileViaAPI(\'' + safePath + '\')" onmouseover="this.style.borderColor=\'#fdae4b\'; this.style.transform=\'translateY(-2px)\'" onmouseout="this.style.borderColor=\'#e5e7eb\'; this.style.transform=\'translateY(0)\'">' +
                                    '<div style="display: flex; align-items: center; gap: 0.75rem;">' +
                                    '<i class="fa-solid ' + icon + '" style="font-size: 1.5rem; color: #fdae4b;"></i>' +
                                    '<div style="flex: 1;">' +
                                    '<strong style="color: #0f033a; font-size: 0.9rem; display: block; margin-bottom: 0.25rem;">' + title + '</strong>' +
                                    '<span style="color: #64748b; font-size: 0.75rem;">انقر للفتح</span>' +
                                    '</div>' +
                                    '<i class="fa-solid fa-external-link-alt" style="color: #64748b;"></i>' +
                                    '</div>' +
                                    '</div>';
                            };
                            
                            let docsHtml = '<div style="margin-top: 1.5rem;"><h6 style="color: #0f033a; font-weight: 700; font-size: 1.1rem; margin-bottom: 1rem;">الوثائق المرفوعة</h6><div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1rem;">';
                            
                            if (f.biometric_id) docsHtml += renderDocCard('بطاقة الهوية البيومترية (الوجه الأمامي)', f.biometric_id);
                            if (f.biometric_id_back) docsHtml += renderDocCard('بطاقة الهوية البيومترية (الوجه الخلفي)', f.biometric_id_back);
                            
                            const cats = f.categorie_sociale || '';
                            if (cats === 'عديم الدخل') {
                                if (f.Certificate_of_none_income) docsHtml += renderDocCard('شهادة عدم الدخل', f.Certificate_of_none_income);
                                if (f.Certificate_of_non_affiliation_to_social_security) docsHtml += renderDocCard('شهادة عدم الانتساب للضمان الاجتماعي', f.Certificate_of_non_affiliation_to_social_security);
                            } else if (cats === 'الدخل الشهري أقل أو يساوي مبلغ الأجر الوطني الأدنى المضمون') {
                                if (f.crossed_ccp) docsHtml += renderDocCard('صك بريدي مشطوب', f.crossed_ccp);
                            }
                            
                            if (f.salary_certificate) docsHtml += renderDocCard('شهادة الراتب', f.salary_certificate);
                            
                            docsHtml += '</div></div>';
                            return docsHtml;
                        })()}
                    </div>
                </div>
            `;
        }
        
        // Add Mother Info Section (collapsible)
        if (e.mother_id && e.mother) {
            const m = e.mother;
            html += `
                <div class="parent-info-section" style="background: linear-gradient(135deg, #fce7f3 0%, #fbcfe8 100%); padding: 1.5rem; border-radius: 12px; margin-bottom: 1.5rem; box-shadow: 0 4px 12px rgba(0,0,0,0.08);">
                    <div style="display: flex; align-items: center; justify-content: space-between; cursor: pointer; margin-bottom: 1rem;" onclick="toggleParentInfo('motherInfo')">
                        <h6 style="color: #0f033a; font-weight: 700; font-size: 1.25rem; margin: 0; padding-bottom: 1rem; border-bottom: 3px solid #fdae4b; display: flex; align-items: center; gap: 0.75rem;">
                            <i class="fa-solid fa-venus" style="color: #fdae4b;"></i>
                            معلومات الأم
                        </h6>
                        <i class="fa-solid fa-chevron-down" id="motherInfoIcon" style="color: #0f033a; font-size: 1.25rem; transition: transform 0.3s ease;"></i>
                    </div>
                    <div id="motherInfo" style="display: none;">
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem;">
                            <div style="background: white; padding: 1rem 1.25rem; border-radius: 8px; border-right: 4px solid #fdae4b;">
                                <strong style="color: #64748b; font-weight: 600; font-size: 0.85rem; display: block; margin-bottom: 0.5rem;">الرقم الوطني (NIN)</strong>
                                <p style="margin: 0; color: #0f1419; font-size: 1rem; font-weight: 600;">${m.nin || '—'}</p>
                            </div>
                            <div style="background: white; padding: 1rem 1.25rem; border-radius: 8px; border-right: 4px solid #fdae4b;">
                                <strong style="color: #64748b; font-weight: 600; font-size: 0.85rem; display: block; margin-bottom: 0.5rem;">رقم الضمان الاجتماعي (NSS)</strong>
                                <p style="margin: 0; color: #0f1419; font-size: 1rem; font-weight: 600;">${m.nss || '—'}</p>
                            </div>
                            <div style="background: white; padding: 1rem 1.25rem; border-radius: 8px; border-right: 4px solid #fdae4b;">
                                <strong style="color: #64748b; font-weight: 600; font-size: 0.85rem; display: block; margin-bottom: 0.5rem;">لقب الأم بالعربية</strong>
                                <p style="margin: 0; color: #0f1419; font-size: 1rem; font-weight: 600;">${m.nom_ar || '—'}</p>
                            </div>
                            <div style="background: white; padding: 1rem 1.25rem; border-radius: 8px; border-right: 4px solid #fdae4b;">
                                <strong style="color: #64748b; font-weight: 600; font-size: 0.85rem; display: block; margin-bottom: 0.5rem;">اسم الأم بالعربية</strong>
                                <p style="margin: 0; color: #0f1419; font-size: 1rem; font-weight: 600;">${m.prenom_ar || '—'}</p>
                            </div>
                            <div style="background: white; padding: 1rem 1.25rem; border-radius: 8px; border-right: 4px solid #fdae4b;">
                                <strong style="color: #64748b; font-weight: 600; font-size: 0.85rem; display: block; margin-bottom: 0.5rem;">الفئة الاجتماعية</strong>
                                <p style="margin: 0; color: #0f1419; font-size: 1rem; font-weight: 600;">${m.categorie_sociale || 'غير محدد'}</p>
                            </div>
                        </div>
                        
                        ${(() => {
                            const getFileIcon = (filePath) => {
                                if (!filePath) return 'fa-file';
                                const ext = filePath.split('.').pop().toLowerCase();
                                if (ext === 'pdf') return 'fa-file-pdf';
                                if (['jpg', 'jpeg', 'png', 'gif'].includes(ext)) return 'fa-file-image';
                                return 'fa-file';
                            };
                            
                            const renderDocCard = (title, filePath) => {
                                if (!filePath) return '';
                                const icon = getFileIcon(filePath);
                                const safePath = filePath.replace(/'/g, "&#39;").replace(/"/g, "&quot;");
                                return '<div style="background: white; padding: 1rem; border-radius: 8px; border: 2px solid #e5e7eb; transition: all 0.3s ease; cursor: pointer; margin-bottom: 0.75rem;" onclick="openFileViaAPI(\'' + safePath + '\')" onmouseover="this.style.borderColor=\'#fdae4b\'; this.style.transform=\'translateY(-2px)\'" onmouseout="this.style.borderColor=\'#e5e7eb\'; this.style.transform=\'translateY(0)\'">' +
                                    '<div style="display: flex; align-items: center; gap: 0.75rem;">' +
                                    '<i class="fa-solid ' + icon + '" style="font-size: 1.5rem; color: #fdae4b;"></i>' +
                                    '<div style="flex: 1;">' +
                                    '<strong style="color: #0f033a; font-size: 0.9rem; display: block; margin-bottom: 0.25rem;">' + title + '</strong>' +
                                    '<span style="color: #64748b; font-size: 0.75rem;">انقر للفتح</span>' +
                                    '</div>' +
                                    '<i class="fa-solid fa-external-link-alt" style="color: #64748b;"></i>' +
                                    '</div>' +
                                    '</div>';
                            };
                            
                            let docsHtml = '<div style="margin-top: 1.5rem;"><h6 style="color: #0f033a; font-weight: 700; font-size: 1.1rem; margin-bottom: 1rem;">الوثائق المرفوعة</h6><div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1rem;">';
                            
                            if (m.biometric_id) docsHtml += renderDocCard('بطاقة الهوية البيومترية (الوجه الأمامي)', m.biometric_id);
                            if (m.biometric_id_back) docsHtml += renderDocCard('بطاقة الهوية البيومترية (الوجه الخلفي)', m.biometric_id_back);
                            
                            const cats = m.categorie_sociale || '';
                            if (cats === 'عديم الدخل') {
                                if (m.Certificate_of_none_income) docsHtml += renderDocCard('شهادة عدم الدخل', m.Certificate_of_none_income);
                                if (m.Certificate_of_non_affiliation_to_social_security) docsHtml += renderDocCard('شهادة عدم الانتساب للضمان الاجتماعي', m.Certificate_of_non_affiliation_to_social_security);
                            } else if (cats === 'الدخل الشهري أقل أو يساوي مبلغ الأجر الوطني الأدنى المضمون') {
                                if (m.crossed_ccp) docsHtml += renderDocCard('صك بريدي مشطوب', m.crossed_ccp);
                            }
                            
                            if (m.salary_certificate) docsHtml += renderDocCard('شهادة الراتب', m.salary_certificate);
                            
                            docsHtml += '</div></div>';
                            return docsHtml;
                        })()}
                    </div>
                </div>
            `;
        }
        
        // Add Tuteur Info Section (collapsible)
        if (e.tuteur) {
            const t = e.tuteur;
            
            let sectionTitle = '';
            if (e.relation_tuteur === 1 || e.relation_tuteur === '1') {
                sectionTitle = 'معلومات الولي';
            } else if (e.relation_tuteur === 2 || e.relation_tuteur === '2') {
                sectionTitle = 'معلومات الولي';
            } else if (e.relation_tuteur === 3 || e.relation_tuteur === '3') {
                sectionTitle = 'معلومات الوصي';
            } else {
                sectionTitle = 'معلومات الوصي/الولي';
            }
            
            html += `
                <div class="parent-info-section" style="background: linear-gradient(135deg, #e0f2fe 0%, #bae6fd 100%); padding: 1.5rem; border-radius: 12px; margin-bottom: 1.5rem; box-shadow: 0 4px 12px rgba(0,0,0,0.08);">
                    <div style="display: flex; align-items: center; justify-content: space-between; cursor: pointer; margin-bottom: 1rem;" onclick="toggleParentInfo('tuteurInfo')">
                        <h6 style="color: #0f033a; font-weight: 700; font-size: 1.25rem; margin: 0; padding-bottom: 1rem; border-bottom: 3px solid #fdae4b; display: flex; align-items: center; gap: 0.75rem;">
                            <i class="fa-solid fa-user-circle" style="color: #fdae4b;"></i>
                            ${sectionTitle}
                        </h6>
                        <i class="fa-solid fa-chevron-down" id="tuteurInfoIcon" style="color: #0f033a; font-size: 1.25rem; transition: transform 0.3s ease;"></i>
                    </div>
                    <div id="tuteurInfo" style="display: none;">
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem;">
                            <div style="background: white; padding: 1rem 1.25rem; border-radius: 8px; border-right: 4px solid #fdae4b;">
                                <strong style="color: #64748b; font-weight: 600; font-size: 0.85rem; display: block; margin-bottom: 0.5rem;">الرقم الوطني (NIN)</strong>
                                <p style="margin: 0; color: #0f1419; font-size: 1rem; font-weight: 600;">${t.nin || '—'}</p>
                            </div>
                            <div style="background: white; padding: 1rem 1.25rem; border-radius: 8px; border-right: 4px solid #fdae4b;">
                                <strong style="color: #64748b; font-weight: 600; font-size: 0.85rem; display: block; margin-bottom: 0.5rem;">رقم الضمان الاجتماعي (NSS)</strong>
                                <p style="margin: 0; color: #0f1419; font-size: 1rem; font-weight: 600;">${t.nss || '—'}</p>
                            </div>
                            <div style="background: white; padding: 1rem 1.25rem; border-radius: 8px; border-right: 4px solid #fdae4b;">
                                <strong style="color: #64748b; font-weight: 600; font-size: 0.85rem; display: block; margin-bottom: 0.5rem;">لقب الوصي/الولي بالعربية</strong>
                                <p style="margin: 0; color: #0f1419; font-size: 1rem; font-weight: 600;">${t.nom_ar || '—'}</p>
                            </div>
                            <div style="background: white; padding: 1rem 1.25rem; border-radius: 8px; border-right: 4px solid #fdae4b;">
                                <strong style="color: #64748b; font-weight: 600; font-size: 0.85rem; display: block; margin-bottom: 0.5rem;">اسم الوصي/الولي بالعربية</strong>
                                <p style="margin: 0; color: #0f1419; font-size: 1rem; font-weight: 600;">${t.prenom_ar || '—'}</p>
                            </div>
                        </div>
                        
                        ${(() => {
                            const docs = [];
                            if (t.biometric_id) docs.push({ title: 'بطاقة الهوية البيومترية (الوجه الأمامي)', path: t.biometric_id });
                            if (t.biometric_id_back) docs.push({ title: 'بطاقة الهوية البيومترية (الوجه الخلفي)', path: t.biometric_id_back });
                            if (t.Certificate_of_none_income) docs.push({ title: 'شهادة عدم الدخل', path: t.Certificate_of_none_income });
                            if (t.Certificate_of_non_affiliation_to_social_security) docs.push({ title: 'شهادة عدم الانتساب للضمان الاجتماعي', path: t.Certificate_of_non_affiliation_to_social_security });
                            if (t.crossed_ccp) docs.push({ title: 'صك بريدي مشطوب', path: t.crossed_ccp });
                            if (t.salary_certificate) docs.push({ title: 'شهادة الراتب', path: t.salary_certificate });
                            
                            if (docs.length === 0) return '';
                            
                            const getFileIcon = (filePath) => {
                                if (!filePath) return 'fa-file';
                                const ext = filePath.split('.').pop().toLowerCase();
                                if (ext === 'pdf') return 'fa-file-pdf';
                                if (['jpg', 'jpeg', 'png', 'gif'].includes(ext)) return 'fa-file-image';
                                return 'fa-file';
                            };
                            
                            const renderDocCard = (title, filePath) => {
                                if (!filePath) return '';
                                const icon = getFileIcon(filePath);
                                const safePath = filePath.replace(/'/g, "&#39;").replace(/"/g, "&quot;");
                                return '<div style="background: white; padding: 1rem; border-radius: 8px; border: 2px solid #e5e7eb; transition: all 0.3s ease; cursor: pointer; margin-bottom: 0.75rem;" onclick="openFileViaAPI(\'' + safePath + '\')" onmouseover="this.style.borderColor=\'#fdae4b\'; this.style.transform=\'translateY(-2px)\'" onmouseout="this.style.borderColor=\'#e5e7eb\'; this.style.transform=\'translateY(0)\'">' +
                                    '<div style="display: flex; align-items: center; gap: 0.75rem;">' +
                                    '<i class="fa-solid ' + icon + '" style="font-size: 1.5rem; color: #fdae4b;"></i>' +
                                    '<div style="flex: 1;">' +
                                    '<strong style="color: #0f033a; font-size: 0.9rem; display: block; margin-bottom: 0.25rem;">' + title + '</strong>' +
                                    '<span style="color: #64748b; font-size: 0.75rem;">انقر للفتح</span>' +
                                    '</div>' +
                                    '<i class="fa-solid fa-external-link-alt" style="color: #64748b;"></i>' +
                                    '</div>' +
                                    '</div>';
                            };
                            
                            let docsHtml = '<div style="margin-top: 1.5rem;"><h6 style="color: #0f033a; font-weight: 700; font-size: 1.1rem; margin-bottom: 1rem;">الوثائق المرفوعة</h6><div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1rem;">';
                            
                            docs.forEach(doc => {
                                docsHtml += renderDocCard(doc.title, doc.path);
                            });
                            
                            docsHtml += '</div></div>';
                            return docsHtml;
                        })()}
                    </div>
                </div>
            `;
        }
        
        html += `</div>`;
        
        Swal.fire({
            title: 'تفاصيل التلميذ',
            html: html,
            width: '90%',
            maxWidth: '1200px',
            showCloseButton: true,
            confirmButtonText: 'إغلاق',
            confirmButtonColor: '#0f033a',
            customClass: {
                popup: 'swal-tuteur-modal',
                htmlContainer: 'swal-tuteur-content'
            }
        });
        
    } catch (error) {
        console.error('Error loading eleve data:', error);
        Swal.fire({
            icon: 'error',
            title: 'خطأ',
            text: 'حدث خطأ أثناء تحميل البيانات',
            confirmButtonText: 'حسنًا'
        });
    }
}

// Toggle parent info expand/collapse
function toggleParentInfo(parentId) {
    const infoDiv = document.getElementById(parentId);
    const icon = document.getElementById(parentId + 'Icon');
    
    if (!infoDiv || !icon) return;
    
    if (infoDiv.style.display === 'none') {
        infoDiv.style.display = 'block';
        icon.style.transform = 'rotate(180deg)';
    } else {
        infoDiv.style.display = 'none';
        icon.style.transform = 'rotate(0deg)';
    }
}

// Helper function to open files via API
function openFileViaAPI(filePath) {
    if (!filePath) return;
    
    // Show loading indicator immediately
    if (window.Swal) {
        Swal.fire({
            title: 'جارٍ التحميل...',
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: () => Swal.showLoading(),
            timer: 10000,
            timerProgressBar: true
        });
    }
    
    const apiUrl = getApiUrlPath('/api/user/files/' + encodeURIComponent(filePath));
    const token = localStorage.getItem('api_token');
    
    const headers = {
        'Accept': 'application/octet-stream, */*'
    };
    
    if (token) {
        headers['Authorization'] = 'Bearer ' + token;
    }
    
    fetch(apiUrl, {
        method: 'GET',
        headers: headers,
        credentials: 'include'
    })
    .then(response => {
        if (!response.ok) {
            const contentType = response.headers.get('content-type');
            if (contentType && contentType.includes('application/json')) {
                return response.json().then(data => {
                    throw new Error(data.message || 'Failed to load file');
                });
            }
            throw new Error('Failed to load file: ' + response.status);
        }
        return response.blob();
    })
    .then(blob => {
        if (window.Swal) {
            Swal.close();
        }
        const url = window.URL.createObjectURL(blob);
        window.open(url, '_blank');
        setTimeout(() => window.URL.revokeObjectURL(url), 100);
    })
    .catch(error => {
        console.error('Error loading file:', error);
        if (window.Swal) {
            Swal.fire({
                icon: 'error',
                title: 'خطأ',
                text: 'فشل تحميل الملف: ' + error.message
            });
        }
    });
}

// Generate istimara PDF
async function generateIstimaraPDF(num_scolaire) {
    if (!num_scolaire) {
        Swal.fire({
            icon: 'error',
            title: 'خطأ',
            text: 'رقم التعريف المدرسي مفقود',
            confirmButtonText: 'حسنًا'
        });
        return;
    }

    Swal.fire({
        title: 'جارٍ التوليد...',
        html: 'جاري توليد ملف PDF...',
        allowOutsideClick: false,
        showConfirmButton: false,
        didOpen: () => { Swal.showLoading(); }
    });

    try {
        const response = await fetch(getApiUrlPath(`/api/user/eleves/${num_scolaire}/istimara/generate`), {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        const data = await response.json();

        if (!response.ok || !data.success) {
            throw new Error(data.message || 'فشل توليد PDF');
        }

        Swal.close();
        const pdfUrl = (data.url || `/eleves/${num_scolaire}/istimara`) + '?regenerate=1';
        window.open(pdfUrl, '_blank');

    } catch (error) {
        console.error('Error generating PDF:', error);
        Swal.fire({
            icon: 'error',
            title: 'خطأ',
            text: error.message || 'حدث خطأ أثناء توليد PDF',
            confirmButtonText: 'حسنًا'
        });
    }
}

// Approve eleve from modal
async function approveEleveFromModal(num_scolaire) {
    const result = await Swal.fire({
        title: 'تأكيد الموافقة',
        text: `هل تريد الموافقة على التلميذ رقم ${num_scolaire}؟`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'نعم، أوافق',
        cancelButtonText: 'إلغاء',
        reverseButtons: true,
        confirmButtonColor: '#10b981'
    });
    
    if (result.isConfirmed) {
        try {
            const response = await fetch(getApiUrlPath(`/api/user/eleves/${num_scolaire}/approve`), {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json'
                }
            });
            
            const data = await response.json();
            
            if (data.success) {
                await Swal.fire({
                    icon: 'success',
                    title: 'تمت الموافقة',
                    text: 'تمت الموافقة على التلميذ بنجاح',
                    confirmButtonText: 'حسنًا'
                });
                window.location.reload();
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ',
                    text: data.message || 'فشلت الموافقة',
                    confirmButtonText: 'حسنًا'
                });
            }
        } catch (error) {
            Swal.fire({
                icon: 'error',
                title: 'خطأ',
                text: 'حدث خطأ أثناء الموافقة',
                confirmButtonText: 'حسنًا'
            });
        }
    }
}

// Comment eleve - Enhanced with rich styling
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
        const response = await fetch(getApiUrlPath(`/api/user/eleves/${num_scolaire}/comments`), {
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
            const response = await fetch(getApiUrlPath(`/api/user/eleves/${num_scolaire}/comments`), {
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

// Delete eleve from modal
async function deleteEleveFromModal(num_scolaire) {
    const result = await Swal.fire({
        title: 'تأكيد الحذف',
        text: `هل أنت متأكد من حذف التلميذ رقم ${num_scolaire}؟ سيتم فقدان كل البيانات المرتبطة.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'نعم، احذف',
        cancelButtonText: 'إلغاء',
        reverseButtons: true,
        confirmButtonColor: '#ef4444'
    });
    
    if (result.isConfirmed) {
        try {
            const response = await fetch(getApiUrlPath(`/api/user/eleves/${num_scolaire}`), {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json'
                }
            });
            
            const data = await response.json();
            
            if (data.success) {
                await Swal.fire({
                    icon: 'success',
                    title: 'تم الحذف',
                    text: 'تم حذف التلميذ بنجاح',
                    confirmButtonText: 'حسنًا'
                });
                window.location.reload();
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ',
                    text: data.message || 'فشل الحذف',
                    confirmButtonText: 'حسنًا'
                });
            }
        } catch (error) {
            Swal.fire({
                icon: 'error',
                title: 'خطأ',
                text: 'حدث خطأ أثناء الحذف',
                confirmButtonText: 'حسنًا'
            });
        }
    }
}

// DAS Accept Eleve
async function dasAcceptEleve(num_scolaire) {
    // Check if token is available
    if (!API_TOKEN || API_TOKEN === '') {
        Swal.fire({
            icon: 'error',
            title: 'خطأ في المصادقة',
            text: 'الرمز المميز غير متوفر. يرجى تسجيل الدخول مرة أخرى.',
            confirmButtonText: 'حسنًا',
            confirmButtonColor: '#ef4444'
        });
        console.error('API_TOKEN is missing or empty:', API_TOKEN);
        return;
    }

    const result = await Swal.fire({
        title: 'تأكيد القبول',
        text: 'هل أنت متأكد من قبول هذا الطالب؟',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'نعم، قبول',
        cancelButtonText: 'إلغاء',
        confirmButtonColor: '#10b981',
        cancelButtonColor: '#6b7280',
        reverseButtons: true
    });

    if (result.isConfirmed) {
        try {
            const response = await fetch(getApiUrlPath(`/api/das/eleves/${num_scolaire}/accept`), {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${API_TOKEN}`,
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            });
            
            if (response.status === 401) {
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ في المصادقة',
                    text: 'انتهت صلاحية الجلسة. يرجى تسجيل الدخول مرة أخرى.',
                    confirmButtonText: 'حسنًا',
                    confirmButtonColor: '#ef4444'
                }).then(() => {
                    window.location.href = '/user/login';
                });
                return;
            }

            const data = await response.json();

            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'تم القبول',
                    text: 'تم قبول الطالب بنجاح',
                    confirmButtonText: 'حسنًا',
                    confirmButtonColor: '#10b981'
                });
                loadStudents(currentPage, currentFilter, currentNumScolaireSearch, currentStatusFilter);
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ',
                    text: data.message || 'فشل قبول الطالب',
                    confirmButtonText: 'حسنًا',
                    confirmButtonColor: '#ef4444'
                });
            }
        } catch (error) {
            Swal.fire({
                icon: 'error',
                title: 'خطأ',
                text: 'حدث خطأ أثناء قبول الطالب',
                confirmButtonText: 'حسنًا',
                confirmButtonColor: '#ef4444'
            });
        }
    }
}

// Show refuse motif modal (read-only) - DAS
function showRefuseModalFromRow(btn) {
    const tr = btn.closest('tr');
    const motif = (tr.dataset.motif || '').replace(/&quot;/g, '"').replace(/&lt;/g, '<').replace(/&gt;/g, '>').replace(/&amp;/g, '&');
    const cnas = parseInt(tr.dataset.cnas, 10) || 0;
    const casnos = parseInt(tr.dataset.casnos, 10) || 0;
    showRefuseModalReadOnly(motif, cnas, casnos);
}

function showRefuseModalReadOnly(motif, cnasRefuse, casnosRefuse) {
    const cnasChecked = cnasRefuse === 1;
    const casnosChecked = casnosRefuse === 1;
    const motifSafe = (motif || '—').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/\n/g, '<br>');
    Swal.fire({
        title: 'سبب الرفض',
        html: `
            <div class="swal-decline-form swal-decline-readonly">
                <label class="swal-decline-label">سبب الرفض</label>
                <div class="swal-decline-readonly-text">${motifSafe}</div>
                <div class="swal-decline-checkboxes swal-decline-readonly-checks">
                    <span class="swal-decline-check readonly"><input type="checkbox" ${cnasChecked ? 'checked' : ''} disabled class="swal-decline-checkbox"> <span>CNAS</span></span>
                    <span class="swal-decline-check readonly"><input type="checkbox" ${casnosChecked ? 'checked' : ''} disabled class="swal-decline-checkbox"> <span>CASNOS</span></span>
                </div>
            </div>
        `,
        showConfirmButton: true,
        confirmButtonText: 'حسنًا',
        confirmButtonColor: '#0f033a',
        customClass: { popup: 'swal-decline-popup swal-decline-readonly-popup' }
    });
}

// Comité Wilaya: Show refuse modal with Edit button
function showRefuseModalFromRowComite(btn) {
    const tr = btn.closest('tr');
    const motif = (tr.dataset.motif || '').replace(/&quot;/g, '"').replace(/&lt;/g, '<').replace(/&gt;/g, '>').replace(/&amp;/g, '&');
    const cnas = parseInt(tr.dataset.cnas, 10) || 0;
    const casnos = parseInt(tr.dataset.casnos, 10) || 0;
    const numScolaire = tr.dataset.numScolaire || '';
    showRefuseModalComiteWithEdit(numScolaire, motif, cnas, casnos);
}

function showRefuseModalComiteWithEdit(num_scolaire, motif, cnasRefuse, casnosRefuse) {
    const cnasChecked = cnasRefuse === 1;
    const casnosChecked = casnosRefuse === 1;
    const motifSafe = (motif || '—').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/\n/g, '<br>');
    Swal.fire({
        title: 'سبب الرفض',
        html: `
            <div class="swal-decline-form swal-decline-readonly">
                <label class="swal-decline-label">سبب الرفض</label>
                <div class="swal-decline-readonly-text">${motifSafe}</div>
                <div class="swal-decline-checkboxes swal-decline-readonly-checks">
                    <span class="swal-decline-check readonly"><input type="checkbox" ${cnasChecked ? 'checked' : ''} disabled class="swal-decline-checkbox"> <span>CNAS</span></span>
                    <span class="swal-decline-check readonly"><input type="checkbox" ${casnosChecked ? 'checked' : ''} disabled class="swal-decline-checkbox"> <span>CASNOS</span></span>
                </div>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'تعديل',
        cancelButtonText: 'إغلاق',
        confirmButtonColor: '#2563eb',
        cancelButtonColor: '#6b7280',
        reverseButtons: true,
        customClass: { popup: 'swal-decline-popup swal-decline-readonly-popup' }
    }).then((result) => {
        if (result.isConfirmed) {
            openEditRefuseModalEleve(num_scolaire, motif || '', cnasRefuse, casnosRefuse);
        }
    });
}

async function openEditRefuseModalEleve(num_scolaire, motif, cnasRefuse, casnosRefuse) {
    const result = await Swal.fire({
        title: 'تعديل سبب الرفض',
        html: `
            <div class="swal-decline-form">
                <label class="swal-decline-label">سبب الرفض</label>
                <textarea id="swal-edit-motif" class="swal-decline-textarea" rows="3">${(motif || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;')}</textarea>
                <div class="swal-decline-checkboxes mt-3">
                    <label class="swal-decline-check"><input type="checkbox" id="swal-edit-cnas" class="swal-decline-checkbox" ${cnasRefuse === 1 ? 'checked' : ''}> <span>CNAS</span></label>
                    <label class="swal-decline-check"><input type="checkbox" id="swal-edit-casnos" class="swal-decline-checkbox" ${casnosRefuse === 1 ? 'checked' : ''}> <span>CASNOS</span></label>
                </div>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'حفظ',
        cancelButtonText: 'إلغاء',
        confirmButtonColor: '#10b981',
        cancelButtonColor: '#6b7280',
        reverseButtons: true,
        preConfirm: () => ({
            motif: document.getElementById('swal-edit-motif').value.trim(),
            cnas_refuse: document.getElementById('swal-edit-cnas').checked ? 1 : 0,
            casnos_refuse: document.getElementById('swal-edit-casnos').checked ? 1 : 0
        })
    });
    if (result.isConfirmed && result.value) {
        try {
            const response = await fetch(getApiUrlPath(`/api/comite_wilaya/eleves/${num_scolaire}/refuse-details`), {
                method: 'PATCH',
                headers: {
                    'Authorization': `Bearer ${API_TOKEN}`,
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(result.value)
            });
            const data = await response.json();
            if (response.ok && data.success) {
                Swal.fire({ icon: 'success', title: 'تم الحفظ', text: 'تم تحديث سبب الرفض بنجاح', confirmButtonColor: '#10b981' });
                loadStudents(currentPage, currentFilter, currentNumScolaireSearch, currentStatusFilter);
            } else {
                Swal.fire({ icon: 'error', title: 'خطأ', text: data.message || 'فشل الحفظ', confirmButtonColor: '#ef4444' });
            }
        } catch (e) {
            Swal.fire({ icon: 'error', title: 'خطأ', text: 'حدث خطأ أثناء الحفظ', confirmButtonColor: '#ef4444' });
        }
    }
}

// Comité Wilaya Accept/Decline Eleve
async function comiteAcceptEleve(num_scolaire) {
    if (!API_TOKEN) {
        Swal.fire({ icon: 'error', title: 'خطأ في المصادقة', text: 'الرمز المميز غير متوفر. يرجى تسجيل الدخول مرة أخرى.', confirmButtonColor: '#ef4444' });
        return;
    }
    const result = await Swal.fire({
        title: 'تأكيد القبول',
        text: 'هل أنت متأكد من قبول هذا الطالب؟',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'نعم، قبول',
        cancelButtonText: 'إلغاء',
        confirmButtonColor: '#10b981',
        cancelButtonColor: '#6b7280',
        reverseButtons: true
    });
    if (result.isConfirmed) {
        try {
            const response = await fetch(getApiUrlPath(`/api/comite_wilaya/eleves/${num_scolaire}/accept`), {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${API_TOKEN}`,
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            });
            const data = await response.json();
            if (response.ok && data.success) {
                Swal.fire({ icon: 'success', title: 'تم القبول', text: 'تم قبول الطالب بنجاح', confirmButtonColor: '#10b981' });
                loadStudents(currentPage, currentFilter, currentNumScolaireSearch, currentStatusFilter);
            } else {
                Swal.fire({ icon: 'error', title: 'خطأ', text: data.message || 'فشل القبول', confirmButtonColor: '#ef4444' });
            }
        } catch (e) {
            Swal.fire({ icon: 'error', title: 'خطأ', text: 'حدث خطأ أثناء القبول', confirmButtonColor: '#ef4444' });
        }
    }
}

async function comiteDeclineEleve(num_scolaire, btn) {
    if (!API_TOKEN) {
        Swal.fire({ icon: 'error', title: 'خطأ في المصادقة', text: 'الرمز المميز غير متوفر. يرجى تسجيل الدخول مرة أخرى.', confirmButtonColor: '#ef4444' });
        return;
    }
    let motif = '', cnas = 0, casnos = 0;
    if (btn) {
        const row = btn.closest('tr');
        if (row && row.hasAttribute('data-motif')) {
            motif = (row.getAttribute('data-motif') || '').replace(/&quot;/g, '"').replace(/&amp;/g, '&').replace(/&lt;/g, '<').replace(/&gt;/g, '>');
            cnas = parseInt(row.getAttribute('data-cnas'), 10) || 0;
            casnos = parseInt(row.getAttribute('data-casnos'), 10) || 0;
        }
    }
    const motifEscaped = (motif || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    const result = await Swal.fire({
        title: 'رفض الطالب',
        html: `
            <div class="swal-decline-form">
                <label class="swal-decline-label">سبب الرفض</label>
                <textarea id="swal-motif" class="swal-decline-textarea" placeholder="أدخل سبب الرفض..." rows="3" required>${motifEscaped}</textarea>
                <div class="swal-decline-checkboxes mt-3">
                    <label class="swal-decline-check"><input type="checkbox" id="swal-cnas" class="swal-decline-checkbox" ${cnas ? 'checked' : ''}> <span>CNAS</span></label>
                    <label class="swal-decline-check"><input type="checkbox" id="swal-casnos" class="swal-decline-checkbox" ${casnos ? 'checked' : ''}> <span>CASNOS</span></label>
                </div>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'رفض',
        cancelButtonText: 'إلغاء',
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        reverseButtons: true,
        customClass: { popup: 'swal-decline-popup' },
        preConfirm: () => {
            const motifVal = document.getElementById('swal-motif').value.trim();
            if (!motifVal) { Swal.showValidationMessage('يرجى إدخال سبب الرفض'); return false; }
            return { motif: motifVal, cnas_refuse: document.getElementById('swal-cnas').checked ? 1 : 0, casnos_refuse: document.getElementById('swal-casnos').checked ? 1 : 0 };
        }
    });
    if (result.isConfirmed && result.value) {
        try {
            const response = await fetch(getApiUrlPath(`/api/comite_wilaya/eleves/${num_scolaire}/decline`), {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${API_TOKEN}`,
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(result.value)
            });
            const data = await response.json();
            if (response.ok && data.success) {
                Swal.fire({ icon: 'success', title: 'تم الرفض', text: 'تم رفض الطالب بنجاح', confirmButtonColor: '#10b981' });
                loadStudents(currentPage, currentFilter, currentNumScolaireSearch, currentStatusFilter);
            } else {
                Swal.fire({ icon: 'error', title: 'خطأ', text: data.message || 'فشل الرفض', confirmButtonColor: '#ef4444' });
            }
        } catch (e) {
            Swal.fire({ icon: 'error', title: 'خطأ', text: 'حدث خطأ أثناء الرفض', confirmButtonColor: '#ef4444' });
        }
    }
}

// ATR (Antenne Régionale) Accept Eleve
async function antrAcceptEleve(num_scolaire) {
    if (!API_TOKEN) {
        Swal.fire({ icon: 'error', title: 'خطأ في المصادقة', text: 'الرمز المميز غير متوفر. يرجى تسجيل الدخول مرة أخرى.', confirmButtonColor: '#ef4444' });
        return;
    }
    const result = await Swal.fire({
        title: 'تأكيد القبول النهائي',
        text: 'هل أنت متأكد من القبول النهائي لهذا الطالب؟',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'نعم، قبول نهائي',
        cancelButtonText: 'إلغاء',
        confirmButtonColor: '#10b981',
        cancelButtonColor: '#6b7280',
        reverseButtons: true
    });
    if (result.isConfirmed) {
        try {
            const response = await fetch(getApiUrlPath(`/api/antr/eleves/${num_scolaire}/accept`), {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${API_TOKEN}`,
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            });
            const data = await response.json();
            if (response.ok && data.success) {
                Swal.fire({ icon: 'success', title: 'تم القبول النهائي', text: 'تم قبول الطالب نهائيا بنجاح', confirmButtonColor: '#10b981' });
                loadStudents(currentPage, currentFilter, currentNumScolaireSearch, currentStatusFilter);
            } else {
                Swal.fire({ icon: 'error', title: 'خطأ', text: data.message || 'فشل القبول', confirmButtonColor: '#ef4444' });
            }
        } catch (e) {
            Swal.fire({ icon: 'error', title: 'خطأ', text: 'حدث خطأ أثناء القبول', confirmButtonColor: '#ef4444' });
        }
    }
}

async function antrDeclineEleve(num_scolaire) {
    if (!API_TOKEN) {
        Swal.fire({ icon: 'error', title: 'خطأ في المصادقة', text: 'الرمز المميز غير متوفر. يرجى تسجيل الدخول مرة أخرى.', confirmButtonColor: '#ef4444' });
        return;
    }
    const result = await Swal.fire({
        title: 'رفض الطالب',
        html: `<div class="swal-decline-form"><label class="swal-decline-label">سبب الرفض</label><textarea id="swal-motif" class="swal-decline-textarea" placeholder="أدخل سبب الرفض..." rows="3" required></textarea></div>`,
        showCancelButton: true,
        confirmButtonText: 'رفض',
        cancelButtonText: 'إلغاء',
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        reverseButtons: true,
        preConfirm: () => {
            const motifVal = document.getElementById('swal-motif').value.trim();
            if (!motifVal) { Swal.showValidationMessage('يرجى إدخال سبب الرفض'); return false; }
            return { motif: motifVal };
        }
    });
    if (result.isConfirmed && result.value) {
        try {
            const response = await fetch(getApiUrlPath(`/api/antr/eleves/${num_scolaire}/decline`), {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${API_TOKEN}`,
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(result.value)
            });
            const data = await response.json();
            if (response.ok && data.success) {
                Swal.fire({ icon: 'success', title: 'تم الرفض', text: 'تم رفض الطالب بنجاح', confirmButtonColor: '#10b981' });
                loadStudents(currentPage, currentFilter, currentNumScolaireSearch, currentStatusFilter);
            } else {
                Swal.fire({ icon: 'error', title: 'خطأ', text: data.message || 'فشل الرفض', confirmButtonColor: '#ef4444' });
            }
        } catch (e) {
            Swal.fire({ icon: 'error', title: 'خطأ', text: 'حدث خطأ أثناء الرفض', confirmButtonColor: '#ef4444' });
        }
    }
}

// DAS Decline Eleve
async function dasDeclineEleve(num_scolaire) {
    // Check if token is available
    if (!API_TOKEN) {
        Swal.fire({
            icon: 'error',
            title: 'خطأ في المصادقة',
            text: 'الرمز المميز غير متوفر. يرجى تسجيل الدخول مرة أخرى.',
            confirmButtonText: 'حسنًا',
            confirmButtonColor: '#ef4444'
        });
        return;
    }

    const result = await Swal.fire({
        title: 'رفض الطالب',
        html: `
            <div class="swal-decline-form">
                <label class="swal-decline-label">سبب الرفض</label>
                <textarea id="swal-motif" class="swal-decline-textarea" placeholder="أدخل سبب الرفض..." rows="3" required></textarea>
                <div class="swal-decline-checkboxes">
                    <label class="swal-decline-check">
                        <input type="checkbox" id="swal-cnas" class="swal-decline-checkbox">
                        <span>CNAS</span>
                    </label>
                    <label class="swal-decline-check">
                        <input type="checkbox" id="swal-casnos" class="swal-decline-checkbox">
                        <span>CASNOS</span>
                    </label>
                </div>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'رفض',
        cancelButtonText: 'إلغاء',
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        reverseButtons: true,
        customClass: { popup: 'swal-decline-popup' },
        preConfirm: () => {
            const motif = document.getElementById('swal-motif').value.trim();
            if (!motif) {
                Swal.showValidationMessage('يرجى إدخال سبب الرفض');
                return false;
            }
            return {
                motif,
                cnas_refuse: document.getElementById('swal-cnas').checked ? 1 : 0,
                casnos_refuse: document.getElementById('swal-casnos').checked ? 1 : 0
            };
        }
    });

    if (result.isConfirmed && result.value) {
        try {
            const response = await fetch(getApiUrlPath(`/api/das/eleves/${num_scolaire}/decline`), {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${API_TOKEN}`,
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(result.value)
            });

            const data = await response.json();
            
            if (response.status === 401) {
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ في المصادقة',
                    text: 'انتهت صلاحية الجلسة. يرجى تسجيل الدخول مرة أخرى.',
                    confirmButtonText: 'حسنًا',
                    confirmButtonColor: '#ef4444'
                }).then(() => { window.location.href = '/user/login'; });
                return;
            }

            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'تم الرفض',
                    text: 'تم رفض الطالب بنجاح',
                    confirmButtonText: 'حسنًا',
                    confirmButtonColor: '#10b981'
                });
                loadStudents(currentPage, currentFilter, currentNumScolaireSearch, currentStatusFilter);
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ',
                    text: data.message || 'فشل رفض الطالب',
                    confirmButtonText: 'حسنًا',
                    confirmButtonColor: '#ef4444'
                });
            }
        } catch (error) {
            Swal.fire({
                icon: 'error',
                title: 'خطأ',
                text: 'حدث خطأ أثناء رفض الطالب',
                confirmButtonText: 'حسنًا',
                confirmButtonColor: '#ef4444'
            });
        }
    }
}

// Show appeal details for admin review
async function showAdminAppeal(num_scolaire) {
    Swal.fire({ title: 'جارٍ التحميل...', html: '<div class="spinner-border text-primary" role="status"></div>', allowOutsideClick: false, showConfirmButton: false });
    try {
        const response = await fetch(getApiUrlPath(`/api/user/eleves/${num_scolaire}/appeal`), {
            credentials: 'include',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                'Accept': 'application/json'
            }
        });
        const result = await response.json();
        if (!result.success) {
            Swal.fire({ icon: 'error', title: 'خطأ', text: result.message || 'فشل تحميل البيانات', confirmButtonColor: '#ef4444' });
            return;
        }
        const d = result.data;
        const docLink = d.appeal_document ? `<a href="${getApiUrlPath('/api/user/files/' + d.appeal_document)}" target="_blank" style="color:#6366f1;font-weight:600;text-decoration:underline;"><i class="fa-solid fa-file-arrow-down"></i> تحميل الوثيقة المرفقة</a>` : '<span style="color:#9ca3af;">لا توجد وثيقة</span>';

        Swal.fire({
            title: '<i class="fa-solid fa-gavel" style="color:#8b5cf6;margin-left:0.5rem;"></i> تفاصيل الطعن',
            html: `
                <div style="text-align:right;direction:rtl;">
                    <div style="background:#f9fafb;border-radius:10px;padding:1rem;margin-bottom:0.75rem;">
                        <label style="font-weight:700;color:#374151;display:block;margin-bottom:0.5rem;"><i class="fa-solid fa-user-graduate"></i> التلميذ:</label>
                        <p style="margin:0;color:#1f2937;">${d.nom || ''} ${d.prenom || ''} (${d.num_scolaire})</p>
                    </div>
                    <div style="background:#f0f0ff;border-radius:10px;padding:1rem;margin-bottom:0.75rem;">
                        <label style="font-weight:700;color:#374151;display:block;margin-bottom:0.5rem;"><i class="fa-solid fa-pen"></i> نص الطعن:</label>
                        <p style="margin:0;color:#1f2937;line-height:1.8;white-space:pre-wrap;">${d.appeal_text || '—'}</p>
                    </div>
                    <div style="background:#f9fafb;border-radius:10px;padding:1rem;margin-bottom:0.75rem;">
                        <label style="font-weight:700;color:#374151;display:block;margin-bottom:0.5rem;"><i class="fa-solid fa-paperclip"></i> الوثيقة المرفقة:</label>
                        ${docLink}
                    </div>
                    <div style="display:flex;gap:0.75rem;justify-content:center;margin-top:1rem;">
                        <button id="btn-accept-appeal" class="btn btn-success" style="padding:0.5rem 1.5rem;border-radius:8px;font-weight:600;border:none;background:linear-gradient(135deg,#10b981,#059669);color:#fff;"><i class="fa-solid fa-check-circle"></i> قبول الطعن</button>
                        <button id="btn-refuse-appeal" class="btn btn-danger" style="padding:0.5rem 1.5rem;border-radius:8px;font-weight:600;border:none;background:linear-gradient(135deg,#ef4444,#dc2626);color:#fff;"><i class="fa-solid fa-times-circle"></i> رفض الطعن</button>
                    </div>
                </div>
            `,
            showConfirmButton: false,
            showCloseButton: true,
            customClass: { popup: 'swal-wide' },
            didOpen: () => {
                document.getElementById('btn-accept-appeal').addEventListener('click', async () => {
                    const confirm = await Swal.fire({
                        title: 'تأكيد قبول الطعن',
                        text: 'سيتم إعادة قبول جميع تلاميذ هذا الولي/الوصي. هل أنت متأكد؟',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'نعم، قبول',
                        cancelButtonText: 'إلغاء',
                        confirmButtonColor: '#10b981',
                        cancelButtonColor: '#6b7280',
                        reverseButtons: true
                    });
                    if (confirm.isConfirmed) {
                        try {
                            const res = await fetch(getApiUrlPath(`/api/user/eleves/${num_scolaire}/appeal/accept`), {
                                method: 'POST',
                                credentials: 'include',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                                    'Accept': 'application/json',
                                    'Content-Type': 'application/json'
                                }
                            });
                            const data = await res.json();
                            if (res.ok && data.success) {
                                Swal.fire({ icon: 'success', title: 'تم القبول', text: data.message, confirmButtonColor: '#10b981' });
                                loadStudents(currentPage, currentFilter, currentNumScolaireSearch, currentStatusFilter);
                            } else {
                                Swal.fire({ icon: 'error', title: 'خطأ', text: data.message || 'فشل القبول', confirmButtonColor: '#ef4444' });
                            }
                        } catch (e) {
                            Swal.fire({ icon: 'error', title: 'خطأ', text: 'حدث خطأ', confirmButtonColor: '#ef4444' });
                        }
                    }
                });
                document.getElementById('btn-refuse-appeal').addEventListener('click', async () => {
                    const confirm = await Swal.fire({
                        title: 'تأكيد رفض الطعن',
                        text: 'هل أنت متأكد من رفض هذا الطعن؟',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'نعم، رفض',
                        cancelButtonText: 'إلغاء',
                        confirmButtonColor: '#ef4444',
                        cancelButtonColor: '#6b7280',
                        reverseButtons: true
                    });
                    if (confirm.isConfirmed) {
                        try {
                            const res = await fetch(getApiUrlPath(`/api/user/eleves/${num_scolaire}/appeal/refuse`), {
                                method: 'POST',
                                credentials: 'include',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                                    'Accept': 'application/json',
                                    'Content-Type': 'application/json'
                                }
                            });
                            const data = await res.json();
                            if (res.ok && data.success) {
                                Swal.fire({ icon: 'success', title: 'تم الرفض', text: data.message, confirmButtonColor: '#10b981' });
                                loadStudents(currentPage, currentFilter, currentNumScolaireSearch, currentStatusFilter);
                            } else {
                                Swal.fire({ icon: 'error', title: 'خطأ', text: data.message || 'فشل الرفض', confirmButtonColor: '#ef4444' });
                            }
                        } catch (e) {
                            Swal.fire({ icon: 'error', title: 'خطأ', text: 'حدث خطأ', confirmButtonColor: '#ef4444' });
                        }
                    }
                });
            }
        });
    } catch (e) {
        Swal.fire({ icon: 'error', title: 'خطأ', text: 'حدث خطأ أثناء تحميل البيانات', confirmButtonColor: '#ef4444' });
    }
}
</script>

@endsection
