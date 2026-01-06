@extends('layouts.main')

@section('title', 'قائمة الأوصياء/الأولياء')

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

/* Tuteur Modal Styles */
.tuteur-details-modal {
    text-align: right;
}

.tuteur-info-section {
    background: #f8fafc;
    padding: 1.5rem;
    border-radius: 12px;
    margin-bottom: 2rem;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

.tuteur-info-section h6 {
    color: var(--primary-color);
    font-weight: 700;
    font-size: 1.25rem;
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 3px solid var(--accent-color);
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1.5rem;
}

.info-item {
    background: white;
    padding: 1rem 1.25rem;
    border-radius: 8px;
    border-right: 4px solid var(--accent-color);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    transition: all 0.3s ease;
}

.info-item:hover {
    transform: translateX(-5px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.info-item strong {
    color: var(--text-secondary);
    font-weight: 600;
    font-size: 0.9rem;
    display: block;
    margin-bottom: 0.5rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.info-item p {
    color: var(--text-primary);
    font-size: 1rem;
    margin: 0;
    font-weight: 500;
    word-break: break-word;
}

.expand-toggle-container {
    text-align: center;
    margin-top: 1.5rem;
    padding-top: 1.5rem;
    border-top: 2px solid var(--border-light);
}

.expand-toggle-btn {
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%);
    color: white;
    border: none;
    border-radius: 8px;
    padding: 0.75rem 2rem;
    font-weight: 600;
    font-size: 0.95rem;
    cursor: pointer;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    box-shadow: 0 4px 12px rgba(15, 3, 58, 0.2);
}

.expand-toggle-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(15, 3, 58, 0.3);
}

.eleves-section {
    background: white;
    padding: 1.5rem;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

.eleves-section h6 {
    color: var(--primary-color);
    font-weight: 700;
    font-size: 1.25rem;
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 3px solid var(--accent-color);
}

.eleves-table-container {
    overflow-x: auto;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.eleves-table {
    width: 100%;
    border-collapse: collapse;
    min-width: 700px;
    background: white;
}

.eleves-table thead {
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%);
    color: white;
}

.eleves-table thead th {
    padding: 1rem;
    text-align: center;
    font-weight: 600;
    font-size: 0.95rem;
    border: none;
    white-space: nowrap;
}

.eleves-table tbody tr {
    border-bottom: 1px solid var(--border-light);
    transition: all 0.2s ease;
}

.eleves-table tbody tr:hover {
    background: var(--bg-secondary);
}

.eleves-table tbody td {
    padding: 1rem;
    text-align: center;
    color: var(--text-primary);
    font-size: 0.9rem;
    border: none;
    vertical-align: middle;
}

.status-badge {
    display: inline-block;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
    letter-spacing: 0.5px;
}

.status-badge.approved {
    background: linear-gradient(135deg, #10b981, #059669);
    color: white;
    box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);
}

.status-badge.pending {
    background: linear-gradient(135deg, #6b7280, #4b5563);
    color: white;
    box-shadow: 0 2px 8px rgba(107, 114, 128, 0.3);
}

.empty-state {
    background: linear-gradient(135deg, #dbeafe, #bfdbfe);
    padding: 2rem;
    border-radius: 12px;
    text-align: center;
    color: #1e40af;
    font-weight: 500;
    border: 2px dashed #3b82f6;
}

.empty-state i {
    font-size: 2rem;
    margin-bottom: 0.5rem;
    display: block;
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
                <li class="sidebar-item active">
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
        <h2 id="user-name">قائمة الأوصياء/الأولياء</h2>
        <p>إدارة جميع الأوصياء والأولياء المسجلين في المنصة</p>
    </div>

    <!-- Table Section -->
    <div class="children-table-section">

        
        <!-- Filters Row -->
        <div class="filters-row" style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1.5rem; flex-wrap: wrap; padding: 1rem; background: #f8fafc; border-radius: 12px; border: 1px solid #e5e7eb;">
            <div style="display: flex; align-items: center; gap: 0.75rem; flex: 1; min-width: 250px;">
                <label style="color: #374151; font-weight: 600; white-space: nowrap; font-size: 0.9rem;">البحث بـ NIN:</label>
                <input type="text" id="ninSearch" placeholder="ابحث برقم التعريف الوطني..." style="padding: 0.5rem 1rem; border: 2px solid #e5e7eb; border-radius: 8px; font-family: 'Cairo', sans-serif; font-size: 0.95rem; flex: 1; transition: all 0.3s ease;">
            </div>
            <div style="display: flex; align-items: center; gap: 0.75rem; flex: 1; min-width: 250px;">
                <label style="color: #374151; font-weight: 600; white-space: nowrap; font-size: 0.9rem;">فلترة حسب مؤسسة التربية والتعليم:</label>
                <div style="position: relative; flex: 1;">
                    <input type="text" id="schoolSearch" placeholder="ابحث عن مدرسة..." style="padding: 0.5rem 1rem; padding-right: 2.5rem; border: 2px solid #e5e7eb; border-radius: 8px; font-family: 'Cairo', sans-serif; font-size: 0.95rem; width: 100%; transition: all 0.3s ease;">
                    <div id="schoolDropdown" style="position: absolute; top: 100%; right: 0; left: 0; background: white; border: 2px solid #e5e7eb; border-radius: 8px; max-height: 300px; overflow-y: auto; display: none; z-index: 1000; margin-top: 0.25rem; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
                        <div id="schoolDropdownList"></div>
                    </div>
                    <select id="schoolFilter" style="display: none;">
                        <option value="">جميع المدارس</option>
                        @foreach($schools as $school)
                            <option value="{{ $school->code_etabliss }}" data-name="{{ $school->nom_etabliss }}">{{ $school->nom_etabliss }}</option>
                        @endforeach
                    </select>
                    <div id="selectedSchool" style="position: absolute; top: 50%; right: 1rem; transform: translateY(-50%); pointer-events: none; color: #6b7280; font-size: 0.9rem; z-index: 0;">اختر...</div>
                </div>
            </div>
            <button id="clearFilters" style="padding: 0.5rem 1.5rem; background: #6b7280; color: white; border: none; border-radius: 8px; cursor: pointer; font-family: 'Cairo', sans-serif; font-weight: 600; display: none; transition: all 0.3s ease; white-space: nowrap;">
                <i class="fa-solid fa-times"></i> مسح الفلاتر
            </button>
        </div>
        <div class="children-table-wrapper">
        <table class="children-table" id="main-table">
            <thead id="table-head">
                <tr>
                    <th>الإجراءات</th>
                    <th>حالة الموافقة</th>
                    <th>عدد الأطفال</th>
                    <th>الفئة الاجتماعية</th>
                    <th>الاسم الكامل</th>
                    <th>رقم التعريف الوطني</th>
                </tr>
            </thead>
            <tbody id="table-body">
                <tr>
                    <td colspan="6" style="text-align: center; padding: 20px;">
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
let currentPage = 1;
let currentFilter = '';
let currentNinSearch = '';
let searchTimeout = null;
let allSchools = [];

document.addEventListener('DOMContentLoaded', () => {
    const tableBody = document.getElementById('table-body');
    const schoolFilter = document.getElementById('schoolFilter');
    const schoolSearch = document.getElementById('schoolSearch');
    const schoolDropdown = document.getElementById('schoolDropdown');
    const schoolDropdownList = document.getElementById('schoolDropdownList');
    const selectedSchool = document.getElementById('selectedSchool');
    const ninSearch = document.getElementById('ninSearch');
    const clearFilters = document.getElementById('clearFilters');
    const paginationContainer = document.getElementById('pagination-container');

    // Store all schools data
    schoolFilter.querySelectorAll('option').forEach(option => {
        if (option.value) {
            allSchools.push({
                code: option.value,
                name: option.textContent || option.getAttribute('data-name')
            });
        }
    });

    // Render school dropdown
    function renderSchoolDropdown(filteredSchools = allSchools) {
        schoolDropdownList.innerHTML = '';
        
        // Add "All schools" option
        const allOption = document.createElement('div');
        allOption.className = 'school-dropdown-item';
        allOption.style.cssText = 'padding: 0.75rem 1rem; cursor: pointer; transition: background 0.2s; border-bottom: 1px solid #e5e7eb;';
        allOption.textContent = 'جميع المدارس';
        allOption.addEventListener('mouseenter', () => allOption.style.background = '#f3f4f6');
        allOption.addEventListener('mouseleave', () => allOption.style.background = 'white');
        allOption.addEventListener('click', () => {
            currentFilter = '';
            schoolSearch.value = '';
            selectedSchool.textContent = 'اختر...';
            schoolDropdown.style.display = 'none';
            updateClearButton();
            loadTuteurs(1, currentFilter, currentNinSearch);
        });
        schoolDropdownList.appendChild(allOption);
        
        // Add filtered schools
        filteredSchools.forEach(school => {
            const item = document.createElement('div');
            item.className = 'school-dropdown-item';
            item.style.cssText = 'padding: 0.75rem 1rem; cursor: pointer; transition: background 0.2s; border-bottom: 1px solid #e5e7eb;';
            item.textContent = school.name;
            item.addEventListener('mouseenter', () => item.style.background = '#f3f4f6');
            item.addEventListener('mouseleave', () => item.style.background = 'white');
            item.addEventListener('click', () => {
                currentFilter = school.code;
                schoolSearch.value = school.name;
                selectedSchool.textContent = school.name;
                schoolDropdown.style.display = 'none';
                updateClearButton();
                loadTuteurs(1, currentFilter, currentNinSearch);
            });
            schoolDropdownList.appendChild(item);
        });
    }

    // Initial render
    renderSchoolDropdown();

    // Show dropdown on search input focus
    schoolSearch.addEventListener('focus', () => {
        schoolDropdown.style.display = 'block';
        renderSchoolDropdown(allSchools);
    });

    // Filter schools dropdown based on search
    schoolSearch.addEventListener('input', (e) => {
        const searchTerm = e.target.value.toLowerCase();
        const filteredSchools = allSchools.filter(school => 
            school.name.toLowerCase().includes(searchTerm)
        );
        renderSchoolDropdown(filteredSchools);
        schoolDropdown.style.display = 'block';
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', (e) => {
        if (!schoolSearch.contains(e.target) && !schoolDropdown.contains(e.target)) {
            schoolDropdown.style.display = 'none';
        }
    });

    // Function to update clear button visibility
    function updateClearButton() {
        if (currentFilter || currentNinSearch) {
            clearFilters.style.display = 'block';
        } else {
            clearFilters.style.display = 'none';
        }
    }

    // NIN search with debounce
    ninSearch.addEventListener('input', (e) => {
        currentNinSearch = e.target.value.trim();
        updateClearButton();
        
        // Clear previous timeout
        if (searchTimeout) {
            clearTimeout(searchTimeout);
        }

        // Set new timeout for real-time search
        searchTimeout = setTimeout(() => {
            loadTuteurs(1, currentFilter, currentNinSearch);
        }, 500);
    });

    // Clear filters button
    clearFilters.addEventListener('click', () => {
        currentFilter = '';
        currentNinSearch = '';
        schoolFilter.value = '';
        schoolSearch.value = '';
        selectedSchool.textContent = 'اختر...';
        ninSearch.value = '';
        schoolDropdown.style.display = 'none';
        updateClearButton();
        loadTuteurs(1);
    });

    // Load tuteurs with pagination
    async function loadTuteurs(page = 1, code_etabliss = '', nin_search = '') {
        tableBody.innerHTML = `
            <tr>
                <td colspan="6" style="text-align: center; padding: 20px;">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">جارٍ التحميل...</span>
                    </div>
                </td>
            </tr>
        `;

        try {
            const url = new URL('/user/tuteurs', window.location.origin);
            url.searchParams.append('page', page);
            if (code_etabliss) {
                url.searchParams.append('code_etabliss', code_etabliss);
            }
            if (nin_search) {
                url.searchParams.append('nin_search', nin_search);
            }

            const response = await fetch(url, {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            });

            const result = await response.json();

            if (!result.success) {
                tableBody.innerHTML = '<tr><td colspan="6" style="text-align: center; padding: 20px; color: red;">حدث خطأ أثناء تحميل البيانات</td></tr>';
                return;
            }

            const tuteurs = result.data;
            currentPage = result.current_page;
            const lastPage = result.last_page;

            if (tuteurs.length === 0) {
                tableBody.innerHTML = '<tr><td colspan="6" style="text-align: center; padding: 20px;">لا يوجد أوصياء/أولياء مسجلين</td></tr>';
                paginationContainer.innerHTML = '';
                return;
            }

            // Build table rows
            let html = '';
            tuteurs.forEach(tuteur => {
                let statusBadge = '';
                if (tuteur.total_count > 0) {
                    if (tuteur.all_approved) {
                        statusBadge = `<span class="badge bg-success">موافق عليه بالكامل (${tuteur.approved_count}/${tuteur.total_count})</span>`;
                    } else if (tuteur.some_approved) {
                        statusBadge = `<span class="badge bg-warning">موافق عليه جزئياً (${tuteur.approved_count}/${tuteur.total_count})</span>`;
                    } else {
                        statusBadge = `<span class="badge bg-secondary">غير موافق عليه (0/${tuteur.total_count})</span>`;
                    }
                } else {
                    statusBadge = '<span class="badge bg-secondary">لا يوجد أطفال</span>';
                }

                html += `
                    <tr>
                        <td>
                            <div class="action-buttons" style="display: flex; gap: 5px; justify-content: center;">
                                <button class="btn btn-sm btn-info" onclick="viewTuteur('${tuteur.nin}')" title="عرض">
                                    <i class="fa-solid fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-danger" onclick="deleteTuteur('${tuteur.nin}')" title="حذف">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </div>
                        </td>
                        <td>${statusBadge}</td>
                        <td>${tuteur.total_count}</td>
                        <td>${tuteur.cats}</td>
                        <td>${tuteur.nom} ${tuteur.prenom}</td>
                        <td>${tuteur.nin}</td>
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
                    paginationHTML += `<button onclick="loadTuteursPage(${currentPage - 1})" style="padding: 0.5rem 1rem; background: #2563eb; color: white; border: none; border-radius: 6px; cursor: pointer;">◀ السابق</button>`;
                }

                // Page numbers
                for (let i = 1; i <= lastPage; i++) {
                    if (i === 1 || i === lastPage || (i >= currentPage - 2 && i <= currentPage + 2)) {
                        paginationHTML += `<button onclick="loadTuteursPage(${i})" style="padding: 0.5rem 1rem; background: ${i === currentPage ? '#0f033a' : '#e5e7eb'}; color: ${i === currentPage ? 'white' : '#374151'}; border: none; border-radius: 6px; cursor: pointer; font-weight: ${i === currentPage ? '600' : '400'};" ${i === currentPage ? 'disabled' : ''}>${i}</button>`;
                    } else if (i === currentPage - 3 || i === currentPage + 3) {
                        paginationHTML += '<span style="padding: 0.5rem;">...</span>';
                    }
                }

                // Next button
                if (currentPage < lastPage) {
                    paginationHTML += `<button onclick="loadTuteursPage(${currentPage + 1})" style="padding: 0.5rem 1rem; background: #2563eb; color: white; border: none; border-radius: 6px; cursor: pointer;">التالي ▶</button>`;
                }

                paginationHTML += '</div>';
            }
            paginationContainer.innerHTML = paginationHTML;

        } catch (error) {
            console.error('Error loading tuteurs:', error);
            tableBody.innerHTML = '<tr><td colspan="6" style="text-align: center; padding: 20px; color: red;">حدث خطأ أثناء تحميل البيانات</td></tr>';
        }
    }

    // Make loadTuteursPage available globally
    window.loadTuteursPage = function(page) {
        loadTuteurs(page, currentFilter, currentNinSearch);
    };

    // Initial load
    loadTuteurs(1);
});

// View tuteur with full details
async function viewTuteur(nin) {
    // Show loading
    Swal.fire({
        title: 'جارٍ التحميل...',
        html: '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">جارٍ التحميل...</span></div>',
        allowOutsideClick: false,
        allowEscapeKey: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    try {
        const response = await fetch(`/user/tuteurs/${nin}`, {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        });
        
        const data = await response.json();
        
        if (!data.success || !data.tuteur) {
            Swal.fire({
                icon: 'error',
                title: 'خطأ',
                text: data.message || 'فشل تحميل البيانات',
                confirmButtonText: 'حسنًا'
            });
            return;
        }
        
        const t = data.tuteur;
        const eleves = t.eleves || [];
        
        // Get commune name
        let communeName = '-';
        if (t.commune_residence && t.commune_residence.lib_comm_ar) {
            communeName = t.commune_residence.lib_comm_ar;
        } else if (t.communeResidence && t.communeResidence.lib_comm_ar) {
            communeName = t.communeResidence.lib_comm_ar;
        }
        
        // Determine tuteur role text
        let roleText = '—';
        if (t.relation_tuteur === 1 || t.relation_tuteur === '1') {
            roleText = 'ولي (أب)';
        } else if (t.relation_tuteur === 2 || t.relation_tuteur === '2') {
            roleText = 'ولي (أم)';
        } else if (t.relation_tuteur === 3 || t.relation_tuteur === '3') {
            roleText = 'وصي';
        }
        
        // Build modal content HTML with modern styling
        let html = `
            <div class="tuteur-details-modal">
                <div class="tuteur-info-section">
                    <h6>معلومات الوصي/الولي</h6>
                    <div class="info-grid" id="tuteurInfoGrid">
                        <div class="info-item">
                            <strong>الاسم الكامل</strong>
                            <p>${(t.prenom_ar || t.prenom_fr || '')} ${(t.nom_ar || t.nom_fr || '')}</p>
                        </div>
                        <div class="info-item">
                            <strong>رقم التعريف الوطني (NIN)</strong>
                            <p>${t.nin || '-'}</p>
                        </div>
                        <div class="info-item">
                            <strong>الصفة</strong>
                            <p>${roleText}</p>
                        </div>
                        <div class="info-item">
                            <strong>تاريخ الميلاد</strong>
                            <p>${t.date_naiss || '-'}</p>
                        </div>
                        <div class="info-item">
                            <strong>البلدية</strong>
                            <p>${communeName}</p>
                        </div>
                        <div class="info-item">
                            <strong>الفئة الاجتماعية</strong>
                            <p>${t.cats || '-'}</p>
                        </div>
                        <div class="info-item expandable-item" style="display: none;">
                            <strong>مكان الميلاد</strong>
                            <p>${(t.commune_naissance && t.commune_naissance.lib_comm_ar) ? t.commune_naissance.lib_comm_ar : (t.commune_naiss || '-')}</p>
                        </div>
                        <div class="info-item expandable-item" style="display: none;">
                            <strong>العنوان</strong>
                            <p>${t.adresse || '-'}</p>
                        </div>
                        <div class="info-item expandable-item" style="display: none;">
                            <strong>الهاتف</strong>
                            <p>${t.tel || '-'}</p>
                        </div>
                        <div class="info-item expandable-item" style="display: none;">
                            <strong>البريد الإلكتروني</strong>
                            <p>${t.email || '-'}</p>
                        </div>
                        <div class="info-item expandable-item" style="display: none;">
                            <strong>رقم بطاقة التعريف الوطنية</strong>
                            <p>${t.num_cni || '-'}</p>
                        </div>
                        <div class="info-item expandable-item" style="display: none;">
                            <strong>تاريخ إصدار البطاقة</strong>
                            <p>${t.date_cni || '-'}</p>
                        </div>
                        <div class="info-item expandable-item" style="display: none;">
                            <strong>مكان إصدار البطاقة</strong>
                            <p>${t.lieu_cni || '-'}</p>
                        </div>
                        <div class="info-item expandable-item" style="display: none;">
                            <strong>رقم الحساب البريدي</strong>
                            <p>${(t.num_cpt || '') + (t.cle_cpt ? ' - ' + t.cle_cpt : '') || '-'}</p>
                        </div>
                        <div class="info-item expandable-item" style="display: none;">
                            <strong>عدد الأبناء المتمدرسين (الإجمالي)</strong>
                            <p>${t.total_eleves_count || t.totalElevesCount || t.nbr_enfants_scolarise || 0}</p>
                        </div>
                        <div class="info-item expandable-item" style="display: none;">
                            <strong>رقم الضمان الاجتماعي (NSS)</strong>
                            <p>${t.nss || '-'}</p>
                        </div>
                        <div class="info-item expandable-item" style="display: none;">
                            <strong>الدخل الشهري</strong>
                            <p>${t.montant_s ? t.montant_s + ' دج' : '-'}</p>
                        </div>
                    </div>
                    <div class="expand-toggle-container">
                        <button type="button" class="expand-toggle-btn" onclick="toggleTuteurInfo()" id="expandToggleBtn">
                            <i class="fa-solid fa-chevron-down" id="expandIcon"></i>
                            <span id="expandText">عرض الكل</span>
                        </button>
                    </div>
                </div>
                
                <div class="eleves-section">
                    <div style="display:flex; align-items:center; justify-content:space-between; gap:1rem;">
                      <h6 style="margin:0;">التلاميذ (${eleves.length})</h6>
                    </div>
        `;
        
        if (eleves.length === 0) {
            html += `
                <div class="empty-state">
                    <i class="fa-solid fa-info-circle"></i>
                    <div>لا يوجد تلاميذ مسجلين في هذه البلدية</div>
                </div>
            `;
        } else {
            html += `
                <div class="eleves-table-container">
                    <table class="eleves-table">
                        <thead>
                            <tr>
                                <th>الاسم الكامل</th>
                                <th>رقم التعريف المدرسي</th>
                                <th>تاريخ الميلاد</th>
                                <th>المستوى الدراسي</th>
                                <th>المؤسسة التعليمية</th>
                                <th>قرار اللجنة</th>
                            </tr>
                        </thead>
                        <tbody>
            `;
            
            eleves.forEach(eleve => {
                const isApproved = eleve.dossier_depose === 'oui';
                const statusClass = isApproved ? 'approved' : 'pending';
                const statusText = isApproved ? 'مقبول' : 'قيد المراجعة';
                
                // Get mother's name from relationship
                let motherName = '-';
                if (eleve.mother) {
                    motherName = `${eleve.mother.prenom_ar || ''} ${eleve.mother.nom_ar || ''}`.trim() || '-';
                }
                
                html += `
                    <tr>
                        <td>${(eleve.prenom || '') + ' ' + (eleve.nom || '')}</td>
                        <td>${eleve.num_scolaire || '-'}</td>
                        <td>${eleve.date_naiss || '-'}</td>
                        <td>${eleve.classe_scol || eleve.niv_scol || '-'}</td>
                        <td>${(eleve.etablissement && eleve.etablissement.nom_etabliss) ? eleve.etablissement.nom_etabliss : '-'}</td>
                        <td><span class="status-badge ${statusClass}">${statusText}</span></td>
                    </tr>
                `;
            });
            
            html += `
                        </tbody>
                    </table>
                </div>
            `;
        }
        
        html += `</div></div>`;
        
        // Show SweetAlert2 modal with HTML content
        Swal.fire({
            title: 'تفاصيل الوصي/الولي',
            html: html,
            width: '90%',
            maxWidth: '1200px',
            showCloseButton: true,
            showConfirmButton: true,
            confirmButtonText: 'إغلاق',
            confirmButtonColor: '#0f033a',
            customClass: {
                popup: 'swal-tuteur-modal',
                htmlContainer: 'swal-tuteur-content'
            },
            didOpen: () => {
                // Make content scrollable
                const content = document.querySelector('.swal-tuteur-content');
                if (content) {
                    content.style.maxHeight = '70vh';
                    content.style.overflowY = 'auto';
                }
            }
        });
        
        // Store expand state
        window.tuteurInfoExpanded = false;
        
    } catch (error) {
        console.error('Error loading tuteur data:', error);
        Swal.fire({
            icon: 'error',
            title: 'خطأ',
            text: 'حدث خطأ أثناء تحميل البيانات',
            confirmButtonText: 'حسنًا'
        });
    }
}

// Toggle tuteur info expand/collapse
function toggleTuteurInfo() {
    const expandableItems = document.querySelectorAll('.expandable-item');
    const expandIcon = document.getElementById('expandIcon');
    const expandText = document.getElementById('expandText');
    const isExpanded = window.tuteurInfoExpanded || false;
    
    expandableItems.forEach(item => {
        if (isExpanded) {
            item.style.display = 'none';
        } else {
            item.style.display = 'block';
        }
    });
    
    if (isExpanded) {
        expandIcon.classList.remove('fa-chevron-up');
        expandIcon.classList.add('fa-chevron-down');
        expandText.textContent = 'عرض الكل';
        window.tuteurInfoExpanded = false;
    } else {
        expandIcon.classList.remove('fa-chevron-down');
        expandIcon.classList.add('fa-chevron-up');
        expandText.textContent = 'إخفاء';
        window.tuteurInfoExpanded = true;
    }
}

// Delete tuteur (placeholder)
function deleteTuteur(nin) {
    Swal.fire({
        title: 'تأكيد الحذف',
        text: `هل أنت متأكد من حذف الوصي/الولي ${nin}؟`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'نعم، احذف',
        cancelButtonText: 'إلغاء',
        reverseButtons: true,
        confirmButtonColor: '#ef4444'
    }).then((result) => {
        if (result.isConfirmed) {
            // TODO: Implement delete functionality
            Swal.fire('تم الحذف', 'سيتم تنفيذ الحذف قريباً', 'success');
        }
    });
}
</script>

@endsection

