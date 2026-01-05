@extends('layouts.main')

@section('title', 'لوحة الوصي/الولي')

@push('styles')
@vite(['resources/css/tuteur-dashboard.css'])
<!-- SweetAlert2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
<style>
    .notification-bar {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 12px 0;
        box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        right: 0 !important;
        width: 100% !important;
        z-index: 10000 !important; /* Higher than navbar (100) and any other element */
        border-bottom: 2px solid rgba(255,255,255,0.2);
        margin: 0 !important;
    }
    
    /* Ensure navbar and other elements are below notification bar */
    .main-navbar {
        z-index: 100 !important;
    }
    
    /* Ensure dashboard header doesn't overlap */
    .dashboard-header {
        position: relative;
        z-index: 1;
    }
    
    /* Adjust main content when notification is visible */
    .main-content.has-notification {
        padding-top: calc(2rem + 56px) !important;
    }
    
    .notification-bar .container-fluid {
        max-width: 100%;
        padding: 0 20px;
        margin: 0 auto;
    }
    
    .notification-bar .notification-text {
        font-size: 15px;
        font-weight: 500;
        margin: 0;
        letter-spacing: 0.3px;
        line-height: 1.4;
    }
    
    .notification-bar .fa-info-circle {
        font-size: 18px;
        opacity: 0.9;
        flex-shrink: 0;
    }
    
    .btn-close-notification {
        background: rgba(255, 255, 255, 0.2);
        border: none;
        color: white;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
        padding: 0;
        margin: 0;
        flex-shrink: 0;
    }
    
    .btn-close-notification:hover {
        background: rgba(255, 255, 255, 0.3);
        transform: scale(1.1);
    }
    
    .btn-close-notification:active {
        transform: scale(0.95);
    }
    
    .btn-close-notification .fa-times {
        font-size: 14px;
    }
    
    /* Animation for slide down */
    @keyframes slideDown {
        from {
            transform: translateY(-100%);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }
    
    .notification-bar.show {
        display: block !important;
        animation: slideDown 0.4s ease-out;
    }
    
    /* Adjust dashboard container when notification is visible */
    .dashboard-container {
        transition: padding-top 0.3s ease;
    }
    
    .dashboard-container.has-notification {
        padding-top: 56px; /* Height of notification bar + padding */
    }
    
    /* Adjust main content when notification is visible */
    .main-content.has-notification {
        padding-top: calc(2rem + 56px) !important;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .notification-bar {
            padding: 10px 0;
        }
        
        .notification-bar .notification-text {
            font-size: 13px;
            line-height: 1.3;
        }
        
        .notification-bar .container-fluid {
            padding: 0 15px;
        }
        
        .notification-bar .fa-info-circle {
            font-size: 16px;
        }
        
        .btn-close-notification {
            width: 28px;
            height: 28px;
        }
        
        .btn-close-notification .fa-times {
            font-size: 12px;
        }
        
        .dashboard-container.has-notification {
            padding-top: 48px; /* Smaller padding on mobile */
        }
    }
    
    @media (max-width: 480px) {
        .notification-bar .notification-text {
            font-size: 12px;
        }
        
        .notification-bar .fa-info-circle {
            font-size: 14px;
            margin-right: 8px !important;
        }
        
        .dashboard-container.has-notification {
            padding-top: 44px;
        }
    }
    
    /* Fix radio button display for handicap field - Standard Bootstrap styling */
    #addChildModal .form-check,
    #editChildModal .form-check {
        display: flex !important;
        align-items: center !important;
        margin-bottom: 0 !important;
        padding-left: 0 !important;
    }
    
    #addChildModal .form-check-input[type="radio"],
    #editChildModal .form-check-input[type="radio"] {
        float: none !important;
        margin-left: 0 !important;
        margin-right: 0.5em !important;
        margin-top: 0.125em !important;
        width: 1em !important;
        height: 1em !important;
        vertical-align: middle !important;
        cursor: pointer !important;
        flex-shrink: 0 !important;
    }
    
    #addChildModal .form-check-label,
    #editChildModal .form-check-label {
        cursor: pointer !important;
        margin-bottom: 0 !important;
        padding-right: 0 !important;
        user-select: none !important;
        line-height: 1.5 !important;
    }
    
    /* Ensure radio buttons are visible and properly styled */
    #addChildModal input[type="radio"],
    #editChildModal input[type="radio"] {
        appearance: radio !important;
        -webkit-appearance: radio !important;
        -moz-appearance: radio !important;
        display: inline-block !important;
        visibility: visible !important;
        opacity: 1 !important;
    }
</style>
@endpush

@section('content')
<!-- Notification Bar -->
<div id="notification-bar" class="notification-bar" style="display: none;">
    <div class="container-fluid">
        <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center">
                <i class="fas fa-info-circle me-2"></i>
                <span class="notification-text">آخر أجل للولوج إلى المنصة: 28 فيفري 2026</span>
            </div>
            <button type="button" class="btn-close-notification" id="close-notification" aria-label="إغلاق">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
</div>

<div class="dashboard-container">

    <!-- Logout Form (hidden) -->
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>


    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        async function confirmLogout() {
            const result = await Swal.fire({
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
                buttonsStyling: false // ✅ allows us to fully control the button design
            }).then(async (result) => {
                if (result.isConfirmed) {
                    // Call API logout to revoke token
                    try {
                        await apiFetch('/api/auth/tuteur/logout', {
                            method: 'POST',
                        });
                    } catch (error) {
                        // Logout API error
                    }
                    
                    // Clear token from localStorage
                    localStorage.removeItem('api_token');
                    localStorage.removeItem('token_type');
                    
                    // Submit form for web logout (if needed)
                    document.getElementById('logout-form').submit();
                }
            });
        }
    </script>



    <!-- Welcome header -->
    <div class="dashboard-header">
        <div class="dashboard-header-content">
            @php
                $tuteur = session('tuteur');
                $nom = $tuteur['nom_ar'] ?? $tuteur['nom_fr'] ?? '';
                $prenom = $tuteur['prenom_ar'] ?? $tuteur['prenom_fr'] ?? '';
            @endphp

            <h2>مرحبًا بك، {{ trim($nom . ' ' . $prenom) ?: 'الوصي' }}</h2>
            <p>إدارة بياناتك وبيانات التلاميذ من خلال هذه الواجهة</p>
        </div>
        <button class="logout-btn" onclick="confirmLogout()">
            <i class="fa-solid fa-right-from-bracket"></i>
            <span>تسجيل الخروج</span>
        </button>
    </div>

    <!-- Quick action boxes -->
    <div class="dashboard-actions">
        <!-- Settings -->
        <div class="action-card">
            <i class="fa-solid fa-gear"></i>
            <h4>الإعدادات</h4>
            <p>تغيير كلمة المرور </p>
        </div>

        <div class="action-card" data-bs-toggle="modal" data-bs-target="#addChildModal">
            <i class="fa-solid fa-user-plus"></i>
            <h4>إضافة تلميذ</h4>
            <p>تسجيل تلميذ جديد</p>
        </div>

        <div class="action-card" onclick="window.location.href='{{ route('tuteur.profile') }}'">
            <i class="fa-solid fa-user"></i>
            <h4>معلوماتي الشخصية</h4>
            <p>عرض وتحديث بيانات الحساب</p>
        </div>
    </div>


<!-- Modal personnalisé pour le changement de mot de passe -->
<div id="settingsModal" class="settings-modal">
    <div class="settings-content animate-scale">
        <div class="modal-header-custom">
            <i class="fas fa-lock fa-lg"></i>
            <h3>تغيير كلمة المرور</h3>
        </div>

        <form id="changePasswordForm" dir="rtl" class="modal-form">
            @csrf

            <div class="mb-3 password-field">
                <label class="form-label required">كلمة المرور الحالية</label>
                <div class="input-wrapper">
                    <i class="fas fa-eye toggle-password" onclick="togglePassword(this)"></i>
                    <input type="password" name="current_password" class="form-control shadow-sm" required>
                </div>
                <div class="error-msg"></div>
            </div>

            <div class="mb-3 password-field">
                <label class="form-label required">كلمة المرور الجديدة</label>
                <div class="input-wrapper">
                    <i class="fas fa-eye toggle-password" onclick="togglePassword(this)"></i>
                    <input type="password" name="new_password" class="form-control shadow-sm" required>
                </div>
                <div class="error-msg"></div>
            </div>

            <div class="mb-4 password-field">
                <label class="form-label required">تأكيد كلمة المرور الجديدة</label>
                <div class="input-wrapper">
                    <i class="fas fa-eye toggle-password" onclick="togglePassword(this)"></i>
                    <input type="password" name="confirm_password" class="form-control shadow-sm" required>
                </div>
                <div class="error-msg"></div>
            </div>

            <div class="d-flex justify-content-between">
                <button type="button" id="confirmChangeBtn" class="btn btn-golden">تأكيد</button>
                <button type="button" id="cancelSettingsBtn" class="btn btn-outline-dark">إلغاء</button>
            </div>
        </form>
    </div>
</div>

    <!-- Table of children -->
    <div class="children-table-section">
        <h3>قائمة التلاميذ</h3>
        <div class="table-responsive-wrapper">
            <table class="students-table">
                <thead>
                    <tr>
                        <th>الاسم الكامل</th>
                        <th>تاريخ الميلاد</th>
                        <th>المستوى الدراسي</th>
                        <th>المؤسسة التعليمية</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody id="studentsTableBody">
                    <tr>
                        <td colspan="5" class="loading-message">جارٍ تحميل البيانات...</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="students-mobile-container"></div>
    </div>
</div>
<!-- Custom Dark Overlay -->
<div id="customModalOverlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.75); z-index: 1040; backdrop-filter: blur(2px);"></div>

<!-- View Child Modal (Read-Only) -->
<div class="modal fade" id="viewChildModal" tabindex="-1" aria-labelledby="viewChildModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg rounded-4">
      
      <!-- Header -->
      <div class="modal-header" style="background-color:#0f033a; color:white;">
        <h5 class="modal-title" id="viewChildModalLabel">
          <i class="fa-solid fa-eye me-2 text-warning"></i> عرض معلومات التلميذ
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="إغلاق"></button>
      </div>

      <!-- Form (Read-Only) -->
      <form id="viewChildForm" class="p-3">
        <div class="modal-body">
          <div class="container-fluid">
            <div id="viewStep2" class="step-content" dir="rtl" style="text-align: right;">
                <h5 class="fw-bold mb-3 text-center" style="color:#0f033a;">معلومات التلميذ</h5>

                <div class="row g-3">
                    <!-- الاسم واللقب -->
                    <div class="col-md-6">
                      <label class="form-label fw-bold">اللقب بالعربية</label>
                      <input type="text" id="view_nom" class="form-control" dir="rtl" readonly>
                    </div>
                    <div class="col-md-6">
                      <label class="form-label fw-bold">الاسم بالعربية</label>
                      <input type="text" id="view_prenom" class="form-control" dir="rtl" readonly>
                    </div>

                    <!-- الأب والأم -->
                    <div class="col-md-6">
                      <label class="form-label fw-bold">لقب الأب بالعربية</label>
                      <input type="text" id="view_nom_pere" class="form-control" dir="rtl" readonly>
                    </div>
                    <div class="col-md-6">
                      <label class="form-label fw-bold">اسم الأب بالعربية</label>
                      <input type="text" id="view_prenom_pere" class="form-control" dir="rtl" readonly>
                    </div>

                    <div class="col-md-6">
                      <label class="form-label fw-bold">لقب الأم بالعربية</label>
                      <input type="text" id="view_nom_mere" class="form-control" dir="rtl" readonly>
                    </div>
                    <div class="col-md-6">
                      <label class="form-label fw-bold">اسم الأم بالعربية</label>
                      <input type="text" id="view_prenom_mere" class="form-control" dir="rtl" readonly>
                    </div>

                    <!-- الميلاد -->
                    <div class="col-md-6">
                      <label class="form-label fw-bold">تاريخ الميلاد</label>
                      <input type="text" id="view_date_naiss" class="form-control" readonly>
                    </div>

                    <div class="col-md-3">
                      <label class="form-label fw-bold">ولاية الميلاد</label>
                      <input type="text" id="view_wilaya_naiss" class="form-control" readonly>
                    </div>
                    <div class="col-md-3">
                      <label class="form-label fw-bold">بلدية الميلاد</label>
                      <input type="text" id="view_commune_naiss" class="form-control" readonly>
                    </div>

                    <!-- القسم والجنس -->
                    <div class="col-md-6">
                      <label class="form-label fw-bold">القسم</label>
                      <input type="text" id="view_classe_scol" class="form-control" readonly>
                    </div>

                    <div class="col-md-6">
                      <label class="form-label fw-bold">الجنس</label>
                      <input type="text" id="view_sexe" class="form-control" readonly>
                    </div>

                    <div class="col-md-4">
                      <label class="form-label fw-bold">العلاقة بالتلميذ</label>
                      <input type="text" id="view_relation_tuteur" class="form-control" readonly>
                    </div>

                    <!-- الحالة الاجتماعية -->
                    <div class="col-md-4">
                      <label class="form-label fw-bold">هل لديه احتياجات خاصة؟</label>
                      <input type="text" id="view_handicap" class="form-control" readonly>
                    </div>

                    <!-- NIN + NSS -->
                    <div class="col-md-6">
                      <label class="form-label fw-bold">الرقم الوطني للأب (NIN)</label>
                      <input type="text" id="view_nin_pere" class="form-control" readonly>
                    </div>
                    <div class="col-md-6">
                      <label class="form-label fw-bold">الرقم الوطني للأم (NIN)</label>
                      <input type="text" id="view_nin_mere" class="form-control" readonly>
                    </div>

                    <div class="col-md-6">
                      <label class="form-label fw-bold">رقم الضمان الاجتماعي  للأب (NSS)</label>
                      <input type="text" id="view_nss_pere" class="form-control" readonly>
                    </div>
                    <div class="col-md-6">
                      <label class="form-label fw-bold">رقم الضمان الاجتماعي  للأم (NSS)</label>
                      <input type="text" id="view_nss_mere" class="form-control" readonly>
                    </div>

                    <!-- School Info -->
                    <div class="col-md-12">
                      <hr class="my-4">
                      <h6 class="fw-bold mb-3" style="color:#0f033a;">معلومات المؤسسة التعليمية</h6>
                    </div>

                    <div class="col-md-6">
                      <label class="form-label fw-bold">المؤسسة التعليمية</label>
                      <input type="text" id="view_etablissement" class="form-control" readonly>
                    </div>
                    <div class="col-md-3">
                      <label class="form-label fw-bold">مؤسسة التربية والتعليم</label>
                      <input type="text" id="view_type_ecole" class="form-control" readonly>
                    </div>
                    <div class="col-md-3">
                      <label class="form-label fw-bold">المستوى الدراسي</label>
                      <input type="text" id="view_niveau" class="form-control" readonly>
                    </div>
                </div>

                <!-- Close Button -->
                <div class="d-flex justify-content-center mt-4">
                  <button type="button" class="btn px-5" data-bs-dismiss="modal" style="background-color:#0f033a; color:white; font-weight:bold;">
                    إغلاق <i class="fa-solid fa-times ms-1"></i>
                  </button>
                </div>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Edit Child Modal (Two-Step) -->
