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
                <li class="sidebar-item">
                    <a href="{{ route('user.add.student') }}" class="sidebar-link">
                        <i class="fa-solid fa-user-plus"></i>
                        <span>إضافة تلميذ جديد</span>
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
            <div class="dashboard-header">
                <h2>قائمة التلاميذ</h2>
                <p>عرض وإدارة جميع التلاميذ المسجلين في المنصة</p>
            </div>

            <!-- Filters Section -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">البحث برقم التعريف المدرسي</label>
                            <input type="text" id="num_scolaire_search" class="form-control" placeholder="أدخل رقم التعريف المدرسي">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">المؤسسة التعليمية</label>
                            <select id="code_etabliss_filter" class="form-select">
                                <option value="">جميع المؤسسات</option>
                                @foreach($schools as $school)
                                    <option value="{{ $school->code_etabliss }}">{{ $school->nom_etabliss }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="button" class="btn btn-primary w-100" onclick="loadStudents(1)">
                                <i class="fa-solid fa-search me-2"></i>بحث
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Students Table -->
            <div class="card">
                <div class="card-body">
                    <div id="studentsTableContainer">
                        <div class="text-center py-5">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">جارٍ التحميل...</span>
                            </div>
                            <p class="mt-3">جارٍ تحميل البيانات...</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <div id="paginationContainer" class="mt-4"></div>
        </div>
    </div>
</div>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
let currentPage = 1;

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

async function loadStudents(page = 1) {
    currentPage = page;
    const num_scolaire = document.getElementById('num_scolaire_search').value.trim();
    const code_etabliss = document.getElementById('code_etabliss_filter').value;

    const params = new URLSearchParams({
        page: page,
    });
    if (num_scolaire) params.append('num_scolaire_search', num_scolaire);
    if (code_etabliss) params.append('code_etabliss', code_etabliss);

    try {
        const response = await fetch(`/user/eleves?${params.toString()}`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'include'
        });

        const result = await response.json();

        if (result.success) {
            renderStudentsTable(result.data);
            renderPagination(result);
        } else {
            Swal.fire({
                icon: 'error',
                title: 'خطأ',
                text: result.message || 'فشل تحميل البيانات'
            });
        }
    } catch (error) {
        console.error('Error loading students:', error);
        Swal.fire({
            icon: 'error',
            title: 'خطأ',
            text: 'حدث خطأ أثناء تحميل البيانات'
        });
    }
}

function renderStudentsTable(students) {
    const container = document.getElementById('studentsTableContainer');
    
    if (!students || students.length === 0) {
        container.innerHTML = `
            <div class="text-center py-5">
                <i class="fa-solid fa-inbox" style="font-size: 3rem; color: #ccc; margin-bottom: 1rem;"></i>
                <p class="text-muted">لا توجد بيانات</p>
            </div>
        `;
        return;
    }

    let html = `
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>رقم التعريف المدرسي</th>
                        <th>الاسم واللقب</th>
                        <th>تاريخ الميلاد</th>
                        <th>المستوى</th>
                        <th>القسم</th>
                        <th>المؤسسة</th>
                        <th>الولي/الوصي</th>
                        <th>حالة الملف</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
    `;

    students.forEach(eleve => {
        const dossierBadge = eleve.dossier_depose === 'oui' 
            ? '<span class="badge bg-success">مودع</span>'
            : '<span class="badge bg-warning">غير مودع</span>';

        html += `
            <tr>
                <td>${eleve.num_scolaire || '—'}</td>
                <td>${eleve.nom || '—'} ${eleve.prenom || '—'}</td>
                <td>${eleve.date_naiss || '—'}</td>
                <td>${eleve.niv_scol || '—'}</td>
                <td>${eleve.classe_scol || '—'}</td>
                <td>${eleve.etablissement_nom || '—'}</td>
                <td>${eleve.tuteur_nom || '—'} ${eleve.tuteur_prenom || '—'}<br><small class="text-muted">${eleve.relation_tuteur_text || '—'}</small></td>
                <td>${dossierBadge}</td>
                <td>
                    <button class="btn btn-sm btn-info" onclick="viewStudent('${eleve.num_scolaire}')">
                        <i class="fa-solid fa-eye"></i> عرض
                    </button>
                </td>
            </tr>
        `;
    });

    html += `
                </tbody>
            </table>
        </div>
    `;

    container.innerHTML = html;
}

function renderPagination(data) {
    const container = document.getElementById('paginationContainer');
    
    if (data.last_page <= 1) {
        container.innerHTML = '';
        return;
    }

    let html = '<nav><ul class="pagination justify-content-center">';
    
    // Previous button
    html += `
        <li class="page-item ${data.current_page === 1 ? 'disabled' : ''}">
            <a class="page-link" href="#" onclick="loadStudents(${data.current_page - 1}); return false;">السابق</a>
        </li>
    `;

    // Page numbers
    for (let i = 1; i <= data.last_page; i++) {
        if (i === 1 || i === data.last_page || (i >= data.current_page - 2 && i <= data.current_page + 2)) {
            html += `
                <li class="page-item ${i === data.current_page ? 'active' : ''}">
                    <a class="page-link" href="#" onclick="loadStudents(${i}); return false;">${i}</a>
                </li>
            `;
        } else if (i === data.current_page - 3 || i === data.current_page + 3) {
            html += '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }
    }

    // Next button
    html += `
        <li class="page-item ${data.current_page === data.last_page ? 'disabled' : ''}">
            <a class="page-link" href="#" onclick="loadStudents(${data.current_page + 1}); return false;">التالي</a>
        </li>
    `;

    html += '</ul></nav>';
    container.innerHTML = html;
}

function viewStudent(num_scolaire) {
    window.location.href = `/user/eleves/${num_scolaire}`;
}

// Load students on page load
document.addEventListener('DOMContentLoaded', function() {
    loadStudents(1);
});

// Allow Enter key to trigger search
document.getElementById('num_scolaire_search').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        loadStudents(1);
    }
});
</script>

@endsection
