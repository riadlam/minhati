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

        <!-- Mothers Info (Role 1 and 3 only) -->
        <div class="action-card" id="mothersInfoCard" onclick="window.location.href='{{ route('tuteur.mother') }}'">
            <i class="fa-solid fa-venus"></i>
            <h4 id="mothersInfoCardTitle">معلومات الأمهات</h4>
            <p id="mothersInfoCardDesc">إدارة معلومات الأمهات</p>
        </div>

        <!-- Father Info (Role 2 and 3 only) -->
        <div class="action-card" id="fatherInfoCard" onclick="window.location.href='{{ route('tuteur.father') }}'">
            <i class="fa-solid fa-mars"></i>
            <h4>معلومات الأب</h4>
            <p>عرض وتحديث معلومات الأب</p>
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

<!-- Mothers Info Modal -->
<div class="modal fade" id="mothersInfoModal" tabindex="-1" aria-labelledby="mothersInfoModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg rounded-4">
      
      <!-- Header -->
      <div class="modal-header" style="background-color:#0f033a; color:white;">
        <h5 class="modal-title" id="mothersInfoModalLabel">
          <i class="fa-solid fa-venus me-2 text-warning"></i><span id="mothersInfoModalTitle">معلومات الأمهات</span>
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="إغلاق"></button>
      </div>

      <!-- Content -->
      <div class="modal-body">
        <div class="container-fluid">
          <!-- Mothers List View -->
          <div id="mothersListView" dir="rtl" style="text-align: right;">
            <!-- Add New Mother Button (Hidden for role 3) -->
            <div class="mb-4" id="addMotherBtnWrapper">
              <button type="button" class="btn px-4" id="addMotherBtn" style="background-color:#fdae4b; color:#0f033a; font-weight:bold;">
                <i class="fa-solid fa-plus me-2"></i>إضافة أم جديدة
              </button>
            </div>

            <!-- Mothers List -->
            <div id="mothersListContainer">
              <div class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                  <span class="visually-hidden">جاري التحميل...</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Add/Edit Mother Form (Hidden by default) -->
          <div id="motherFormContainer" class="d-none" dir="rtl" style="text-align: right;">
            <h5 class="fw-bold mb-3 text-center" style="color:#0f033a;" id="motherFormTitle">إضافة أم جديدة</h5>
            
            <form id="motherForm">
              <input type="hidden" id="motherFormId" name="id">
              
              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label fw-bold required">الرقم الوطني للأم (NIN) *</label>
                  <input type="text" id="mother_nin" name="nin" class="form-control" maxlength="18" required>
                  <div class="form-text">يجب أن يكون 18 رقمًا</div>
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-bold">رقم الضمان الاجتماعي للأم (NSS)</label>
                  <input type="text" id="mother_nss" name="nss" class="form-control" maxlength="12">
                  <div class="form-text">يجب أن يكون 12 رقمًا</div>
                </div>
              </div>

              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label fw-bold required">لقب الأم بالعربية *</label>
                  <input type="text" id="mother_nom_ar" name="nom_ar" class="form-control" required>
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-bold required">اسم الأم بالعربية *</label>
                  <input type="text" id="mother_prenom_ar" name="prenom_ar" class="form-control" required>
                </div>
              </div>

              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label fw-bold">لقب الأم بالفرنسية</label>
                  <input type="text" id="mother_nom_fr" name="nom_fr" class="form-control">
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-bold">اسم الأم بالفرنسية</label>
                  <input type="text" id="mother_prenom_fr" name="prenom_fr" class="form-control">
                </div>
              </div>

              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label fw-bold">الفئة الاجتماعية</label>
                  <select id="mother_categorie_sociale" name="categorie_sociale" class="form-select">
                    <option value="">اختر الفئة الاجتماعية</option>
                    <option value="عديم الدخل">عديم الدخل</option>
                    <option value="الدخل الشهري أقل أو يساوي مبلغ الأجر الوطني الأدنى المضمون">الدخل الشهري أقل أو يساوي مبلغ الأجر الوطني الأدنى المضمون</option>
                  </select>
                </div>
                <div class="col-md-6">
                  <div id="mother_montant_wrapper" style="display: none;">
                    <label class="form-label fw-bold">مبلغ الدخل الشهري</label>
                    <input type="number" id="mother_montant_s" name="montant_s" class="form-control" step="0.01" min="0">
                  </div>
                </div>
              </div>

              <!-- Buttons -->
              <div class="d-flex justify-content-between align-items-center mt-4 flex-row-reverse">
                <button type="submit" class="btn px-4" id="saveMotherBtn" style="background-color:#fdae4b; color:#0f033a; font-weight:bold;">
                  حفظ <i class="fa-solid fa-check ms-1"></i>
                </button>
                <button type="button" class="btn btn-outline-danger px-4" id="cancelMotherFormBtn">
                  <i class="fa-solid fa-times me-1"></i> إلغاء
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Father Info Modal -->
<div class="modal fade" id="fatherInfoModal" tabindex="-1" aria-labelledby="fatherInfoModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg rounded-4">
      
      <!-- Header -->
      <div class="modal-header" style="background-color:#0f033a; color:white;">
        <h5 class="modal-title" id="fatherInfoModalLabel">
          <i class="fa-solid fa-mars me-2 text-warning"></i>معلومات الأب
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="إغلاق"></button>
      </div>

      <!-- Content -->
      <div class="modal-body">
        <div class="container-fluid">
          <!-- Father Info View -->
          <div id="fatherInfoView" dir="rtl" style="text-align: right;">
            <div id="fatherInfoContainer">
              <div class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                  <span class="visually-hidden">جاري التحميل...</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Edit Father Form (Hidden by default) -->
          <div id="fatherFormContainer" class="d-none" dir="rtl" style="text-align: right;">
            <h5 class="fw-bold mb-3 text-center" style="color:#0f033a;">تعديل معلومات الأب</h5>
            
            <form id="fatherForm">
              <input type="hidden" id="fatherFormId" name="id">
              
              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label fw-bold required">الرقم الوطني للأب (NIN) *</label>
                  <input type="text" id="father_nin" name="nin" class="form-control" maxlength="18" required>
                  <div class="form-text">يجب أن يكون 18 رقمًا</div>
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-bold">رقم الضمان الاجتماعي للأب (NSS)</label>
                  <input type="text" id="father_nss" name="nss" class="form-control" maxlength="12">
                  <div class="form-text">يجب أن يكون 12 رقمًا</div>
                </div>
              </div>

              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label fw-bold required">لقب الأب بالعربية *</label>
                  <input type="text" id="father_nom_ar" name="nom_ar" class="form-control" required>
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-bold required">اسم الأب بالعربية *</label>
                  <input type="text" id="father_prenom_ar" name="prenom_ar" class="form-control" required>
                </div>
              </div>

              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label fw-bold">لقب الأب بالفرنسية</label>
                  <input type="text" id="father_nom_fr" name="nom_fr" class="form-control">
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-bold">اسم الأب بالفرنسية</label>
                  <input type="text" id="father_prenom_fr" name="prenom_fr" class="form-control">
                </div>
              </div>

              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label fw-bold">الفئة الاجتماعية</label>
                  <select id="father_categorie_sociale" name="categorie_sociale" class="form-select">
                    <option value="">اختر الفئة الاجتماعية</option>
                    <option value="عديم الدخل">عديم الدخل</option>
                    <option value="الدخل الشهري أقل أو يساوي مبلغ الأجر الوطني الأدنى المضمون">الدخل الشهري أقل أو يساوي مبلغ الأجر الوطني الأدنى المضمون</option>
                  </select>
                </div>
                <div class="col-md-6">
                  <div id="father_montant_wrapper" style="display: none;">
                    <label class="form-label fw-bold">مبلغ الدخل الشهري</label>
                    <input type="number" id="father_montant_s" name="montant_s" class="form-control" step="0.01" min="0">
                  </div>
                </div>
              </div>

              <!-- Buttons -->
              <div class="d-flex justify-content-between align-items-center mt-4 flex-row-reverse">
                <button type="submit" class="btn px-4" id="saveFatherBtn" style="background-color:#fdae4b; color:#0f033a; font-weight:bold;">
                  حفظ <i class="fa-solid fa-check ms-1"></i>
                </button>
                <button type="button" class="btn btn-outline-danger px-4" id="cancelFatherFormBtn">
                  <i class="fa-solid fa-times me-1"></i> إلغاء
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
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
<!-- View Child Modal (Read-Only, Same Structure as Edit/Add) -->
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

            <!-- === STEP 1: School Info (Read-Only) === -->
            <div id="viewStep1" class="step-content" dir="rtl" style="text-align: right;">
                <h5 class="fw-bold mb-3 text-center" style="color:#0f033a;">معلومات المؤسسة التعليمية</h5>
                <div class="row g-3">
                    <div class="col-md-6">
                      <label class="form-label fw-bold">مؤسسة التربية والتعليم</label>
                      <input type="text" id="view_type_ecole" class="form-control" readonly style="background-color: #f8f9fa;">
                    </div>
                    <div class="col-md-6">
                      <label class="form-label fw-bold">المستوى الدراسي</label>
                      <input type="text" id="view_niveau" class="form-control" readonly style="background-color: #f8f9fa;">
                    </div>
                    <div class="col-md-6">
                      <label class="form-label fw-bold">الولاية</label>
                      <input type="text" id="view_wilaya" class="form-control" readonly style="background-color: #f8f9fa;">
                    </div>
                    <div class="col-md-6">
                      <label class="form-label fw-bold">البلدية</label>
                      <input type="text" id="view_commune" class="form-control" readonly style="background-color: #f8f9fa;">
                    </div>
                    <div class="col-md-12">
                      <label class="form-label fw-bold">المؤسسة التعليمية</label>
                      <input type="text" id="view_etablissement" class="form-control" readonly style="background-color: #f8f9fa;">
                    </div>
                </div>
                <div class="d-flex justify-content-end mt-4">
                  <button type="button" class="btn px-4" id="viewNextStep" style="background-color:#fdae4b; color:#0f033a; font-weight:bold;">
                    التالي <i class="fa-solid fa-arrow-left ms-1"></i>
                  </button>
                </div>
                    </div>

            <!-- === STEP 2: Student Info (Read-Only) === -->
            <div id="viewStep2" class="step-content d-none" dir="rtl" style="text-align: right;">
                <h5 class="fw-bold mb-3 text-center" style="color:#0f033a;">معلومات التلميذ</h5>

                <div class="row g-3">
                    <!-- الأم/الزوجة و صفة طالب المنحة - Top Row -->
                    <div class="col-md-6" id="view_motherSelectWrapper">
                      <label class="form-label fw-bold" id="view_motherSelectLabel">الأم/الزوجة</label>
                      <input type="text" id="view_motherName" class="form-control" readonly style="background-color: #f8f9fa;">
                    </div>

                    <!-- Father Info (for Guardian role only) -->
                    <div class="col-md-6" id="view_fatherInfoWrapper" style="display: none;">
                      <label class="form-label fw-bold">الأب</label>
                      <input type="text" id="view_fatherNameDisplay" class="form-control" readonly style="background-color: #f8f9fa;">
                    </div>

                    <div class="col-md-6">
                      <label class="form-label fw-bold">صفة طالب المنحة</label>
                      <input type="text" id="view_relation_tuteur" class="form-control" readonly style="background-color: #f8f9fa;">
                    </div>

                    <!-- 🆔 الرقم التعريفي المدرسي -->
                    <div class="col-md-6">
                      <label class="form-label fw-bold">الرقم التعريفي المدرسي</label>
                      <input type="text" id="view_num_scolaire" class="form-control" readonly style="background-color: #f8f9fa;">
                    </div>

                    <!-- الاسم واللقب -->
                    <div class="col-md-6">
                      <label class="form-label fw-bold">اللقب بالعربية</label>
                      <input type="text" id="view_nom" class="form-control" dir="rtl" readonly style="background-color: #f8f9fa;">
                    </div>
                    <div class="col-md-6">
                      <label class="form-label fw-bold">الاسم بالعربية</label>
                      <input type="text" id="view_prenom" class="form-control" dir="rtl" readonly style="background-color: #f8f9fa;">
                    </div>

                    <!-- الأب -->
                    <div class="col-md-6" id="view_nomPereWrapper">
                      <label class="form-label fw-bold" id="view_nomPereLabel">لقب الأب بالعربية</label>
                      <input type="text" id="view_nom_pere" class="form-control" dir="rtl" readonly style="background-color: #f8f9fa;">
                    </div>
                    <div class="col-md-6" id="view_prenomPereWrapper">
                      <label class="form-label fw-bold" id="view_prenomPereLabel">اسم الأب بالعربية</label>
                      <input type="text" id="view_prenom_pere" class="form-control" dir="rtl" readonly style="background-color: #f8f9fa;">
                    </div>

                    <!-- الميلاد -->
                    <div class="col-md-6">
                      <label class="form-label fw-bold">تاريخ الميلاد</label>
                      <input type="text" id="view_date_naiss" class="form-control" readonly style="background-color: #f8f9fa;">
                    </div>

                    <div class="col-md-3">
                      <label class="form-label fw-bold">ولاية الميلاد</label>
                      <input type="text" id="view_wilaya_naiss" class="form-control" readonly style="background-color: #f8f9fa;">
                    </div>
                    <div class="col-md-3">
                      <label class="form-label fw-bold">بلدية الميلاد</label>
                      <input type="text" id="view_commune_naiss" class="form-control" readonly style="background-color: #f8f9fa;">
                    </div>

                    <!-- القسم والجنس -->
                    <div class="col-md-6">
                      <label class="form-label fw-bold">القسم</label>
                      <input type="text" id="view_classe_scol" class="form-control" readonly style="background-color: #f8f9fa;">
                    </div>

                    <div class="col-md-6">
                      <label class="form-label fw-bold">الجنس</label>
                      <input type="text" id="view_sexe" class="form-control" readonly style="background-color: #f8f9fa;">
                    </div>

                    <!-- الحالة الاجتماعية -->
                    <div class="col-md-12" dir="rtl">
                      <label class="form-label fw-bold mb-3 d-block">فئة ذوي الاحتياجات الخاصة؟</label>
                      <div class="d-flex align-items-center gap-4">
                        <div class="form-check">
                          <input class="form-check-input" type="radio" name="view_handicap" value="1" id="view_handicapYes" disabled>
                          <label class="form-check-label" for="view_handicapYes">نعم</label>
                    </div>
                        <div class="form-check">
                          <input class="form-check-input" type="radio" name="view_handicap" value="0" id="view_handicapNo" disabled checked>
                          <label class="form-check-label" for="view_handicapNo">لا</label>
                    </div>
                    </div>
                    </div>

                    <!-- تفاصيل الإعاقة -->
                    <div class="col-md-6" id="view_handicapNatureWrapper" style="display: none;">
                      <label class="form-label fw-bold">طبيعة الإعاقة</label>
                      <input type="text" id="view_handicap_nature" class="form-control" readonly style="background-color: #f8f9fa;">
                    </div>
                    <div class="col-md-6" id="view_handicapPercentageWrapper" style="display: none;">
                      <label class="form-label fw-bold">نسبة الإعاقة (%)</label>
                      <input type="text" id="view_handicap_percentage" class="form-control" readonly style="background-color: #f8f9fa;">
                    </div>

                    <!-- NIN + NSS for Father -->
                    <div class="col-md-6" id="view_ninPereWrapper" style="display: none;">
                      <label class="form-label fw-bold">الرقم الوطني للأب (NIN)</label>
                      <input type="text" id="view_ninPere" class="form-control" readonly style="background-color: #f8f9fa;">
                    </div>
                    <div class="col-md-6" id="view_nssPereWrapper" style="display: none;">
                      <label class="form-label fw-bold">رقم الضمان الاجتماعي للأب (NSS)</label>
                      <input type="text" id="view_nssPere" class="form-control" readonly style="background-color: #f8f9fa;">
                    </div>

                    <!-- NIN + NSS for Mother -->
                    <div class="col-md-6" id="view_ninMereWrapper" style="display: none;">
                      <label class="form-label fw-bold">الرقم الوطني للأم (NIN)</label>
                      <input type="text" id="view_ninMere" class="form-control" readonly style="background-color: #f8f9fa;">
                    </div>
                    <div class="col-md-6" id="view_nssMereWrapper" style="display: none;">
                      <label class="form-label fw-bold">رقم الضمان الاجتماعي للأم (NSS)</label>
                      <input type="text" id="view_nssMere" class="form-control" readonly style="background-color: #f8f9fa;">
                    </div>

                    <!-- NIN + NSS for Guardian -->
                    <div class="col-md-6" id="view_ninGuardianWrapper" style="display: none;">
                      <label class="form-label fw-bold">الرقم الوطني للوصي (NIN)</label>
                      <input type="text" id="view_ninGuardian" class="form-control" readonly style="background-color: #f8f9fa;">
                    </div>
                    <div class="col-md-6" id="view_nssGuardianWrapper" style="display: none;">
                      <label class="form-label fw-bold">رقم الضمان الاجتماعي للوصي (NSS)</label>
                      <input type="text" id="view_nssGuardian" class="form-control" readonly style="background-color: #f8f9fa;">
                    </div>
                </div>

                <!-- Navigation Buttons -->
                <div class="d-flex justify-content-between align-items-center mt-4 flex-row-reverse">
                  <button type="button" class="btn px-5" data-bs-dismiss="modal" style="background-color:#0f033a; color:white; font-weight:bold;">
                    إغلاق <i class="fa-solid fa-times ms-1"></i>
                  </button>
                  <button type="button" class="btn btn-outline-secondary px-4" id="viewPrevStep">
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

                    <!-- Father Select (for Mother role) -->
                    <div class="col-md-6" id="edit_fatherSelectWrapper" style="display: none;">
                      <label class="form-label fw-bold" id="edit_fatherSelectLabel">الأب</label>
                      <select name="father_id" id="editFatherSelect" class="form-select">
                        <option value="">اختر الأب...</option>
                      </select>
                    </div>

                    <div class="col-md-6">
                      <label class="form-label fw-bold required">صفة طالب المنحة</label>
                      <select name="relation_tuteur" id="edit_relation_tuteur" class="form-select" required>
                          <option value="">اختر...</option>
                          <option value="1" id="editWaliOption">الولي (الأب)</option>
                          <option value="2">الولي (الأم)</option>
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
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0" style="color:#0f033a;">الخطوة 2: إدخال معلومات التلميذ</h5>
                    <button type="button" id="clearStep2Btn" class="btn btn-outline-danger btn-sm">
                        <i class="bi bi-arrow-clockwise"></i> مسح الكل والبدء من جديد
                    </button>
                </div>

                <div class="row g-3">
                    <!-- الأم/الزوجة و صفة طالب المنحة - Top Row -->
                    <div class="col-md-6" id="motherSelectWrapper">
                      <label class="form-label fw-bold" id="motherSelectLabel">الأم/الزوجة</label>
                      <select name="mother_id" id="motherSelect" class="form-select">
                        <option value="">اختر الأم/الزوجة...</option>
                      </select>
                    </div>

                    <!-- Father Select (for Mother role) -->
                    <div class="col-md-6" id="fatherSelectWrapper" style="display: none;">
                      <label class="form-label fw-bold" id="fatherSelectLabel">الأب</label>
                      <select name="father_id" id="fatherSelect" class="form-select">
                        <option value="">اختر الأب...</option>
                      </select>
                    </div>

                    <div class="col-md-6">
                      <label class="form-label fw-bold required">صفة طالب المنحة</label>
                      <select name="relation_tuteur" id="relationSelect" class="form-select" required>
                          <option value="">اختر...</option>
                          <option value="1" id="waliOption">الولي (الأب)</option>
                          <option value="2">الولي (الأم)</option>
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
    
    // If body is a string (JSON), ensure Content-Type is set
    if (typeof options.body === 'string' && !mergedHeaders['Content-Type']) {
      mergedHeaders['Content-Type'] = 'application/json';
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
  @if(isset($tuteur) && isset($tuteur['relation_tuteur']))
    window.currentUserRelationTuteur = {{ $tuteur['relation_tuteur'] }};
  @endif
</script>
<script>
document.addEventListener("DOMContentLoaded", async () => {
  // Initialize cards visibility if role is already available from session
  if (window.currentUserRelationTuteur) {
    // Define the function first if it doesn't exist yet
    if (typeof updateInfoCardsVisibility === 'function') {
      updateInfoCardsVisibility();
    }
  }
  
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
    
    // Options are now fixed: الولي (الأب), الولي (الأم), وصي
    // No need to dynamically update based on role
    
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
      // Trigger change event to update dependent fields
      relationSelect.dispatchEvent(new Event('change'));
    }
    
    // Also update edit form if it exists
    if (relationValue && editRelationSelect) {
      editRelationSelect.value = relationValue;
    }
  }

  /* ===============================
     👤 Update Form for Guardian Role (Create Form)
     Accepts optional relation parameter - if not provided, uses logged-in user's role
     relation: selected relation_tuteur value from dropdown ('1' for ولي, '2' for أم, '3' for وصي)
  =============================== */
  function updateFormForGuardianRole(relation = null) {
    const selectedRelation = relation !== null ? relation : window.currentUserRelationTuteur;
    const motherSelectWrapper = document.getElementById('motherSelectWrapper');
    const fatherSelectWrapper = document.getElementById('fatherSelectWrapper');
    const motherSelect = document.getElementById('motherSelect');
    const fatherSelect = document.getElementById('fatherSelect');
    
    // Get fields fresh each time to ensure they exist after clearing
    const nomPere = document.getElementById('nomPere');
    const prenomPere = document.getElementById('prenomPere');
    const nomPereWrapper = document.getElementById('nomPereWrapper');
    const prenomPereWrapper = document.getElementById('prenomPereWrapper');
    const nomPereLabel = document.getElementById('nomPereLabel');
    const prenomPereLabel = document.getElementById('prenomPereLabel');
    
    // Get logged-in user info
    const tuteurNomAr = "{{ $tuteur['nom_ar'] ?? '' }}";
    const tuteurPrenomAr = "{{ $tuteur['prenom_ar'] ?? '' }}";
    
    if (selectedRelation === '1' || selectedRelation === 1) {
      // Role 1 (ولي/Father): Logged-in user is the father
      // Show mother dropdown, hide father dropdown
      if (fatherSelectWrapper) {
        fatherSelectWrapper.style.display = 'none';
        if (fatherSelect) {
          fatherSelect.required = false;
          fatherSelect.value = '';
        }
      }
      if (motherSelectWrapper) {
        motherSelectWrapper.style.display = 'block';
        if (motherSelect) {
          motherSelect.required = true;
          motherSelect.disabled = false;
        }
      } else if (motherSelectWrapper) {
        motherSelectWrapper.style.display = 'none';
        if (motherSelect) {
          motherSelect.required = false;
          motherSelect.value = '';
        }
      }
      
      // Set labels to father (logged-in user is the father)
      if (nomPereLabel) {
        nomPereLabel.textContent = 'لقب الأب بالعربية';
      }
      if (prenomPereLabel) {
        prenomPereLabel.textContent = 'اسم الأب بالعربية';
      }
      
      // Auto-fill father name fields with logged-in user's info (logged-in user is the father)
      // Use retry mechanism to ensure values are available
      const fillFatherNameFields = () => {
        // Get values from multiple sources - try window.tuteurData first (from API), then Blade template
        let finalNomAr = '';
        let finalPrenomAr = '';
        
        // Try to get from window.tuteurData (loaded from API)
        if (window.tuteurData && window.tuteurData.nom_ar) {
          finalNomAr = window.tuteurData.nom_ar;
        }
        if (window.tuteurData && window.tuteurData.prenom_ar) {
          finalPrenomAr = window.tuteurData.prenom_ar;
        }
        
        // Fallback to Blade template values
        if (!finalNomAr || finalNomAr === '') {
          finalNomAr = tuteurNomAr || "{{ $tuteur['nom_ar'] ?? '' }}";
        }
        if (!finalPrenomAr || finalPrenomAr === '') {
          finalPrenomAr = tuteurPrenomAr || "{{ $tuteur['prenom_ar'] ?? '' }}";
        }
        
        // Re-get fields to ensure they exist
        const nomPereEl = document.getElementById('nomPere');
        const prenomPereEl = document.getElementById('prenomPere');
        
        if (nomPereEl && finalNomAr && finalNomAr.trim() !== '' && finalNomAr !== 'undefined') {
          nomPereEl.value = finalNomAr.trim();
          nomPereEl.setAttribute('readonly', true);
          nomPereEl.readOnly = true;
          nomPereEl.style.backgroundColor = '#f8f9fa';
        }
        if (prenomPereEl && finalPrenomAr && finalPrenomAr.trim() !== '' && finalPrenomAr !== 'undefined') {
          prenomPereEl.value = finalPrenomAr.trim();
          prenomPereEl.setAttribute('readonly', true);
          prenomPereEl.readOnly = true;
          prenomPereEl.style.backgroundColor = '#f8f9fa';
        }
      };
      
      // Fill immediately
      fillFatherNameFields();
      
      // Also try again after a short delay in case values are loaded asynchronously
      setTimeout(fillFatherNameFields, 200);
      setTimeout(fillFatherNameFields, 500);
      
      // Show and auto-fill father NIN/NSS (logged-in user is the father)
      const ninPereWrapper = document.getElementById('ninPereWrapper');
      const nssPereWrapper = document.getElementById('nssPereWrapper');
      
      if (ninPereWrapper) ninPereWrapper.style.display = 'block';
      if (nssPereWrapper) nssPereWrapper.style.display = 'block';
      
      // Get NIN/NSS from window or fallback to session - ensure we get the actual values
      let userNIN = window.currentUserNIN;
      let userNSS = window.currentUserNSS;
      
      // If window values are empty or undefined, try to get from Blade template
      if (!userNIN || userNIN === '' || userNIN === 'undefined') {
        const bladeNIN = "{{ $tuteur['nin'] ?? '' }}";
        if (bladeNIN && bladeNIN !== '' && bladeNIN !== 'undefined') {
          userNIN = bladeNIN;
          window.currentUserNIN = bladeNIN; // Store it for future use
        }
      }
      
      if (!userNSS || userNSS === '' || userNSS === 'undefined') {
        const bladeNSS = "{{ $tuteur['nss'] ?? '' }}";
        if (bladeNSS && bladeNSS !== '' && bladeNSS !== 'undefined') {
          userNSS = bladeNSS;
          window.currentUserNSS = bladeNSS; // Store it for future use
        }
      }
      
      // Fill the fields - try multiple times to ensure values are available
      const fillFatherNINNSS = () => {
        const ninPere = document.getElementById('ninPere');
        const nssPere = document.getElementById('nssPere');
        
        // Re-check values in case they were loaded asynchronously
        let finalNIN = userNIN || window.currentUserNIN || "{{ $tuteur['nin'] ?? '' }}";
        let finalNSS = userNSS || window.currentUserNSS || "{{ $tuteur['nss'] ?? '' }}";
        
        if (ninPere && finalNIN && finalNIN.trim() !== '' && finalNIN !== 'undefined') {
          ninPere.value = finalNIN.trim();
          ninPere.setAttribute('readonly', true);
          ninPere.readOnly = true;
          ninPere.style.backgroundColor = '#f8f9fa';
        }
        if (nssPere && finalNSS && finalNSS.trim() !== '' && finalNSS !== 'undefined') {
          nssPere.value = finalNSS.trim();
          nssPere.setAttribute('readonly', true);
          nssPere.readOnly = true;
          nssPere.style.backgroundColor = '#f8f9fa';
        }
      };
      
      // Fill immediately
      fillFatherNINNSS();
      
      // Also try again after a short delay in case values are loaded asynchronously
      setTimeout(fillFatherNINNSS, 200);
      setTimeout(fillFatherNINNSS, 500);
      
      // Hide guardian NIN/NSS fields
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
    } else if (selectedRelation === '2' || selectedRelation === 2) {
      // Role 2 (Mother): Logged-in user is the mother
      // Hide mother dropdown, show father dropdown, change labels to mother
      if (motherSelectWrapper) {
        motherSelectWrapper.style.display = 'none';
      }
      
      if (fatherSelectWrapper) {
        fatherSelectWrapper.style.display = 'block';
        if (fatherSelect) {
          fatherSelect.required = true;
          fatherSelect.disabled = false;
        }
      }
      // Ensure mother dropdown is hidden and not required for role 2
      if (motherSelectWrapper) {
        motherSelectWrapper.style.display = 'none';
        if (motherSelect) {
          motherSelect.required = false;
          motherSelect.value = '';
        }
      }
      
      // Change labels to mother (logged-in user is the mother)
      if (nomPereLabel) {
        nomPereLabel.textContent = 'لقب الأم بالعربية';
      }
      if (prenomPereLabel) {
        prenomPereLabel.textContent = 'اسم الأم بالعربية';
      }
      
      // Auto-fill mother name fields with logged-in user's info (logged-in user is the mother)
      // Use retry mechanism to ensure values are available
      const fillMotherNameFields = () => {
        // Get values from multiple sources - try window.tuteurData first (from API), then Blade template
        let finalNomAr = '';
        let finalPrenomAr = '';
        
        // Try to get from window.tuteurData (loaded from API)
        if (window.tuteurData && window.tuteurData.nom_ar) {
          finalNomAr = window.tuteurData.nom_ar;
        }
        if (window.tuteurData && window.tuteurData.prenom_ar) {
          finalPrenomAr = window.tuteurData.prenom_ar;
        }
        
        // Fallback to Blade template values
        if (!finalNomAr || finalNomAr === '') {
          finalNomAr = tuteurNomAr || "{{ $tuteur['nom_ar'] ?? '' }}";
        }
        if (!finalPrenomAr || finalPrenomAr === '') {
          finalPrenomAr = tuteurPrenomAr || "{{ $tuteur['prenom_ar'] ?? '' }}";
        }
        
        // Re-get fields to ensure they exist
        const nomPereEl = document.getElementById('nomPere');
        const prenomPereEl = document.getElementById('prenomPere');
        
        if (nomPereEl && finalNomAr && finalNomAr.trim() !== '' && finalNomAr !== 'undefined') {
          nomPereEl.value = finalNomAr.trim();
          nomPereEl.setAttribute('readonly', true);
          nomPereEl.readOnly = true;
          nomPereEl.style.backgroundColor = '#f8f9fa';
        }
        if (prenomPereEl && finalPrenomAr && finalPrenomAr.trim() !== '' && finalPrenomAr !== 'undefined') {
          prenomPereEl.value = finalPrenomAr.trim();
          prenomPereEl.setAttribute('readonly', true);
          prenomPereEl.readOnly = true;
          prenomPereEl.style.backgroundColor = '#f8f9fa';
        }
      };
      
      // Fill immediately
      fillMotherNameFields();
      
      // Also try again after a short delay in case values are loaded asynchronously
      setTimeout(fillMotherNameFields, 200);
      setTimeout(fillMotherNameFields, 500);
      
      // Show and auto-fill mother NIN/NSS (logged-in user is the mother)
      const ninMereWrapper = document.getElementById('ninMereWrapper');
      const nssMereWrapper = document.getElementById('nssMereWrapper');
      const ninMere = document.getElementById('ninMere');
      const nssMere = document.getElementById('nssMere');
      
      if (ninMereWrapper) ninMereWrapper.style.display = 'block';
      if (nssMereWrapper) nssMereWrapper.style.display = 'block';
      
      if (ninMere && window.currentUserNIN) {
        ninMere.value = window.currentUserNIN;
        ninMere.setAttribute('readonly', true);
        ninMere.readOnly = true;
        ninMere.style.backgroundColor = '#f8f9fa';
      }
      if (nssMere && window.currentUserNSS) {
        nssMere.value = window.currentUserNSS;
        nssMere.setAttribute('readonly', true);
        nssMere.readOnly = true;
        nssMere.style.backgroundColor = '#f8f9fa';
      }
      
      // Hide guardian NIN/NSS fields
      const ninGuardianWrapper = document.getElementById('ninGuardianWrapper');
      const nssGuardianWrapper = document.getElementById('nssGuardianWrapper');
      if (ninGuardianWrapper) ninGuardianWrapper.style.display = 'none';
      if (nssGuardianWrapper) nssGuardianWrapper.style.display = 'none';
      
      // Initially hide father NIN/NSS - will be shown when father is selected
      const ninPereWrapper = document.getElementById('ninPereWrapper');
      const nssPereWrapper = document.getElementById('nssPereWrapper');
      if (ninPereWrapper) ninPereWrapper.style.display = 'none';
      if (nssPereWrapper) nssPereWrapper.style.display = 'none';
      
      // If a father is already selected, show and fill the fields
      if (fatherSelect && fatherSelect.value) {
        const selectedFatherId = fatherSelect.value;
        if (window.fathersData && window.fathersData.length > 0) {
          const selectedFather = window.fathersData.find(f => f.id == selectedFatherId);
          if (selectedFather) {
            if (ninPereWrapper) ninPereWrapper.style.display = 'block';
            if (nssPereWrapper) nssPereWrapper.style.display = 'block';
            
            const ninPere = document.getElementById('ninPere');
            const nssPere = document.getElementById('nssPere');
            if (ninPere && selectedFather.nin) {
              ninPere.value = selectedFather.nin;
            }
            if (nssPere && selectedFather.nss) {
              nssPere.value = selectedFather.nss;
            }
          }
        }
      }
    } else if (selectedRelation === '3' || selectedRelation === 3) {
      // Guardian role (وصي): Logged-in user is the guardian
      // Show both mother and father dropdowns, and all NIN/NSS fields
      // Show mother dropdown
      if (motherSelectWrapper) {
        motherSelectWrapper.style.display = 'block';
        if (motherSelect) {
          motherSelect.required = true;
          motherSelect.disabled = false;
          motherSelect.style.backgroundColor = '';
        }
      }
      
      // Show father dropdown
      if (fatherSelectWrapper) {
        fatherSelectWrapper.style.display = 'block';
        if (fatherSelect) {
          fatherSelect.required = true;
          fatherSelect.disabled = false;
        }
      }
      // Ensure mother dropdown is hidden and not required for role 2
      if (motherSelectWrapper) {
        motherSelectWrapper.style.display = 'none';
        if (motherSelect) {
          motherSelect.required = false;
          motherSelect.value = '';
        }
      }
      
      // Change labels to guardian (وصي) - logged-in user is the guardian
      if (nomPereLabel) {
        nomPereLabel.textContent = 'لقب الوصي بالعربية';
      }
      if (prenomPereLabel) {
        prenomPereLabel.textContent = 'اسم الوصي بالعربية';
      }
      
      // Auto-fill guardian name fields with logged-in user's info (logged-in user is the guardian)
      const fillGuardianNameFields = () => {
        // Get values from multiple sources - try window.tuteurData first (from API), then Blade template
        let finalNomAr = '';
        let finalPrenomAr = '';
        
        // Try to get from window.tuteurData (loaded from API)
        if (window.tuteurData && window.tuteurData.nom_ar) {
          finalNomAr = window.tuteurData.nom_ar;
        }
        if (window.tuteurData && window.tuteurData.prenom_ar) {
          finalPrenomAr = window.tuteurData.prenom_ar;
        }
        
        // Fallback to Blade template values
        if (!finalNomAr || finalNomAr === '') {
          finalNomAr = tuteurNomAr || "{{ $tuteur['nom_ar'] ?? '' }}";
        }
        if (!finalPrenomAr || finalPrenomAr === '') {
          finalPrenomAr = tuteurPrenomAr || "{{ $tuteur['prenom_ar'] ?? '' }}";
        }
        
        // Re-get fields to ensure they exist
        const nomPereEl = document.getElementById('nomPere');
        const prenomPereEl = document.getElementById('prenomPere');
        
        if (nomPereEl && finalNomAr && finalNomAr.trim() !== '' && finalNomAr !== 'undefined') {
          nomPereEl.value = finalNomAr.trim();
          nomPereEl.setAttribute('readonly', true);
          nomPereEl.readOnly = true;
          nomPereEl.style.backgroundColor = '#f8f9fa';
        }
        if (prenomPereEl && finalPrenomAr && finalPrenomAr.trim() !== '' && finalPrenomAr !== 'undefined') {
          prenomPereEl.value = finalPrenomAr.trim();
          prenomPereEl.setAttribute('readonly', true);
          prenomPereEl.readOnly = true;
          prenomPereEl.style.backgroundColor = '#f8f9fa';
        }
      };
      
      // Fill immediately
      fillGuardianNameFields();
      
      // Also try again after a short delay in case values are loaded asynchronously
      setTimeout(fillGuardianNameFields, 200);
      setTimeout(fillGuardianNameFields, 500);
      
      // Show guardian (tuteur) NIN and NSS fields
      const ninGuardianWrapper = document.getElementById('ninGuardianWrapper');
      const nssGuardianWrapper = document.getElementById('nssGuardianWrapper');
      
      if (ninGuardianWrapper) ninGuardianWrapper.style.display = 'block';
      if (nssGuardianWrapper) nssGuardianWrapper.style.display = 'block';
      
      // Auto-fill guardian (tuteur) NIN and NSS (logged-in user is the guardian)
      const fillGuardianNINNSS = () => {
        // Get NIN/NSS from window or fallback to session - ensure we get the actual values
        let userNIN = window.currentUserNIN;
        let userNSS = window.currentUserNSS;
        
        // If window values are empty or undefined, try to get from Blade template
        if (!userNIN || userNIN === '' || userNIN === 'undefined') {
          const bladeNIN = "{{ $tuteur['nin'] ?? '' }}";
          if (bladeNIN && bladeNIN !== '' && bladeNIN !== 'undefined') {
            userNIN = bladeNIN;
            window.currentUserNIN = bladeNIN; // Store it for future use
          }
        }
        
        if (!userNSS || userNSS === '' || userNSS === 'undefined') {
          const bladeNSS = "{{ $tuteur['nss'] ?? '' }}";
          if (bladeNSS && bladeNSS !== '' && bladeNSS !== 'undefined') {
            userNSS = bladeNSS;
            window.currentUserNSS = bladeNSS; // Store it for future use
          }
        }
        
        // Fill the fields
        const ninGuardian = document.getElementById('ninGuardian');
        const nssGuardian = document.getElementById('nssGuardian');
        
        // Re-check values in case they were loaded asynchronously
        let finalNIN = userNIN || window.currentUserNIN || "{{ $tuteur['nin'] ?? '' }}";
        let finalNSS = userNSS || window.currentUserNSS || "{{ $tuteur['nss'] ?? '' }}";
        
        if (ninGuardian && finalNIN && finalNIN.trim() !== '' && finalNIN !== 'undefined') {
          ninGuardian.value = finalNIN.trim();
          ninGuardian.setAttribute('readonly', true);
          ninGuardian.readOnly = true;
          ninGuardian.style.backgroundColor = '#f8f9fa';
        }
        if (nssGuardian && finalNSS && finalNSS.trim() !== '' && finalNSS !== 'undefined') {
          nssGuardian.value = finalNSS.trim();
          nssGuardian.setAttribute('readonly', true);
          nssGuardian.readOnly = true;
          nssGuardian.style.backgroundColor = '#f8f9fa';
        }
      };
      
      // Fill immediately
      fillGuardianNINNSS();
      
      // Also try again after a short delay in case values are loaded asynchronously
      setTimeout(fillGuardianNINNSS, 200);
      setTimeout(fillGuardianNINNSS, 500);
      
      // Initially hide mother and father NIN/NSS - will be shown when selected
      const ninMereWrapper = document.getElementById('ninMereWrapper');
      const nssMereWrapper = document.getElementById('nssMereWrapper');
      const ninPereWrapper = document.getElementById('ninPereWrapper');
      const nssPereWrapper = document.getElementById('nssPereWrapper');
      
      if (ninMereWrapper) ninMereWrapper.style.display = 'none';
      if (nssMereWrapper) nssMereWrapper.style.display = 'none';
      if (ninPereWrapper) ninPereWrapper.style.display = 'none';
      if (nssPereWrapper) nssPereWrapper.style.display = 'none';
      
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
      
      // If a father is already selected, show and fill the fields
      if (fatherSelect && fatherSelect.value) {
        const selectedFatherId = fatherSelect.value;
        if (window.fathersData && window.fathersData.length > 0) {
          const selectedFather = window.fathersData.find(f => f.id == selectedFatherId);
          if (selectedFather) {
            if (ninPereWrapper) ninPereWrapper.style.display = 'block';
            if (nssPereWrapper) nssPereWrapper.style.display = 'block';
            
            const ninPere = document.getElementById('ninPere');
            const nssPere = document.getElementById('nssPere');
            if (ninPere && selectedFather.nin) {
              ninPere.value = selectedFather.nin;
            }
            if (nssPere && selectedFather.nss) {
              nssPere.value = selectedFather.nss;
            }
          }
        }
      }
    } else {
      // Default/Other roles: Hide all special fields
      if (fatherSelectWrapper) {
        fatherSelectWrapper.style.display = 'none';
        if (fatherSelect) {
          fatherSelect.required = false;
          fatherSelect.value = '';
        }
      }
      if (motherSelectWrapper) {
        motherSelectWrapper.style.display = 'block';
        if (motherSelect) {
          motherSelect.required = false;
        }
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
     Accepts optional relation parameter - if not provided, uses logged-in user's role
     relation: selected relation_tuteur value from dropdown ('1' for ولي, '2' for أم, '3' for وصي)
  =============================== */
  function updateFormForEditGuardianRole(relation = null) {
    const selectedRelation = relation !== null ? relation : window.currentUserRelationTuteur;
    const editMotherSelectWrapper = document.getElementById('edit_motherSelectWrapper');
    const editFatherSelectWrapper = document.getElementById('edit_fatherSelectWrapper');
    const editMotherSelect = document.getElementById('editMotherSelect');
    const editMotherSelectLabel = document.getElementById('edit_motherSelectLabel');
    const editFatherSelect = document.getElementById('editFatherSelect');
    const editNomPere = document.getElementById('edit_nom_pere');
    const editPrenomPere = document.getElementById('edit_prenom_pere');
    const editNomPereWrapper = document.getElementById('edit_nomPereWrapper');
    const editPrenomPereWrapper = document.getElementById('edit_prenomPereWrapper');
    const editNomPereLabel = document.getElementById('edit_nomPereLabel');
    const editPrenomPereLabel = document.getElementById('edit_prenomPereLabel');
    
    if (selectedRelation === '1' || selectedRelation === 1) {
      // Role 1 (ولي/Father): Show mother dropdown, hide father dropdown
      if (editFatherSelectWrapper) {
        editFatherSelectWrapper.style.display = 'none';
        if (editFatherSelect) {
          editFatherSelect.required = false;
          editFatherSelect.value = '';
        }
      }
      if (editMotherSelectWrapper) {
        editMotherSelectWrapper.style.display = 'block';
        if (editMotherSelect) {
          editMotherSelect.required = true;
          editMotherSelect.disabled = false;
          editMotherSelect.style.backgroundColor = '';
        }
      } else if (editMotherSelectWrapper) {
        editMotherSelectWrapper.style.display = 'none';
        if (editMotherSelect) {
          editMotherSelect.required = false;
          editMotherSelect.value = '';
        }
      }
      if (editMotherSelectLabel) {
        editMotherSelectLabel.textContent = 'الأم/الزوجة';
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
    } else if (selectedRelation === '2' || selectedRelation === 2) {
      // Role 2 (Mother): Hide mother dropdown, show father dropdown, change labels to mother
      if (editMotherSelectWrapper) {
        editMotherSelectWrapper.style.display = 'none';
        if (editMotherSelect) {
          editMotherSelect.required = false;
          editMotherSelect.value = '';
        }
      }
      
      if (editFatherSelectWrapper) {
        editFatherSelectWrapper.style.display = 'block';
        if (editFatherSelect) {
          editFatherSelect.required = true;
          editFatherSelect.disabled = false;
        }
      } else if (editFatherSelectWrapper) {
        editFatherSelectWrapper.style.display = 'none';
        if (editFatherSelect) {
          editFatherSelect.required = false;
          editFatherSelect.value = '';
        }
      }
      
      // Change labels to mother
      if (editNomPereLabel) {
        editNomPereLabel.textContent = 'لقب الأم بالعربية';
      }
      if (editPrenomPereLabel) {
        editPrenomPereLabel.textContent = 'اسم الأم بالعربية';
      }
      
      // Hide guardian NIN/NSS fields (father NIN/NSS will be shown when father is selected)
      const editNinGuardianWrapper = document.getElementById('edit_ninGuardianWrapper');
      const editNssGuardianWrapper = document.getElementById('edit_nssGuardianWrapper');
      
      if (editNinGuardianWrapper) editNinGuardianWrapper.style.display = 'none';
      if (editNssGuardianWrapper) editNssGuardianWrapper.style.display = 'none';
      
      // Initially hide father NIN/NSS - will be shown when father is selected
      const editNinPereWrapper = document.getElementById('edit_ninPereWrapper');
      const editNssPereWrapper = document.getElementById('edit_nssPereWrapper');
      if (editNinPereWrapper) editNinPereWrapper.style.display = 'none';
      if (editNssPereWrapper) editNssPereWrapper.style.display = 'none';
      
      // If a father is already selected, show and fill the fields
      if (editFatherSelect && editFatherSelect.value) {
        const selectedFatherId = editFatherSelect.value;
        if (window.fathersData && window.fathersData.length > 0) {
          const selectedFather = window.fathersData.find(f => f.id == selectedFatherId);
          if (selectedFather) {
            if (editNinPereWrapper) editNinPereWrapper.style.display = 'block';
            if (editNssPereWrapper) editNssPereWrapper.style.display = 'block';
            
            const editNinPere = document.getElementById('edit_ninPere');
            const editNssPere = document.getElementById('edit_nssPere');
            if (editNinPere && selectedFather.nin) {
              editNinPere.value = selectedFather.nin;
            }
            if (editNssPere && selectedFather.nss) {
              editNssPere.value = selectedFather.nss;
            }
          }
        }
      }
      
      // Show and auto-fill mother NIN and NSS
      const editNinMereWrapper = document.getElementById('edit_ninMereWrapper');
      const editNssMereWrapper = document.getElementById('edit_nssMereWrapper');
      const editNinMere = document.getElementById('edit_ninMere');
      const editNssMere = document.getElementById('edit_nssMere');
      
      if (editNinMereWrapper) editNinMereWrapper.style.display = 'block';
      if (editNssMereWrapper) editNssMereWrapper.style.display = 'block';
    } else if (selectedRelation === '3' || selectedRelation === 3) {
      // Guardian role (وصي): Logged-in user is the guardian
      // Show both mother and father dropdowns, and all NIN/NSS fields
      // Show mother dropdown
      if (editMotherSelectWrapper) {
        editMotherSelectWrapper.style.display = 'block';
        if (editMotherSelect) {
          editMotherSelect.required = true;
          editMotherSelect.disabled = false;
          editMotherSelect.style.backgroundColor = '';
        }
      }
      
      // Show father dropdown
      if (editFatherSelectWrapper) {
        editFatherSelectWrapper.style.display = 'block';
        if (editFatherSelect) {
          editFatherSelect.required = true;
          editFatherSelect.disabled = false;
        }
      }
      
      // Change labels to guardian (وصي) - logged-in user is the guardian
      if (editNomPereLabel) {
        editNomPereLabel.textContent = 'لقب الوصي بالعربية';
      }
      if (editPrenomPereLabel) {
        editPrenomPereLabel.textContent = 'اسم الوصي بالعربية';
      }
      
      // Auto-fill guardian name fields with logged-in user's info (logged-in user is the guardian)
      const fillEditGuardianNameFields = () => {
        // Get values from multiple sources - try window.tuteurData first (from API), then Blade template
        let finalNomAr = '';
        let finalPrenomAr = '';
        
        // Try to get from window.tuteurData (loaded from API)
        if (window.tuteurData && window.tuteurData.nom_ar) {
          finalNomAr = window.tuteurData.nom_ar;
        }
        if (window.tuteurData && window.tuteurData.prenom_ar) {
          finalPrenomAr = window.tuteurData.prenom_ar;
        }
        
        // Fallback to Blade template values
        if (!finalNomAr || finalNomAr === '') {
          finalNomAr = tuteurNomAr || "{{ $tuteur['nom_ar'] ?? '' }}";
        }
        if (!finalPrenomAr || finalPrenomAr === '') {
          finalPrenomAr = tuteurPrenomAr || "{{ $tuteur['prenom_ar'] ?? '' }}";
        }
        
        // Re-get fields to ensure they exist
        const editNomPereEl = document.getElementById('edit_nom_pere');
        const editPrenomPereEl = document.getElementById('edit_prenom_pere');
        
        if (editNomPereEl && finalNomAr && finalNomAr.trim() !== '' && finalNomAr !== 'undefined') {
          editNomPereEl.value = finalNomAr.trim();
          editNomPereEl.setAttribute('readonly', true);
          editNomPereEl.readOnly = true;
          editNomPereEl.style.backgroundColor = '#f8f9fa';
        }
        if (editPrenomPereEl && finalPrenomAr && finalPrenomAr.trim() !== '' && finalPrenomAr !== 'undefined') {
          editPrenomPereEl.value = finalPrenomAr.trim();
          editPrenomPereEl.setAttribute('readonly', true);
          editPrenomPereEl.readOnly = true;
          editPrenomPereEl.style.backgroundColor = '#f8f9fa';
        }
      };
      
      // Fill immediately
      fillEditGuardianNameFields();
      
      // Also try again after a short delay in case values are loaded asynchronously
      setTimeout(fillEditGuardianNameFields, 200);
      setTimeout(fillEditGuardianNameFields, 500);
      
      // Show guardian (tuteur) NIN and NSS fields
      const editNinGuardianWrapper = document.getElementById('edit_ninGuardianWrapper');
      const editNssGuardianWrapper = document.getElementById('edit_nssGuardianWrapper');
      
      if (editNinGuardianWrapper) editNinGuardianWrapper.style.display = 'block';
      if (editNssGuardianWrapper) editNssGuardianWrapper.style.display = 'block';
      
      // Auto-fill guardian (tuteur) NIN and NSS (logged-in user is the guardian)
      const fillEditGuardianNINNSS = () => {
        // Get NIN/NSS from window or fallback to session - ensure we get the actual values
        let userNIN = window.currentUserNIN;
        let userNSS = window.currentUserNSS;
        
        // If window values are empty or undefined, try to get from Blade template
        if (!userNIN || userNIN === '' || userNIN === 'undefined') {
          const bladeNIN = "{{ $tuteur['nin'] ?? '' }}";
          if (bladeNIN && bladeNIN !== '' && bladeNIN !== 'undefined') {
            userNIN = bladeNIN;
            window.currentUserNIN = bladeNIN; // Store it for future use
          }
        }
        
        if (!userNSS || userNSS === '' || userNSS === 'undefined') {
          const bladeNSS = "{{ $tuteur['nss'] ?? '' }}";
          if (bladeNSS && bladeNSS !== '' && bladeNSS !== 'undefined') {
            userNSS = bladeNSS;
            window.currentUserNSS = bladeNSS; // Store it for future use
          }
        }
        
        // Fill the fields
        const editNinGuardian = document.getElementById('edit_ninGuardian');
        const editNssGuardian = document.getElementById('edit_nssGuardian');
        
        // Re-check values in case they were loaded asynchronously
        let finalNIN = userNIN || window.currentUserNIN || "{{ $tuteur['nin'] ?? '' }}";
        let finalNSS = userNSS || window.currentUserNSS || "{{ $tuteur['nss'] ?? '' }}";
        
        if (editNinGuardian && finalNIN && finalNIN.trim() !== '' && finalNIN !== 'undefined') {
          editNinGuardian.value = finalNIN.trim();
          editNinGuardian.setAttribute('readonly', true);
          editNinGuardian.readOnly = true;
          editNinGuardian.style.backgroundColor = '#f8f9fa';
        }
        if (editNssGuardian && finalNSS && finalNSS.trim() !== '' && finalNSS !== 'undefined') {
          editNssGuardian.value = finalNSS.trim();
          editNssGuardian.setAttribute('readonly', true);
          editNssGuardian.readOnly = true;
          editNssGuardian.style.backgroundColor = '#f8f9fa';
        }
      };
      
      // Fill immediately
      fillEditGuardianNINNSS();
      
      // Also try again after a short delay in case values are loaded asynchronously
      setTimeout(fillEditGuardianNINNSS, 200);
      setTimeout(fillEditGuardianNINNSS, 500);
      
      // Initially hide mother and father NIN/NSS - will be shown when selected
      const editNinMereWrapper = document.getElementById('edit_ninMereWrapper');
      const editNssMereWrapper = document.getElementById('edit_nssMereWrapper');
      const editNinPereWrapper = document.getElementById('edit_ninPereWrapper');
      const editNssPereWrapper = document.getElementById('edit_nssPereWrapper');
      
      if (editNinMereWrapper) editNinMereWrapper.style.display = 'none';
      if (editNssMereWrapper) editNssMereWrapper.style.display = 'none';
      if (editNinPereWrapper) editNinPereWrapper.style.display = 'none';
      if (editNssPereWrapper) editNssPereWrapper.style.display = 'none';
      
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
      
      // If a father is already selected, show and fill the fields
      if (editFatherSelect && editFatherSelect.value) {
        const selectedFatherId = editFatherSelect.value;
        if (window.fathersData && window.fathersData.length > 0) {
          const selectedFather = window.fathersData.find(f => f.id == selectedFatherId);
          if (selectedFather) {
            if (editNinPereWrapper) editNinPereWrapper.style.display = 'block';
            if (editNssPereWrapper) editNssPereWrapper.style.display = 'block';
            
            const editNinPere = document.getElementById('edit_ninPere');
            const editNssPere = document.getElementById('edit_nssPere');
            if (editNinPere && selectedFather.nin) {
              editNinPere.value = selectedFather.nin;
            }
            if (editNssPere && selectedFather.nss) {
              editNssPere.value = selectedFather.nss;
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
        if (editMotherSelect) {
          editMotherSelect.required = false;
        }
      }
      if (editFatherSelectWrapper) {
        editFatherSelectWrapper.style.display = 'none';
        if (editFatherSelect) {
          editFatherSelect.required = false;
          editFatherSelect.value = '';
        }
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
     👤 Load Fathers for Tuteur
  =============================== */
  async function loadFathers() {
    try {
      const response = await apiFetch('/api/fathers');

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

      let fathers;
      try {
        fathers = await response.json();
      } catch (e) {
        // Invalid JSON response
        return;
      }
      
      // Handle both array and object with data property
      const fathersArray = Array.isArray(fathers) ? fathers : (fathers?.data || []);
      
      // Store fathers data globally for auto-fill
      window.fathersData = fathersArray || [];
      
      const fatherSelect = document.getElementById('fatherSelect');
      const editFatherSelect = document.getElementById('editFatherSelect');
      
      // Clear existing options and populate
      if (fatherSelect) {
        fatherSelect.innerHTML = '<option value="">اختر الأب...</option>';
        if (Array.isArray(fathersArray) && fathersArray.length > 0) {
          fathersArray.forEach(father => {
            if (father && father.id) {
              const option = document.createElement('option');
              option.value = father.id;
              const fatherName = `${father.prenom_ar || ''} ${father.nom_ar || ''}`.trim();
              option.textContent = fatherName || `الأب ${father.id}`;
              fatherSelect.appendChild(option);
            }
          });
        }
      }

      if (editFatherSelect) {
        editFatherSelect.innerHTML = '<option value="">اختر الأب...</option>';
        if (Array.isArray(fathersArray) && fathersArray.length > 0) {
          fathersArray.forEach(father => {
            if (father && father.id) {
              const option = document.createElement('option');
              option.value = father.id;
              const fatherName = `${father.prenom_ar || ''} ${father.nom_ar || ''}`.trim();
              option.textContent = fatherName || `الأب ${father.id}`;
              editFatherSelect.appendChild(option);
            }
          });
        }
      }
    } catch (error) {
      // Silently handle error
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
        
        // Show/hide Mothers and Father Info cards based on role
        updateInfoCardsVisibility();
        
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
    await loadFathers();
    
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

  // Note: nin_pere and nss_pere fields removed - using father relationship instead
  const ninPere = document.getElementById('ninPere'); // Display-only field
  const nssPere = document.getElementById('nssPere'); // Display-only field
  
  // Declare motherSelect and fatherSelect at top level for use in event listeners
  const motherSelect = document.getElementById('motherSelect');
  const fatherSelect = document.getElementById('fatherSelect');

  // Function to auto-fill NIN and NSS based on relation
  function autoFillParentData(relation) {
    const relationSelect = document.getElementById('relationSelect') || form.querySelector('[name="relation_tuteur"]');
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

  /* ===============================
     🧹 Clear Step 2 Form
  =============================== */
  function clearStep2Form() {
    // Clear all select fields
    const motherSelect = document.getElementById('motherSelect');
    const fatherSelect = document.getElementById('fatherSelect');
    const relationSelect = document.getElementById('relationSelect');
    
    if (motherSelect) motherSelect.value = '';
    if (fatherSelect) fatherSelect.value = '';
    
      // Clear all input fields in step 2 (but preserve auto-filled fields that will be re-filled)
      const step2 = document.getElementById('step2');
      if (step2) {
        const inputs = step2.querySelectorAll('input[type="text"], input[type="date"], input[type="number"]');
        inputs.forEach(input => {
          // Don't clear num_scolaire, and don't clear fields that will be auto-filled
          // (ninPere, nssPere, ninMere, nssMere, ninGuardian, nssGuardian will be re-filled)
          if (input.name !== 'num_scolaire' && 
              input.id !== 'ninPere' && input.id !== 'nssPere' &&
              input.id !== 'ninMere' && input.id !== 'nssMere' &&
              input.id !== 'ninGuardian' && input.id !== 'nssGuardian' &&
              input.id !== 'nomPere' && input.id !== 'prenomPere') {
            input.value = '';
            input.removeAttribute('readonly');
            input.readOnly = false;
            input.style.backgroundColor = '';
            input.classList.remove('is-invalid');
          }
        });
      
      // Clear radio buttons
      const radios = step2.querySelectorAll('input[type="radio"]');
      radios.forEach(radio => {
        radio.checked = false;
      });
      
      // Clear selects
      const selects = step2.querySelectorAll('select');
      selects.forEach(select => {
        if (select.id !== 'relationSelect') { // Keep relation_tuteur
          select.value = '';
          select.disabled = false;
          select.style.backgroundColor = '';
          select.classList.remove('is-invalid');
        }
      });
      
      // Hide all NIN/NSS wrappers (they will be shown again by updateFormForGuardianRole)
      const ninNssWrappers = step2.querySelectorAll('[id$="Wrapper"]');
      ninNssWrappers.forEach(wrapper => {
        if (wrapper.id.includes('nin') || wrapper.id.includes('nss')) {
          wrapper.style.display = 'none';
        }
      });
      
      // Clear NIN/NSS field values (they will be re-filled by updateFormForGuardianRole)
      const ninPere = document.getElementById('ninPere');
      const nssPere = document.getElementById('nssPere');
      const ninMere = document.getElementById('ninMere');
      const nssMere = document.getElementById('nssMere');
      const ninGuardian = document.getElementById('ninGuardian');
      const nssGuardian = document.getElementById('nssGuardian');
      
      if (ninPere) ninPere.value = '';
      if (nssPere) nssPere.value = '';
      if (ninMere) ninMere.value = '';
      if (nssMere) nssMere.value = '';
      if (ninGuardian) ninGuardian.value = '';
      if (nssGuardian) nssGuardian.value = '';
      
      // Remove error messages
      const errorMessages = step2.querySelectorAll('.error-msg');
      errorMessages.forEach(msg => msg.remove());
    }
  }

  if (relationSelect) {
  relationSelect.addEventListener('change', () => {
      const selectedRelation = relationSelect.value;
      
      // Clear step 2 form when relation changes
      clearStep2Form();
      
      // Use setTimeout to ensure DOM is updated before filling
      setTimeout(() => {
        // Update form conditionally based on selected relation
        updateFormForGuardianRole(selectedRelation);
        
        // Auto-fill functions (these may not be needed anymore but keeping for compatibility)
        autoFillParentData(selectedRelation);
        autoFillTuteurData(selectedRelation);
      }, 50);
    });
    // Initial fill based on default/selected value
    const initialRelation = relationSelect.value;
    if (initialRelation) {
      updateFormForGuardianRole(initialRelation);
      autoFillParentData(initialRelation);
      autoFillTuteurData(initialRelation);
    }
  }

  // Clear Step 2 button
  const clearStep2Btn = document.getElementById('clearStep2Btn');
  if (clearStep2Btn) {
    clearStep2Btn.addEventListener('click', () => {
      clearStep2Form();
      // Re-apply form logic based on current relation
      const relationSelect = document.getElementById('relationSelect');
      if (relationSelect && relationSelect.value) {
        updateFormForGuardianRole(relationSelect.value);
      }
    });
  }

  // Edit form relation select change handler
  const editRelationSelect = document.getElementById('edit_relation_tuteur');
  if (editRelationSelect) {
    editRelationSelect.addEventListener('change', () => {
      const selectedRelation = editRelationSelect.value;
      // Update edit form conditionally based on selected relation
      updateFormForEditGuardianRole(selectedRelation);
    });
  }

  // Auto-fill when mother is selected (for role 1 - Father, and role 3 - Guardian)
  if (motherSelect) {
    motherSelect.addEventListener('change', function() {
      const selectedMotherId = this.value;
      const relationSelect = document.getElementById('relationSelect');
      const selectedRelation = relationSelect ? relationSelect.value : null;
      
      // For role 1 (Father) or role 3 (Guardian), show and fill mother NIN/NSS when mother is selected
      if ((selectedRelation === '1' || selectedRelation === 1 || selectedRelation === '3' || selectedRelation === 3) && selectedMotherId && window.mothersData && window.mothersData.length > 0) {
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
        // Hide mother NIN/NSS if no mother selected or not role 1/3
        const ninMereWrapper = document.getElementById('ninMereWrapper');
        const nssMereWrapper = document.getElementById('nssMereWrapper');
        if (ninMereWrapper) ninMereWrapper.style.display = 'none';
        if (nssMereWrapper) nssMereWrapper.style.display = 'none';
      }
    });
  }

  // Auto-fill when father is selected (for role 2 - Mother, and role 3 - Guardian)
  const fatherSelect = document.getElementById('fatherSelect');
  if (fatherSelect) {
    fatherSelect.addEventListener('change', function() {
      const selectedFatherId = this.value;
      const relationSelect = document.getElementById('relationSelect');
      const selectedRelation = relationSelect ? relationSelect.value : null;
      
      // For role 2 (Mother) or role 3 (Guardian), show and fill father NIN/NSS when father is selected
      if ((selectedRelation === '2' || selectedRelation === 2 || selectedRelation === '3' || selectedRelation === 3) && selectedFatherId && window.fathersData && window.fathersData.length > 0) {
        const selectedFather = window.fathersData.find(f => f.id == selectedFatherId);
        if (selectedFather) {
          // Show father NIN/NSS fields
          const ninPereWrapper = document.getElementById('ninPereWrapper');
          const nssPereWrapper = document.getElementById('nssPereWrapper');
          const ninPere = document.getElementById('ninPere');
          const nssPere = document.getElementById('nssPere');
          
          if (ninPereWrapper) ninPereWrapper.style.display = 'block';
          if (nssPereWrapper) nssPereWrapper.style.display = 'block';
          
          // Fill father NIN and NSS
          if (ninPere && selectedFather.nin) {
            ninPere.value = selectedFather.nin;
          }
          if (nssPere && selectedFather.nss) {
            nssPere.value = selectedFather.nss;
          }
        }
      } else {
        // Hide father NIN/NSS if no father selected or not role 2/3
        const ninPereWrapper = document.getElementById('ninPereWrapper');
        const nssPereWrapper = document.getElementById('nssPereWrapper');
        if (ninPereWrapper) ninPereWrapper.style.display = 'none';
        if (nssPereWrapper) nssPereWrapper.style.display = 'none';
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

      // Remove required attribute from hidden fields to prevent HTML5 validation errors
      const fatherSelectWrapper = document.getElementById('fatherSelectWrapper');
      const motherSelectWrapper = document.getElementById('motherSelectWrapper');
      // Note: fatherSelect and motherSelect are already declared at top level
      
      if (fatherSelectWrapper && fatherSelectWrapper.style.display === 'none' && fatherSelect) {
        fatherSelect.required = false;
      }
      if (motherSelectWrapper && motherSelectWrapper.style.display === 'none' && motherSelect) {
        motherSelect.required = false;
      }

      // Reset state (clear any previous errors)
      form.querySelectorAll('.error-msg').forEach(e => e.remove());
      form.querySelectorAll('.is-invalid').forEach(e => e.classList.remove('is-invalid'));

      // === Submit form ===
      const formData = new FormData(form);
      
      // Get selected relation_tuteur from dropdown
      const relationSelect = form.querySelector('[name="relation_tuteur"]');
      const selectedRelation = relationSelect ? relationSelect.value : null;
      
      // Set relation_tuteur, mother_id, and father_id based on selected relation
      // Note: motherSelect and fatherSelect are already declared above
      if (selectedRelation === '1') {
        // الولي (الأب): Set mother_id, relation_tuteur = 1, no father_id
        if (motherSelect && motherSelect.value) {
          formData.set('mother_id', motherSelect.value);
        }
        formData.set('relation_tuteur', '1');
        // Remove father_id if it exists
        formData.delete('father_id');
      } else if (selectedRelation === '2') {
        // الولي (الأم): Set father_id, relation_tuteur = 2, no mother_id
        if (fatherSelect && fatherSelect.value) {
          formData.set('father_id', fatherSelect.value);
        }
        formData.set('relation_tuteur', '2');
        // Remove mother_id if it exists
        formData.delete('mother_id');
      } else if (selectedRelation === '3') {
        // وصي: Set both mother_id and father_id, relation_tuteur = 3
        if (motherSelect && motherSelect.value) {
          formData.set('mother_id', motherSelect.value);
        }
        if (fatherSelect && fatherSelect.value) {
          formData.set('father_id', fatherSelect.value);
        }
        formData.set('relation_tuteur', '3');
      }
      
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
            
            // Handle validation errors (422 status)
            if (response.status === 422 && errorData.errors) {
              // Display validation errors on form fields
              const errors = errorData.errors;
              
              // Clear previous errors
              form.querySelectorAll('.error-msg').forEach(e => e.remove());
              form.querySelectorAll('.is-invalid').forEach(e => e.classList.remove('is-invalid'));
              
              // Display errors on corresponding fields
              Object.keys(errors).forEach(fieldName => {
                const field = form.querySelector(`[name="${fieldName}"]`);
                if (field) {
                  field.classList.add('is-invalid');
                  const errorMsg = document.createElement('div');
                  errorMsg.className = 'error-msg text-danger small mt-1';
                  errorMsg.textContent = errors[fieldName][0]; // Show first error message
                  field.parentElement.appendChild(errorMsg);
                }
              });
              
              // Scroll to first error
              const firstErrorField = form.querySelector('.is-invalid');
              if (firstErrorField) {
                firstErrorField.scrollIntoView({ behavior: 'smooth', block: 'center' });
              }
              
              // Show general error message
              const errorMessages = Object.values(errors).flat();
              errorMessage = errorMessages.join('\n');
              Swal.fire('خطأ في التحقق من البيانات', errorMessage, 'error');
              return;
            }
            
            // Handle other errors
            if (errorData.message) {
              errorMessage = errorData.message;
            } else if (errorData.errors) {
              const errorMessages = Object.values(errorData.errors).flat();
              errorMessage = errorMessages.join('\n');
            }
          } catch (e) {
            // Error parsing response
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

    // Global function to open view modal (same structure as edit, read-only)
    window.openViewModal = async function(num_scolaire) {
      try {
        // Open modal first
        const modal = new bootstrap.Modal(document.getElementById('viewChildModal'));
        modal.show();
        customOverlay.style.display = 'block';
        
        // Show step 1 first
        const viewStep1 = document.getElementById('viewStep1');
        const viewStep2 = document.getElementById('viewStep2');
        viewStep1.classList.remove('d-none');
        viewStep2.classList.add('d-none');
        
        // Use same API endpoint as edit
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
        
        // Get tuteur role for conditional display
        const relationTuteur = window.currentUserRelationTuteur;
        
        // ===== STEP 1: School Info =====
        if (eleve.etablissement) {
          document.getElementById('view_type_ecole').value = eleve.etablissement.nature_etablissement || '—';
          document.getElementById('view_etablissement').value = eleve.etablissement.nom_etabliss || '—';
        } else {
          document.getElementById('view_type_ecole').value = '—';
          document.getElementById('view_etablissement').value = '—';
        }
        document.getElementById('view_niveau').value = eleve.niv_scol || '—';
        
        // Get wilaya and commune names
        if (eleve.commune_residence) {
          try {
            const wilayasRes = await apiFetch('/api/wilayas');
            if (wilayasRes.ok) {
              const wilayas = await wilayasRes.json();
              const wilaya = wilayas.find(w => w.code_wil === eleve.commune_residence.code_wilaya);
              document.getElementById('view_wilaya').value = wilaya ? wilaya.lib_wil_ar : `ولاية ${eleve.commune_residence.code_wilaya}`;
            }
          } catch (err) {
            document.getElementById('view_wilaya').value = eleve.commune_residence.code_wilaya ? `ولاية ${eleve.commune_residence.code_wilaya}` : '—';
          }
          document.getElementById('view_commune').value = eleve.commune_residence.lib_comm_ar || '—';
        } else {
          document.getElementById('view_wilaya').value = '—';
          document.getElementById('view_commune').value = '—';
        }
        
        // ===== STEP 2: Student Info =====
        document.getElementById('view_num_scolaire').value = eleve.num_scolaire || '—';
        document.getElementById('view_nom').value = eleve.nom || '—';
        document.getElementById('view_prenom').value = eleve.prenom || '—';
        document.getElementById('view_date_naiss').value = eleve.date_naiss || '—';
        document.getElementById('view_classe_scol').value = eleve.classe_scol || '—';
        document.getElementById('view_sexe').value = eleve.sexe || '—';
        
        // Convert relation_tuteur integer to text for display
        let relationText = '—';
        if (eleve.relation_tuteur === 1 || eleve.relation_tuteur === '1') {
          relationText = 'الولي (الأب)';
        } else if (eleve.relation_tuteur === 2 || eleve.relation_tuteur === '2') {
          relationText = 'الولي (الأم)';
        } else if (eleve.relation_tuteur === 3 || eleve.relation_tuteur === '3') {
          relationText = 'وصي';
        }
        document.getElementById('view_relation_tuteur').value = relationText;
        
        // Handicap
        const handicapValue = eleve.handicap === '1' || eleve.handicap === 1;
        if (handicapValue) {
          document.getElementById('view_handicapYes').checked = true;
          document.getElementById('view_handicapNo').checked = false;
          // Show handicap details
          document.getElementById('view_handicapNatureWrapper').style.display = 'block';
          document.getElementById('view_handicapPercentageWrapper').style.display = 'block';
          document.getElementById('view_handicap_nature').value = eleve.handicap_nature || '—';
          document.getElementById('view_handicap_percentage').value = eleve.handicap_percentage || '—';
        } else {
          document.getElementById('view_handicapYes').checked = false;
          document.getElementById('view_handicapNo').checked = true;
          document.getElementById('view_handicapNatureWrapper').style.display = 'none';
          document.getElementById('view_handicapPercentageWrapper').style.display = 'none';
        }
        
        // Birth place
        if (eleve.commune_naissance) {
            try {
            const wilayasRes = await apiFetch('/api/wilayas');
              if (wilayasRes.ok) {
                const wilayas = await wilayasRes.json();
              const wilaya = wilayas.find(w => w.code_wil === eleve.commune_naissance.code_wilaya);
              document.getElementById('view_wilaya_naiss').value = wilaya ? wilaya.lib_wil_ar : `ولاية ${eleve.commune_naissance.code_wilaya}`;
              }
            } catch (err) {
            document.getElementById('view_wilaya_naiss').value = eleve.commune_naissance.code_wilaya ? `ولاية ${eleve.commune_naissance.code_wilaya}` : '—';
          }
          document.getElementById('view_commune_naiss').value = eleve.commune_naissance.lib_comm_ar || '—';
        } else {
          document.getElementById('view_wilaya_naiss').value = '—';
          document.getElementById('view_commune_naiss').value = '—';
        }
        
        // ===== Conditional Fields Based on Role =====
        const viewMotherSelectWrapper = document.getElementById('view_motherSelectWrapper');
        const viewFatherInfoWrapper = document.getElementById('view_fatherInfoWrapper');
        const viewNomPereWrapper = document.getElementById('view_nomPereWrapper');
        const viewPrenomPereWrapper = document.getElementById('view_prenomPereWrapper');
        const viewNomPereLabel = document.getElementById('view_nomPereLabel');
        const viewPrenomPereLabel = document.getElementById('view_prenomPereLabel');
        
        // Father info
        if (eleve.father) {
          const fatherName = `${eleve.father.prenom_ar || ''} ${eleve.father.nom_ar || ''}`.trim();
          document.getElementById('view_nom_pere').value = eleve.father.nom_ar || '—';
          document.getElementById('view_prenom_pere').value = eleve.father.prenom_ar || '—';
          document.getElementById('view_fatherNameDisplay').value = fatherName || '—';
        } else {
          document.getElementById('view_nom_pere').value = '—';
          document.getElementById('view_prenom_pere').value = '—';
          document.getElementById('view_fatherNameDisplay').value = '—';
        }
        
        // Mother info
        if (eleve.mother) {
          const motherName = `${eleve.mother.prenom_ar || ''} ${eleve.mother.nom_ar || ''}`.trim();
          document.getElementById('view_motherName').value = motherName || '—';
        } else {
          document.getElementById('view_motherName').value = '—';
        }
        
        // Role-based conditional display (same logic as edit)
        if (relationTuteur === '2' || relationTuteur === 2) {
          // Mother role: Hide mother dropdown, show father info, change labels
          if (viewMotherSelectWrapper) viewMotherSelectWrapper.style.display = 'none';
          if (viewFatherInfoWrapper) viewFatherInfoWrapper.style.display = 'block';
          if (viewNomPereLabel) viewNomPereLabel.textContent = 'لقب الأم بالعربية';
          if (viewPrenomPereLabel) viewPrenomPereLabel.textContent = 'اسم الأم بالعربية';
          
          // Show father NIN/NSS
          document.getElementById('view_ninPereWrapper').style.display = 'block';
          document.getElementById('view_nssPereWrapper').style.display = 'block';
          if (eleve.father) {
            document.getElementById('view_ninPere').value = eleve.father.nin || '—';
            document.getElementById('view_nssPere').value = eleve.father.nss || '—';
          }
          
          // Show mother (tuteur) NIN/NSS
          document.getElementById('view_ninMereWrapper').style.display = 'block';
          document.getElementById('view_nssMereWrapper').style.display = 'block';
          if (window.currentUserNIN) document.getElementById('view_ninMere').value = window.currentUserNIN;
          if (window.currentUserNSS) document.getElementById('view_nssMere').value = window.currentUserNSS;
        } else if (relationTuteur === '3' || relationTuteur === 3) {
          // Guardian role: Show mother dropdown and father info
          if (viewMotherSelectWrapper) viewMotherSelectWrapper.style.display = 'block';
          if (viewFatherInfoWrapper) viewFatherInfoWrapper.style.display = 'block';
          
          // Show father NIN/NSS
          document.getElementById('view_ninPereWrapper').style.display = 'block';
          document.getElementById('view_nssPereWrapper').style.display = 'block';
          if (eleve.father) {
            document.getElementById('view_ninPere').value = eleve.father.nin || '—';
            document.getElementById('view_nssPere').value = eleve.father.nss || '—';
          }
          
          // Show mother NIN/NSS
          document.getElementById('view_ninMereWrapper').style.display = 'block';
          document.getElementById('view_nssMereWrapper').style.display = 'block';
          if (eleve.mother) {
            document.getElementById('view_ninMere').value = eleve.mother.nin || '—';
            document.getElementById('view_nssMere').value = eleve.mother.nss || '—';
          }
          
          // Show guardian (tuteur) NIN/NSS
          document.getElementById('view_ninGuardianWrapper').style.display = 'block';
          document.getElementById('view_nssGuardianWrapper').style.display = 'block';
          if (window.currentUserNIN) document.getElementById('view_ninGuardian').value = window.currentUserNIN;
          if (window.currentUserNSS) document.getElementById('view_nssGuardian').value = window.currentUserNSS;
          
          // Update mother label
          const viewMotherSelectLabel = document.getElementById('view_motherSelectLabel');
          if (viewMotherSelectLabel) viewMotherSelectLabel.textContent = 'الأم';
        } else {
          // Father role (default): Show mother dropdown, hide father info
          if (viewMotherSelectWrapper) viewMotherSelectWrapper.style.display = 'block';
          if (viewFatherInfoWrapper) viewFatherInfoWrapper.style.display = 'none';
        }
        
      } catch (error) {
        // Error loading student data
        Swal.fire('خطأ', 'فشل تحميل بيانات التلميذ', 'error');
        const modal = bootstrap.Modal.getInstance(document.getElementById('viewChildModal'));
        if (modal) modal.hide();
      }
    };
    
    // View modal step navigation
    const viewStep1 = document.getElementById('viewStep1');
    const viewStep2 = document.getElementById('viewStep2');
    const viewNextStep = document.getElementById('viewNextStep');
    const viewPrevStep = document.getElementById('viewPrevStep');
    
    if (viewNextStep) {
      viewNextStep.addEventListener('click', () => {
        viewStep1.classList.add('d-none');
        viewStep2.classList.remove('d-none');
      });
    }
    
    if (viewPrevStep) {
      viewPrevStep.addEventListener('click', () => {
        viewStep2.classList.add('d-none');
        viewStep1.classList.remove('d-none');
      });
    }

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
          // Trigger change event to update dependent fields
          editRelationSelect.dispatchEvent(new Event('change'));
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
      await loadFathers();
      // Update edit form based on tuteur role
      updateFormForEditGuardianRole();
      
      // Add event listener for edit mother select (for role 1)
      const editMotherSelect = document.getElementById('editMotherSelect');
      if (editMotherSelect) {
        editMotherSelect.removeEventListener('change', editMotherSelect._changeHandler);
        editMotherSelect._changeHandler = function() {
          const selectedMotherId = this.value;
          const editRelationSelect = document.getElementById('edit_relation_tuteur');
          const selectedRelation = editRelationSelect ? editRelationSelect.value : null;
          
          // For role 1 (Father) or role 3 (Guardian), show and fill mother NIN/NSS when mother is selected
          if ((selectedRelation === '1' || selectedRelation === 1 || selectedRelation === '3' || selectedRelation === 3) && selectedMotherId && window.mothersData && window.mothersData.length > 0) {
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

      // Add event listener for edit father select (for role 2)
      const editFatherSelect = document.getElementById('editFatherSelect');
      if (editFatherSelect) {
        editFatherSelect.removeEventListener('change', editFatherSelect._changeHandler);
        editFatherSelect._changeHandler = function() {
          const selectedFatherId = this.value;
          const editRelationSelect = document.getElementById('edit_relation_tuteur');
          const selectedRelation = editRelationSelect ? editRelationSelect.value : null;
          
          // For role 2 (Mother) or role 3 (Guardian), show and fill father NIN/NSS when father is selected
          if ((selectedRelation === '2' || selectedRelation === 2 || selectedRelation === '3' || selectedRelation === 3) && selectedFatherId && window.fathersData && window.fathersData.length > 0) {
            const selectedFather = window.fathersData.find(f => f.id == selectedFatherId);
            if (selectedFather) {
              // Show father NIN/NSS fields
              const editNinPereWrapper = document.getElementById('edit_ninPereWrapper');
              const editNssPereWrapper = document.getElementById('edit_nssPereWrapper');
              const editNinPere = document.getElementById('edit_ninPere');
              const editNssPere = document.getElementById('edit_nssPere');
              
              if (editNinPereWrapper) editNinPereWrapper.style.display = 'block';
              if (editNssPereWrapper) editNssPereWrapper.style.display = 'block';
              
              // Fill father NIN and NSS
              if (editNinPere && selectedFather.nin) {
                editNinPere.value = selectedFather.nin;
              }
              if (editNssPere && selectedFather.nss) {
                editNssPere.value = selectedFather.nss;
              }
            }
          } else {
            // Hide father NIN/NSS if no father selected or not role 2
            const editNinPereWrapper = document.getElementById('edit_ninPereWrapper');
            const editNssPereWrapper = document.getElementById('edit_nssPereWrapper');
            if (editNinPereWrapper) editNinPereWrapper.style.display = 'none';
            if (editNssPereWrapper) editNssPereWrapper.style.display = 'none';
          }
        };
        editFatherSelect.addEventListener('change', editFatherSelect._changeHandler);
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

      // Remove required attribute from hidden fields to prevent HTML5 validation errors
      const editFatherSelectWrapper = document.getElementById('edit_fatherSelectWrapper');
      const editMotherSelectWrapper = document.getElementById('edit_motherSelectWrapper');
      const editFatherSelect = document.getElementById('editFatherSelect');
      const editMotherSelect = document.getElementById('editMotherSelect');
      
      if (editFatherSelectWrapper && editFatherSelectWrapper.style.display === 'none' && editFatherSelect) {
        editFatherSelect.required = false;
      }
      if (editMotherSelectWrapper && editMotherSelectWrapper.style.display === 'none' && editMotherSelect) {
        editMotherSelect.required = false;
      }

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

      // Get selected relation_tuteur from dropdown
      const editRelationSelect = editForm.querySelector('[name="relation_tuteur"]');
      const selectedRelation = editRelationSelect ? editRelationSelect.value : null;
      
      // Get mother and father selects
      const editMotherSelect = document.getElementById('editMotherSelect');
      const editFatherSelect = document.getElementById('editFatherSelect');

      // Convert FormData to JSON for API route (Laravel API routes work better with JSON)
      const jsonPayload = {};
      for (const [key, value] of formData.entries()) {
        // Skip Laravel-specific fields
        if (key !== '_token' && key !== '_method') {
          jsonPayload[key] = value || null;
        }
      }
      
      // Set relation_tuteur, mother_id, and father_id based on selected relation
      if (selectedRelation === '1') {
        // الولي (الأب): Set mother_id, relation_tuteur = 1, no father_id
        if (editMotherSelect && editMotherSelect.value) {
          jsonPayload['mother_id'] = editMotherSelect.value;
        } else {
          jsonPayload['mother_id'] = null;
        }
        jsonPayload['relation_tuteur'] = '1';
        jsonPayload['father_id'] = null;
      } else if (selectedRelation === '2') {
        // الولي (الأم): Set father_id, relation_tuteur = 2, no mother_id
        if (editFatherSelect && editFatherSelect.value) {
          jsonPayload['father_id'] = editFatherSelect.value;
        } else {
          jsonPayload['father_id'] = null;
        }
        jsonPayload['relation_tuteur'] = '2';
        jsonPayload['mother_id'] = null;
      } else if (selectedRelation === '3') {
        // وصي: Set both mother_id and father_id, relation_tuteur = 3
        if (editMotherSelect && editMotherSelect.value) {
          jsonPayload['mother_id'] = editMotherSelect.value;
        } else {
          jsonPayload['mother_id'] = null;
        }
        if (editFatherSelect && editFatherSelect.value) {
          jsonPayload['father_id'] = editFatherSelect.value;
        } else {
          jsonPayload['father_id'] = null;
        }
        jsonPayload['relation_tuteur'] = '3';
      }

      try {
        // Use API endpoint with apiFetch helper which handles authentication automatically
        // Send as JSON instead of FormData for better API compatibility
        const response = await apiFetch(`/api/eleves/${num_scolaire}`, {
          method: 'PUT',
          body: JSON.stringify(jsonPayload),
          headers: {
            'Content-Type': 'application/json'
          }
        });

        if (!response.ok) {
          let errorMessage = 'خطأ أثناء التحديث';
          try {
          const errorData = await response.json();
            if (errorData.message) {
              errorMessage = errorData.message;
            } else if (errorData.errors) {
              // Format validation errors
              const errorMessages = Object.values(errorData.errors).flat();
              errorMessage = errorMessages.join(', ');
            }
          } catch (e) {
            errorMessage = `خطأ ${response.status}: ${response.statusText}`;
          }
          throw new Error(errorMessage);
        }
        
        const responseData = await response.json();

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

  /* ===============================
     👩 Mothers & Father Info Management
  =============================== */
  
  // Show/hide action cards based on tuteur role
  function updateInfoCardsVisibility() {
    const role = window.currentUserRelationTuteur;
    const mothersCard = document.getElementById('mothersInfoCard');
    const fatherCard = document.getElementById('fatherInfoCard');
    
    // Role 1 (Father): Show both Mothers and Father Info
    // Role 2 (Mother): Show only Father Info (tuteur is the mother)
    // Role 3 (Guardian): Show both Mothers and Father Info (singular for mother)
    
    if (mothersCard) {
      if (role === '1' || role === 1 || role === '3' || role === 3) {
        mothersCard.style.display = 'block';
        
        // Update title based on role
        const titleEl = document.getElementById('mothersInfoCardTitle');
        const descEl = document.getElementById('mothersInfoCardDesc');
        if (titleEl && descEl) {
          if (role === '3' || role === 3) {
            titleEl.textContent = 'معلومات الأم';
            descEl.textContent = 'عرض وتعديل معلومات الأم';
          } else {
            titleEl.textContent = 'معلومات الأمهات';
            descEl.textContent = 'إدارة معلومات الأمهات';
          }
        }
      } else {
        mothersCard.style.display = 'none';
      }
    }
    
    if (fatherCard) {
      if (role === '2' || role === 2 || role === '3' || role === 3) {
        fatherCard.style.display = 'block';
      } else {
        fatherCard.style.display = 'none';
      }
    }
  }

  // Load mothers list
  async function loadMothersList() {
    const container = document.getElementById('mothersListContainer');
    if (!container) return;
    
    try {
      const response = await apiFetch('/api/mothers');
      
      if (!response.ok) {
        let errorMessage = 'حدث خطأ أثناء تحميل قائمة الأمهات';
        try {
          const errorData = await response.json();
          errorMessage = errorData.message || errorMessage;
        } catch (e) {
          errorMessage = `خطأ ${response.status}: ${response.statusText}`;
        }
        container.innerHTML = `<div class="alert alert-danger text-center">${errorMessage}</div>`;
        return;
      }
      
      const responseData = await response.json();
      
      // Handle different response formats (array or object with data property)
      const mothers = Array.isArray(responseData) ? responseData : (responseData.data || []);
      
      if (!Array.isArray(mothers)) {
        container.innerHTML = '<div class="alert alert-danger text-center">خطأ في تنسيق البيانات المستلمة</div>';
        return;
      }
      
      if (mothers.length === 0) {
        const role = window.currentUserRelationTuteur;
        const isRole3 = (role === '3' || role === 3);
        const message = isRole3 ? 'لا توجد معلومات أم مسجلة' : 'لا توجد أمهات مسجلة';
        container.innerHTML = `<div class="alert alert-info text-center">${message}</div>`;
        return;
      }
      
      let html = '<div class="table-responsive"><table class="table table-striped"><thead><tr>';
      html += '<th>الرقم الوطني</th><th>الاسم</th><th>الإجراءات</th></tr></thead><tbody>';
      
      mothers.forEach(mother => {
        html += `<tr>
          <td>${mother.nin}</td>
          <td>${mother.nom_ar} ${mother.prenom_ar}</td>
          <td>
            <button class="btn btn-sm btn-primary me-1" onclick="editMother(${mother.id})">
              <i class="fa-solid fa-edit"></i> تعديل
            </button>
            <button class="btn btn-sm btn-danger" onclick="deleteMother(${mother.id}, '${mother.nom_ar} ${mother.prenom_ar}')">
              <i class="fa-solid fa-trash"></i> حذف
            </button>
          </td>
        </tr>`;
      });
      
      html += '</tbody></table></div>';
      container.innerHTML = html;
    } catch (error) {
      container.innerHTML = `<div class="alert alert-danger text-center">حدث خطأ أثناء تحميل قائمة الأمهات: ${error.message}</div>`;
    }
  }

  // Load father info
  async function loadFatherInfo() {
    const container = document.getElementById('fatherInfoContainer');
    if (!container) return;
    
    try {
      // Get father_id from tuteur data
      const tuteurData = window.tuteurData;
      if (!tuteurData || !tuteurData.father_id) {
        container.innerHTML = '<div class="alert alert-info text-center">لا توجد معلومات للأب</div>';
        return;
      }
      
      const response = await apiFetch(`/api/fathers/${tuteurData.father_id}`);
      if (!response.ok) throw new Error('Failed to load father');
      
      const father = await response.json();
      
      let html = '<div class="card"><div class="card-body">';
      html += `<h6 class="card-title">معلومات الأب</h6>`;
      html += `<p><strong>الرقم الوطني:</strong> ${father.nin}</p>`;
      html += `<p><strong>رقم الضمان الاجتماعي:</strong> ${father.nss || 'غير محدد'}</p>`;
      html += `<p><strong>الاسم بالعربية:</strong> ${father.nom_ar} ${father.prenom_ar}</p>`;
      if (father.nom_fr || father.prenom_fr) {
        html += `<p><strong>الاسم بالفرنسية:</strong> ${father.nom_fr || ''} ${father.prenom_fr || ''}</p>`;
      }
      html += `<p><strong>الفئة الاجتماعية:</strong> ${father.categorie_sociale || 'غير محدد'}</p>`;
      html += `<p><strong>مبلغ الدخل الشهري:</strong> ${father.montant_s || 'غير محدد'}</p>`;
      html += `<button class="btn btn-primary mt-3" onclick="showEditFatherForm(${father.id})">
        <i class="fa-solid fa-edit me-2"></i>تعديل المعلومات
      </button>`;
      html += '</div></div>';
      
      container.innerHTML = html;
    } catch (error) {
      container.innerHTML = '<div class="alert alert-danger text-center">حدث خطأ أثناء تحميل معلومات الأب</div>';
    }
  }

  // Show edit father form
  async function showEditFatherForm(fatherId) {
    try {
      const response = await apiFetch(`/api/fathers/${fatherId}`);
      if (!response.ok) throw new Error('Failed to load father');
      
      const father = await response.json();
      
      // Fill form
      document.getElementById('fatherFormId').value = father.id;
      document.getElementById('father_nin').value = father.nin || '';
      document.getElementById('father_nss').value = father.nss || '';
      document.getElementById('father_nom_ar').value = father.nom_ar || '';
      document.getElementById('father_prenom_ar').value = father.prenom_ar || '';
      document.getElementById('father_nom_fr').value = father.nom_fr || '';
      document.getElementById('father_prenom_fr').value = father.prenom_fr || '';
      
      // Set categorie_sociale dropdown
      const categorieSelect = document.getElementById('father_categorie_sociale');
      const montantWrapper = document.getElementById('father_montant_wrapper');
      const montantInput = document.getElementById('father_montant_s');
      
      if (categorieSelect) {
        categorieSelect.value = father.categorie_sociale || '';
        
        // Show/hide montant based on categorie_sociale
        if (categorieSelect.value === 'الدخل الشهري أقل أو يساوي مبلغ الأجر الوطني الأدنى المضمون') {
          if (montantWrapper) montantWrapper.style.display = 'block';
          if (montantInput) montantInput.value = father.montant_s || '';
        } else {
          if (montantWrapper) montantWrapper.style.display = 'none';
          if (montantInput) montantInput.value = '';
        }
      }
      
      // Show form, hide info view
      document.getElementById('fatherInfoView').classList.add('d-none');
      document.getElementById('fatherFormContainer').classList.remove('d-none');
    } catch (error) {
      Swal.fire('خطأ', 'حدث خطأ أثناء تحميل معلومات الأب', 'error');
    }
  }

  // Edit mother
  async function editMother(motherId) {
    try {
      const response = await apiFetch(`/api/mothers/${motherId}`);
      if (!response.ok) throw new Error('Failed to load mother');
      
      const mother = await response.json();
      
      // Fill form
      document.getElementById('motherFormId').value = mother.id;
      document.getElementById('mother_nin').value = mother.nin || '';
      document.getElementById('mother_nss').value = mother.nss || '';
      document.getElementById('mother_nom_ar').value = mother.nom_ar || '';
      document.getElementById('mother_prenom_ar').value = mother.prenom_ar || '';
      document.getElementById('mother_nom_fr').value = mother.nom_fr || '';
      document.getElementById('mother_prenom_fr').value = mother.prenom_fr || '';
      
      // Set categorie_sociale dropdown
      const motherCategorieSelect = document.getElementById('mother_categorie_sociale');
      const motherMontantWrapper = document.getElementById('mother_montant_wrapper');
      const motherMontantInput = document.getElementById('mother_montant_s');
      
      if (motherCategorieSelect) {
        motherCategorieSelect.value = mother.categorie_sociale || '';
        
        // Show/hide montant based on categorie_sociale
        if (motherCategorieSelect.value === 'الدخل الشهري أقل أو يساوي مبلغ الأجر الوطني الأدنى المضمون') {
          if (motherMontantWrapper) motherMontantWrapper.style.display = 'block';
          if (motherMontantInput) motherMontantInput.value = mother.montant_s || '';
        } else {
          if (motherMontantWrapper) motherMontantWrapper.style.display = 'none';
          if (motherMontantInput) motherMontantInput.value = '';
        }
      }
      
      // Update form title
      document.getElementById('motherFormTitle').textContent = 'تعديل معلومات الأم';
      
      // Show form, hide list view
      document.getElementById('mothersListView').classList.add('d-none');
      document.getElementById('motherFormContainer').classList.remove('d-none');
      
      // Scroll to form
      document.getElementById('motherFormContainer').scrollIntoView({ behavior: 'smooth' });
    } catch (error) {
      Swal.fire('خطأ', 'حدث خطأ أثناء تحميل معلومات الأم', 'error');
    }
  }

  // Delete mother
  async function deleteMother(motherId, motherName) {
    const result = await Swal.fire({
      title: 'تأكيد الحذف',
      text: `هل أنت متأكد من حذف ${motherName}؟`,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'نعم، احذف',
      cancelButtonText: 'إلغاء',
      reverseButtons: true
    });
    
    if (!result.isConfirmed) return;
    
    try {
      const response = await apiFetch(`/api/mothers/${motherId}`, {
        method: 'DELETE'
      });
      
      if (!response.ok) {
        const error = await response.json();
        throw new Error(error.message || 'Failed to delete mother');
      }
      
      Swal.fire('نجح', 'تم حذف الأم بنجاح', 'success');
      loadMothersList();
    } catch (error) {
      Swal.fire('خطأ', error.message || 'حدث خطأ أثناء حذف الأم', 'error');
    }
  }

  // Event listeners for mothers modal
  document.addEventListener('DOMContentLoaded', function() {
    // Mothers modal events
    const mothersModal = document.getElementById('mothersInfoModal');
    if (mothersModal) {
      mothersModal.addEventListener('show.bs.modal', function() {
        // Update modal title and hide/show add button based on role
        const role = window.currentUserRelationTuteur;
        const isRole3 = (role === '3' || role === 3);
        const modalTitleEl = document.getElementById('mothersInfoModalTitle');
        const addBtnWrapper = document.getElementById('addMotherBtnWrapper');
        
        if (modalTitleEl) {
          modalTitleEl.textContent = isRole3 ? 'معلومات الأم' : 'معلومات الأمهات';
        }
        
        if (addBtnWrapper) {
          addBtnWrapper.style.display = isRole3 ? 'none' : 'block';
        }
        
        loadMothersList();
        document.getElementById('motherFormContainer').classList.add('d-none');
        document.getElementById('mothersListView').classList.remove('d-none');
      });
      
      document.getElementById('addMotherBtn')?.addEventListener('click', function() {
        // Reset form
        document.getElementById('motherForm').reset();
        document.getElementById('motherFormId').value = '';
        document.getElementById('motherFormTitle').textContent = 'إضافة أم جديدة';
        
        // Show form, hide list view
        document.getElementById('mothersListView').classList.add('d-none');
        document.getElementById('motherFormContainer').classList.remove('d-none');
        document.getElementById('motherFormContainer').scrollIntoView({ behavior: 'smooth' });
      });
      
      document.getElementById('cancelMotherFormBtn')?.addEventListener('click', function() {
        document.getElementById('motherFormContainer').classList.add('d-none');
        document.getElementById('mothersListView').classList.remove('d-none');
        loadMothersList();
      });
      
      // Handle categorie_sociale dropdown change for mother
      const motherCategorieSelect = document.getElementById('mother_categorie_sociale');
      const motherMontantWrapper = document.getElementById('mother_montant_wrapper');
      const motherMontantInput = document.getElementById('mother_montant_s');
      
      if (motherCategorieSelect && motherMontantWrapper && motherMontantInput) {
        motherCategorieSelect.addEventListener('change', function() {
          if (this.value === 'الدخل الشهري أقل أو يساوي مبلغ الأجر الوطني الأدنى المضمون') {
            motherMontantWrapper.style.display = 'block';
            motherMontantInput.required = true;
          } else {
            motherMontantWrapper.style.display = 'none';
            motherMontantInput.required = false;
            motherMontantInput.value = '';
          }
        });
      }
      
      // Add input restrictions for NIN and NSS fields
      const motherNinInput = document.getElementById('mother_nin');
      const motherNssInput = document.getElementById('mother_nss');
      const fatherNinInput = document.getElementById('father_nin');
      const fatherNssInput = document.getElementById('father_nss');
      
      // Only allow digits for NIN and NSS
      [motherNinInput, motherNssInput, fatherNinInput, fatherNssInput].forEach(input => {
        if (input) {
          input.addEventListener('keypress', function(e) {
            if (!/[0-9]/.test(e.key) && !['Backspace', 'Delete', 'Tab', 'ArrowLeft', 'ArrowRight'].includes(e.key)) {
              e.preventDefault();
            }
          });
          
          input.addEventListener('paste', function(e) {
            e.preventDefault();
            const paste = (e.clipboardData || window.clipboardData).getData('text');
            const digitsOnly = paste.replace(/\D/g, '');
            const maxLength = this.maxLength || (this.id.includes('nin') ? 18 : 12);
            this.value = digitsOnly.substring(0, maxLength);
          });
        }
      });
      
      document.getElementById('motherForm')?.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const motherId = document.getElementById('motherFormId').value;
        const isEdit = motherId !== '';
        
        // Validate NIN (must be 18 digits)
        const nin = document.getElementById('mother_nin').value.trim();
        if (nin.length !== 18 || !/^\d+$/.test(nin)) {
          Swal.fire('خطأ', 'الرقم الوطني للأم يجب أن يكون 18 رقمًا بالضبط', 'error');
          return;
        }
        
        // Validate NSS if provided (must be 12 digits)
        const nss = document.getElementById('mother_nss').value.trim();
        if (nss && (nss.length !== 12 || !/^\d+$/.test(nss))) {
          Swal.fire('خطأ', 'رقم الضمان الاجتماعي للأم يجب أن يكون 12 رقمًا بالضبط', 'error');
          return;
        }
        
        const motherCategorieValue = document.getElementById('mother_categorie_sociale').value;
        const motherMontantValue = document.getElementById('mother_montant_s').value;
        
        const data = {
          nin: nin,
          nss: nss || null,
          nom_ar: document.getElementById('mother_nom_ar').value,
          prenom_ar: document.getElementById('mother_prenom_ar').value,
          nom_fr: document.getElementById('mother_nom_fr').value || null,
          prenom_fr: document.getElementById('mother_prenom_fr').value || null,
          categorie_sociale: motherCategorieValue || null,
          montant_s: (motherCategorieValue === 'الدخل الشهري أقل أو يساوي مبلغ الأجر الوطني الأدنى المضمون') ? (motherMontantValue || null) : null
        };
        
        try {
          const url = isEdit ? `/api/mothers/${motherId}` : '/api/mothers';
          const method = isEdit ? 'PUT' : 'POST';
          
          const response = await apiFetch(url, {
            method: method,
            body: JSON.stringify(data),
            headers: {
              'Content-Type': 'application/json'
            }
          });
          
          if (!response.ok) {
            const error = await response.json();
            throw new Error(error.message || 'Failed to save mother');
          }
          
          Swal.fire('نجح', isEdit ? 'تم تحديث الأم بنجاح' : 'تم إضافة الأم بنجاح', 'success');
          loadMothersList();
          document.getElementById('motherFormContainer').classList.add('d-none');
          document.getElementById('mothersListView').classList.remove('d-none');
          this.reset();
        } catch (error) {
          Swal.fire('خطأ', error.message || 'حدث خطأ أثناء حفظ الأم', 'error');
        }
      });
    }
    
    // Father modal events
    const fatherModal = document.getElementById('fatherInfoModal');
    if (fatherModal) {
      fatherModal.addEventListener('show.bs.modal', function() {
        loadFatherInfo();
        document.getElementById('fatherFormContainer').classList.add('d-none');
        document.getElementById('fatherInfoView').classList.remove('d-none');
      });
      
      document.getElementById('cancelFatherFormBtn')?.addEventListener('click', function() {
        document.getElementById('fatherFormContainer').classList.add('d-none');
        document.getElementById('fatherInfoView').classList.remove('d-none');
        loadFatherInfo();
      });
      
      // Handle categorie_sociale dropdown change for father
      const fatherCategorieSelect = document.getElementById('father_categorie_sociale');
      const fatherMontantWrapper = document.getElementById('father_montant_wrapper');
      const fatherMontantInput = document.getElementById('father_montant_s');
      
      if (fatherCategorieSelect && fatherMontantWrapper && fatherMontantInput) {
        fatherCategorieSelect.addEventListener('change', function() {
          if (this.value === 'الدخل الشهري أقل أو يساوي مبلغ الأجر الوطني الأدنى المضمون') {
            fatherMontantWrapper.style.display = 'block';
            fatherMontantInput.required = true;
          } else {
            fatherMontantWrapper.style.display = 'none';
            fatherMontantInput.required = false;
            fatherMontantInput.value = '';
          }
        });
      }
      
      document.getElementById('fatherForm')?.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const fatherId = document.getElementById('fatherFormId').value;
        
        // Validate NIN (must be 18 digits)
        const nin = document.getElementById('father_nin').value.trim();
        if (nin.length !== 18 || !/^\d+$/.test(nin)) {
          Swal.fire('خطأ', 'الرقم الوطني للأب يجب أن يكون 18 رقمًا بالضبط', 'error');
          return;
        }
        
        // Validate NSS if provided (must be 12 digits)
        const nss = document.getElementById('father_nss').value.trim();
        if (nss && (nss.length !== 12 || !/^\d+$/.test(nss))) {
          Swal.fire('خطأ', 'رقم الضمان الاجتماعي للأب يجب أن يكون 12 رقمًا بالضبط', 'error');
          return;
        }
        
        const categorieValue = document.getElementById('father_categorie_sociale').value;
        const montantValue = document.getElementById('father_montant_s').value;
        
        const data = {
          nin: nin,
          nss: nss || null,
          nom_ar: document.getElementById('father_nom_ar').value,
          prenom_ar: document.getElementById('father_prenom_ar').value,
          nom_fr: document.getElementById('father_nom_fr').value || null,
          prenom_fr: document.getElementById('father_prenom_fr').value || null,
          categorie_sociale: categorieValue || null,
          montant_s: (categorieValue === 'الدخل الشهري أقل أو يساوي مبلغ الأجر الوطني الأدنى المضمون') ? (montantValue || null) : null
        };
        
        try {
          const response = await apiFetch(`/api/fathers/${fatherId}`, {
            method: 'PUT',
            body: JSON.stringify(data),
            headers: {
              'Content-Type': 'application/json'
            }
          });
          
          if (!response.ok) {
            const error = await response.json();
            throw new Error(error.message || 'Failed to update father');
          }
          
          Swal.fire('نجح', 'تم تحديث معلومات الأب بنجاح', 'success');
          loadFatherInfo();
          document.getElementById('fatherFormContainer').classList.add('d-none');
          document.getElementById('fatherInfoView').classList.remove('d-none');
        } catch (error) {
          Swal.fire('خطأ', error.message || 'حدث خطأ أثناء تحديث الأب', 'error');
        }
      });
    }
  });
</script>

@endsection