<div class="modal fade" id="editChildModal" tabindex="-1" aria-labelledby="editChildModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg rounded-4">
      
      <!-- Header -->
      <div class="modal-header" style="background-color:#0f033a; color:white;">
        <h5 class="modal-title" id="editChildModalLabel">
          <i class="fa-solid fa-user-edit me-2 text-warning"></i> تعديل معلومات التلميذ
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="إغلاق"></button>
      </div>

      <!-- Form -->
      <form id="editChildForm" class="p-3">
        @csrf
        @method('PUT')
        <input type="hidden" name="num_scolaire" id="edit_num_scolaire">
        <div class="modal-body">
          <div class="container-fluid">

            <!-- === STEP 1: School Selection (Arabic RTL) === -->
            <div id="editStep1" class="step-content" dir="rtl" style="text-align: right;">
                <h5 class="fw-bold mb-3 text-center" style="color:#0f033a;">الخطوة 1: اختيار المؤسسة التعليمية</h5>
                <div class="row g-3">

                    <!-- مؤسسة التربية والتعليم + المستوى الدراسي -->
                    <div class="col-md-6">
                    <label class="form-label fw-bold required">مؤسسة التربية والتعليم</label>
                    <select class="form-select" name="type_ecole" id="edit_type_ecole" required>
                        <option value="">اختر...</option>
                        <option value="عمومية">عمومية</option>
                        <option value="متخصصة">متخصصة عمومية</option>
                    </select>
                    </div>

                    <div class="col-md-6">
                    <label class="form-label fw-bold required">المستوى الدراسي</label>
                    <select class="form-select" name="niveau" id="edit_niveau" required>
                        <option value="">اختر...</option>
                        <option value="ابتدائي">ابتدائي</option>
                        <option value="متوسط">متوسط</option>
                        <option value="ثانوي">ثانوي</option>
                    </select>
                    </div>

                    <!-- الولاية + البلدية -->
                    <div class="col-md-6">
                    <label class="form-label fw-bold required">الولاية</label>
                    <select class="form-select" name="wilaya_id" id="editWilayaSelect" required>
                        <option value="">اختر...</option>
                    </select>
                    </div>

                    <div class="col-md-6">
                    <label class="form-label fw-bold required">البلدية</label>
                    <select class="form-select" name="commune_id" id="editCommuneSelect" required disabled>
                        <option value="">اختر الولاية أولا...</option>
                    </select>
                    </div>

                    <!-- المؤسسة -->
                    <div class="col-md-12">
                    <label class="form-label fw-bold required">المؤسسة التعليمية</label>
                    <select class="form-select" name="ecole" id="editEcoleSelect" required>
                        <option value="">اختر...</option>
                    </select>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="d-flex justify-content-between align-items-center mt-4 flex-row-reverse">
                    <button type="button" class="btn px-4" id="editNextStep"
                    style="background-color:#fdae4b; color:#0f033a; font-weight:bold;">
                    التالي <i class="fa-solid fa-arrow-left ms-1"></i>
                    </button>
                    <button type="button" class="btn btn-outline-danger px-4" id="editReloadStep1">
                    <i class="fa-solid fa-rotate"></i> إعادة تعيين
                    </button>
                </div>
            </div>

            <!-- === STEP 2: Student Info (Arabic RTL) === -->
            <div id="editStep2" class="step-content d-none" dir="rtl" style="text-align: right;">
                <h5 class="fw-bold mb-3 text-center" style="color:#0f033a;">الخطوة 2: تعديل معلومات التلميذ</h5>

                <div class="row g-3">
                    <!-- الأم/الزوجة و صفة طالب المنحة - Top Row -->
                    <div class="col-md-6" id="edit_motherSelectWrapper">
                      <label class="form-label fw-bold" id="edit_motherSelectLabel">الأم/الزوجة</label>
                      <select name="mother_id" id="editMotherSelect" class="form-select">
                        <option value="">اختر الأم/الزوجة...</option>
                      </select>
                    </div>

                    <!-- Father Info (for Guardian role only) -->
                    <div class="col-md-6" id="edit_fatherInfoWrapper" style="display: none;">
                      <label class="form-label fw-bold">الأب</label>
                      <input type="text" id="edit_fatherNameDisplay" class="form-control" readonly style="background-color: #f8f9fa;">
                    </div>

                    <div class="col-md-6">
                      <label class="form-label fw-bold required">صفة طالب المنحة</label>
                      <select name="relation_tuteur" id="edit_relation_tuteur" class="form-select" required>
                          <option value="">اختر...</option>
                          <option value="1" id="editWaliOption">ولي</option>
                          <option value="3">وصي</option>
                      </select>
                    </div>

                    <!-- الاسم واللقب -->
                    <div class="col-md-6">
                      <label class="form-label fw-bold required">اللقب بالعربية</label>
                      <input type="text" name="nom" id="edit_nom" class="form-control" dir="rtl" required>
                    </div>
                    <div class="col-md-6">
                      <label class="form-label fw-bold required">الاسم بالعربية</label>
                      <input type="text" name="prenom" id="edit_prenom" class="form-control" dir="rtl" required>
                    </div>

                    <!-- الأب والأم -->
                    <div class="col-md-6" id="edit_nomPereWrapper">
                      <label class="form-label fw-bold required" id="edit_nomPereLabel">لقب الأب بالعربية</label>
                      <input type="text" name="nom_pere" id="edit_nom_pere" class="form-control" dir="rtl" required>
                    </div>
                    <div class="col-md-6" id="edit_prenomPereWrapper">
                      <label class="form-label fw-bold required" id="edit_prenomPereLabel">اسم الأب بالعربية</label>
                      <input type="text" name="prenom_pere" id="edit_prenom_pere" class="form-control" dir="rtl" required>
                    </div>

                    <!-- الميلاد -->
                    <div class="col-md-6">
                      <label class="form-label fw-bold required">تاريخ الميلاد</label>
                      <input type="date" name="date_naiss" id="edit_date_naiss" class="form-control">
                    </div>

                    <div class="col-md-3">
                      <label class="form-label fw-bold required">ولاية الميلاد</label>
                      <select name="wilaya_naiss" id="editWilayaNaiss" class="form-select" required>
                          <option value="">اختر...</option>
                      </select>
                    </div>
                    <div class="col-md-3">
                      <label class="form-label fw-bold required">بلدية الميلاد</label>
                      <select name="commune_naiss" id="editCommuneNaiss" class="form-select" required disabled>
                          <option value="">اختر الولاية أولا...</option>
                      </select>
                    </div>

                    <!-- القسم والجنس -->
                    <div class="col-md-6">
                      <label class="form-label fw-bold required">القسم</label>
                      <select id="editClasseSelect" name="classe_scol" class="form-select" required>
                        <option value="">اختر...</option>
                      </select>
                    </div>

                    <div class="col-md-6">
                      <label class="form-label fw-bold required">الجنس</label>
                      <div class="d-flex gap-4 mt-2">
                        <div class="form-check">
                          <input class="form-check-input" type="radio" name="sexe" id="edit_male" value="ذكر" required>
                          <label class="form-check-label" for="edit_male">ذكر</label>
                        </div>
                        <div class="form-check">
                          <input class="form-check-input" type="radio" name="sexe" id="edit_female" value="أنثى" required>
                          <label class="form-check-label" for="edit_female">أنثى</label>
                        </div>
                      </div>
                    </div>

                    <!-- الحالة الاجتماعية -->
                    <div class="col-md-12" dir="rtl">
                      <label class="form-label fw-bold mb-3 d-block">فئة ذوي الاحتياجات الخاصة؟</label>
                      <div class="d-flex align-items-center gap-4">
                        <div class="form-check">
                          <input class="form-check-input" type="radio" name="handicap" value="1" id="edit_handicapYes">
                          <label class="form-check-label" for="edit_handicapYes">نعم</label>
                      </div>
                        <div class="form-check">
                          <input class="form-check-input" type="radio" name="handicap" value="0" id="edit_handicapNo" checked>
                          <label class="form-check-label" for="edit_handicapNo">لا</label>
                    </div>
                      </div>
                    </div>


                    <!-- تفاصيل الإعاقة -->
                    <div class="col-md-6 handicap-details d-none" id="edit_handicapNatureWrapper">
                      <label class="form-label fw-bold">طبيعة الإعاقة</label>
                      <input type="text" name="handicap_nature" id="edit_handicap_nature" class="form-control" placeholder="مثال: حركية، بصرية، سمعية">
                    </div>
                    <div class="col-md-6 handicap-details d-none" id="edit_handicapPercentageWrapper">
                      <label class="form-label fw-bold">نسبة الإعاقة (%)</label>
                      <input type="number" name="handicap_percentage" id="edit_handicap_percentage" class="form-control" min="0" max="100" step="0.1" placeholder="0 - 100">
                    </div>

                    <!-- NIN + NSS for Father (read-only, from relationship) -->
                    <div class="col-md-6" id="edit_ninPereWrapper" style="display: none;">
                      <label class="form-label fw-bold">الرقم الوطني للأب (NIN)</label>
                      <input type="text" id="edit_ninPere" class="form-control" readonly style="background-color: #f8f9fa;">
                    </div>

                    <div class="col-md-6" id="edit_nssPereWrapper" style="display: none;">
                      <label class="form-label fw-bold">رقم الضمان الاجتماعي  للأب (NSS)</label>
                      <input type="text" id="edit_nssPere" class="form-control" readonly style="background-color: #f8f9fa;">
                    </div>

                    <!-- NIN + NSS for Mother (for Guardian role) -->
                    <div class="col-md-6" id="edit_ninMereWrapper" style="display: none;">
                      <label class="form-label fw-bold">الرقم الوطني للأم (NIN)</label>
                      <input type="text" name="nin_mere" id="edit_ninMere" class="form-control" maxlength="18" minlength="18" pattern="\d{18}" readonly style="background-color: #f8f9fa;">
                    </div>

                    <div class="col-md-6" id="edit_nssMereWrapper" style="display: none;">
                      <label class="form-label fw-bold">رقم الضمان الاجتماعي  للأم (NSS)</label>
                      <input type="text" name="nss_mere" id="edit_nssMere" class="form-control" maxlength="12" minlength="12" pattern="\d{12}" readonly style="background-color: #f8f9fa;">
                    </div>

                    <!-- NIN + NSS for Guardian (for Guardian role) -->
                    <div class="col-md-6" id="edit_ninGuardianWrapper" style="display: none;">
                      <label class="form-label fw-bold">الرقم الوطني للوصي (NIN)</label>
                      <input type="text" name="nin_guardian" id="edit_ninGuardian" class="form-control" maxlength="18" minlength="18" pattern="\d{18}" readonly style="background-color: #f8f9fa;">
                    </div>

                    <div class="col-md-6" id="edit_nssGuardianWrapper" style="display: none;">
                      <label class="form-label fw-bold">رقم الضمان الاجتماعي  للوصي (NSS)</label>
                      <input type="text" name="nss_guardian" id="edit_nssGuardian" class="form-control" maxlength="12" minlength="12" pattern="\d{12}" readonly style="background-color: #f8f9fa;">
                    </div>
                </div>

                <!-- Navigation Buttons -->
                <div class="d-flex justify-content-between align-items-center mt-4 flex-row-reverse">
                  <button type="submit" class="btn px-4" style="background-color:#fdae4b; color:#0f033a; font-weight:bold;">
                    حفظ التعديلات <i class="fa-solid fa-check ms-1"></i>
                  </button>
                  <button type="button" class="btn btn-outline-secondary px-4" id="editPrevStep">
                    <i class="fa-solid fa-arrow-right me-1"></i> العودة
                  </button>
                </div>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Add Child Modal (Two-Step) -->
<div class="modal fade" id="addChildModal" tabindex="-1" aria-labelledby="addChildModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg rounded-4">
      
      <!-- Header -->
      <div class="modal-header" style="background-color:#0f033a; color:white;">
        <h5 class="modal-title" id="addChildModalLabel">
          <i class="fa-solid fa-user-plus me-2 text-warning"></i> إضافة تلميذ جديد
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="إغلاق"></button>
      </div>

      <!-- Form -->
      <form id="addChildForm" class="p-3">
        @csrf
        <div class="modal-body">
          <div class="container-fluid">

            <!-- === STEP 1: School Selection (Arabic RTL) === -->
            <div id="step1" class="step-content" dir="rtl" style="text-align: right;">
                <h5 class="fw-bold mb-3 text-center" style="color:#0f033a;">الخطوة 1: اختيار المؤسسة التعليمية</h5>
                <div class="row g-3">

                    <!-- مؤسسة التربية والتعليم + المستوى الدراسي -->
                    <div class="col-md-6">
                    <label class="form-label fw-bold required">مؤسسة التربية والتعليم</label>
                    <select class="form-select" name="type_ecole" required>
                        <option value="">اختر...</option>
                        <option value="عمومية">عمومية</option>
                        <option value="متخصصة"> متخصصة عمومية</option>
                    </select>
                    </div>

                    <div class="col-md-6">
                    <label class="form-label fw-bold required">المستوى الدراسي</label>
                    <select class="form-select" name="niveau" required>
                        <option value="">اختر...</option>
                        <option value="ابتدائي">ابتدائي</option>
                        <option value="متوسط">متوسط</option>
                        <option value="ثانوي">ثانوي</option>
                    </select>
                    </div>

                    <!-- الولاية + البلدية -->
                    <div class="col-md-6">
                    <label class="form-label fw-bold required">الولاية</label>
                    <select class="form-select" name="wilaya_id" id="wilayaSelect" required>
                        <option value="">اختر...</option>
                        <!-- Dynamically loaded from DB -->
                    </select>
                    </div>

                    <div class="col-md-6">
                    <label class="form-label fw-bold required">البلدية</label>
                    <select class="form-select" name="commune_id" id="communeSelect" required disabled>
                        <option value="">اختر الولاية أولا...</option>
                    </select>
                    </div>

                    <!-- المؤسسة -->
                    <div class="col-md-12">
                    <label class="form-label fw-bold required">المؤسسة التعليمية</label>
                    <select class="form-select" name="ecole" id="ecoleSelect" required disabled>
                        <option value="">اختر كل المعايير أولا (مؤسسة التربية والتعليم، المستوى الدراسي، البلدية)</option>
                    </select>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="d-flex justify-content-between align-items-center mt-4 flex-row-reverse">
                    <button type="button" class="btn px-4" id="nextStep"
                    style="background-color:#fdae4b; color:#0f033a; font-weight:bold;">
                    التالي <i class="fa-solid fa-arrow-left ms-1"></i>
                    </button>
                    <button type="button" class="btn btn-outline-danger px-4" id="reloadStep1">
                    <i class="fa-solid fa-rotate"></i> إعادة تعيين
                    </button>
                </div>
            </div>

            <!-- === STEP 2: Student Info (Arabic RTL) === -->
            <div id="step2" class="step-content d-none" dir="rtl" style="text-align: right;">
                <h5 class="fw-bold mb-3 text-center" style="color:#0f033a;">الخطوة 2: إدخال معلومات التلميذ</h5>

                <div class="row g-3">
                    <!-- الأم/الزوجة و صفة طالب المنحة - Top Row -->
                    <div class="col-md-6" id="motherSelectWrapper">
                      <label class="form-label fw-bold" id="motherSelectLabel">الأم/الزوجة</label>
                      <select name="mother_id" id="motherSelect" class="form-select">
                        <option value="">اختر الأم/الزوجة...</option>
                      </select>
                    </div>

                    <!-- Father Info (for Guardian role only) -->
                    <div class="col-md-6" id="fatherInfoWrapper" style="display: none;">
                      <label class="form-label fw-bold">الأب</label>
                      <input type="text" id="fatherNameDisplay" class="form-control" readonly style="background-color: #f8f9fa;">
                    </div>

                    <div class="col-md-6">
                      <label class="form-label fw-bold required">صفة طالب المنحة</label>
                      <select name="relation_tuteur" id="relationSelect" class="form-select" required>
                          <option value="">اختر...</option>
                          <option value="1" id="waliOption">ولي</option>
                          <option value="3">وصي</option>
                      </select>
                    </div>

                    <!-- 🆔 الرقم التعريفي المدرسي -->
                    <div class="col-md-6">
                    <label class="form-label fw-bold required">الرقم التعريفي المدرسي</label>
                    <input type="text" name="num_scolaire" class="form-control" maxlength="16" minlength="16" pattern="\d{16}" placeholder="16 رقمًا" required>
                    </div>

                    <!-- الاسم واللقب -->
                    <div class="col-md-6">
                      <label class="form-label fw-bold required">اللقب بالعربية</label>
                      <input type="text" name="nom" id="nomEleve" class="form-control" dir="rtl" required>
                    </div>
                    <div class="col-md-6">
                      <label class="form-label fw-bold required">الاسم بالعربية</label>
                      <input type="text" name="prenom" class="form-control" dir="rtl" required>
                    </div>

                    <!-- الأب والأم -->
                    <div class="col-md-6" id="nomPereWrapper">
                      <label class="form-label fw-bold required" id="nomPereLabel">لقب الأب بالعربية</label>
                      <input type="text" name="nom_pere" id="nomPere" class="form-control" dir="rtl" required>
                    </div>
                    <div class="col-md-6" id="prenomPereWrapper">
                      <label class="form-label fw-bold required" id="prenomPereLabel">اسم الأب بالعربية</label>
                      <input type="text" name="prenom_pere" id="prenomPere" class="form-control" dir="rtl" required>
                    </div>

                    <!-- الميلاد -->
                    <div class="col-md-6">
                      <label class="form-label fw-bold required">تاريخ الميلاد</label>
                      <input type="date" name="date_naiss" class="form-control">
                    </div>

                    <div class="col-md-3">
                      <label class="form-label fw-bold required">ولاية الميلاد</label>
                      <select name="wilaya_naiss" id="wilayaNaiss" class="form-select" required>
                          <option value="">اختر...</option>
                      </select>
                    </div>
                    <div class="col-md-3">
                      <label class="form-label fw-bold required">بلدية الميلاد</label>
                      <select name="commune_naiss" id="communeNaiss" class="form-select" required disabled>
                          <option value="">اختر الولاية أولا...</option>
                      </select>
                    </div>

                    <!-- القسم والجنس -->
                    <div class="col-md-6">
                      <label class="form-label fw-bold required">القسم</label>
                      <select id="classeSelect" name="classe_scol" class="form-select" required>
                        <option value="">اختر...</option>
                      </select>
                    </div>

                    <div class="col-md-6">
                      <label class="form-label fw-bold required">الجنس</label>
                      <div class="d-flex gap-4 mt-2">
                        <div class="form-check">
                          <input class="form-check-input" type="radio" name="sexe" id="male" value="ذكر" required>
                          <label class="form-check-label" for="male">ذكر</label>
                        </div>
                        <div class="form-check">
                          <input class="form-check-input" type="radio" name="sexe" id="female" value="أنثى" required>
                          <label class="form-check-label" for="female">أنثى</label>
                        </div>
                      </div>
                    </div>


                    <!-- الحالة الاجتماعية -->
                    <div class="col-md-12" dir="rtl">
                      <label class="form-label fw-bold mb-3 d-block">فئة ذوي الاحتياجات الخاصة؟</label>
                      <div class="d-flex align-items-center gap-4">
                        <div class="form-check">
                          <input class="form-check-input" type="radio" name="handicap" value="1" id="handicapYes">
                          <label class="form-check-label" for="handicapYes">نعم</label>
                        </div>
                        <div class="form-check">
                          <input class="form-check-input" type="radio" name="handicap" value="0" id="handicapNo" checked>
                          <label class="form-check-label" for="handicapNo">لا</label>
                        </div>
                      </div>
                    </div>


                    <!-- تفاصيل الإعاقة -->
                    <div class="col-md-6 handicap-details d-none" id="handicapNatureWrapper">
                      <label class="form-label fw-bold">طبيعة الإعاقة</label>
                      <input type="text" name="handicap_nature" class="form-control" placeholder="مثال: حركية، بصرية، سمعية">
                      </div>
                    <div class="col-md-6 handicap-details d-none" id="handicapPercentageWrapper">
                      <label class="form-label fw-bold">نسبة الإعاقة (%)</label>
                      <input type="number" name="handicap_percentage" class="form-control" min="0" max="100" step="0.1" placeholder="0 - 100">
                    </div>

                    <!-- NIN + NSS for Father (read-only, from relationship) -->
                    <div class="col-md-6" id="ninPereWrapper" style="display: none;">
                      <label class="form-label fw-bold">الرقم الوطني للأب (NIN)</label>
                      <input type="text" id="ninPere" class="form-control" readonly style="background-color: #f8f9fa;">
                    </div>

                    <div class="col-md-6" id="nssPereWrapper" style="display: none;">
                      <label class="form-label fw-bold">رقم الضمان الاجتماعي  للأب (NSS)</label>
                      <input type="text" id="nssPere" class="form-control" readonly style="background-color: #f8f9fa;">
                    </div>

                    <!-- NIN + NSS for Mother (for Guardian role) -->
                    <div class="col-md-6" id="ninMereWrapper" style="display: none;">
                      <label class="form-label fw-bold">الرقم الوطني للأم (NIN)</label>
                      <input type="text" name="nin_mere" id="ninMere" class="form-control" maxlength="18" minlength="18" pattern="\d{18}" readonly style="background-color: #f8f9fa;">
                    </div>

                    <div class="col-md-6" id="nssMereWrapper" style="display: none;">
                      <label class="form-label fw-bold">رقم الضمان الاجتماعي  للأم (NSS)</label>
                      <input type="text" name="nss_mere" id="nssMere" class="form-control" maxlength="12" minlength="12" pattern="\d{12}" readonly style="background-color: #f8f9fa;">
                    </div>

                    <!-- NIN + NSS for Guardian (for Guardian role) -->
                    <div class="col-md-6" id="ninGuardianWrapper" style="display: none;">
                      <label class="form-label fw-bold">الرقم الوطني للوصي (NIN)</label>
                      <input type="text" name="nin_guardian" id="ninGuardian" class="form-control" maxlength="18" minlength="18" pattern="\d{18}" readonly style="background-color: #f8f9fa;">
                    </div>

                    <div class="col-md-6" id="nssGuardianWrapper" style="display: none;">
                      <label class="form-label fw-bold">رقم الضمان الاجتماعي  للوصي (NSS)</label>
                      <input type="text" name="nss_guardian" id="nssGuardian" class="form-control" maxlength="12" minlength="12" pattern="\d{12}" readonly style="background-color: #f8f9fa;">
                    </div>
                </div>

                <!-- Navigation Buttons -->
                <div class="d-flex justify-content-between align-items-center mt-4 flex-row-reverse">
                  <button type="submit" class="btn px-4" style="background-color:#fdae4b; color:#0f033a; font-weight:bold;">
                    إضافة <i class="fa-solid fa-check ms-1"></i>
                  </button>
                  <button type="button" class="btn btn-outline-secondary px-4" id="prevStep">
                    <i class="fa-solid fa-arrow-right me-1"></i> العودة
                  </button>
                </div>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

