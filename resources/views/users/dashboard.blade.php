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
                    <a href="#" class="sidebar-link">
                        <i class="fa-solid fa-home"></i>
                        <span>الرئيسية</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="#" class="sidebar-link">
                        <i class="fa-solid fa-users"></i>
                        <span>الأوصياء والأولياء</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="#" class="sidebar-link">
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

    <!-- Stats Cards Section -->
    <div class="stats-section">
        <div class="stat-card">
            <div class="stat-card-header">
                <div class="stat-card-icon primary">
                    <i class="fa-solid fa-users"></i>
                </div>
            </div>
            <div class="stat-card-body">
                <div class="stat-card-label">إجمالي الأوصياء/الأولياء</div>
                <div class="stat-card-value" id="total-tuteurs">0</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-card-header">
                <div class="stat-card-icon info">
                    <i class="fa-solid fa-user-graduate"></i>
                </div>
            </div>
            <div class="stat-card-body">
                <div class="stat-card-label">إجمالي التلاميذ</div>
                <div class="stat-card-value" id="total-students">0</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-card-header">
                <div class="stat-card-icon success">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
            </div>
            <div class="stat-card-body">
                <div class="stat-card-label">الطلبات المقبولة</div>
                <div class="stat-card-value" id="approved-count">0</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-card-header">
                <div class="stat-card-icon warning">
                    <i class="fa-solid fa-clock"></i>
                </div>
            </div>
            <div class="stat-card-body">
                <div class="stat-card-label">الطلبات المعلقة</div>
                <div class="stat-card-value" id="pending-count">0</div>
            </div>
        </div>
    </div>

    <!-- Table Section -->
    <div class="children-table-section">
        <!-- Title Row -->
        <div class="table-title-row" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <div style="display: flex; align-items: center; gap: 1rem;">
                <h3 id="table-title" style="margin: 0; color: #0f033a; font-size: 1.5rem; font-weight: 700;">قائمة الأوصياء/الأولياء</h3>
                <button id="back-btn" style="display:none; padding: 0.5rem 1rem; background: #2563eb; color: white; border: none; border-radius: 6px; cursor: pointer; font-family: 'Cairo', sans-serif;">◀ العودة</button>
            </div>
        </div>
        
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
// Open view tuteur modal using SweetAlert2
async function openViewTuteurModal(nin) {
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
        
        // Debug: Log to see what data we're getting
        console.log('Tuteur data:', t);
        console.log('Tuteur code_commune:', t.code_commune);
        console.log('Commune Residence:', t.communeResidence);
        console.log('Commune Residence lib_comm_ar:', t.communeResidence?.lib_comm_ar);
        
        // Get commune name - try multiple ways to access it
        let communeName = '-';
        if (t.communeResidence && t.communeResidence.lib_comm_ar) {
            communeName = t.communeResidence.lib_comm_ar;
        } else if (t.commune_residence && t.commune_residence.lib_comm_ar) {
            communeName = t.commune_residence.lib_comm_ar;
        }
        
        console.log('Final commune name:', communeName);
        
        // Build modal content HTML with modern styling
        let html = `
            <div class="tuteur-details-modal">
                <div class="tuteur-info-section">
                    <h6>معلومات الوصي/الولي</h6>
                    <div class="info-grid" id="tuteurInfoGrid">
                        <div class="info-item">
                            <strong>الاسم الكامل</strong>
                            <p>${(t.nom_ar || t.nom_fr || '')} ${(t.prenom_ar || t.prenom_fr || '')}</p>
                        </div>
                        <div class="info-item">
                            <strong>رقم التعريف الوطني</strong>
                            <p>${t.nin || '-'}</p>
                        </div>
                        <div class="info-item">
                            <strong>تاريخ الميلاد</strong>
                            <p>${t.date_naiss || '-'}</p>
                        </div>
                        <div class="info-item">
                            <strong>البلدية</strong>
                            <p>${communeName}</p>
                        </div>
                        <div class="info-item expandable-item" style="display: none;">
                            <strong>مكان الميلاد</strong>
                            <p>${(t.communeNaissance && t.communeNaissance.lib_comm_ar) ? t.communeNaissance.lib_comm_ar : (t.commune_naiss || '-')}</p>
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
                            <strong>رقم الحساب البريدي</strong>
                            <p>${(t.num_cpt || '') + (t.cle_cpt ? ' - ' + t.cle_cpt : '') || '-'}</p>
                        </div>
                        <div class="info-item expandable-item" style="display: none;">
                            <strong>الفئة الاجتماعية</strong>
                            <p>${t.cats || '-'}</p>
                        </div>
                        <div class="info-item expandable-item" style="display: none;">
                            <strong>عدد الأبناء المتمدرسين</strong>
                            <p>${t.total_eleves_count || t.totalElevesCount || t.nbr_enfants_scolarise || 0}</p>
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
                      <a class="btn btn-sm btn-outline-primary" href="/user/eleves/export" style="white-space:nowrap;">
                        <i class="fa-solid fa-file-csv"></i> تصدير CSV
                      </a>
                    </div>
        `;
        
        if (eleves.length === 0) {
            html += `
                <div class="empty-state">
                    <i class="fa-solid fa-info-circle"></i>
                    <div>لا يوجد تلاميذ مسجلين</div>
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
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
            `;
            
            eleves.forEach(eleve => {
                const isApproved = eleve.dossier_depose === 'oui';
                const statusClass = isApproved ? 'approved' : 'pending';
                const statusText = isApproved ? 'مقبول' : 'مرفوض';
                
                html += `
                    <tr>
                        <td>${(eleve.prenom || '')} ${(eleve.nom || '')}</td>
                        <td>${eleve.num_scolaire || '-'}</td>
                        <td>${eleve.date_naiss || '-'}</td>
                        <td>${eleve.classe_scol || eleve.niv_scol || '-'}</td>
                        <td>${(eleve.etablissement && eleve.etablissement.nom_etabliss) ? eleve.etablissement.nom_etabliss : '-'}</td>
                        <td><span class="status-badge ${statusClass}">${statusText}</span></td>
                        <td>
                            <div class="eleve-actions" style="display: flex; gap: 0.5rem; justify-content: center; flex-wrap: wrap;">
                                <button class="btn-action btn-view" onclick="openViewEleveModal('${eleve.num_scolaire}')" title="عرض">
                                    <i class="fa-solid fa-eye"></i>
                                </button>
                                <button class="btn-action btn-pdf" onclick="generateIstimaraPDF('${eleve.num_scolaire}')" title="PDF">
                                    <i class="fa-solid fa-file-pdf"></i>
                                </button>
                                ${!isApproved ? `<button class="btn-action btn-approve" onclick="approveEleveFromModal('${eleve.num_scolaire}')" title="موافقة">
                                    <i class="fa-solid fa-check"></i>
                                </button>` : ''}
                                <button class="btn-action btn-delete" onclick="deleteEleveFromModal('${eleve.num_scolaire}')" title="حذف">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                                <button class="btn-action btn-comment" onclick="commentEleve('${eleve.num_scolaire}')" title="تعليق">
                                    <i class="fa-solid fa-comment"></i>
                                </button>
                                ${!isApproved ? `<button class="btn-action btn-decline" onclick="declineEleve('${eleve.num_scolaire}')" title="رفض">
                                    <i class="fa-solid fa-xmark"></i>
                                </button>` : ''}
                            </div>
                        </td>
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
            showCloseButton: false,
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

// Open view eleve modal
async function openViewEleveModal(num_scolaire) {
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
        const response = await fetch(`/user/eleves/${num_scolaire}`, {
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
        
        // Build modal content HTML
        let html = `
            <div class="eleve-details-modal" style="text-align: right; max-height: 70vh; overflow-y: auto; padding: 1rem;">
                <div class="eleve-info-section" style="background: #f8fafc; padding: 1.5rem; border-radius: 12px; margin-bottom: 2rem;">
                    <h6 style="color: #0f033a; font-weight: 700; font-size: 1.25rem; margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 3px solid #2563eb;">معلومات التلميذ</h6>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem;">
                        <div style="background: white; padding: 1rem 1.25rem; border-radius: 8px; border-right: 4px solid #2563eb;">
                            <strong style="color: #4b5563; font-weight: 600; font-size: 0.9rem; display: block; margin-bottom: 0.5rem;">الاسم الكامل</strong>
                            <p style="margin: 0; color: #111827; font-size: 1rem; font-weight: 500;">${(e.prenom || '')} ${(e.nom || '')}</p>
                        </div>
                        <div style="background: white; padding: 1rem 1.25rem; border-radius: 8px; border-right: 4px solid #2563eb;">
                            <strong style="color: #4b5563; font-weight: 600; font-size: 0.9rem; display: block; margin-bottom: 0.5rem;">رقم التعريف المدرسي</strong>
                            <p style="margin: 0; color: #111827; font-size: 1rem; font-weight: 500;">${e.num_scolaire || '-'}</p>
                        </div>
                        <div style="background: white; padding: 1rem 1.25rem; border-radius: 8px; border-right: 4px solid #2563eb;">
                            <strong style="color: #4b5563; font-weight: 600; font-size: 0.9rem; display: block; margin-bottom: 0.5rem;">تاريخ الميلاد</strong>
                            <p style="margin: 0; color: #111827; font-size: 1rem; font-weight: 500;">${e.date_naiss || '-'}</p>
                        </div>
                        <div style="background: white; padding: 1rem 1.25rem; border-radius: 8px; border-right: 4px solid #2563eb;">
                            <strong style="color: #4b5563; font-weight: 600; font-size: 0.9rem; display: block; margin-bottom: 0.5rem;">مكان الميلاد</strong>
                            <p style="margin: 0; color: #111827; font-size: 1rem; font-weight: 500;">${(e.commune_naissance && e.commune_naissance.lib_comm_ar) ? e.commune_naissance.lib_comm_ar : (e.commune_naiss || '-')}</p>
                        </div>
                        <div style="background: white; padding: 1rem 1.25rem; border-radius: 8px; border-right: 4px solid #2563eb;">
                            <strong style="color: #4b5563; font-weight: 600; font-size: 0.9rem; display: block; margin-bottom: 0.5rem;">الجنس</strong>
                            <p style="margin: 0; color: #111827; font-size: 1rem; font-weight: 500;">${e.sexe || '-'}</p>
                        </div>
                        <div style="background: white; padding: 1rem 1.25rem; border-radius: 8px; border-right: 4px solid #2563eb;">
                            <strong style="color: #4b5563; font-weight: 600; font-size: 0.9rem; display: block; margin-bottom: 0.5rem;">المستوى الدراسي</strong>
                            <p style="margin: 0; color: #111827; font-size: 1rem; font-weight: 500;">${e.classe_scol || e.niv_scol || '-'}</p>
                        </div>
                        <div style="background: white; padding: 1rem 1.25rem; border-radius: 8px; border-right: 4px solid #2563eb;">
                            <strong style="color: #4b5563; font-weight: 600; font-size: 0.9rem; display: block; margin-bottom: 0.5rem;">المؤسسة التعليمية</strong>
                            <p style="margin: 0; color: #111827; font-size: 1rem; font-weight: 500;">${(e.etablissement && e.etablissement.nom_etabliss) ? e.etablissement.nom_etabliss : '-'}</p>
                        </div>
                        <div style="background: white; padding: 1rem 1.25rem; border-radius: 8px; border-right: 4px solid #2563eb;">
                            <strong style="color: #4b5563; font-weight: 600; font-size: 0.9rem; display: block; margin-bottom: 0.5rem;">البلدية</strong>
                            <p style="margin: 0; color: #111827; font-size: 1rem; font-weight: 500;">${(e.commune_residence && e.commune_residence.lib_comm_ar) ? e.commune_residence.lib_comm_ar : (e.code_commune || '-')}</p>
                        </div>
                        <div style="background: white; padding: 1rem 1.25rem; border-radius: 8px; border-right: 4px solid #2563eb;">
                            <strong style="color: #4b5563; font-weight: 600; font-size: 0.9rem; display: block; margin-bottom: 0.5rem;">اسم الأب</strong>
                            <p style="margin: 0; color: #111827; font-size: 1rem; font-weight: 500;">${(e.prenom_pere || '')} ${(e.nom_pere || '') || '-'}</p>
                        </div>
                        <div style="background: white; padding: 1rem 1.25rem; border-radius: 8px; border-right: 4px solid #2563eb;">
                            <strong style="color: #4b5563; font-weight: 600; font-size: 0.9rem; display: block; margin-bottom: 0.5rem;">اسم الأم</strong>
                            <p style="margin: 0; color: #111827; font-size: 1rem; font-weight: 500;">
                                ${(e.mother && e.mother.prenom_ar) ? e.mother.prenom_ar : ''} ${(e.mother && e.mother.nom_ar) ? e.mother.nom_ar : ''}
                                ${(e.mother && (e.mother.nom_fr || e.mother.prenom_fr)) ? '<br><small style="color: #6b7280;">' + (e.mother.prenom_fr || '') + ' ' + (e.mother.nom_fr || '') + '</small>' : ''}
                                ${(!e.mother || (!e.mother.nom_ar && !e.mother.prenom_ar)) ? '-' : ''}
                            </p>
                        </div>
                        <div style="background: white; padding: 1rem 1.25rem; border-radius: 8px; border-right: 4px solid #2563eb;">
                            <strong style="color: #4b5563; font-weight: 600; font-size: 0.9rem; display: block; margin-bottom: 0.5rem;">حالة الموافقة</strong>
                            <p style="margin: 0;">
                                <span style="background: ${e.dossier_depose === 'oui' ? '#10b981' : '#6b7280'}; color: white; padding: 0.5rem 1rem; border-radius: 20px; font-size: 0.85rem; font-weight: 600;">
                                    ${e.dossier_depose === 'oui' ? 'موافق عليه' : 'غير موافق عليه'}
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        Swal.fire({
            title: 'تفاصيل التلميذ',
            html: html,
            width: '90%',
            maxWidth: '1200px',
            showCloseButton: false,
            showConfirmButton: true,
            confirmButtonText: 'إغلاق',
            confirmButtonColor: '#0f033a',
            customClass: {
                popup: 'swal-eleve-modal',
                htmlContainer: 'swal-eleve-content'
            },
            didOpen: () => {
                const content = document.querySelector('.swal-eleve-content');
                if (content) {
                    content.style.maxHeight = '70vh';
                    content.style.overflowY = 'auto';
                }
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

// Approve eleve from modal
async function approveEleveFromModal(num_scolaire) {
    Swal.fire({
        title: 'تأكيد الموافقة',
        text: `هل تريد الموافقة على التلميذ رقم ${num_scolaire}؟`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'نعم، أوافق',
        cancelButtonText: 'إلغاء',
        reverseButtons: true,
        confirmButtonColor: '#10b981'
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                const response = await fetch(`/user/eleves/${num_scolaire}/approve`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json'
                    }
                });
                
                const data = await response.json();
                
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'تمت الموافقة',
                        text: 'تمت الموافقة على التلميذ بنجاح',
                        confirmButtonText: 'حسنًا'
                    }).then(() => {
                        window.location.reload();
                    });
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
    });
}

// Delete eleve from modal
async function deleteEleveFromModal(num_scolaire) {
    Swal.fire({
        title: 'تأكيد الحذف',
            text: `هل أنت متأكد من حذف التلميذ رقم ${num_scolaire}؟ سيتم فقدان كل البيانات المرتبطة.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'نعم، احذف',
        cancelButtonText: 'إلغاء',
        reverseButtons: true,
        confirmButtonColor: '#ef4444'
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                const response = await fetch(`/user/eleves/${num_scolaire}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json'
                    }
                });
                
                const data = await response.json();
                
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'تم الحذف',
                        text: 'تم حذف التلميذ بنجاح',
                        confirmButtonText: 'حسنًا'
                    }).then(() => {
                        window.location.reload();
                    });
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
    });
}