@push('scripts')
<script>
  // Helper function to get API headers with token
  function getApiHeaders(includeCSRF = true) {
    const headers = {
      'Accept': 'application/json',
      'Content-Type': 'application/json',
    };
    
    // Add CSRF token if needed
    if (includeCSRF) {
      const csrfToken = document.querySelector('meta[name="csrf-token"]');
      if (csrfToken) {
        headers['X-CSRF-TOKEN'] = csrfToken.content;
      }
    }
    
    // Add Authorization token if available
    const token = localStorage.getItem('api_token');
    const tokenType = localStorage.getItem('token_type') || 'Bearer';
    if (token) {
      headers['Authorization'] = `${tokenType} ${token}`;
    }
    
    return headers;
  }
  
  // Helper function for API fetch with automatic token
  async function apiFetch(url, options = {}) {
    const defaultHeaders = getApiHeaders();
    const mergedHeaders = { ...defaultHeaders, ...(options.headers || {}) };
    
    // For FormData, remove Content-Type to let browser set it with boundary
    if (options.body instanceof FormData) {
      delete mergedHeaders['Content-Type'];
    }
    
    const response = await fetch(url, {
      ...options,
      headers: mergedHeaders,
    });
    
    // If unauthorized, check if it's an authentication error
    if (response.status === 401) {
      // Clone response to read body without consuming it
      const clonedResponse = response.clone();
      try {
        const data = await clonedResponse.json();
        // 401 Response received
        
        // Only logout if it's an authentication error (not validation)
        const isAuthError = data.error === 'Authentication required' || 
                           data.message?.includes('Token') || 
                           data.message?.includes('Unauthorized') ||
                           data.message?.includes('Invalid token') ||
                           data.message?.includes('expired') ||
                           data.message?.includes('Token required');
        
        if (isAuthError) {
          // Authentication error detected, logging out
          localStorage.removeItem('api_token');
          localStorage.removeItem('token_type');
          window.location.href = '/login';
          return response;
        } else {
          // 401 but not auth error, might be validation
        }
      } catch (e) {
        // If we can't parse JSON, it might be HTML error page
        // Could not parse 401 response
        // Don't logout automatically - let the calling code handle it
      }
    }
    
    return response;
  }
</script>
<script>
@php
    $tuteur = session('tuteur');
@endphp

<script>
  // Initialize with session data (fallback)
  window.currentUserNIN = "{{ $tuteur['nin'] ?? '' }}";
  window.currentUserNSS = "{{ $tuteur['nss'] ?? '' }}";
  window.currentUserSexe = "{{ $tuteur['sexe'] ?? '' }}";
</script>
<script>
document.addEventListener("DOMContentLoaded", async () => {
  /* ===============================
     📢 Notification Bar Management
  =============================== */
  const notificationBar = document.getElementById('notification-bar');
  const closeNotificationBtn = document.getElementById('close-notification');
  const dashboardContainer = document.querySelector('.dashboard-container');
  const mainContent = document.querySelector('.main-content');
  const notificationDismissedKey = 'notification_bar_dismissed';
  
  // Function to show notification bar
  function showNotificationBar() {
    if (notificationBar) {
      // Force z-index and positioning to ensure it's always on top
      notificationBar.style.setProperty('z-index', '10000', 'important');
      notificationBar.style.setProperty('position', 'fixed', 'important');
      notificationBar.style.setProperty('top', '0', 'important');
      notificationBar.style.setProperty('left', '0', 'important');
      notificationBar.style.setProperty('right', '0', 'important');
      notificationBar.style.setProperty('width', '100%', 'important');
      notificationBar.style.display = 'block';
      setTimeout(() => {
        notificationBar.classList.add('show');
      }, 10);
      
      // Add padding to dashboard container
      if (dashboardContainer) {
        dashboardContainer.classList.add('has-notification');
      }
      
      // Add padding to main content if it exists
      if (mainContent) {
        mainContent.classList.add('has-notification');
      }
    }
  }
  
  // Function to hide notification bar
  function hideNotificationBar() {
    if (notificationBar) {
      notificationBar.classList.remove('show');
      setTimeout(() => {
        notificationBar.style.display = 'none';
        // Remove padding from dashboard container
        if (dashboardContainer) {
          dashboardContainer.classList.remove('has-notification');
        }
        // Remove padding from main content
        if (mainContent) {
          mainContent.classList.remove('has-notification');
        }
      }, 400);
    }
  }
  
  // Check if notification was previously dismissed
  const wasDismissed = localStorage.getItem(notificationDismissedKey);
  
  if (!wasDismissed && notificationBar) {
    // Show notification bar with animation
    showNotificationBar();
  }
  
  // Handle close button click
  if (closeNotificationBtn) {
    closeNotificationBtn.addEventListener('click', () => {
      // Save dismissal state
      localStorage.setItem(notificationDismissedKey, 'true');
      
      // Hide notification with animation
      hideNotificationBar();
    });
  }

  /* ===============================
     👤 Load Guardian Parents Data (Father & Mother)
  =============================== */
  async function loadGuardianParentsData(tuteurData) {
    // Store tuteur data globally for form submission
    window.tuteurData = tuteurData;
    
    try {
      // Load father data if father_id exists (for role 2 and 3)
      if (tuteurData.father_id) {
        if (tuteurData.father) {
          window.guardianFather = tuteurData.father;
        } else {
          const fatherResponse = await apiFetch(`/api/fathers/${tuteurData.father_id}`);
          if (fatherResponse.ok) {
            window.guardianFather = await fatherResponse.json();
          }
        }
      }
      
      // Load mother data if mother_id exists (for role 3 only)
      if (tuteurData.relation_tuteur === '3' || tuteurData.relation_tuteur === 3) {
        if (tuteurData.mother_id) {
          if (tuteurData.mother) {
            window.guardianMother = tuteurData.mother;
          } else {
            const motherResponse = await apiFetch(`/api/mothers/${tuteurData.mother_id}`);
            if (motherResponse.ok) {
              window.guardianMother = await motherResponse.json();
            }
          }
        }
      }
      
      // Update form fields based on role
      updateFormForGuardianRole();
    } catch (error) {
      // Silently handle error
    }
  }

  /* ===============================
     👤 Auto-fill Relation Tuteur based on Tuteur Role
  =============================== */
  function autoFillRelationTuteur(tuteurRole) {
    const relationSelect = document.getElementById('relationSelect');
    const editRelationSelect = document.getElementById('edit_relation_tuteur');
    
    // Update the "ولي" option text based on role (for both create and edit forms)
    const waliOption = document.getElementById('waliOption');
    const editWaliOption = document.getElementById('editWaliOption');
    
    if (waliOption) {
      if (tuteurRole === '1' || tuteurRole === 1) {
        waliOption.textContent = 'ولي (أب)';
      } else if (tuteurRole === '2' || tuteurRole === 2) {
        waliOption.textContent = 'ولي (أم)';
      } else {
        waliOption.textContent = 'ولي';
      }
    }
    
    if (editWaliOption) {
      if (tuteurRole === '1' || tuteurRole === 1) {
        editWaliOption.textContent = 'ولي (أب)';
      } else if (tuteurRole === '2' || tuteurRole === 2) {
        editWaliOption.textContent = 'ولي (أم)';
      } else {
        editWaliOption.textContent = 'ولي';
      }
    }
    
    // Map tuteur role to student relation_tuteur (as integer)
    // Role 1 (Father) or 2 (Mother) → 1 (ولي)
    // Role 3 (Guardian) → 3 (وصي)
    let relationValue = null;
    if (tuteurRole === '1' || tuteurRole === 1 || tuteurRole === '2' || tuteurRole === 2) {
      relationValue = '1'; // ولي
    } else if (tuteurRole === '3' || tuteurRole === 3) {
      relationValue = '3'; // وصي
    }
    
    if (relationValue && relationSelect) {
      relationSelect.value = relationValue;
      // Make it read-only since it's based on tuteur's role
      relationSelect.disabled = true;
      relationSelect.style.backgroundColor = '#f8f9fa';
      
      // Trigger change event to update dependent fields
      relationSelect.dispatchEvent(new Event('change'));
    }
    
    // Also update edit form if it exists
    if (relationValue && editRelationSelect) {
      editRelationSelect.value = relationValue;
      editRelationSelect.disabled = true;
      editRelationSelect.style.backgroundColor = '#f8f9fa';
    }
  }

  /* ===============================
     👤 Update Form for Guardian Role (Create Form)
  =============================== */
  function updateFormForGuardianRole() {
    const relationTuteur = window.currentUserRelationTuteur;
    const motherSelectWrapper = document.getElementById('motherSelectWrapper');
    const fatherInfoWrapper = document.getElementById('fatherInfoWrapper');
    const motherSelect = document.getElementById('motherSelect');
    const fatherNameDisplay = document.getElementById('fatherNameDisplay');
    const nomPere = document.getElementById('nomPere');
    const prenomPere = document.getElementById('prenomPere');
    const nomPereWrapper = document.getElementById('nomPereWrapper');
    const prenomPereWrapper = document.getElementById('prenomPereWrapper');
    const nomPereLabel = document.getElementById('nomPereLabel');
    const prenomPereLabel = document.getElementById('prenomPereLabel');
    
    if (relationTuteur === '2' || relationTuteur === 2) {
      // Mother role: Hide mother dropdown, show father info, change labels to mother
      if (motherSelectWrapper) {
        motherSelectWrapper.style.display = 'none';
      }
      
      if (fatherInfoWrapper) {
        fatherInfoWrapper.style.display = 'block';
        if (window.guardianFather && fatherNameDisplay) {
          const fatherName = `${window.guardianFather.prenom_ar || ''} ${window.guardianFather.nom_ar || ''}`.trim();
          fatherNameDisplay.value = fatherName || '—';
        }
      }
      
      // Change labels to mother (since tuteur is mother)
      if (nomPereLabel) {
        nomPereLabel.textContent = 'لقب الأم بالعربية';
      }
      if (prenomPereLabel) {
        prenomPereLabel.textContent = 'اسم الأم بالعربية';
      }
      
      // Auto-fill mother name fields from tuteur (since tuteur is the mother)
      if (nomPere && prenomPere) {
        const tuteurNomAr = "{{ $tuteur['nom_ar'] ?? '' }}";
        const tuteurPrenomAr = "{{ $tuteur['prenom_ar'] ?? '' }}";
        if (tuteurNomAr && tuteurPrenomAr) {
          nomPere.value = tuteurNomAr;
          prenomPere.value = tuteurPrenomAr;
          nomPere.setAttribute('readonly', true);
          prenomPere.setAttribute('readonly', true);
          nomPere.readOnly = true;
          prenomPere.readOnly = true;
          nomPere.style.backgroundColor = '#f8f9fa';
          prenomPere.style.backgroundColor = '#f8f9fa';
        }
      }
      
      // Show and auto-fill father NIN and NSS from relationship
      const ninPereWrapper = document.getElementById('ninPereWrapper');
      const nssPereWrapper = document.getElementById('nssPereWrapper');
      const ninPereEl = document.getElementById('ninPere');
      const nssPereEl = document.getElementById('nssPere');
      
      // Show father NIN/NSS fields for role 2
      if (ninPereWrapper) ninPereWrapper.style.display = 'block';
      if (nssPereWrapper) nssPereWrapper.style.display = 'block';
      
      // Auto-fill father NIN and NSS from relationship
      if (window.guardianFather) {
        if (ninPereEl && window.guardianFather.nin) {
          ninPereEl.value = window.guardianFather.nin;
          ninPereEl.setAttribute('readonly', true);
          ninPereEl.readOnly = true;
          ninPereEl.style.backgroundColor = '#f8f9fa';
        }
        if (nssPereEl && window.guardianFather.nss) {
          nssPereEl.value = window.guardianFather.nss;
          nssPereEl.setAttribute('readonly', true);
          nssPereEl.readOnly = true;
          nssPereEl.style.backgroundColor = '#f8f9fa';
        }
      }
      
      // Show and auto-fill mother NIN and NSS (tuteur is the mother for role 2)
      const ninMereWrapper = document.getElementById('ninMereWrapper');
      const nssMereWrapper = document.getElementById('nssMereWrapper');
      const ninMere = document.getElementById('ninMere');
      const nssMere = document.getElementById('nssMere');
      
      if (ninMereWrapper) ninMereWrapper.style.display = 'block';
      if (nssMereWrapper) nssMereWrapper.style.display = 'block';
      
      // Pre-fill from tuteur's NIN and NSS (since tuteur is the mother)
      if (ninMere && window.currentUserNIN) {
        ninMere.value = window.currentUserNIN;
      }
      if (nssMere && window.currentUserNSS) {
        nssMere.value = window.currentUserNSS;
      }
    } else if (relationTuteur === '3' || relationTuteur === 3) {
      // Guardian role: Show both mother and father info
      if (motherSelectWrapper) {
        motherSelectWrapper.style.display = 'block';
        if (motherSelect) {
          // For guardian, mother is already set, so not required as input
          if (window.guardianMother) {
            motherSelect.required = false;
            motherSelect.disabled = true;
          } else {
            motherSelect.required = true;
            motherSelect.disabled = false;
          }
        }
      }
      
      if (fatherInfoWrapper) {
        fatherInfoWrapper.style.display = 'block';
        if (window.guardianFather && fatherNameDisplay) {
          const fatherName = `${window.guardianFather.prenom_ar || ''} ${window.guardianFather.nom_ar || ''}`.trim();
          fatherNameDisplay.value = fatherName || '—';
        }
      }
      
      // Auto-fill father fields from relationship
      if (window.guardianFather) {
        if (nomPere && prenomPere) {
          nomPere.value = window.guardianFather.nom_ar || '';
          prenomPere.value = window.guardianFather.prenom_ar || '';
          nomPere.setAttribute('readonly', true);
          prenomPere.setAttribute('readonly', true);
          nomPere.readOnly = true;
          prenomPere.readOnly = true;
        }
        // Auto-fill father NIN and NSS
        const ninPereEl = document.getElementById('ninPere');
        const nssPereEl = document.getElementById('nssPere');
        if (ninPereEl && window.guardianFather.nin) {
          ninPereEl.value = window.guardianFather.nin;
          ninPereEl.setAttribute('readonly', true);
          ninPereEl.readOnly = true;
          ninPereEl.style.backgroundColor = '#f8f9fa';
        }
        if (nssPereEl && window.guardianFather.nss) {
          nssPereEl.value = window.guardianFather.nss;
          nssPereEl.setAttribute('readonly', true);
          nssPereEl.readOnly = true;
          nssPereEl.style.backgroundColor = '#f8f9fa';
        }
      }
      
      // Auto-fill mother NIN and NSS (for Guardian role)
      if (window.guardianMother) {
        const ninMereWrapper = document.getElementById('ninMereWrapper');
        const nssMereWrapper = document.getElementById('nssMereWrapper');
        const ninMere = document.getElementById('ninMere');
        const nssMere = document.getElementById('nssMere');
        
        if (ninMereWrapper) ninMereWrapper.style.display = 'block';
        if (nssMereWrapper) nssMereWrapper.style.display = 'block';
        
        if (ninMere && window.guardianMother.nin) {
          ninMere.value = window.guardianMother.nin;
        }
        if (nssMere && window.guardianMother.nss) {
          nssMere.value = window.guardianMother.nss;
        }
      }
      
      // Auto-fill guardian (tuteur) NIN and NSS (for Guardian role)
      if (window.currentUserNIN && window.currentUserNSS) {
        const ninGuardianWrapper = document.getElementById('ninGuardianWrapper');
        const nssGuardianWrapper = document.getElementById('nssGuardianWrapper');
        const ninGuardian = document.getElementById('ninGuardian');
        const nssGuardian = document.getElementById('nssGuardian');
        
        if (ninGuardianWrapper) ninGuardianWrapper.style.display = 'block';
        if (nssGuardianWrapper) nssGuardianWrapper.style.display = 'block';
        
        if (ninGuardian) {
          ninGuardian.value = window.currentUserNIN;
        }
        if (nssGuardian) {
          nssGuardian.value = window.currentUserNSS;
        }
      }
      
      // Show mother name if available (for guardian role)
      if (window.guardianMother && motherSelect) {
        const motherName = `${window.guardianMother.prenom_ar || ''} ${window.guardianMother.nom_ar || ''}`.trim();
        // Add mother as selected option and disable
        motherSelect.innerHTML = `<option value="${window.guardianMother.id}" selected>${motherName}</option>`;
        motherSelect.disabled = true;
        motherSelect.style.backgroundColor = '#f8f9fa';
        const motherLabel = document.getElementById('motherSelectLabel');
        if (motherLabel) {
          motherLabel.classList.remove('required');
        }
      }
    } else if (relationTuteur === '1' || relationTuteur === 1) {
      // Role 1 (Father): Show mother dropdown normally, hide father info
      if (fatherInfoWrapper) {
        fatherInfoWrapper.style.display = 'none';
      }
      if (motherSelectWrapper) {
        motherSelectWrapper.style.display = 'block';
        if (motherSelect) {
          motherSelect.required = true;
          motherSelect.disabled = false;
        }
      }
      
      // Reset labels to father (default)
      if (nomPereLabel) {
        nomPereLabel.textContent = 'لقب الأب بالعربية';
      }
      if (prenomPereLabel) {
        prenomPereLabel.textContent = 'اسم الأب بالعربية';
      }
      
      // Hide guardian NIN/NSS fields (mother NIN/NSS will be shown when mother is selected)
      const ninGuardianWrapper = document.getElementById('ninGuardianWrapper');
      const nssGuardianWrapper = document.getElementById('nssGuardianWrapper');
      
      if (ninGuardianWrapper) ninGuardianWrapper.style.display = 'none';
      if (nssGuardianWrapper) nssGuardianWrapper.style.display = 'none';
      
      // Initially hide mother NIN/NSS - will be shown when mother is selected
      const ninMereWrapper = document.getElementById('ninMereWrapper');
      const nssMereWrapper = document.getElementById('nssMereWrapper');
      if (ninMereWrapper) ninMereWrapper.style.display = 'none';
      if (nssMereWrapper) nssMereWrapper.style.display = 'none';
      
      // If a mother is already selected, show and fill the fields
      if (motherSelect && motherSelect.value) {
        const selectedMotherId = motherSelect.value;
        if (window.mothersData && window.mothersData.length > 0) {
          const selectedMother = window.mothersData.find(m => m.id == selectedMotherId);
          if (selectedMother) {
            if (ninMereWrapper) ninMereWrapper.style.display = 'block';
            if (nssMereWrapper) nssMereWrapper.style.display = 'block';
            
            const ninMere = document.getElementById('ninMere');
            const nssMere = document.getElementById('nssMere');
            if (ninMere && selectedMother.nin) {
              ninMere.value = selectedMother.nin;
            }
            if (nssMere && selectedMother.nss) {
              nssMere.value = selectedMother.nss;
            }
          }
        }
      }
    } else {
      // Default/Other roles: Hide all special fields
      if (fatherInfoWrapper) {
        fatherInfoWrapper.style.display = 'none';
      }
      if (motherSelectWrapper) {
        motherSelectWrapper.style.display = 'block';
      }
      
      // Reset labels to father (default)
      if (nomPereLabel) {
        nomPereLabel.textContent = 'لقب الأب بالعربية';
      }
      if (prenomPereLabel) {
        prenomPereLabel.textContent = 'اسم الأب بالعربية';
      }
      
      // Hide mother and guardian NIN/NSS fields
      const ninMereWrapper = document.getElementById('ninMereWrapper');
      const nssMereWrapper = document.getElementById('nssMereWrapper');
      const ninGuardianWrapper = document.getElementById('ninGuardianWrapper');
      const nssGuardianWrapper = document.getElementById('nssGuardianWrapper');
      
      if (ninMereWrapper) ninMereWrapper.style.display = 'none';
      if (nssMereWrapper) nssMereWrapper.style.display = 'none';
      if (ninGuardianWrapper) ninGuardianWrapper.style.display = 'none';
      if (nssGuardianWrapper) nssGuardianWrapper.style.display = 'none';
    }
  }

  /* ===============================
     👤 Update Form for Guardian Role (Edit Form)
  =============================== */
  function updateFormForEditGuardianRole() {
    const relationTuteur = window.currentUserRelationTuteur;
    const editMotherSelectWrapper = document.getElementById('edit_motherSelectWrapper');
    const editFatherInfoWrapper = document.getElementById('edit_fatherInfoWrapper');
    const editMotherSelect = document.getElementById('editMotherSelect');
    const editFatherNameDisplay = document.getElementById('edit_fatherNameDisplay');
    const editNomPere = document.getElementById('edit_nom_pere');
    const editPrenomPere = document.getElementById('edit_prenom_pere');
    const editNomPereWrapper = document.getElementById('edit_nomPereWrapper');
    const editPrenomPereWrapper = document.getElementById('edit_prenomPereWrapper');
    const editNomPereLabel = document.getElementById('edit_nomPereLabel');
    const editPrenomPereLabel = document.getElementById('edit_prenomPereLabel');
    
    if (relationTuteur === '2' || relationTuteur === 2) {
      // Mother role: Hide mother dropdown, show father info, change labels to mother
      if (editMotherSelectWrapper) {
        editMotherSelectWrapper.style.display = 'none';
      }
      
      if (editFatherInfoWrapper) {
        editFatherInfoWrapper.style.display = 'block';
        if (window.guardianFather && editFatherNameDisplay) {
          const fatherName = `${window.guardianFather.prenom_ar || ''} ${window.guardianFather.nom_ar || ''}`.trim();
          editFatherNameDisplay.value = fatherName || '—';
        }
      }
      
      // Change labels to mother (since tuteur is mother)
      if (editNomPereLabel) {
        editNomPereLabel.textContent = 'لقب الأم بالعربية';
      }
      if (editPrenomPereLabel) {
        editPrenomPereLabel.textContent = 'اسم الأم بالعربية';
      }
      
      // Auto-fill mother name fields from tuteur (since tuteur is the mother)
      if (editNomPere && editPrenomPere) {
        const tuteurNomAr = "{{ $tuteur['nom_ar'] ?? '' }}";
        const tuteurPrenomAr = "{{ $tuteur['prenom_ar'] ?? '' }}";
        if (tuteurNomAr && tuteurPrenomAr) {
          editNomPere.value = tuteurNomAr;
          editPrenomPere.value = tuteurPrenomAr;
          editNomPere.setAttribute('readonly', true);
          editPrenomPere.setAttribute('readonly', true);
          editNomPere.readOnly = true;
          editPrenomPere.readOnly = true;
          editNomPere.style.backgroundColor = '#f8f9fa';
          editPrenomPere.style.backgroundColor = '#f8f9fa';
        }
      }
      
      // Show and auto-fill father NIN and NSS from relationship
      const editNinPereWrapper = document.getElementById('edit_ninPereWrapper');
      const editNssPereWrapper = document.getElementById('edit_nssPereWrapper');
      const editNinPereEl = document.getElementById('edit_ninPere');
      const editNssPereEl = document.getElementById('edit_nssPere');
      
      // Show father NIN/NSS fields for role 2
      if (editNinPereWrapper) editNinPereWrapper.style.display = 'block';
      if (editNssPereWrapper) editNssPereWrapper.style.display = 'block';
      
      // Auto-fill father NIN and NSS from relationship
      if (window.guardianFather) {
        if (editNinPereEl && window.guardianFather.nin) {
          editNinPereEl.value = window.guardianFather.nin;
          editNinPereEl.setAttribute('readonly', true);
          editNinPereEl.readOnly = true;
          editNinPereEl.style.backgroundColor = '#f8f9fa';
        }
        if (editNssPereEl && window.guardianFather.nss) {
          editNssPereEl.value = window.guardianFather.nss;
          editNssPereEl.setAttribute('readonly', true);
          editNssPereEl.readOnly = true;
          editNssPereEl.style.backgroundColor = '#f8f9fa';
        }
      }
      
      // Show and auto-fill mother NIN and NSS (tuteur is the mother for role 2)
      const editNinMereWrapper = document.getElementById('edit_ninMereWrapper');
      const editNssMereWrapper = document.getElementById('edit_nssMereWrapper');
      const editNinMere = document.getElementById('edit_ninMere');
      const editNssMere = document.getElementById('edit_nssMere');
      
      if (editNinMereWrapper) editNinMereWrapper.style.display = 'block';
      if (editNssMereWrapper) editNssMereWrapper.style.display = 'block';
      
      // Pre-fill from tuteur's NIN and NSS (since tuteur is the mother)
      if (editNinMere && window.currentUserNIN) {
        editNinMere.value = window.currentUserNIN;
      }
      if (editNssMere && window.currentUserNSS) {
        editNssMere.value = window.currentUserNSS;
      }
    } else if (relationTuteur === '3' || relationTuteur === 3) {
      // Guardian role: Show both mother and father info
      if (editMotherSelectWrapper) {
        editMotherSelectWrapper.style.display = 'block';
        if (editMotherSelect) {
          if (window.guardianMother) {
            editMotherSelect.required = false;
            editMotherSelect.disabled = true;
          } else {
            editMotherSelect.required = true;
            editMotherSelect.disabled = false;
          }
        }
      }
      
      if (editFatherInfoWrapper) {
        editFatherInfoWrapper.style.display = 'block';
        if (window.guardianFather && editFatherNameDisplay) {
          const fatherName = `${window.guardianFather.prenom_ar || ''} ${window.guardianFather.nom_ar || ''}`.trim();
          editFatherNameDisplay.value = fatherName || '—';
        }
      }
      
      // Auto-fill father fields from relationship
      if (window.guardianFather) {
        if (editNomPere && editPrenomPere) {
          editNomPere.value = window.guardianFather.nom_ar || '';
          editPrenomPere.value = window.guardianFather.prenom_ar || '';
          editNomPere.setAttribute('readonly', true);
          editPrenomPere.setAttribute('readonly', true);
          editNomPere.readOnly = true;
          editPrenomPere.readOnly = true;
        }
        // Auto-fill father NIN and NSS
        const editNinPereEl = document.getElementById('edit_ninPere');
        const editNssPereEl = document.getElementById('edit_nssPere');
        if (editNinPereEl && window.guardianFather.nin) {
          editNinPereEl.value = window.guardianFather.nin;
          editNinPereEl.setAttribute('readonly', true);
          editNinPereEl.readOnly = true;
          editNinPereEl.style.backgroundColor = '#f8f9fa';
        }
        if (editNssPereEl && window.guardianFather.nss) {
          editNssPereEl.value = window.guardianFather.nss;
          editNssPereEl.setAttribute('readonly', true);
          editNssPereEl.readOnly = true;
          editNssPereEl.style.backgroundColor = '#f8f9fa';
        }
      }
      
      // Auto-fill mother NIN and NSS (for Guardian role)
      if (window.guardianMother) {
        const editNinMereWrapper = document.getElementById('edit_ninMereWrapper');
        const editNssMereWrapper = document.getElementById('edit_nssMereWrapper');
        const editNinMere = document.getElementById('edit_ninMere');
        const editNssMere = document.getElementById('edit_nssMere');
        
        if (editNinMereWrapper) editNinMereWrapper.style.display = 'block';
        if (editNssMereWrapper) editNssMereWrapper.style.display = 'block';
        
        if (editNinMere && window.guardianMother.nin) {
          editNinMere.value = window.guardianMother.nin;
        }
        if (editNssMere && window.guardianMother.nss) {
          editNssMere.value = window.guardianMother.nss;
        }
      }
      
      // Auto-fill guardian (tuteur) NIN and NSS (for Guardian role)
      if (window.currentUserNIN && window.currentUserNSS) {
        const editNinGuardianWrapper = document.getElementById('edit_ninGuardianWrapper');
        const editNssGuardianWrapper = document.getElementById('edit_nssGuardianWrapper');
        const editNinGuardian = document.getElementById('edit_ninGuardian');
        const editNssGuardian = document.getElementById('edit_nssGuardian');
        
        if (editNinGuardianWrapper) editNinGuardianWrapper.style.display = 'block';
        if (editNssGuardianWrapper) editNssGuardianWrapper.style.display = 'block';
        
        if (editNinGuardian && window.currentUserNIN) {
          editNinGuardian.value = window.currentUserNIN;
        }
        if (editNssGuardian && window.currentUserNSS) {
          editNssGuardian.value = window.currentUserNSS;
        }
      }
    } else if (relationTuteur === '1' || relationTuteur === 1) {
      // Role 1 (Father): Show mother dropdown normally, hide father info
      if (editFatherInfoWrapper) {
        editFatherInfoWrapper.style.display = 'none';
      }
      if (editMotherSelectWrapper) {
        editMotherSelectWrapper.style.display = 'block';
        if (editMotherSelect) {
          editMotherSelect.required = true;
          editMotherSelect.disabled = false;
        }
      }
      
      // Reset labels to father (default)
      if (editNomPereLabel) {
        editNomPereLabel.textContent = 'لقب الأب بالعربية';
      }
      if (editPrenomPereLabel) {
        editPrenomPereLabel.textContent = 'اسم الأب بالعربية';
      }
      
      // Hide guardian NIN/NSS fields (mother NIN/NSS will be shown when mother is selected)
      const editNinGuardianWrapper = document.getElementById('edit_ninGuardianWrapper');
      const editNssGuardianWrapper = document.getElementById('edit_nssGuardianWrapper');
      
      if (editNinGuardianWrapper) editNinGuardianWrapper.style.display = 'none';
      if (editNssGuardianWrapper) editNssGuardianWrapper.style.display = 'none';
      
      // Initially hide mother NIN/NSS - will be shown when mother is selected
      const editNinMereWrapper = document.getElementById('edit_ninMereWrapper');
      const editNssMereWrapper = document.getElementById('edit_nssMereWrapper');
      if (editNinMereWrapper) editNinMereWrapper.style.display = 'none';
      if (editNssMereWrapper) editNssMereWrapper.style.display = 'none';
      
      // If a mother is already selected, show and fill the fields
      if (editMotherSelect && editMotherSelect.value) {
        const selectedMotherId = editMotherSelect.value;
        if (window.mothersData && window.mothersData.length > 0) {
          const selectedMother = window.mothersData.find(m => m.id == selectedMotherId);
          if (selectedMother) {
            if (editNinMereWrapper) editNinMereWrapper.style.display = 'block';
            if (editNssMereWrapper) editNssMereWrapper.style.display = 'block';
            
            const editNinMere = document.getElementById('edit_ninMere');
            const editNssMere = document.getElementById('edit_nssMere');
            if (editNinMere && selectedMother.nin) {
              editNinMere.value = selectedMother.nin;
            }
            if (editNssMere && selectedMother.nss) {
              editNssMere.value = selectedMother.nss;
            }
          }
        }
      }
    } else {
      // Default/Other roles: Hide all special fields
      if (editFatherInfoWrapper) {
        editFatherInfoWrapper.style.display = 'none';
      }
      if (editMotherSelectWrapper) {
        editMotherSelectWrapper.style.display = 'block';
      }
      
      // Reset labels to father (default)
      if (editNomPereLabel) {
        editNomPereLabel.textContent = 'لقب الأب بالعربية';
      }
      if (editPrenomPereLabel) {
        editPrenomPereLabel.textContent = 'اسم الأب بالعربية';
      }
      
      // Hide mother and guardian NIN/NSS fields
      const editNinMereWrapper = document.getElementById('edit_ninMereWrapper');
      const editNssMereWrapper = document.getElementById('edit_nssMereWrapper');
      const editNinGuardianWrapper = document.getElementById('edit_ninGuardianWrapper');
      const editNssGuardianWrapper = document.getElementById('edit_nssGuardianWrapper');
      
      if (editNinMereWrapper) editNinMereWrapper.style.display = 'none';
      if (editNssMereWrapper) editNssMereWrapper.style.display = 'none';
      if (editNinGuardianWrapper) editNinGuardianWrapper.style.display = 'none';
      if (editNssGuardianWrapper) editNssGuardianWrapper.style.display = 'none';
    }
  }

  /* ===============================
     👤 Load Mothers for Tuteur
  =============================== */
  async function loadMothers() {
    try {
      const response = await apiFetch('/api/tuteurs/mothers');

      if (!response.ok) {
        // If unauthorized, might be token issue - don't show error, just return
        if (response.status === 401 || response.status === 403) {
          return;
        }
        // For other errors, try to get error message
        try {
          const errorData = await response.json();
          // Error occurred but don't log to console for security
          return;
        } catch (e) {
          return;
        }
      }

      let mothers;
      try {
        mothers = await response.json();
      } catch (e) {
        // Invalid JSON response
        return;
      }
      
      // Handle both array and object with data property
      const mothersArray = Array.isArray(mothers) ? mothers : (mothers?.data || []);
      
      // Store mothers data globally for auto-fill
      window.mothersData = mothersArray || [];
      
      const motherSelect = document.getElementById('motherSelect');
      const editMotherSelect = document.getElementById('editMotherSelect');
      
      // Clear existing options and populate
      if (motherSelect) {
        motherSelect.innerHTML = '<option value="">اختر الأم/الزوجة...</option>';
        if (Array.isArray(mothersArray) && mothersArray.length > 0) {
          mothersArray.forEach(mother => {
            if (mother && mother.id) {
              const option = document.createElement('option');
              option.value = mother.id;
              const motherName = `${mother.prenom_ar || ''} ${mother.nom_ar || ''}`.trim();
              option.textContent = motherName || `الأم ${mother.id}`;
              motherSelect.appendChild(option);
            }
          });
        }
      }

      if (editMotherSelect) {
        editMotherSelect.innerHTML = '<option value="">اختر الأم/الزوجة...</option>';
        if (Array.isArray(mothersArray) && mothersArray.length > 0) {
          mothersArray.forEach(mother => {
            if (mother && mother.id) {
              const option = document.createElement('option');
              option.value = mother.id;
              const motherName = `${mother.prenom_ar || ''} ${mother.nom_ar || ''}`.trim();
              option.textContent = motherName || `الأم ${mother.id}`;
              editMotherSelect.appendChild(option);
            }
          });
        }
      }
    } catch (error) {
      // Network error or other exception - silently handle
      return;
    }
  }

  /* ===============================
     👤 Load Tuteur Data via API
  =============================== */
  async function loadTuteurData() {
    try {
      const nin = window.currentUserNIN || "{{ session('tuteur.nin') }}";
      if (!nin) {
        // No NIN available
        return;
      }

      const response = await apiFetch(`/api/tuteurs/${nin}`);
      if (response.ok) {
        const tuteurData = await response.json();
        
        // Store tuteur data globally for form submission
        window.tuteurData = tuteurData;
        
        // Update window variables with complete data from API
        if (tuteurData.nin) window.currentUserNIN = tuteurData.nin;
        if (tuteurData.nss) window.currentUserNSS = tuteurData.nss;
        if (tuteurData.sexe) window.currentUserSexe = tuteurData.sexe;
        if (tuteurData.relation_tuteur) window.currentUserRelationTuteur = tuteurData.relation_tuteur;
        
        // Auto-fill relation_tuteur dropdown based on tuteur's role
        autoFillRelationTuteur(tuteurData.relation_tuteur);
        
        // Load father and mother data if role is 2 (Mother) or 3 (Guardian)
        if (tuteurData.relation_tuteur === '2' || tuteurData.relation_tuteur === 2 || 
            tuteurData.relation_tuteur === '3' || tuteurData.relation_tuteur === 3) {
          await loadGuardianParentsData(tuteurData);
        }
        
        // Tuteur data loaded successfully
      } else {
        // Failed to load tuteur data from API
      }
    } catch (error) {
      // Silently handle error
    }
  }

  // Load tuteur data immediately
  await loadTuteurData();

  /* ===============================
     🧒 Load children list
  =============================== */
  async function loadChildrenList() {
    const tableBody = document.getElementById('studentsTableBody');
    const mobileContainer = document.querySelector('.students-mobile-container');
    
    tableBody.innerHTML = '<tr><td colspan="5" class="loading-message">جارٍ تحميل البيانات...</td></tr>';
    if (mobileContainer) mobileContainer.innerHTML = '<div style="text-align:center;padding:2rem;color:#777;">جارٍ تحميل البيانات...</div>';

    try {
      const nin = window.currentUserNIN || "{{ session('tuteur.nin') ?? '' }}";
      if (!nin) {
        // No NIN available
        tableBody.innerHTML = '<tr><td colspan="5" class="loading-message">خطأ: لا يمكن تحديد الهوية</td></tr>';
        return;
      }

      const response = await apiFetch(`/api/tuteur/${nin}/eleves`);
      
      // Check if response is JSON
      const contentType = response.headers.get('content-type');
      if (!contentType || !contentType.includes('application/json')) {
        const text = await response.text();
        // Non-JSON response received
        tableBody.innerHTML = '<tr><td colspan="5" class="loading-message">حدث خطأ أثناء تحميل البيانات</td></tr>';
        if (mobileContainer) {
          mobileContainer.innerHTML = '<div style="text-align:center;padding:2rem;color:#777;">حدث خطأ أثناء تحميل البيانات</div>';
        }
        return;
      }

      const responseData = await response.json();

      // Handle response structure: could be array directly or wrapped in {data: [...]}
      const data = Array.isArray(responseData) ? responseData : (responseData.data || []);

      if (!response.ok) {
        // Failed to load children
        tableBody.innerHTML = '<tr><td colspan="5" class="loading-message">حدث خطأ أثناء تحميل البيانات</td></tr>';
        if (mobileContainer) {
          mobileContainer.innerHTML = '<div style="text-align:center;padding:2rem;color:#777;">حدث خطأ أثناء تحميل البيانات</div>';
        }
        return;
      }

      if (!Array.isArray(data) || data.length === 0) {
        tableBody.innerHTML = '<tr><td colspan="5" class="loading-message">لا يوجد تلاميذ مسجلين بعد.</td></tr>';
        if (mobileContainer) {
          mobileContainer.innerHTML = '<div style="text-align:center;padding:2rem;color:#777;">لا يوجد تلاميذ مسجلين بعد.</div>';
        }
      } else {
        // Desktop table
        tableBody.innerHTML = data.map(eleve => `
          <tr>
            <td>${eleve.nom ?? ''} ${eleve.prenom ?? ''}</td>
            <td>${eleve.date_naiss ?? '—'}</td>
            <td>${eleve.classe_scol ?? '—'}</td>
            <td>${eleve.etablissement?.nom_etabliss ?? '—'}</td>
            <td>
              <div class="action-buttons">
                <button class="btn btn-outline-danger btn-sm" onclick="openIstimaraPDF('${eleve.num_scolaire}')">
                  <i class="fa-solid fa-file-pdf"></i> PDF
                </button>
                <button class="btn-delete" data-id="${eleve.num_scolaire}">
                  <i class="fa-solid fa-trash"></i> حذف
                </button>
                <button class="btn-view" data-num-scolaire="${eleve.num_scolaire}" onclick="openViewModal('${eleve.num_scolaire}')">
                  <i class="fa-solid fa-eye"></i> عرض
                </button>
                <button class="btn-edit" data-num-scolaire="${eleve.num_scolaire}" onclick="openEditModal('${eleve.num_scolaire}')">
                  <i class="fa-solid fa-pen"></i> تعديل
                </button>
                <button class="btn btn-outline-info btn-sm" onclick="showComments('${eleve.num_scolaire}', '${eleve.nom ?? ''} ${eleve.prenom ?? ''}')" title="التعليقات">
                  <i class="fa-solid fa-comments"></i> تعليقات
                </button>
              </div>
            </td>
          </tr>
        `).join('');
        
        // Mobile cards
        if (mobileContainer) {
          mobileContainer.innerHTML = data.map(eleve => `
            <div class="student-mobile-card">
              <div class="student-mobile-card-header">${eleve.nom ?? ''} ${eleve.prenom ?? ''}</div>
              <div class="student-mobile-card-row">
                <span class="student-mobile-card-label">تاريخ الميلاد:</span>
                <span class="student-mobile-card-value">${eleve.date_naiss ?? '—'}</span>
              </div>
              <div class="student-mobile-card-row">
                <span class="student-mobile-card-label">المستوى الدراسي:</span>
                <span class="student-mobile-card-value">${eleve.classe_scol ?? '—'}</span>
              </div>
              <div class="student-mobile-card-row">
                <span class="student-mobile-card-label">المؤسسة التعليمية:</span>
                <span class="student-mobile-card-value">${eleve.etablissement?.nom_etabliss ?? '—'}</span>
              </div>
              <div class="student-mobile-card-actions">
                <button class="btn btn-outline-danger btn-sm" onclick="openIstimaraPDF('${eleve.num_scolaire}')">
                  <i class="fa-solid fa-file-pdf"></i> PDF
                </button>
                <button class="btn-delete" data-id="${eleve.num_scolaire}">
                  <i class="fa-solid fa-trash"></i> حذف
                </button>
                <button class="btn-view" data-num-scolaire="${eleve.num_scolaire}" onclick="openViewModal('${eleve.num_scolaire}')">
                  <i class="fa-solid fa-eye"></i> عرض
                </button>
                <button class="btn-edit" data-num-scolaire="${eleve.num_scolaire}" onclick="openEditModal('${eleve.num_scolaire}')">
                  <i class="fa-solid fa-pen"></i> تعديل
                </button>
                <button class="btn btn-outline-info btn-sm" onclick="showComments('${eleve.num_scolaire}', '${eleve.nom ?? ''} ${eleve.prenom ?? ''}')" title="التعليقات">
                  <i class="fa-solid fa-comments"></i> تعليقات
                </button>
              </div>
            </div>
          `).join('');
        }
      }
    } catch (error) {
      // Error loading children
      tableBody.innerHTML = '<tr><td colspan="5" style="color:red;padding:2rem;text-align:center;">حدث خطأ أثناء تحميل البيانات.</td></tr>';
      if (mobileContainer) {
        mobileContainer.innerHTML = '<div style="text-align:center;padding:2rem;color:red;">حدث خطأ أثناء تحميل البيانات.</div>';
      }
    }
  }
  loadChildrenList();
    /* ===============================
   🏫 Step 1 → School Selection
   =============================== */
  const step1 = document.getElementById('step1');
  const step2 = document.getElementById('step2');
  const nextStep = document.getElementById('nextStep');
  const prevStep = document.getElementById('prevStep');
  const reloadStep1 = document.getElementById('reloadStep1');
  const form = document.getElementById('addChildForm');

  // Select elements - scoped to the add form
  const wilayaSelect = document.getElementById('wilayaSelect');
  const communeSelect = document.getElementById('communeSelect');
  const typeSelect = form.querySelector('select[name="type_ecole"]');
  const niveauSelect = form.querySelector('select[name="niveau"]');
  const ecoleSelect = form.querySelector('select[name="ecole"]');
  const wilayaNaiss = document.getElementById('wilayaNaiss');
  const communeNaiss = document.getElementById('communeNaiss');
  const nomEleve = form.querySelector('[name="nom"]');
  const nomPere = form.querySelector('[name="nom_pere"]');
  const prenomPere = form.querySelector('[name="prenom_pere"]');

  // When modal opens → load wilayas and show dark overlay
  const addChildModal = document.getElementById('addChildModal');
  const customOverlay = document.getElementById('customModalOverlay');
  
  // Hide Bootstrap's default backdrop
  const style = document.createElement('style');
  style.textContent = '.modal-backdrop { display: none !important; }';
  document.head.appendChild(style);
  
  addChildModal.addEventListener('show.bs.modal', async () => {
    customOverlay.style.display = 'block';
    if (wilayaSelect && communeSelect) {
    await loadWilayasGeneric(wilayaSelect, communeSelect);
    }
    if (wilayaNaiss && communeNaiss) {
    await loadWilayasGeneric(wilayaNaiss, communeNaiss);
    }
    await loadMothers();
    
    // Auto-fill relation_tuteur if tuteur role is already loaded
    if (window.currentUserRelationTuteur) {
      autoFillRelationTuteur(window.currentUserRelationTuteur);
    }
    
    // Update form based on tuteur role
    updateFormForGuardianRole();
    
    // Check if all school selection fields are already filled and load schools
    setTimeout(() => {
      if (typeSelect && niveauSelect && communeSelect && ecoleSelect) {
        if (typeSelect.value && niveauSelect.value && communeSelect.value) {
          // All fields selected, loading schools
          loadEtablissements();
        }
      }
    }, 500);
  });
  
  addChildModal.addEventListener('hidden.bs.modal', () => {
    customOverlay.style.display = 'none';
  });

  /* 🟢 Load wilayas from DB */
    /* ===============================
    🧩 Generic Wilaya / Commune Loader
    =============================== */
  async function loadWilayasGeneric(wilayaSelectEl, communeSelectEl) {
    if (!wilayaSelectEl || !communeSelectEl) {
      // Wilaya or commune select element not found
      return;
    }
    
    try {
      wilayaSelectEl.innerHTML = '<option value="">جارٍ التحميل...</option>';
      const res = await apiFetch('/api/wilayas');
      
      if (!res.ok) {
        throw new Error(`HTTP error! status: ${res.status}`);
      }
      
      const responseData = await res.json();
      
      // Handle response structure: could be array directly or wrapped in {data: [...]}
      const wilayas = Array.isArray(responseData) ? responseData : (responseData.data || []);

      wilayaSelectEl.innerHTML = '<option value="">اختر...</option>';
      if (Array.isArray(wilayas) && wilayas.length > 0) {
      wilayas.forEach(w => {
        wilayaSelectEl.innerHTML += `<option value="${w.code_wil}">${w.lib_wil_ar}</option>`;
      });
      }

      // 🏙️ When wilaya changes → load communes dynamically
      // Use a flag to prevent duplicate listeners
      if (!wilayaSelectEl.dataset.listenerAdded) {
        wilayaSelectEl.dataset.listenerAdded = 'true';
      wilayaSelectEl.addEventListener('change', async (e) => {
        const wilayaCode = e.target.value;
        communeSelectEl.innerHTML = '<option value="">جارٍ التحميل...</option>';
        communeSelectEl.disabled = true;

        if (!wilayaCode) {
          communeSelectEl.innerHTML = '<option value="">اختر الولاية أولا...</option>';
          communeSelectEl.disabled = true;
          return;
        }

        try {
          const res = await apiFetch(`/api/communes/by-wilaya/${wilayaCode}`);
          
          if (!res.ok) {
            throw new Error(`HTTP error! status: ${res.status}`);
          }
          
          const responseData = await res.json();
          
          // Handle response structure: could be array directly or wrapped in {data: [...]}
          const communes = Array.isArray(responseData) ? responseData : (responseData.data || []);

          communeSelectEl.innerHTML = '<option value="">اختر...</option>';
          if (Array.isArray(communes) && communes.length > 0) {
          communes.forEach(c => {
            communeSelectEl.innerHTML += `<option value="${c.code_comm}">${c.lib_comm_ar}</option>`;
          });
          }
          communeSelectEl.disabled = false;
        } catch (err) {
          // Error loading communes
          communeSelectEl.innerHTML = '<option value="">تعذر تحميل البيانات</option>';
          communeSelectEl.disabled = true;
        }
      });
      }
    } catch (err) {
      // Error loading wilayas
      wilayaSelectEl.innerHTML = '<option value="">تعذر تحميل البيانات</option>';
    }
  }


  /* ===============================
    🧩 Generic Commune Loader
    =============================== */
  async function handleWilayaChange(wilayaSelectEl, communeSelectEl, ecoleSelectEl = null) {
    const wilayaCode = wilayaSelectEl.value;

    communeSelectEl.innerHTML = '<option value="">جارٍ التحميل...</option>';
    communeSelectEl.disabled = true;

    // If an école select exists, reset it too
    if (ecoleSelectEl) {
      ecoleSelectEl.innerHTML = '<option value="">اختر كل المعايير أولا (مؤسسة التربية والتعليم، المستوى الدراسي، البلدية)</option>';
      ecoleSelectEl.disabled = true;
    }

    if (!wilayaCode) {
      communeSelectEl.innerHTML = '<option value="">اختر الولاية أولا...</option>';
      return;
    }

    try {
      const res = await fetch(`/api/communes/by-wilaya/${wilayaCode}`);
      const responseData = await res.json();
      
      // Handle response structure: could be array directly or wrapped in {data: [...]}
      const communes = Array.isArray(responseData) ? responseData : (responseData.data || []);

      communeSelectEl.innerHTML = '<option value="">اختر...</option>';
      if (Array.isArray(communes)) {
      communes.forEach(c => {
        communeSelectEl.innerHTML += `<option value="${c.code_comm}">${c.lib_comm_ar}</option>`;
      });
      }
      communeSelectEl.disabled = false;
    } catch (err) {
      // Error loading communes
      communeSelectEl.innerHTML = '<option value="">تعذر تحميل البيانات</option>';
    }
  }
  
  // Note: Event listeners for wilaya changes are handled inside loadWilayasGeneric
  // to avoid duplicate listeners. handleWilayaChange is kept for backward compatibility

  /* 🟢 Load établissements dynamically when commune + niveau + type are selected */
  if (typeSelect && niveauSelect && communeSelect) {
    [typeSelect, niveauSelect, communeSelect].forEach(sel => {
      if (sel) {
        sel.addEventListener('change', loadEtablissements);
      }
    });
  } else {
    // Missing select elements
  }

  async function loadEtablissements() {
    const code_commune = communeSelect.value;
    const niveau = niveauSelect.value;
    const nature = typeSelect.value;

    // Loading establishments

    // Make sure all are chosen - disable and show message if any is missing
    if (!code_commune || !niveau || !nature) {
      // Missing fields, disabling school dropdown
      ecoleSelect.innerHTML = '<option value="">اختر كل المعايير أولا (مؤسسة التربية والتعليم، المستوى الدراسي، البلدية)</option>';
      ecoleSelect.disabled = true;
      return;
    }

    ecoleSelect.innerHTML = '<option value="">جارٍ التحميل...</option>';
    ecoleSelect.disabled = true;

    try {
      const url = `/api/etablissements?code_commune=${code_commune}&niveau=${encodeURIComponent(niveau)}&nature=${encodeURIComponent(nature)}`;
      const res = await fetch(url);

      if (!res.ok) {
        const errorText = await res.text();
        // API Error
        ecoleSelect.innerHTML = '<option value="">لم يتم العثور على مؤسسات</option>';
        ecoleSelect.disabled = true;
        return;
      }

      const responseData = await res.json();
      
      // Handle response structure: could be array directly or wrapped in {data: [...]}
      const etabs = Array.isArray(responseData) ? responseData : (responseData.data || []);
      
      // Schools received

      if (!etabs || !Array.isArray(etabs) || etabs.length === 0) {
        ecoleSelect.innerHTML = '<option value="">لم يتم العثور على مؤسسات</option>';
        ecoleSelect.disabled = true;
        return;
      }

      ecoleSelect.innerHTML = '<option value="">اختر...</option>';

      etabs.forEach(e => {
        ecoleSelect.innerHTML += `<option value="${e.code_etabliss}">${e.nom_etabliss}</option>`;
      });

      ecoleSelect.disabled = false;
      // School dropdown populated successfully
    } catch (err) {
      // Error loading establishments
      ecoleSelect.innerHTML = '<option value="">تعذر تحميل البيانات</option>';
      ecoleSelect.disabled = true;
    }
  }

  /* ===============================
    🔁 Reset Step 1
  =============================== */
  function resetStep1() {
    step1.querySelectorAll('select').forEach(sel => {
      sel.value = '';
      sel.classList.remove('is-invalid'); // ✅ remove red border
    });

    communeSelect.innerHTML = '<option value="">اختر الولاية أولا...</option>';
    communeSelect.disabled = true;

    ecoleSelect.innerHTML = '<option value="">اختر كل المعايير أولا (مؤسسة التربية والتعليم، المستوى الدراسي، البلدية)</option>';
    ecoleSelect.disabled = true;
  }

  // 🔁 "إعادة تعيين" button click
  reloadStep1.addEventListener('click', resetStep1);

  /* ===============================
    🧹 Full Reset when Modal Closes
  =============================== */
  function fullReset() {
    resetStep1();
    form.reset(); // clears all inputs
    step2.classList.add('d-none'); // hide step 2
    step1.classList.remove('d-none'); // show step 1 again
  }

  // ❌ When modal closes (any method)
  addChildModal.addEventListener('hidden.bs.modal', () => {
    // Stop backdrop interval if running
    if (backdropInterval) {
      clearInterval(backdropInterval);
      backdropInterval = null;
    }
    fullReset();
  });

  /* ===============================
    🟠 Go to Step 2 — but validate first
  =============================== */
  nextStep.addEventListener('click', () => {
    const requiredFields = [
      { el: typeSelect, name: 'مؤسسة التربية والتعليم' },
      { el: niveauSelect, name: 'المستوى الدراسي' },
      { el: wilayaSelect, name: 'الولاية' },
      { el: communeSelect, name: 'البلدية' },
      { el: ecoleSelect, name: 'المؤسسة التعليمية' },
    ];

    let isValid = true;
    let missingFields = [];

    requiredFields.forEach(field => {
      if (!field.el.value) {
        isValid = false;
        missingFields.push(field.name);
        field.el.classList.add('is-invalid'); // 🔴 mark invalid
      } else {
        field.el.classList.remove('is-invalid'); // ✅ remove if valid
      }
    });

    if (!isValid) {
      Swal.fire({
        icon: 'warning',
        title: 'يرجى إكمال البيانات',
        html: `الحقول التالية مطلوبة:<br><b>${missingFields.join('<br>')}</b>`,
        confirmButtonText: 'حسنًا',
        customClass: {
          confirmButton: 'custom-confirm-btn'
        },
        buttonsStyling: false
      });
      return;
    }


    // ✅ All good → go to Step 2
    step1.classList.add('d-none');
    step2.classList.remove('d-none');
  });


  /* ===============================
    🧹 Remove red border when selecting valid value
  =============================== */
  step1.querySelectorAll('select').forEach(sel => {
    sel.addEventListener('change', () => {
      if (sel.value) {
        sel.classList.remove('is-invalid'); // ✅ remove red border instantly
      }
    });
  });

    // Back to Step 1
    if (prevStep) {
      prevStep.addEventListener('click', () => {
        step2.classList.add('d-none');
        step1.classList.remove('d-none');
      });
    }
  // ===============================
  // 🎓 Dynamically update "القسم" options based on selected niveau
  // ===============================

  const classeSelect = document.getElementById('classeSelect');

  function updateClasseOptions() {
    const niveau = niveauSelect.value; // value from Step 1 (ابتدائي / متوسط / ثانوي)
    classeSelect.innerHTML = '<option value="">اختر...</option>'; // reset

    let options = [];

    if (niveau === 'ابتدائي') {
      options = [
        'السنة الأولى ابتدائي',
        'السنة الثانية ابتدائي',
        'السنة الثالثة ابتدائي',
        'السنة الرابعة ابتدائي',
        'السنة الخامسة ابتدائي'
      ];
    } else if (niveau === 'متوسط') {
      options = [
        'السنة الأولى متوسط',
        'السنة الثانية متوسط',
        'السنة الثالثة متوسط',
        'السنة الرابعة متوسط'
      ];
    } else if (niveau === 'ثانوي') {
      options = [
        'السنة الأولى ثانوي',
        'السنة الثانية ثانوي',
        'السنة الثالثة ثانوي'
      ];
    }

    options.forEach(opt => {
      classeSelect.innerHTML += `<option value="${opt}">${opt}</option>`;
    });
  }

  // whenever niveau changes (in Step 1)
  niveauSelect.addEventListener('change', updateClasseOptions);

  // and also update once when moving to Step 2
  nextStep.addEventListener('click', updateClasseOptions);


  // Only auto-fill father's name if it's not read-only (not guardian role)
  if (nomEleve && nomPere) {
    nomEleve.addEventListener('input', () => {
      // Only auto-fill if father's name field is not read-only
      if (!nomPere.readOnly && !nomPere.hasAttribute('readonly')) {
      nomPere.value = nomEleve.value;
      nomPere.setAttribute('readonly', true);
      }
    });
  }

  const relationSelect = document.getElementById('relationSelect') || form.querySelector('[name="relation_tuteur"]');
  const motherSelect = document.getElementById('motherSelect');
  // Note: nin_pere and nss_pere fields removed - using father relationship instead
  const ninPere = document.getElementById('ninPere'); // Display-only field
  const nssPere = document.getElementById('nssPere'); // Display-only field

  // Function to auto-fill NIN and NSS based on relation
  function autoFillParentData(relation) {
    if (!relationSelect || !ninPere || !nssPere) {
      // Form fields not found for auto-fill
      return;
    }

    // Reset display-only fields (these are not submitted)
    if (ninPere) {
      ninPere.value = '';
    }
    if (nssPere) {
      nssPere.value = '';
    }

    // Only auto-fill and lock if relation is "ولي" (guardian)
    if (relation === '1' || relation === 1) {
      const sexeTuteur = window.currentUserSexe?.trim();
      const userNIN = window.currentUserNIN?.trim();
      const userNSS = window.currentUserNSS?.trim();

      // Auto-filling for ولي

      // Note: nin_pere and nss_pere are now display-only fields
      // They are filled from father relationship when role is 2 or 3
      // For role 1 (Father), tuteur is the father, so these fields remain empty
    } else {
      // For "وصي" or any other option, fields remain empty and editable
      // Relation is not ولي, fields cleared
    }
  }

  // Function to auto-fill father's name from tuteur when relation is "ولي" (1)
  function autoFillTuteurData(relation) {
    if ((relation === '1' || relation === 1) && nomPere && prenomPere) {
      // Get tuteur data from session/global
      const tuteurNomAr = "{{ $tuteur['nom_ar'] ?? '' }}";
      const tuteurPrenomAr = "{{ $tuteur['prenom_ar'] ?? '' }}";
      
      if (tuteurNomAr && tuteurPrenomAr) {
        nomPere.value = tuteurNomAr;
        prenomPere.value = tuteurPrenomAr;
        nomPere.setAttribute('readonly', true);
        prenomPere.setAttribute('readonly', true);
        nomPere.readOnly = true;
        prenomPere.readOnly = true;
        // Auto-filled father name from tuteur
      }
    } else if (nomPere && prenomPere) {
      // Clear and make editable if not "ولي"
      nomPere.value = '';
      prenomPere.value = '';
      nomPere.removeAttribute('readonly');
      prenomPere.removeAttribute('readonly');
      nomPere.readOnly = false;
      prenomPere.readOnly = false;
    }
  }

  if (relationSelect) {
    relationSelect.addEventListener('change', () => {
      autoFillParentData(relationSelect.value);
      autoFillTuteurData(relationSelect.value);
    });
    // Initial fill based on default/selected value
    autoFillParentData(relationSelect.value);
    autoFillTuteurData(relationSelect.value);
  }

  // Auto-fill when mother is selected (for role 1 - Father)
  if (motherSelect) {
    motherSelect.addEventListener('change', function() {
      const selectedMotherId = this.value;
      const relationTuteur = window.currentUserRelationTuteur;
      
      // For role 1 (Father), show and fill mother NIN/NSS when mother is selected
      if ((relationTuteur === '1' || relationTuteur === 1) && selectedMotherId && window.mothersData && window.mothersData.length > 0) {
        const selectedMother = window.mothersData.find(m => m.id == selectedMotherId);
        if (selectedMother) {
          // Show mother NIN/NSS fields
          const ninMereWrapper = document.getElementById('ninMereWrapper');
          const nssMereWrapper = document.getElementById('nssMereWrapper');
          const ninMere = document.getElementById('ninMere');
          const nssMere = document.getElementById('nssMere');
          
          if (ninMereWrapper) ninMereWrapper.style.display = 'block';
          if (nssMereWrapper) nssMereWrapper.style.display = 'block';
          
          // Fill mother NIN and NSS
          if (ninMere && selectedMother.nin) {
            ninMere.value = selectedMother.nin;
          }
          if (nssMere && selectedMother.nss) {
            nssMere.value = selectedMother.nss;
          }
        }
      } else {
        // Hide mother NIN/NSS if no mother selected or not role 1
        const ninMereWrapper = document.getElementById('ninMereWrapper');
        const nssMereWrapper = document.getElementById('nssMereWrapper');
        if (ninMereWrapper) ninMereWrapper.style.display = 'none';
        if (nssMereWrapper) nssMereWrapper.style.display = 'none';
      }
    });
  }

  // Handicap toggle (create form)
  const handicapYes = document.getElementById('handicapYes');
  const handicapNo = document.getElementById('handicapNo');
  const handicapNatureWrapper = document.getElementById('handicapNatureWrapper');
  const handicapPercentageWrapper = document.getElementById('handicapPercentageWrapper');
  const handicapNatureInput = document.querySelector('[name="handicap_nature"]');
  const handicapPercentageInput = document.querySelector('[name="handicap_percentage"]');

  function toggleHandicapDetails(show) {
    [handicapNatureWrapper, handicapPercentageWrapper].forEach(el => {
      if (el) el.classList.toggle('d-none', !show);
    });
    if (handicapNatureInput) {
      handicapNatureInput.required = !!show;
      if (!show) handicapNatureInput.value = '';
    }
    if (handicapPercentageInput) {
      handicapPercentageInput.required = !!show;
      if (!show) handicapPercentageInput.value = '';
    }
  }

  if (handicapYes && handicapNo) {
    handicapYes.addEventListener('change', () => toggleHandicapDetails(true));
    handicapNo.addEventListener('change', () => toggleHandicapDetails(false));
    toggleHandicapDetails(handicapYes.checked); // init
  }
  /* ===============================
    ✍️ Input Restrictions
  =============================== */
  function allowArabicOnly(input) {
    input.addEventListener('input', () => {
      input.value = input.value.replace(/[^ء-ي\s]/g, ''); // allow only Arabic letters + spaces
    });
  }

  function allowDigitsOnly(input, maxLength = null) {
    input.addEventListener('input', () => {
      input.value = input.value.replace(/\D/g, ''); // digits only
      if (maxLength) input.value = input.value.slice(0, maxLength);
    });
  }

  /* Apply Arabic restriction */
  document.querySelectorAll('input[name="prenom"], input[name="nom"], input[name="prenom_pere"], input[name="nom_pere"]').forEach(allowArabicOnly);

  /* Apply number restriction */
  allowDigitsOnly(document.querySelector('input[name="num_scolaire"]'), 16);
  // Note: nin_pere and nss_pere fields removed - using father relationship instead

  /* Apply Arabic restriction for edit form */
  document.querySelectorAll('#editChildForm input[name="prenom"], #editChildForm input[name="nom"], #editChildForm input[name="prenom_pere"], #editChildForm input[name="nom_pere"]').forEach(allowArabicOnly);

  /* Apply number restriction for edit form */
  // Note: nin_pere and nss_pere fields removed - using father relationship instead



  /* ===============================
    🚨 Inline Error Display
  =============================== */
  function showError(input, message) {
    removeError(input);
    const error = document.createElement('small');
    error.className = 'text-danger error-msg';
    error.innerText = message;
    input.classList.add('is-invalid');
    input.parentElement.appendChild(error);
  }

  function removeError(input) {
    input.classList.remove('is-invalid');
    const existing = input.parentElement.querySelector('.error-msg');
    if (existing) existing.remove();
  }

  function isValidNSS(NSS) {
    if (!/^\d{12}$/.test(NSS)) return false;

    const digits = NSS.split('').map(Number);
    const sum = (
      digits[0] + digits[2] + digits[4] + digits[6] + digits[8]
    ) * 2 + (
      digits[1] + digits[3] + digits[5] + digits[7] + digits[9]
    );
    const cleN = 99 - sum;
    const formatted = cleN.toString().padStart(2, '0');
    return NSS.slice(10, 12) === formatted;
  }

    /* ===============================
      ✅ Validation + Submit
    =============================== */
    form.addEventListener('submit', async (e) => {
      e.preventDefault();

      // Reset state
      form.querySelectorAll('.error-msg').forEach(e => e.remove());
      form.querySelectorAll('.is-invalid').forEach(e => e.classList.remove('is-invalid'));

      let firstError = null;
      let hasError = false;

      // === Arabic fields check ===
      const arabicInputs = ['prenom','nom','prenom_pere','nom_pere'];
      arabicInputs.forEach(name => {
        const el = form.querySelector(`[name="${name}"]`);
        if (el && el.value.trim() && !/^[ء-ي\s]+$/.test(el.value)) {
          showError(el, 'يجب أن يكون النص بالعربية فقط');
          if (!firstError) firstError = el;
          hasError = true;
        }
      });

      // === Numeric length checks ===
      const numericChecks = [
        { name: 'num_scolaire', len: 16, label: 'الرقم التعريفي المدرسي' }
        // Note: nin_pere and nss_pere fields removed - using father relationship instead
      ];

      numericChecks.forEach(field => {
        const el = form.querySelector(`[name="${field.name}"]`);
        if (el && el.value && el.value.length !== field.len) {
          showError(el, `${field.label} يجب أن يحتوي على ${field.len} رقمًا`);
          if (!firstError) firstError = el;
          hasError = true;
        }
      });

      // === Async: Check matricule existence ===
      const matricule = form.querySelector('[name="num_scolaire"]').value.trim();
      if (matricule) {
        try {
          const res = await fetch(`/api/children/check-matricule/${matricule}`);
          const data = await res.json();
          if (data.exists) {
            const el = form.querySelector('[name="num_scolaire"]');
            showError(el, 'الرقم التعريفي المدرسي موجود مسبقًا');
            if (!firstError) firstError = el;
            hasError = true;
          }
        } catch (err) {
          // Matricule check failed
        }
      }

      // === Age >= 4 years ===
      const dateNaissInput = form.querySelector('[name="date_naiss"]');
      if (dateNaissInput && dateNaissInput.value) {
        const birthDate = new Date(dateNaissInput.value);
        const today = new Date();
        const age = (today - birthDate) / (1000 * 60 * 60 * 24 * 365.25);
        if (age < 4) {
          showError(dateNaissInput, 'عمر التلميذ يجب أن يكون 4 سنوات على الأقل');
          if (!firstError) firstError = dateNaissInput;
          hasError = true;
        }
      }

      // === NSS key validation ===
      const relation = form.querySelector('[name="relation_tuteur"]').value;
      const sexeTuteur = window.currentUserSexe?.trim();
      const tuteurNSS = window.currentUserNSS?.trim();

      // Note: nin_pere and nss_pere fields removed - using father relationship instead
      // Validation is no longer needed as these fields are not submitted

      // === Final check ===
      if (hasError) {
        if (firstError) firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
        return;
      }

      // === Submit form ===
      const formData = new FormData(form);
      
      // Add father_id based on tuteur's role
      const relationTuteur = window.currentUserRelationTuteur;
      if ((relationTuteur === '2' || relationTuteur === 2 || relationTuteur === '3' || relationTuteur === 3) && window.tuteurData && window.tuteurData.father_id) {
        formData.append('father_id', window.tuteurData.father_id);
      }
      // For role 1 (Father), father_id should be null (tuteur is the father)
      
      try {
        // Submitting form
        
        // Use apiFetch which automatically adds the token
        const response = await apiFetch('/api/eleves', {
          method: 'POST',
          body: formData,
          headers: {
            // apiFetch will add Authorization header automatically
            // Don't set Content-Type for FormData - browser will set it with boundary
          }
        });
        
        // Response received
        
        // Check response status
        if (!response.ok) {
          let errorMessage = 'حدث خطأ أثناء الإضافة';
          let errorData = null;
          
          try {
            errorData = await response.json();
            // Error data received
            if (errorData.message) {
              errorMessage = errorData.message;
            } else if (errorData.errors) {
              // Handle validation errors
              const errorMessages = Object.values(errorData.errors).flat();
              errorMessage = errorMessages.join('\n');
            }
          } catch (e) {
            // Error parsing response
            // If we can't parse JSON, use status text
            errorMessage = response.statusText || 'حدث خطأ أثناء الإضافة';
          }
          
          // Only show error if it's not an authentication error (auth errors redirect automatically)
          if (response.status === 401) {
            // 401 Unauthorized - Authentication error
            // Don't show error, apiFetch will handle redirect
            return;
          }
          
          Swal.fire('حدث خطأ!', errorMessage, 'error');
          return;
        }

        // Success
        const result = await response.json();
        Swal.fire({
          title: 'تمت الإضافة بنجاح!',
          text: 'يمكنك الآن تحميل الاستمارة الخاصة بالتلميذ.',
          icon: 'success',
          confirmButtonText: 'حسنًا'
        }).then(() => {
          // ✅ Just close modal using its close button (Bootstrap handles cleanup)
          const closeBtn = document.querySelector('#addChildModal .btn-close');
          if (closeBtn) closeBtn.click();

          // ✅ Reset form and reload data
          fullReset();
          loadChildrenList();
        });

      } catch (err) {
        // Error creating student
        Swal.fire('حدث خطأ!', err.message || 'حدث خطأ أثناء الإضافة', 'error');
      }
    });

    // ===============================
    // ✏️ EDIT MODAL HANDLING
    // ===============================
    const editChildModal = document.getElementById('editChildModal');
    const editForm = document.getElementById('editChildForm');
    const editStep1 = document.getElementById('editStep1');
    const editStep2 = document.getElementById('editStep2');
    const editNextStep = document.getElementById('editNextStep');
    const editPrevStep = document.getElementById('editPrevStep');
    const editReloadStep1 = document.getElementById('editReloadStep1');

    // Global function to open istimara PDF
    window.openIstimaraPDF = function(num_scolaire) {
      if (!num_scolaire) {
        return;
      }
      
      // Open PDF in new tab with regenerate parameter to ensure fresh PDF
      const pdfUrl = `/eleves/${num_scolaire}/istimara?regenerate=1`;
      window.open(pdfUrl, '_blank');
    };

    // Global function to open view modal
    window.openViewModal = async function(num_scolaire) {
      try {
        // Open modal first
        const modal = new bootstrap.Modal(document.getElementById('viewChildModal'));
        modal.show();
        customOverlay.style.display = 'block';
        
        const response = await fetch(`/eleves/${num_scolaire}/edit`);
        if (!response.ok) throw new Error('Failed to load student data');
        
        const eleve = await response.json();
        
        // Populate all fields (read-only)
        document.getElementById('view_nom').value = eleve.nom || '—';
        document.getElementById('view_prenom').value = eleve.prenom || '—';
        document.getElementById('view_nom_pere').value = eleve.nom_pere || '—';
        document.getElementById('view_prenom_pere').value = eleve.prenom_pere || '—';
        // Display mother data from relationship
        if (eleve.mother) {
          document.getElementById('view_nom_mere').value = eleve.mother.nom_ar || '—';
          document.getElementById('view_prenom_mere').value = eleve.mother.prenom_ar || '—';
          document.getElementById('view_nin_mere').value = eleve.mother.nin || '—';
          document.getElementById('view_nss_mere').value = eleve.mother.nss || '—';
        } else {
          document.getElementById('view_nom_mere').value = '—';
          document.getElementById('view_prenom_mere').value = '—';
          document.getElementById('view_nin_mere').value = '—';
          document.getElementById('view_nss_mere').value = '—';
        }
        document.getElementById('view_date_naiss').value = eleve.date_naiss || '—';
        // Convert relation_tuteur integer to text for display
        let relationText = '—';
        if (eleve.relation_tuteur === 1 || eleve.relation_tuteur === '1') {
          relationText = 'ولي';
        } else if (eleve.relation_tuteur === 3 || eleve.relation_tuteur === '3') {
          relationText = 'وصي';
        }
        document.getElementById('view_relation_tuteur').value = relationText;
        // Display father NIN/NSS from relationship if available
        if (eleve.father) {
          document.getElementById('view_nin_pere').value = eleve.father.nin || '—';
          document.getElementById('view_nss_pere').value = eleve.father.nss || '—';
        } else {
          document.getElementById('view_nin_pere').value = '—';
          document.getElementById('view_nss_pere').value = '—';
        }
        document.getElementById('view_classe_scol').value = eleve.classe_scol || '—';
        document.getElementById('view_sexe').value = eleve.sexe || '—';
        document.getElementById('view_handicap').value = (eleve.handicap === '1' || eleve.handicap === 1) ? 'نعم' : 'لا';
        
        // Birth place
        if (eleve.commune_naissance) {
          const birthWilayaCode = eleve.commune_naissance.code_wilaya;
          if (birthWilayaCode) {
            // Try to get wilaya name from all wilayas
            try {
              const wilayasRes = await apiFetch('/api/wilayas');
              if (wilayasRes.ok) {
                const wilayas = await wilayasRes.json();
                const wilaya = wilayas.find(w => w.code_wil === birthWilayaCode);
                document.getElementById('view_wilaya_naiss').value = wilaya ? wilaya.lib_wil_ar : `ولاية ${birthWilayaCode}`;
              } else {
                document.getElementById('view_wilaya_naiss').value = `ولاية ${birthWilayaCode}`;
              }
            } catch (err) {
              document.getElementById('view_wilaya_naiss').value = `ولاية ${birthWilayaCode}`;
            }
          } else {
            document.getElementById('view_wilaya_naiss').value = '—';
          }
          document.getElementById('view_commune_naiss').value = eleve.commune_naissance.lib_comm_ar || '—';
        } else {
          document.getElementById('view_wilaya_naiss').value = '—';
          document.getElementById('view_commune_naiss').value = '—';
        }
        
        // School info
        if (eleve.etablissement) {
          document.getElementById('view_etablissement').value = eleve.etablissement.nom_etabliss || '—';
          document.getElementById('view_type_ecole').value = eleve.etablissement.nature_etablissement || '—';
        } else {
          document.getElementById('view_etablissement').value = '—';
          document.getElementById('view_type_ecole').value = '—';
        }
        document.getElementById('view_niveau').value = eleve.niv_scol || '—';
        
      } catch (error) {
        // Error loading student data
        Swal.fire('Error', 'Failed to load student data', 'error');
        const modal = bootstrap.Modal.getInstance(document.getElementById('viewChildModal'));
        if (modal) modal.hide();
      }
    };

    // View modal events
    const viewChildModal = document.getElementById('viewChildModal');
    viewChildModal.addEventListener('show.bs.modal', () => {
      customOverlay.style.display = 'block';
    });

    viewChildModal.addEventListener('hidden.bs.modal', () => {
      customOverlay.style.display = 'none';
    });

    // Global function to open edit modal
    window.openEditModal = async function(num_scolaire) {
      try {
        // Open modal first
        const modal = new bootstrap.Modal(editChildModal);
        modal.show();
        customOverlay.style.display = 'block';
        
        // Show step 2
        editStep1.classList.add('d-none');
        editStep2.classList.remove('d-none');
        
        const response = await fetch(`/eleves/${num_scolaire}/edit`, {
          headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
          },
          credentials: 'same-origin'
        });
        
        if (!response.ok) {
          const errorText = await response.text();
          throw new Error(`Failed to load student data: ${response.status} ${response.statusText}`);
        }
        
        // Check if response is JSON
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
          const text = await response.text();
          throw new Error('Expected JSON response but got: ' + contentType);
        }
        
        const eleve = await response.json();
        
        // Get all form elements
        const editWilayaSelect = document.getElementById('editWilayaSelect');
        const editCommuneSelect = document.getElementById('editCommuneSelect');
        const editWilayaNaiss = document.getElementById('editWilayaNaiss');
        const editCommuneNaiss = document.getElementById('editCommuneNaiss');
        const editEcoleSelect = document.getElementById('editEcoleSelect');
        const editTypeEcole = document.getElementById('edit_type_ecole');
        const editNiveau = document.getElementById('edit_niveau');
        const editClasseSelect = document.getElementById('editClasseSelect');
        
        // Load wilayas
        await loadWilayasGeneric(editWilayaSelect, editCommuneSelect);
        await loadWilayasGeneric(editWilayaNaiss, editCommuneNaiss);
        
        // Set hidden field
        document.getElementById('edit_num_scolaire').value = eleve.num_scolaire;
        
        // Populate Step 1 - School Selection
        if (eleve.etablissement) {
          if (eleve.etablissement.nature_etablissement) {
            editTypeEcole.value = eleve.etablissement.nature_etablissement;
          }
          if (eleve.niv_scol) {
            editNiveau.value = eleve.niv_scol;
          }
          
          // Set wilaya for school (from commune_residence)
          if (eleve.commune_residence && eleve.commune_residence.code_wilaya) {
            editWilayaSelect.value = eleve.commune_residence.code_wilaya;
            // Load communes for school
            setTimeout(async () => {
              try {
                const res = await fetch(`/api/communes/by-wilaya/${eleve.commune_residence.code_wilaya}`);
                const responseData = await res.json();
                
                // Handle response structure: could be array directly or wrapped in {data: [...]}
                const communes = Array.isArray(responseData) ? responseData : (responseData.data || []);
                
                editCommuneSelect.innerHTML = '<option value="">اختر...</option>';
                if (Array.isArray(communes)) {
                communes.forEach(c => {
                  editCommuneSelect.innerHTML += `<option value="${c.code_comm}" ${c.code_comm === eleve.code_commune ? 'selected' : ''}>${c.lib_comm_ar}</option>`;
                });
                }
                editCommuneSelect.disabled = false;
                
                // Load schools
                if (eleve.code_commune && eleve.niv_scol && eleve.etablissement.nature_etablissement) {
                  setTimeout(async () => {
                    try {
                      const url = `/api/etablissements?code_commune=${eleve.code_commune}&niveau=${eleve.niv_scol}&nature=${eleve.etablissement.nature_etablissement}`;
                      const res = await fetch(url);
                      if (res.ok) {
                        const responseData = await res.json();
                        
                        // Handle response structure: could be array directly or wrapped in {data: [...]}
                        const etabs = Array.isArray(responseData) ? responseData : (responseData.data || []);
                        
                        editEcoleSelect.innerHTML = '<option value="">اختر...</option>';
                        if (Array.isArray(etabs)) {
                        etabs.forEach(e => {
                          editEcoleSelect.innerHTML += `<option value="${e.code_etabliss}" ${e.code_etabliss === eleve.code_etabliss ? 'selected' : ''}>${e.nom_etabliss}</option>`;
                        });
                        }
                        editEcoleSelect.disabled = false;
                      }
                    } catch (err) {
                      // Error loading schools
                    }
                  }, 300);
                }
              } catch (err) {
                // Error loading communes
              }
            }, 300);
          }
        }
        
        // Set birth place wilaya and commune
        if (eleve.commune_naissance && eleve.commune_naissance.code_wilaya) {
          editWilayaNaiss.value = eleve.commune_naissance.code_wilaya;
          setTimeout(async () => {
            try {
              const res = await fetch(`/api/communes/by-wilaya/${eleve.commune_naissance.code_wilaya}`);
              const responseData = await res.json();
              
              // Handle response structure: could be array directly or wrapped in {data: [...]}
              const communes = Array.isArray(responseData) ? responseData : (responseData.data || []);
              
              editCommuneNaiss.innerHTML = '<option value="">اختر...</option>';
              if (Array.isArray(communes)) {
              communes.forEach(c => {
                editCommuneNaiss.innerHTML += `<option value="${c.code_comm}" ${c.code_comm === eleve.commune_naiss ? 'selected' : ''}>${c.lib_comm_ar}</option>`;
              });
              }
              editCommuneNaiss.disabled = false;
            } catch (err) {
              // Error loading birth communes
            }
          }, 300);
        }
        
        // Populate Step 2 fields
        document.getElementById('edit_nom').value = eleve.nom || '';
        document.getElementById('edit_prenom').value = eleve.prenom || '';
        
        // Fill father/mother name fields based on relationship
        if (eleve.father) {
          document.getElementById('edit_nom_pere').value = eleve.father.nom_ar || '';
          document.getElementById('edit_prenom_pere').value = eleve.father.prenom_ar || '';
        } else {
          // Fallback if no father relationship
          document.getElementById('edit_nom_pere').value = '';
          document.getElementById('edit_prenom_pere').value = '';
        }
        
        document.getElementById('edit_date_naiss').value = eleve.date_naiss || '';
        
        // Set relation_tuteur first (this is the student's relation, not tuteur's role)
        const editRelationSelect = document.getElementById('edit_relation_tuteur');
        const originalRelation = eleve.relation_tuteur || '';
        
        if (editRelationSelect && originalRelation) {
          editRelationSelect.value = originalRelation;
          // Make it read-only since it's based on tuteur's role
          editRelationSelect.disabled = true;
          editRelationSelect.style.backgroundColor = '#f8f9fa';
          
          // Prevent relation changes
          editRelationSelect.addEventListener('change', () => {
            editRelationSelect.value = originalRelation;
          });
        }
        
        // Set mother_id if available
        if (eleve.mother_id && editMotherSelect) {
          editMotherSelect.value = eleve.mother_id;
        }
        
        // Update form based on tuteur role BEFORE filling NIN/NSS
        // This will show/hide the appropriate fields
        updateFormForEditGuardianRole();
        
        // Get tuteur role for conditional display
        const relationTuteur = window.currentUserRelationTuteur;
        
        // Fill father NIN/NSS if available (after fields are shown)
        if (eleve.father) {
          const editNinPereWrapper = document.getElementById('edit_ninPereWrapper');
          const editNssPereWrapper = document.getElementById('edit_nssPereWrapper');
          const editNinPere = document.getElementById('edit_ninPere');
          const editNssPere = document.getElementById('edit_nssPere');
          
          // Show fields for roles 2 (Mother) or 3 (Guardian)
          if ((relationTuteur === '2' || relationTuteur === 2 || relationTuteur === '3' || relationTuteur === 3) && editNinPereWrapper && editNssPereWrapper) {
            editNinPereWrapper.style.display = 'block';
            editNssPereWrapper.style.display = 'block';
          }
          
          if (editNinPere && eleve.father.nin) {
            editNinPere.value = eleve.father.nin;
            editNinPere.setAttribute('readonly', true);
            editNinPere.readOnly = true;
            editNinPere.style.backgroundColor = '#f8f9fa';
          }
          if (editNssPere && eleve.father.nss) {
            editNssPere.value = eleve.father.nss;
            editNssPere.setAttribute('readonly', true);
            editNssPere.readOnly = true;
            editNssPere.style.backgroundColor = '#f8f9fa';
          }
        }
        
        // Fill mother NIN/NSS if available (after fields are shown)
        if (eleve.mother) {
          const editNinMereWrapper = document.getElementById('edit_ninMereWrapper');
          const editNssMereWrapper = document.getElementById('edit_nssMereWrapper');
          const editNinMere = document.getElementById('edit_ninMere');
          const editNssMere = document.getElementById('edit_nssMere');
          
          // Show fields for roles 2 (Mother) or 3 (Guardian)
          if ((relationTuteur === '2' || relationTuteur === 2 || relationTuteur === '3' || relationTuteur === 3) && editNinMereWrapper && editNssMereWrapper) {
            editNinMereWrapper.style.display = 'block';
            editNssMereWrapper.style.display = 'block';
          }
          
          if (editNinMere && eleve.mother.nin) {
            editNinMere.value = eleve.mother.nin;
            editNinMere.setAttribute('readonly', true);
            editNinMere.readOnly = true;
            editNinMere.style.backgroundColor = '#f8f9fa';
          }
          if (editNssMere && eleve.mother.nss) {
            editNssMere.value = eleve.mother.nss;
            editNssMere.setAttribute('readonly', true);
            editNssMere.readOnly = true;
            editNssMere.style.backgroundColor = '#f8f9fa';
          }
        }
        
        // For role 2 (Mother), also show and fill mother (tuteur) NIN/NSS
        if (relationTuteur === '2' || relationTuteur === 2) {
          const editNinMereWrapper = document.getElementById('edit_ninMereWrapper');
          const editNssMereWrapper = document.getElementById('edit_nssMereWrapper');
          const editNinMere = document.getElementById('edit_ninMere');
          const editNssMere = document.getElementById('edit_nssMere');
          
          if (editNinMereWrapper) editNinMereWrapper.style.display = 'block';
          if (editNssMereWrapper) editNssMereWrapper.style.display = 'block';
          
          // Pre-fill from tuteur's NIN and NSS (since tuteur is the mother)
          if (editNinMere && window.currentUserNIN) {
            editNinMere.value = window.currentUserNIN;
            editNinMere.setAttribute('readonly', true);
            editNinMere.readOnly = true;
            editNinMere.style.backgroundColor = '#f8f9fa';
          }
          if (editNssMere && window.currentUserNSS) {
            editNssMere.value = window.currentUserNSS;
            editNssMere.setAttribute('readonly', true);
            editNssMere.readOnly = true;
            editNssMere.style.backgroundColor = '#f8f9fa';
          }
        }
        
        // For role 1 (Father), if mother is selected, show and fill mother NIN/NSS
        if ((relationTuteur === '1' || relationTuteur === 1) && eleve.mother_id && editMotherSelect && editMotherSelect.value) {
          const selectedMotherId = editMotherSelect.value;
          if (window.mothersData && window.mothersData.length > 0) {
            const selectedMother = window.mothersData.find(m => m.id == selectedMotherId);
            if (selectedMother) {
              const editNinMereWrapper = document.getElementById('edit_ninMereWrapper');
              const editNssMereWrapper = document.getElementById('edit_nssMereWrapper');
              const editNinMere = document.getElementById('edit_ninMere');
              const editNssMere = document.getElementById('edit_nssMere');
              
              if (editNinMereWrapper) editNinMereWrapper.style.display = 'block';
              if (editNssMereWrapper) editNssMereWrapper.style.display = 'block';
              
              if (editNinMere && selectedMother.nin) {
                editNinMere.value = selectedMother.nin;
                editNinMere.setAttribute('readonly', true);
                editNinMere.readOnly = true;
                editNinMere.style.backgroundColor = '#f8f9fa';
              }
              if (editNssMere && selectedMother.nss) {
                editNssMere.value = selectedMother.nss;
                editNssMere.setAttribute('readonly', true);
                editNssMere.readOnly = true;
                editNssMere.style.backgroundColor = '#f8f9fa';
              }
            }
          }
        }
        
        // For role 3 (Guardian), show and fill guardian (tuteur) NIN/NSS
        if (relationTuteur === '3' || relationTuteur === 3) {
          const editNinGuardianWrapper = document.getElementById('edit_ninGuardianWrapper');
          const editNssGuardianWrapper = document.getElementById('edit_nssGuardianWrapper');
          const editNinGuardian = document.getElementById('edit_ninGuardian');
          const editNssGuardian = document.getElementById('edit_nssGuardian');
          
          if (editNinGuardianWrapper) editNinGuardianWrapper.style.display = 'block';
          if (editNssGuardianWrapper) editNssGuardianWrapper.style.display = 'block';
          
          if (editNinGuardian && window.currentUserNIN) {
            editNinGuardian.value = window.currentUserNIN;
            editNinGuardian.setAttribute('readonly', true);
            editNinGuardian.readOnly = true;
            editNinGuardian.style.backgroundColor = '#f8f9fa';
          }
          if (editNssGuardian && window.currentUserNSS) {
            editNssGuardian.value = window.currentUserNSS;
            editNssGuardian.setAttribute('readonly', true);
            editNssGuardian.readOnly = true;
            editNssGuardian.style.backgroundColor = '#f8f9fa';
          }
        }
        
        // Handicap + orphelin radios
        const editHandicapYes = document.getElementById('edit_handicapYes');
        const editHandicapNo = document.getElementById('edit_handicapNo');
        const editHandicapNature = document.getElementById('edit_handicap_nature');
        const editHandicapPercentage = document.getElementById('edit_handicap_percentage');
        const editHandicapNatureWrapper = document.getElementById('edit_handicapNatureWrapper');
        const editHandicapPercentageWrapper = document.getElementById('edit_handicapPercentageWrapper');

        const isHandicap = eleve.handicap === '1' || eleve.handicap === 1;
        if (editHandicapYes && editHandicapNo) {
          editHandicapYes.checked = isHandicap;
          editHandicapNo.checked = !isHandicap;
        }
        if (editHandicapNature) editHandicapNature.value = eleve.handicap_nature || '';
        if (editHandicapPercentage) editHandicapPercentage.value = eleve.handicap_percentage || '';

        function toggleEditHandicapDetails(show) {
          [editHandicapNatureWrapper, editHandicapPercentageWrapper].forEach(el => {
            if (el) el.classList.toggle('d-none', !show);
          });
          if (editHandicapNature) {
            editHandicapNature.required = !!show;
            if (!show) editHandicapNature.value = '';
          }
          if (editHandicapPercentage) {
            editHandicapPercentage.required = !!show;
            if (!show) editHandicapPercentage.value = '';
          }
        }
        toggleEditHandicapDetails(isHandicap);
        if (editHandicapYes && editHandicapNo) {
          editHandicapYes.addEventListener('change', () => toggleEditHandicapDetails(true));
          editHandicapNo.addEventListener('change', () => toggleEditHandicapDetails(false));
        }

        
        // Radio buttons
        if (eleve.sexe) {
          if (eleve.sexe === 'ذكر') document.getElementById('edit_male').checked = true;
          else if (eleve.sexe === 'أنثى') document.getElementById('edit_female').checked = true;
        }
        
        // Set classe
        if (eleve.classe_scol && eleve.niv_scol) {
          const classes = {
            'ابتدائي': ['السنة الأولى ابتدائي', 'السنة الثانية ابتدائي', 'السنة الثالثة ابتدائي', 'السنة الرابعة ابتدائي', 'السنة الخامسة ابتدائي'],
            'متوسط': ['السنة الأولى متوسط', 'السنة الثانية متوسط', 'السنة الثالثة متوسط', 'السنة الرابعة متوسط'],
            'ثانوي': ['السنة الأولى ثانوي', 'السنة الثانية ثانوي', 'السنة الثالثة ثانوي']
          };
          
          if (classes[eleve.niv_scol]) {
            editClasseSelect.innerHTML = '<option value="">اختر...</option>';
            classes[eleve.niv_scol].forEach(cls => {
              editClasseSelect.innerHTML += `<option value="${cls}" ${cls === eleve.classe_scol ? 'selected' : ''}>${cls}</option>`;
            });
          }
        }
        
      } catch (error) {
        // Error loading student data
        Swal.fire('Error', 'Failed to load student data', 'error');
        const modal = bootstrap.Modal.getInstance(editChildModal);
        if (modal) modal.hide();
      }
    };

    // Edit modal events
    editChildModal.addEventListener('show.bs.modal', async () => {
      customOverlay.style.display = 'block';
      await loadMothers();
      // Update edit form based on tuteur role
      updateFormForEditGuardianRole();
      
      // Add event listener for edit mother select (for role 1)
      const editMotherSelect = document.getElementById('editMotherSelect');
      if (editMotherSelect) {
        editMotherSelect.removeEventListener('change', editMotherSelect._changeHandler);
        editMotherSelect._changeHandler = function() {
          const selectedMotherId = this.value;
          const relationTuteur = window.currentUserRelationTuteur;
          
          // For role 1 (Father), show and fill mother NIN/NSS when mother is selected
          if ((relationTuteur === '1' || relationTuteur === 1) && selectedMotherId && window.mothersData && window.mothersData.length > 0) {
            const selectedMother = window.mothersData.find(m => m.id == selectedMotherId);
            if (selectedMother) {
              // Show mother NIN/NSS fields
              const editNinMereWrapper = document.getElementById('edit_ninMereWrapper');
              const editNssMereWrapper = document.getElementById('edit_nssMereWrapper');
              const editNinMere = document.getElementById('edit_ninMere');
              const editNssMere = document.getElementById('edit_nssMere');
              
              if (editNinMereWrapper) editNinMereWrapper.style.display = 'block';
              if (editNssMereWrapper) editNssMereWrapper.style.display = 'block';
              
              // Fill mother NIN and NSS
              if (editNinMere && selectedMother.nin) {
                editNinMere.value = selectedMother.nin;
              }
              if (editNssMere && selectedMother.nss) {
                editNssMere.value = selectedMother.nss;
              }
            }
          } else {
            // Hide mother NIN/NSS if no mother selected or not role 1
            const editNinMereWrapper = document.getElementById('edit_ninMereWrapper');
            const editNssMereWrapper = document.getElementById('edit_nssMereWrapper');
            if (editNinMereWrapper) editNinMereWrapper.style.display = 'none';
            if (editNssMereWrapper) editNssMereWrapper.style.display = 'none';
          }
        };
        editMotherSelect.addEventListener('change', editMotherSelect._changeHandler);
      }
    });

    editChildModal.addEventListener('hidden.bs.modal', () => {
      customOverlay.style.display = 'none';
      editForm.reset();
      editStep1.classList.remove('d-none');
      editStep2.classList.add('d-none');
    });

    // Edit form step navigation
    editNextStep.addEventListener('click', () => {
      if (editStep1.querySelectorAll('select[required]').length > 0) {
        let isValid = true;
        editStep1.querySelectorAll('select[required]').forEach(sel => {
          if (!sel.value) {
            sel.classList.add('is-invalid');
            isValid = false;
          } else {
            sel.classList.remove('is-invalid');
          }
        });
        if (!isValid) return;
      }
      editStep1.classList.add('d-none');
      editStep2.classList.remove('d-none');
    });

    editPrevStep.addEventListener('click', () => {
      editStep2.classList.add('d-none');
      editStep1.classList.remove('d-none');
    });

    editReloadStep1.addEventListener('click', () => {
      editStep1.querySelectorAll('select').forEach(sel => {
        sel.value = '';
        sel.classList.remove('is-invalid');
      });
      editCommuneSelect.innerHTML = '<option value="">اختر الولاية أولا...</option>';
      editCommuneSelect.disabled = true;
      document.getElementById('editEcoleSelect').innerHTML = '<option value="">اختر كل المعايير أولا...</option>';
      document.getElementById('editEcoleSelect').disabled = true;
    });

    // Edit form submission
    editForm.addEventListener('submit', async (e) => {
      e.preventDefault();

      // Reset errors
      editForm.querySelectorAll('.error-msg').forEach(e => e.remove());
      editForm.querySelectorAll('.is-invalid').forEach(e => e.classList.remove('is-invalid'));

      let firstError = null;
      let hasError = false;

      // Validation (same as add form)
      const arabicInputs = ['prenom','nom','prenom_pere','nom_pere'];
      arabicInputs.forEach(name => {
        const el = editForm.querySelector(`[name="${name}"]`);
        if (el && el.value.trim() && !/^[ء-ي\s]+$/.test(el.value)) {
          showError(el, 'يجب أن يكون النص بالعربية فقط');
          if (!firstError) firstError = el;
          hasError = true;
        }
      });

      const numericChecks = [
        // Note: nin_pere and nss_pere fields removed - using father relationship instead
      ];

      numericChecks.forEach(field => {
        const el = editForm.querySelector(`[name="${field.name}"]`);
        if (el && el.value && el.value.length !== field.len) {
          showError(el, `${field.label} يجب أن يحتوي على ${field.len} رقمًا`);
          if (!firstError) firstError = el;
          hasError = true;
        }
      });

      if (hasError) {
        if (firstError) firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
        return;
      }

      // Submit update
      const formData = new FormData(editForm);
      const num_scolaire = document.getElementById('edit_num_scolaire').value;

      try {
        // Use API endpoint with apiFetch helper which handles authentication automatically
        const response = await apiFetch(`/api/eleves/${num_scolaire}`, {
          method: 'PUT',
          body: formData
        });

        if (!response.ok) {
          const errorData = await response.json();
          throw new Error(errorData.message || 'خطأ أثناء التحديث');
        }

        Swal.fire({
          title: 'تم التحديث بنجاح!',
          text: 'تم حفظ التعديلات بنجاح.',
          icon: 'success',
          confirmButtonText: 'حسنًا',
          customClass: {
            confirmButton: 'swal-confirm-btn'
          },
          buttonsStyling: false
        }).then(() => {
          const closeBtn = document.querySelector('#editChildModal .btn-close');
          if (closeBtn) closeBtn.click();
          loadChildrenList();
        });

      } catch (err) {
        Swal.fire('حدث خطأ!', err.message, 'error');
      }
    });

    document.addEventListener('click', async (e) => {
      const btn = e.target.closest('.btn-delete');
      if (!btn) return;

      const id = btn.dataset.id;

      // 🧾 Confirmation popup
      const confirm = await Swal.fire({
        title: 'تأكيد الحذف',
        text: 'هل أنت متأكد أنك تريد حذف هذا التلميذ نهائيًا؟',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'نعم، احذف',
        cancelButtonText: 'إلغاء',
        reverseButtons: true,
        customClass: {
          popup: 'delete-popup',
          title: 'delete-title',
          confirmButton: 'swal-confirm-btn',
          cancelButton: 'swal-cancel-btn'
        },
        buttonsStyling: false
      });

      if (!confirm.isConfirmed) return;

      try {
        const response = await fetch(`/api/eleves/${id}`, {
          method: 'DELETE',
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
          }
        });

        if (!response.ok) throw new Error('فشل الحذف');

        await Swal.fire({
          title: 'تم الحذف بنجاح!',
          icon: 'success',
          confirmButtonText: 'حسنًا',
          customClass: {
            confirmButton: 'swal-confirm-btn'
          },
          buttonsStyling: false
        });

        loadChildrenList(); // refresh table smoothly
      } catch (err) {
        Swal.fire('حدث خطأ!', err.message, 'error');
      }
    });

  });

  // Show comments for a student
  async function showComments(num_scolaire, studentName) {
    Swal.fire({
      title: `تعليقات: ${studentName}`,
      html: '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">جارٍ التحميل...</span></div>',
      allowOutsideClick: false,
      allowEscapeKey: false,
      showConfirmButton: false,
      didOpen: () => {
        Swal.showLoading();
      }
    });

    try {
      const response = await fetch(`/eleves/${num_scolaire}/comments`, {
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
          'Accept': 'application/json'
        }
      });

      const data = await response.json();

      if (!data.success) {
        Swal.fire({
          icon: 'error',
          title: 'خطأ',
          text: data.message || 'فشل تحميل التعليقات',
          confirmButtonText: 'حسنًا'
        });
        return;
      }

      const comments = data.comments || [];

      let commentsHTML = '';
      if (comments.length > 0) {
        commentsHTML = '<div style="max-height: 500px; overflow-y: auto; padding: 1rem; background: #f8fafc; border-radius: 12px;">';
        comments.forEach(comment => {
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
          const userName = (comment.user && comment.user.nom_user) 
            ? `${comment.user.nom_user} ${comment.user.prenom_user || ''}`.trim()
            : 'مستخدم';
          
          commentsHTML += `
            <div style="background: white; padding: 1.25rem; margin-bottom: 1rem; border-radius: 12px; border-right: 4px solid #2563eb; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
              <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem; padding-bottom: 0.75rem; border-bottom: 1px solid #e5e7eb;">
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                  <div style="width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(135deg, #2563eb, #1d4ed8); display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 1rem;">
                    ${userName.charAt(0)}
                  </div>
                  <div>
                    <strong style="color: #0f033a; font-size: 1rem; display: block;">${userName}</strong>
                    <span style="color: #6b7280; font-size: 0.85rem;">${date}</span>
                  </div>
                </div>
              </div>
              <p style="margin: 0; color: #374151; line-height: 1.8; font-size: 1rem; white-space: pre-wrap;">${comment.text}</p>
            </div>
          `;
        });
        commentsHTML += '</div>';
      } else {
        commentsHTML = `
          <div style="text-align: center; padding: 3rem; color: #6b7280; background: linear-gradient(135deg, #f8fafc, #e5e7eb); border-radius: 12px; border: 2px dashed #cbd5e1;">
            <i class="fa-solid fa-comment-slash" style="font-size: 3rem; color: #9ca3af; margin-bottom: 1rem; display: block;"></i>
            <div style="font-size: 1.1rem; font-weight: 500;">لا توجد تعليقات حتى الآن</div>
            <div style="font-size: 0.9rem; margin-top: 0.5rem; color: #9ca3af;">سيتم عرض التعليقات هنا عند إضافتها من قبل موظفي البلدية</div>
          </div>
        `;
      }

      Swal.fire({
        title: `تعليقات: ${studentName}`,
        html: commentsHTML,
        width: '700px',
        showCloseButton: true,
        showConfirmButton: true,
        confirmButtonText: 'إغلاق',
        confirmButtonColor: '#2563eb',
        customClass: {
          popup: 'comments-modal',
          htmlContainer: 'comments-content'
        },
        didOpen: () => {
          const content = document.querySelector('.comments-content');
          if (content) {
            content.style.maxHeight = '500px';
            content.style.overflowY = 'auto';
          }
        }
      });

    } catch (error) {
      // Error loading comments
      Swal.fire({
        icon: 'error',
        title: 'خطأ',
        text: 'حدث خطأ أثناء تحميل التعليقات',
        confirmButtonText: 'حسنًا'
      });
    }
  }