// Comment eleve - open modal to add comment
async function commentEleve(num_scolaire) {
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

    // Build HTML for existing comments
    let commentsHTML = '';
    if (existingComments.length > 0) {
        commentsHTML = '<div style="max-height: 300px; overflow-y: auto; margin-bottom: 1.5rem; padding: 1rem; background: #f8fafc; border-radius: 8px;">';
        existingComments.forEach(comment => {
            const dateObj = new Date(comment.created_at);
            const months = ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'];
            const day = dateObj.getDate();
            const month = months[dateObj.getMonth()];
            const year = dateObj.getFullYear();
            const hours = dateObj.getHours();
            const minutes = String(dateObj.getMinutes()).padStart(2, '0');
            const ampm = hours >= 12 ? 'م' : 'ص';
            const displayHours = hours > 12 ? hours - 12 : (hours === 0 ? 12 : hours);
            const date = `${day} ${month} ${year} في ${displayHours}:${minutes} ${ampm}`;
            commentsHTML += `
                <div style="background: white; padding: 1rem; margin-bottom: 0.75rem; border-radius: 8px; border-right: 4px solid #2563eb;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                        <strong style="color: #0f033a; font-size: 0.9rem;">${(comment.user && comment.user.nom_user) ? comment.user.nom_user + ' ' + (comment.user.prenom_user || '') : 'مستخدم'}</strong>
                        <span style="color: #6b7280; font-size: 0.8rem;">${date}</span>
                    </div>
                    <p style="margin: 0; color: #374151; line-height: 1.6;">${comment.text}</p>
                </div>
            `;
        });
        commentsHTML += '</div>';
    } else {
        commentsHTML = '<div style="text-align: center; padding: 2rem; color: #6b7280; background: #f8fafc; border-radius: 8px; margin-bottom: 1.5rem;">لا توجد تعليقات سابقة</div>';
    }

    Swal.fire({
        title: 'التعليقات',
        html: `
            ${commentsHTML}
            <div style="margin-top: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem; color: #374151; font-weight: 600;">إضافة تعليق جديد:</label>
                <textarea id="commentText" rows="4" style="width: 100%; padding: 0.75rem; border: 2px solid #e5e7eb; border-radius: 8px; font-family: 'Cairo', sans-serif; font-size: 1rem; resize: vertical;" placeholder="اكتب تعليقك هنا..."></textarea>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'إضافة',
        cancelButtonText: 'إلغاء',
        reverseButtons: true,
        confirmButtonColor: '#2563eb',
        width: '600px',
        didOpen: () => {
            const textarea = document.getElementById('commentText');
            if (textarea) {
                textarea.focus();
            }
        },
        preConfirm: async () => {
            const text = document.getElementById('commentText').value.trim();
            if (!text) {
                Swal.showValidationMessage('يرجى إدخال نص التعليق');
                return false;
            }
            if (text.length > 1000) {
                Swal.showValidationMessage('التعليق طويل جداً (الحد الأقصى 1000 حرف)');
                return false;
            }
            return text;
        }
    }).then(async (result) => {
        if (result.isConfirmed && result.value) {
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
                        title: 'تمت الإضافة',
                        text: 'تمت إضافة التعليق بنجاح',
                        confirmButtonText: 'حسنًا',
                        confirmButtonColor: '#2563eb'
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'خطأ',
                        text: data.message || 'فشلت إضافة التعليق',
                        confirmButtonText: 'حسنًا'
                    });
                }
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ',
                    text: 'حدث خطأ أثناء إضافة التعليق',
                    confirmButtonText: 'حسنًا'
                });
            }
        }
    });
}

// Decline eleve (placeholder)
function declineEleve(num_scolaire) {
    Swal.fire({
        title: 'رفض الطلب',
        text: 'هذه الميزة قيد التطوير',
        icon: 'info',
        confirmButtonText: 'حسنًا'
    });
}

// Generate istimara PDF for normal users
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

    // Show loading
    Swal.fire({
        title: 'جارٍ التوليد...',
        html: 'جاري توليد ملف PDF...',
        allowOutsideClick: false,
        allowEscapeKey: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    try {
        // Generate the PDF via AJAX
        const response = await fetch(`/user/eleves/${num_scolaire}/istimara/generate`, {
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

        // Close loading
        Swal.close();

        // Open PDF in new tab using the view route with regenerate parameter to ensure fresh PDF
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

// Load user data from API to update header
async function loadUserData() {
    try {
        const token = localStorage.getItem('api_token');
        const tokenType = localStorage.getItem('token_type') || 'Bearer';
        
        if (!token) {
            console.warn('No token found, using session data');
            return;
        }
        
        const response = await fetch('/api/user/current', {
            headers: {
                'Authorization': `${tokenType} ${token}`,
                'Accept': 'application/json'
            }
        });
        
        if (response.ok) {
            const result = await response.json();
            if (result.success && result.data) {
                const user = result.data;
                
                // Update header elements
                const userNameEl = document.getElementById('user-name');
                const userRoleEl = document.getElementById('user-role');
                const userCommuneEl = document.getElementById('user-commune');
                
                if (userNameEl) {
                    userNameEl.textContent = `مرحباً، ${user.user_name || user.nom_user + ' ' + user.prenom_user}`;
                }
                if (userRoleEl) {
                    userRoleEl.textContent = `الوظيفة: ${user.role || '-'}`;
                }
                if (userCommuneEl) {
                    userCommuneEl.textContent = `بلدية: ${user.commune || 'غير محددة'}`;
                }
            }
        }
    } catch (error) {
        console.error('Error loading user data:', error);
        // Fallback to session data if API fails
    }
}

// Update stats cards based on data
function updateStatsCards(tuteurs) {
    const totalTuteurs = tuteurs.length;
    let totalStudents = 0;
    let approvedCount = 0;
    let pendingCount = 0;
    
    tuteurs.forEach(tuteur => {
        totalStudents += tuteur.total_count || 0;
        approvedCount += tuteur.approved_count || 0;
        pendingCount += (tuteur.total_count || 0) - (tuteur.approved_count || 0);
    });
    
    // Animate the numbers
    animateValue('total-tuteurs', 0, totalTuteurs, 1000);
    animateValue('total-students', 0, totalStudents, 1000);
    animateValue('approved-count', 0, approvedCount, 1000);
    animateValue('pending-count', 0, pendingCount, 1000);
}

// Animate number counter
function animateValue(id, start, end, duration) {
    const element = document.getElementById(id);
    if (!element) return;
    
    const range = end - start;
    const increment = range / (duration / 16); // 60 FPS
    let current = start;
    
    const timer = setInterval(() => {
        current += increment;
        if ((increment > 0 && current >= end) || (increment < 0 && current <= end)) {
            current = end;
            clearInterval(timer);
        }
        element.textContent = Math.floor(current);
    }, 16);
}

// 🔹 Table switch logic
document.addEventListener('DOMContentLoaded', () => {
    // Load user data from API
    loadUserData();
    
    const tableBody = document.getElementById('table-body');
    const tableTitle = document.getElementById('table-title');
    const backBtn = document.getElementById('back-btn');
    const tableHead = document.getElementById('table-head');
    const schoolFilter = document.getElementById('schoolFilter');
    const schoolSearch = document.getElementById('schoolSearch');
    const schoolDropdown = document.getElementById('schoolDropdown');
    const schoolDropdownList = document.getElementById('schoolDropdownList');
    const selectedSchool = document.getElementById('selectedSchool');
    const ninSearch = document.getElementById('ninSearch');
    const clearFilters = document.getElementById('clearFilters');
    const paginationContainer = document.getElementById('pagination-container');

    let currentPage = 1;
    let currentFilter = '';
    let currentNinSearch = '';
    let searchTimeout = null;
    let originalTuteursHTML = '';
    let allSchools = [];

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
                updateStatsCards([]);
                return;
            }
            
            // Update stats cards with tuteurs data
            updateStatsCards(tuteurs);

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
                    <tr class="tuteur-row" data-eleves='${JSON.stringify(tuteur.eleves)}' data-nin="${tuteur.nin}">
                        <td>
                            <div class="action-buttons" style="display: flex; gap: 5px;">
                                <button class="btn btn-sm btn-info view-tuteur-btn" data-nin="${tuteur.nin}" title="عرض">
                                    <i class="fa-solid fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-danger delete-tuteur-btn" data-nin="${tuteur.nin}" title="حذف">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </div>
                        </td>
                        <td>${statusBadge}</td>
                        <td class="children-count clickable-cell">${tuteur.total_count}</td>
                        <td>${tuteur.cats}</td>
                        <td>${tuteur.nom} ${tuteur.prenom}</td>
                        <td>${tuteur.nin}</td>
                    </tr>
                `;
            });

            tableBody.innerHTML = html;
            originalTuteursHTML = html;

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
        }, 500); // Wait 500ms after user stops typing
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

    // Initial load
    loadTuteurs(1);

    // Delegate click events for better performance
    tableBody.addEventListener('click', (e) => {
        if (e.target.classList.contains('clickable-cell')) {
            const row = e.target.closest('tr');
            const eleves = JSON.parse(row.dataset.eleves);
            const tuteurName = row.cells[4].innerText; // Name is now in column 4 (index 4)

            // Change table title
            tableTitle.innerText = `تلاميذ الوصي/الولي: ${tuteurName}`;
            backBtn.style.display = 'inline-block';

            // Change table head
            tableHead.innerHTML = `
                <tr>
                    <th>الإجراءات</th>
                    <th>حالة الموافقة</th>
                    <th>المستوى الدراسي</th>
                    <th>تاريخ الميلاد</th>
                    <th>الاسم الكامل</th>
                </tr>
            `;

            // Fill table body with children
            if (eleves.length === 0) {
                tableBody.innerHTML = '<tr><td colspan="5">لا يوجد أطفال لهذا الوصي/الولي</td></tr>';
                return;
            }

            tableBody.innerHTML = '';
            eleves.forEach(eleve => {
                const isApproved = eleve.dossier_depose === 'oui';
                const statusBadge = isApproved 
                    ? '<span class="badge bg-success">موافق عليه</span>'
                    : '<span class="badge bg-secondary">غير موافق عليه</span>';
                const approveButton = isApproved 
                    ? '<button class="btn btn-sm btn-success" disabled><i class="fa-solid fa-check"></i> موافق عليه</button>'
                    : `<button class="btn btn-sm btn-outline-success approve-eleve-btn" data-num-scolaire="${eleve.num_scolaire}" title="موافقة"><i class="fa-solid fa-check"></i> موافقة</button>`;
                
                tableBody.innerHTML += `
                    <tr>
                        <td>
                            ${approveButton}
                        </td>
                        <td>${statusBadge}</td>
                        <td>${eleve.classe_scol ?? eleve.niv_scol ?? '-'}</td>
                        <td>${eleve.date_naiss ?? '-'}</td>
                        <td>${(eleve.prenom_ar ?? eleve.prenom ?? '') + ' ' + (eleve.nom_ar ?? eleve.nom ?? '')}</td>
                    </tr>
                `;
            });
        }
    });

    // Back button logic
    backBtn.addEventListener('click', () => {
        tableTitle.innerText = 'قائمة الأوصياء/الأولياء';
        backBtn.style.display = 'none';
        tableHead.innerHTML = `
            <tr>
                <th>الإجراءات</th>
                <th>حالة الموافقة</th>
                <th>عدد الأطفال</th>
                <th>الفئة الاجتماعية</th>
                <th>الاسم الكامل</th>
                <th>رقم التعريف الوطني</th>
            </tr>
        `;
        loadTuteurs(currentPage, currentFilter, currentNinSearch);
    });

    // Handle view tuteur button
    document.addEventListener('click', (e) => {
        if (e.target.closest('.view-tuteur-btn')) {
            const btn = e.target.closest('.view-tuteur-btn');
            const nin = btn.dataset.nin;
            openViewTuteurModal(nin);
        }

        // Handle delete tuteur button
        if (e.target.closest('.delete-tuteur-btn')) {
            const btn = e.target.closest('.delete-tuteur-btn');
            const nin = btn.dataset.nin;
            
            Swal.fire({
                title: 'تأكيد الحذف',
                text: `هل أنت متأكد من حذف الوصي/الولي ${nin}؟`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'نعم، احذف',
                cancelButtonText: 'إلغاء',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/user/tuteurs/${nin}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire('تم الحذف', 'تم حذف الوصي/الولي بنجاح', 'success')
                                .then(() => loadTuteurs(currentPage, currentFilter));
                        } else {
                            Swal.fire('خطأ', data.message || 'فشل الحذف', 'error');
                        }
                    })
                    .catch(error => {
                        Swal.fire('خطأ', 'حدث خطأ أثناء الحذف', 'error');
                    });
                }
            });
        }

        // Handle approve eleve button
        if (e.target.closest('.approve-eleve-btn')) {
            const btn = e.target.closest('.approve-eleve-btn');
            const numScolaire = btn.dataset.numScolaire;
            
            Swal.fire({
                title: 'تأكيد الموافقة',
                text: `هل تريد الموافقة على التلميذ رقم ${numScolaire}؟`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'نعم، أوافق',
                cancelButtonText: 'إلغاء',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/user/eleves/${numScolaire}/approve`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire('تمت الموافقة', 'تمت الموافقة على التلميذ بنجاح', 'success')
                                .then(() => window.location.reload());
                        } else {
                            Swal.fire('خطأ', data.message || 'فشلت الموافقة', 'error');
                        }
                    })
                    .catch(error => {
                        Swal.fire('خطأ', 'حدث خطأ أثناء الموافقة', 'error');
                    });
                }
            });
        }
    });
});
</script>

@endsection