</script>

@endpush

<script>
document.addEventListener('DOMContentLoaded', function() {
    const settingsCard = document.querySelector('.action-card i.fa-gear').closest('.action-card');
    const modal = document.getElementById('settingsModal');
    const cancelBtn = document.getElementById('cancelSettingsBtn');
    const confirmBtn = document.getElementById('confirmChangeBtn');
    const form = document.getElementById('changePasswordForm');

    const currentPwd = form.current_password;
    const newPwd = form.new_password;
    const confirmPwd = form.confirm_password;

    const pwdRegex = /^(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&.,;:+=_\-#^])[A-Za-z\d@$!%*?&.,;:+=_\-#^]{8,}$/;

    // 🔹 Vérification en temps réel du mot de passe
    newPwd.addEventListener('input', () => {
        const errorMsg = newPwd.parentElement.parentElement.querySelector('.error-msg');
        if (newPwd.value === '') {
            errorMsg.textContent = '';
            newPwd.classList.remove('is-valid', 'is-invalid');
            return;
        }
        if (!pwdRegex.test(newPwd.value)) {
            errorMsg.textContent = 'يجب أن تحتوي كلمة المرور على 8 أحرف على الأقل، حرف كبير، رقم، ورمز خاص.';
            errorMsg.style.color = '#d9534f';
            newPwd.classList.add('is-invalid');
            newPwd.classList.remove('is-valid');
        } else {
            errorMsg.textContent = 'كلمة المرور قوية ✅';
            errorMsg.style.color = '#28a745';
            newPwd.classList.add('is-valid');
            newPwd.classList.remove('is-invalid');
        }
        validatePasswordMatch();
    });

    // 🔹 Vérification correspondance en temps réel
    confirmPwd.addEventListener('input', validatePasswordMatch);

    function validatePasswordMatch() {
        const errorMsg = confirmPwd.parentElement.parentElement.querySelector('.error-msg');
        if (confirmPwd.value === '') {
            errorMsg.textContent = '';
            confirmPwd.classList.remove('is-valid', 'is-invalid');
            return;
        }

        if (confirmPwd.value !== newPwd.value) {
            errorMsg.textContent = 'كلمتا المرور غير متطابقتين.';
            errorMsg.style.color = '#d9534f';
            confirmPwd.classList.add('is-invalid');
            confirmPwd.classList.remove('is-valid');
        } else {
            errorMsg.textContent = 'كلمتا المرور متطابقتان ✅';
            errorMsg.style.color = '#28a745';
            confirmPwd.classList.add('is-valid');
            confirmPwd.classList.remove('is-invalid');
        }
    }

    // 🔹 Ouvrir le modal
    settingsCard.addEventListener('click', () => {
        modal.style.display = 'flex';
        setTimeout(() => modal.classList.add('show'), 10);
    });

    // 🔹 Fermer le modal
    cancelBtn.addEventListener('click', () => {
        modal.classList.remove('show');
        setTimeout(() => { modal.style.display = 'none'; }, 200);
    });

    // 🔹 Bouton "Confirmer le changement"
    confirmBtn.addEventListener('click', async function(e) {
        e.preventDefault();

        // Validation finale avant envoi
        if (!pwdRegex.test(newPwd.value)) {
            Swal.fire({
                icon: 'error',
                title: 'كلمة المرور الجديدة غير صالحة',
                text: 'يجب أن تحتوي على 8 أحرف على الأقل، حرف كبير، رقم، ورمز خاص.'
            });
            return;
        }

        if (newPwd.value !== confirmPwd.value) {
            Swal.fire({
                icon: 'error',
                title: 'كلمتا المرور غير متطابقتين',
            });
            return;
        }

        // Confirmation avant l'envoi
        const confirm = await Swal.fire({
            title: 'هل أنت متأكد من تغيير كلمة المرور؟',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'نعم، تأكيد',
            cancelButtonText: 'إلغاء',
            reverseButtons: true,
            customClass: {
                popup: 'logout-popup',
                confirmButton: 'swal-confirm-btn',
                cancelButton: 'swal-cancel-btn'
            },
            buttonsStyling: false
        });

        if (!confirm.isConfirmed) return;

        // 🔹 Envoi au backend Laravel
        try {
            const response = await fetch("{{ route('password.change') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    current_password: currentPwd.value,
                    new_password: newPwd.value,
                    new_password_confirmation: confirmPwd.value
                })
            });

            const data = await response.json();

            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: data.message || 'تم تغيير كلمة المرور بنجاح ✅',
                    timer: 1500,
                    showConfirmButton: false
                });
                modal.classList.remove('show');
                setTimeout(() => { modal.style.display = 'none'; }, 200);
                form.reset();
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ',
                    text: data.message || 'كلمة المرور الحالية غير صحيحة.'
                });
            }
        } catch (err) {
            // Error occurred
            Swal.fire({
                icon: 'error',
                title: 'خطأ في الاتصال',
                text: 'يرجى المحاولة لاحقاً.'
            });
        }
    });
});

// 🔹 Fonction affichage/masquage du mot de passe
function togglePassword(icon) {
    const input = icon.nextElementSibling;
    if (input.type === "password") {
        input.type = "text";
        icon.classList.remove("fa-eye");
        icon.classList.add("fa-eye-slash");
    } else {
        input.type = "password";
        icon.classList.remove("fa-eye-slash");
        icon.classList.add("fa-eye");
    }
}
</script>

@endsection
