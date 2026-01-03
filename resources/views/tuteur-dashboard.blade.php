@extends('layouts.main')

@section('title', 'لوحة الوصي/الولي')

@push('styles')
@vite(['resources/css/tuteur-dashboard.css'])
<!-- SweetAlert2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
@endpush

@section('content')
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
                        console.error('Logout API error:', error);
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

                    <div class="col-md-4">
                      <label class="form-label fw-bold">هل هو يتيم؟</label>
                      <input type="text" id="view_orphelin" class="form-control" readonly>
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
                      <label class="form-label fw-bold">الرقم الوطني للضمان الاجتماعي للأب (NSS)</label>
                      <input type="text" id="view_nss_pere" class="form-control" readonly>
                    </div>
                    <div class="col-md-6">
                      <label class="form-label fw-bold">الرقم الوطني للضمان الاجتماعي للأم (NSS)</label>
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
                        <option value="متخصصة">متخصصة</option>
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
                    <div class="col-md-6">
                      <label class="form-label fw-bold required">لقب الأب بالعربية</label>
                      <input type="text" name="nom_pere" id="edit_nom_pere" class="form-control" dir="rtl" required>
                    </div>
                    <div class="col-md-6">
                      <label class="form-label fw-bold required">اسم الأب بالعربية</label>
                      <input type="text" name="prenom_pere" id="edit_prenom_pere" class="form-control" dir="rtl" required>
                    </div>

                    <div class="col-md-12">
                      <label class="form-label fw-bold required">الأم/الزوجة</label>
                      <select name="mother_id" id="editMotherSelect" class="form-select" required>
                        <option value="">اختر الأم/الزوجة...</option>
                      </select>
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

                    <div class="col-md-4">
                      <label class="form-label fw-bold required"> صفة طالب المنحة</label>
                      <select name="relation_tuteur" id="edit_relation_tuteur" class="form-select" required>
                          <option value="">اختر...</option>
                          <option value="ولي">ولي</option>
                          <option value="وصي">وصي</option>
                      </select>
                    </div>

                    <!-- الحالة الاجتماعية -->
                    <div class="col-md-4 d-flex align-items-center justify-content-end pe-0">
                      <label class="form-label fw-bold mb-0 ms-2">فئة ذوي الاحتياجات الخاصة؟</label>
                      <div class="d-flex align-items-center gap-3">
                      <div class="form-check mb-0 d-flex align-items-center">
                          <input class="form-check-input ms-2" type="radio" name="handicap" value="1" id="edit_handicapYes">
                          <label class="form-check-label" for="edit_handicapYes">نعم</label>
                        </div>
                        <div class="form-check mb-0 d-flex align-items-center">
                          <input class="form-check-input ms-2" type="radio" name="handicap" value="0" id="edit_handicapNo" checked>
                          <label class="form-check-label" for="edit_handicapNo">لا</label>
                        </div>
                      </div>
                    </div>

                    <div class="col-md-4 d-flex align-items-center justify-content-end pe-0">
                      <label class="form-label fw-bold mb-0 ms-2">هل هو يتيم؟</label>
                      <div class="d-flex align-items-center gap-3">
                      <div class="form-check mb-0 d-flex align-items-center">
                          <input class="form-check-input ms-2" type="radio" name="orphelin" value="1" id="edit_orphelinYes">
                          <label class="form-check-label" for="edit_orphelinYes">نعم</label>
                      </div>
                        <div class="form-check mb-0 d-flex align-items-center">
                          <input class="form-check-input ms-2" type="radio" name="orphelin" value="0" id="edit_orphelinNo" checked>
                          <label class="form-check-label" for="edit_orphelinNo">لا</label>
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

                    <!-- NIN + NSS -->
                    <div class="col-md-6">
                      <label class="form-label fw-bold">الرقم الوطني للأب (NIN)</label>
                      <input type="text" name="nin_pere" id="edit_nin_pere" class="form-control" maxlength="18" minlength="18" pattern="\d{18}">
                    </div>
                    <div class="col-md-6">
                      <label class="form-label fw-bold">الرقم الوطني للأم (NIN)</label>
                      <input type="text" name="nin_mere" id="edit_nin_mere" class="form-control" maxlength="18" minlength="18" pattern="\d{18}">
                    </div>

                    <div class="col-md-6">
                      <label class="form-label fw-bold">الرقم الوطني للضمان الاجتماعي للأب (NSS)</label>
                      <input type="text" name="nss_pere" id="edit_nss_pere" class="form-control" maxlength="12" minlength="12" pattern="\d{12}">
                    </div>
                    <div class="col-md-6">
                      <label class="form-label fw-bold">الرقم الوطني للضمان الاجتماعي للأم (NSS)</label>
                      <input type="text" name="nss_mere" id="edit_nss_mere" class="form-control" maxlength="12" minlength="12" pattern="\d{12}">
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
                        <option value="متخصصة">متخصصة</option>
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
                    <!-- 🆔 الرقم التعريفي المدرسي -->
                    <div class="col-md-6">
                    <label class="form-label fw-bold required">الرقم التعريفي المدرسي</label>
                    <input type="text" name="num_scolaire" class="form-control" maxlength="16" minlength="16" pattern="\d{16}" placeholder="16 رقمًا" required>
                    </div>

                    <!-- الاسم واللقب -->
                    <div class="col-md-6">
                      <label class="form-label fw-bold required">اللقب بالعربية</label>
                      <input type="text" name="nom" class="form-control" dir="rtl" required>
                    </div>
                    <div class="col-md-6">
                      <label class="form-label fw-bold required">الاسم بالعربية</label>
                      <input type="text" name="prenom" class="form-control" dir="rtl" required>
                    </div>

                    <!-- الأب والأم -->
                    <div class="col-md-6">
                      <label class="form-label fw-bold required">لقب الأب بالعربية</label>
                      <input type="text" name="nom_pere" class="form-control" dir="rtl" required>
                    </div>
                    <div class="col-md-6">
                      <label class="form-label fw-bold required">اسم الأب بالعربية</label>
                      <input type="text" name="prenom_pere" class="form-control" dir="rtl" required>
                    </div>

                    <div class="col-md-12">
                      <label class="form-label fw-bold required">الأم/الزوجة</label>
                      <select name="mother_id" id="motherSelect" class="form-select" required>
                        <option value="">اختر الأم/الزوجة...</option>
                      </select>
                      <small class="text-muted">إذا لم تجد الأم/الزوجة، يرجى إضافتها أولاً من حسابك</small>
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

                    <div class="col-md-4">
                      <label class="form-label fw-bold required"> صفة طالب المنحة</label>
                      <select name="relation_tuteur" class="form-select" required>
                          <option value="">اختر...</option>
                          <option value="ولي">ولي</option>
                          <option value="وصي">وصي</option>
                      </select>
                    </div>

                    <!-- الحالة الاجتماعية -->
                    <div class="col-md-4 d-flex align-items-center justify-content-end pe-0">
                      <label class="form-label fw-bold mb-0 ms-2">فئة ذوي الاحتياجات الخاصة؟</label>
                      <div class="d-flex align-items-center gap-3">
                      <div class="form-check mb-0 d-flex align-items-center">
                          <input class="form-check-input ms-2" type="radio" name="handicap" value="1" id="handicapYes">
                          <label class="form-check-label" for="handicapYes">نعم</label>
                        </div>
                        <div class="form-check mb-0 d-flex align-items-center">
                          <input class="form-check-input ms-2" type="radio" name="handicap" value="0" id="handicapNo" checked>
                          <label class="form-check-label" for="handicapNo">لا</label>
                        </div>
                      </div>
                    </div>

                    <div class="col-md-4 d-flex align-items-center justify-content-end pe-0">
                      <label class="form-label fw-bold mb-0 ms-2">هل هو يتيم؟</label>
                      <div class="d-flex align-items-center gap-3">
                      <div class="form-check mb-0 d-flex align-items-center">
                          <input class="form-check-input ms-2" type="radio" name="orphelin" value="1" id="orphelinYes">
                          <label class="form-check-label" for="orphelinYes">نعم</label>
                      </div>
                        <div class="form-check mb-0 d-flex align-items-center">
                          <input class="form-check-input ms-2" type="radio" name="orphelin" value="0" id="orphelinNo" checked>
                          <label class="form-check-label" for="orphelinNo">لا</label>
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

                    <!-- NIN + NSS -->
                    <div class="col-md-6">
                      <label class="form-label fw-bold">الرقم الوطني للأب (NIN)</label>
                      <input type="text" name="nin_pere" class="form-control" maxlength="18" minlength="18" pattern="\d{18}">
                    </div>

                    <div class="col-md-6">
                      <label class="form-label fw-bold">الرقم الوطني للضمان الاجتماعي للأب (NSS)</label>
                      <input type="text" name="nss_pere" class="form-control" maxlength="12" minlength="12" pattern="\d{12}">
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
    
    // Log request details for debugging
    console.log('apiFetch:', {
      url,
      method: options.method || 'GET',
      hasToken: !!localStorage.getItem('api_token'),
      hasBody: !!options.body,
      isFormData: options.body instanceof FormData,
      authHeader: mergedHeaders['Authorization'] ? mergedHeaders['Authorization'].substring(0, 30) + '...' : 'Missing'
    });
    
    const response = await fetch(url, {
      ...options,
      headers: mergedHeaders,
    });
    
    console.log('apiFetch response:', {
      status: response.status,
      ok: response.ok,
      url: response.url
    });
    
    // If unauthorized, check if it's an authentication error
    if (response.status === 401) {
      // Clone response to read body without consuming it
      const clonedResponse = response.clone();
      try {
        const data = await clonedResponse.json();
        console.error('401 Response data:', data);
        
        // Only logout if it's an authentication error (not validation)
        const isAuthError = data.error === 'Authentication required' || 
                           data.message?.includes('Token') || 
                           data.message?.includes('Unauthorized') ||
                           data.message?.includes('Invalid token') ||
                           data.message?.includes('expired') ||
                           data.message?.includes('Token required');
        
        if (isAuthError) {
          console.error('Authentication error detected, logging out...');
          localStorage.removeItem('api_token');
          localStorage.removeItem('token_type');
          window.location.href = '/login';
          return response;
        } else {
          console.warn('401 but not auth error, might be validation:', data);
        }
      } catch (e) {
        // If we can't parse JSON, it might be HTML error page
        console.error('Could not parse 401 response:', e);
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
     👤 Load Mothers for Tuteur
  =============================== */
  async function loadMothers() {
    try {
      const token = localStorage.getItem('tuteur_token');
      if (!token) {
        console.error('No token found');
        return;
      }

      const response = await fetch('/api/tuteurs/mothers', {
        headers: {
          'Authorization': `Bearer ${token}`,
          'Accept': 'application/json'
        }
      });

      if (!response.ok) {
        throw new Error('Failed to load mothers');
      }

      const mothers = await response.json();
      const motherSelect = document.getElementById('motherSelect');
      const editMotherSelect = document.getElementById('editMotherSelect');
      
      // Clear existing options except the first one
      if (motherSelect) {
        motherSelect.innerHTML = '<option value="">اختر الأم/الزوجة...</option>';
        mothers.forEach(mother => {
          const option = document.createElement('option');
          option.value = mother.id;
          option.textContent = `${mother.prenom_ar} ${mother.nom_ar}`;
          motherSelect.appendChild(option);
        });
      }

      if (editMotherSelect) {
        editMotherSelect.innerHTML = '<option value="">اختر الأم/الزوجة...</option>';
        mothers.forEach(mother => {
          const option = document.createElement('option');
          option.value = mother.id;
          option.textContent = `${mother.prenom_ar} ${mother.nom_ar}`;
          editMotherSelect.appendChild(option);
        });
      }
    } catch (error) {
      console.error('Error loading mothers:', error);
    }
  }

  /* ===============================
     👤 Load Tuteur Data via API
  =============================== */
  async function loadTuteurData() {
    try {
      const nin = window.currentUserNIN || "{{ session('tuteur.nin') }}";
      if (!nin) {
        console.warn('No NIN available to fetch tuteur data');
        return;
      }

      const response = await apiFetch(`/api/tuteurs/${nin}`);
      if (response.ok) {
        const tuteurData = await response.json();
        
        // Update window variables with complete data from API
        if (tuteurData.nin) window.currentUserNIN = tuteurData.nin;
        if (tuteurData.nss) window.currentUserNSS = tuteurData.nss;
        if (tuteurData.sexe) window.currentUserSexe = tuteurData.sexe;
        
        console.log('Tuteur data loaded:', {
          nin: window.currentUserNIN,
          hasNSS: !!window.currentUserNSS,
          sexe: window.currentUserSexe
        });
      } else {
        console.warn('Failed to load tuteur data from API');
      }
    } catch (error) {
      console.error('Error loading tuteur data:', error);
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
        console.error('No NIN available to load children');
        tableBody.innerHTML = '<tr><td colspan="5" class="loading-message">خطأ: لا يمكن تحديد الهوية</td></tr>';
        return;
      }

      const response = await apiFetch(`/api/tuteur/${nin}/eleves`);
      
      // Check if response is JSON
      const contentType = response.headers.get('content-type');
      if (!contentType || !contentType.includes('application/json')) {
        const text = await response.text();
        console.error('Non-JSON response received:', text.substring(0, 200));
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
        console.error('Failed to load children:', response.status, response.statusText, responseData);
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
      console.error(error);
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


  // When modal opens → load wilayas and show dark overlay
  const addChildModal = document.getElementById('addChildModal');
  const customOverlay = document.getElementById('customModalOverlay');
  
  // Hide Bootstrap's default backdrop
  const style = document.createElement('style');
  style.textContent = '.modal-backdrop { display: none !important; }';
  document.head.appendChild(style);
  
  addChildModal.addEventListener('show.bs.modal', async () => {
    customOverlay.style.display = 'block';
    await loadWilayasGeneric(wilayaSelect, communeSelect);
    await loadWilayasGeneric(wilayaNaiss, communeNaiss);
    await loadMothers();
    
    // Check if all school selection fields are already filled and load schools
    setTimeout(() => {
      if (typeSelect && niveauSelect && communeSelect && ecoleSelect) {
        if (typeSelect.value && niveauSelect.value && communeSelect.value) {
          console.log('All fields selected on modal open, loading schools...');
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
    try {
      wilayaSelectEl.innerHTML = '<option value="">جارٍ التحميل...</option>';
      const res = await apiFetch('/api/wilayas');
      const responseData = await res.json();
      
      // Handle response structure: could be array directly or wrapped in {data: [...]}
      const wilayas = Array.isArray(responseData) ? responseData : (responseData.data || []);

      wilayaSelectEl.innerHTML = '<option value="">اختر...</option>';
      if (Array.isArray(wilayas)) {
      wilayas.forEach(w => {
        wilayaSelectEl.innerHTML += `<option value="${w.code_wil}">${w.lib_wil_ar}</option>`;
      });
      }

      // 🏙️ When wilaya changes → load communes dynamically
      wilayaSelectEl.addEventListener('change', async (e) => {
        const wilayaCode = e.target.value;
        communeSelectEl.innerHTML = '<option value="">جارٍ التحميل...</option>';
        communeSelectEl.disabled = true;

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
          console.error('خطأ في تحميل البلديات:', err);
          communeSelectEl.innerHTML = '<option value="">تعذر تحميل البيانات</option>';
        }
      });

    } catch (err) {
      console.error('خطأ في تحميل الولايات:', err);
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
      console.error('⚠️ خطأ في تحميل البلديات:', err);
      communeSelectEl.innerHTML = '<option value="">تعذر تحميل البيانات</option>';
    }
  }
  wilayaSelect.addEventListener('change', () => handleWilayaChange(wilayaSelect, communeSelect, ecoleSelect));
  wilayaNaiss.addEventListener('change', () => handleWilayaChange(wilayaNaiss, communeNaiss));

  /* 🟢 Load établissements dynamically when commune + niveau + type are selected */
  if (typeSelect && niveauSelect && communeSelect) {
    [typeSelect, niveauSelect, communeSelect].forEach(sel => {
      if (sel) {
        sel.addEventListener('change', loadEtablissements);
      }
    });
  } else {
    console.error('Missing select elements:', { typeSelect, niveauSelect, communeSelect, ecoleSelect });
  }

  async function loadEtablissements() {
    const code_commune = communeSelect.value;
    const niveau = niveauSelect.value;
    const nature = typeSelect.value;

    console.log('loadEtablissements called with:', { code_commune, niveau, nature });

    // Make sure all are chosen - disable and show message if any is missing
    if (!code_commune || !niveau || !nature) {
      console.log('Missing fields, disabling school dropdown');
      ecoleSelect.innerHTML = '<option value="">اختر كل المعايير أولا (مؤسسة التربية والتعليم، المستوى الدراسي، البلدية)</option>';
      ecoleSelect.disabled = true;
      return;
    }

    ecoleSelect.innerHTML = '<option value="">جارٍ التحميل...</option>';
    ecoleSelect.disabled = true;

    try {
      const url = `/api/etablissements?code_commune=${code_commune}&niveau=${encodeURIComponent(niveau)}&nature=${encodeURIComponent(nature)}`;
      console.log('Fetching URL:', url);
      const res = await fetch(url);

      console.log('Response status:', res.status, res.statusText);

      if (!res.ok) {
        const errorText = await res.text();
        console.error('API Error:', errorText);
        ecoleSelect.innerHTML = '<option value="">لم يتم العثور على مؤسسات</option>';
        ecoleSelect.disabled = true;
        return;
      }

      const responseData = await res.json();
      
      // Handle response structure: could be array directly or wrapped in {data: [...]}
      const etabs = Array.isArray(responseData) ? responseData : (responseData.data || []);
      
      console.log('Received schools:', etabs);

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
      console.log('School dropdown populated successfully');
    } catch (err) {
      console.error('خطأ في تحميل المؤسسات:', err);
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


  if (nomEleve && nomPere) {
    nomEleve.addEventListener('input', () => {
      nomPere.value = nomEleve.value;
      nomPere.setAttribute('readonly', true);
    });
  }

  const relationSelect = form.querySelector('[name="relation_tuteur"]');
  const ninPere = form.querySelector('[name="nin_pere"]');
  const nssPere = form.querySelector('[name="nss_pere"]');
  const ninMere = form.querySelector('[name="nin_mere"]');
  const nssMere = form.querySelector('[name="nss_mere"]');

  // Function to auto-fill NIN and NSS based on relation
  function autoFillParentData(relation) {
    if (!relationSelect || !ninPere || !nssPere || !ninMere || !nssMere) {
      console.warn('Form fields not found for auto-fill');
      return;
    }

    // Reset all fields first - clear values and make editable
    [ninPere, nssPere, ninMere, nssMere].forEach(f => {
      if (f) {
      f.value = '';
      f.removeAttribute('readonly');
        f.readOnly = false;
      }
    });

    // Only auto-fill and lock if relation is "ولي" (guardian)
    if (relation === 'ولي') {
      const sexeTuteur = window.currentUserSexe?.trim();
      const userNIN = window.currentUserNIN?.trim();
      const userNSS = window.currentUserNSS?.trim();

      console.log('Auto-fill attempt for ولي:', {
        relation,
        sexeTuteur,
        hasNIN: !!userNIN,
        hasNSS: !!userNSS
      });

      if (sexeTuteur === 'ذكر' && userNIN && userNSS) {
        if (ninPere) {
        ninPere.value = userNIN;
        ninPere.setAttribute('readonly', true);
          ninPere.readOnly = true;
          console.log('Filled father NIN:', userNIN.substring(0, 4) + '...');
        }
        if (nssPere) {
          nssPere.value = userNSS;
        nssPere.setAttribute('readonly', true);
          nssPere.readOnly = true;
          console.log('Filled father NSS:', userNSS.substring(0, 4) + '...');
        }
      } else if (sexeTuteur === 'أنثى' && userNIN && userNSS) {
        if (ninMere) {
        ninMere.value = userNIN;
        ninMere.setAttribute('readonly', true);
          ninMere.readOnly = true;
          console.log('Filled mother NIN:', userNIN.substring(0, 4) + '...');
        }
        if (nssMere) {
          nssMere.value = userNSS;
        nssMere.setAttribute('readonly', true);
          nssMere.readOnly = true;
          console.log('Filled mother NSS:', userNSS.substring(0, 4) + '...');
      }
      } else {
        console.warn('Cannot auto-fill: missing data', {
          sexeTuteur,
          hasNIN: !!userNIN,
          hasNSS: !!userNSS
        });
      }
    } else {
      // For "وصي" or any other option, fields remain empty and editable
      console.log('Relation is not ولي, fields cleared and made editable');
    }
  }

  if (relationSelect) {
    relationSelect.addEventListener('change', () => {
      autoFillParentData(relationSelect.value);
    });
    // Initial lock based on default/selected value
    autoFillParentData(relationSelect.value);
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
  document.querySelectorAll('input[name="prenom"], input[name="nom"], input[name="prenom_pere"], input[name="nom_pere"], input[name="prenom_mere"], input[name="nom_mere"]').forEach(allowArabicOnly);

  /* Apply number restriction */
  allowDigitsOnly(document.querySelector('input[name="num_scolaire"]'), 16);
  allowDigitsOnly(document.querySelector('input[name="nin_pere"]'), 18);
  allowDigitsOnly(document.querySelector('input[name="nin_mere"]'), 18);
  allowDigitsOnly(document.querySelector('input[name="nss_pere"]'), 12);
  allowDigitsOnly(document.querySelector('input[name="nss_mere"]'), 12);

  /* Apply Arabic restriction for edit form */
  document.querySelectorAll('#editChildForm input[name="prenom"], #editChildForm input[name="nom"], #editChildForm input[name="prenom_pere"], #editChildForm input[name="nom_pere"], #editChildForm input[name="prenom_mere"], #editChildForm input[name="nom_mere"]').forEach(allowArabicOnly);

  /* Apply number restriction for edit form */
  allowDigitsOnly(document.querySelector('#editChildForm input[name="nin_pere"]'), 18);
  allowDigitsOnly(document.querySelector('#editChildForm input[name="nin_mere"]'), 18);
  allowDigitsOnly(document.querySelector('#editChildForm input[name="nss_pere"]'), 12);
  allowDigitsOnly(document.querySelector('#editChildForm input[name="nss_mere"]'), 12);



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
      const arabicInputs = ['prenom','nom','prenom_pere','nom_pere','prenom_mere','nom_mere'];
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
        { name: 'num_scolaire', len: 16, label: 'الرقم التعريفي المدرسي' },
        { name: 'nin_pere', len: 18, label: 'NIN الأب' },
        { name: 'nin_mere', len: 18, label: 'NIN الأم' },
        { name: 'nss_pere', len: 12, label: 'NSS الأب' },
        { name: 'nss_mere', len: 12, label: 'NSS الأم' }
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
          console.error('Matricule check failed:', err);
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

      const nssPereEl = form.querySelector('[name="nss_pere"]');
      const nssMereEl = form.querySelector('[name="nss_mere"]');

      // Determine which NSS is auto-filled (skip it)
      let skipField = null;
      if (relation === 'ولي') {
        if (sexeTuteur === 'ذكر') skipField = 'nss_pere';
        else if (sexeTuteur === 'أنثى') skipField = 'nss_mere';
      }

     /* // Validate NSS père
      if (nssPereEl.value && skipField !== 'nss_pere' && !isValidNSS(nssPereEl.value)) {
        showError(nssPereEl, 'رقم الضمان الاجتماعي للأب غير صحيح');
        if (!firstError) firstError = nssPereEl;
        hasError = true;
      }

      // Validate NSS mère
      if (nssMereEl.value && skipField !== 'nss_mere' && !isValidNSS(nssMereEl.value)) {
        showError(nssMereEl, 'رقم الضمان الاجتماعي للأم غير صحيح');
        if (!firstError) firstError = nssMereEl;
        hasError = true;
      }
      */

      // === Final check ===
      if (hasError) {
        if (firstError) firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
        return;
      }

      // === Submit form ===
      const formData = new FormData(form);
      try {
        console.log('Submitting form to /api/eleves');
        const token = localStorage.getItem('api_token');
        console.log('Token in localStorage:', token ? 'Present (' + token.substring(0, 20) + '...)' : 'Missing');
        
        // Use apiFetch which automatically adds the token
        const response = await apiFetch('/api/eleves', {
          method: 'POST',
          body: formData,
          headers: {
            // apiFetch will add Authorization header automatically
            // Don't set Content-Type for FormData - browser will set it with boundary
          }
        });
        
        console.log('Response status:', response.status);
        console.log('Response ok:', response.ok);
        
        // Check response status
        if (!response.ok) {
          let errorMessage = 'حدث خطأ أثناء الإضافة';
          let errorData = null;
          
          try {
            errorData = await response.json();
            console.log('Error data:', errorData);
            if (errorData.message) {
              errorMessage = errorData.message;
            } else if (errorData.errors) {
              // Handle validation errors
              const errorMessages = Object.values(errorData.errors).flat();
              errorMessage = errorMessages.join('\n');
            }
          } catch (e) {
            console.error('Error parsing response:', e);
            // If we can't parse JSON, use status text
            errorMessage = response.statusText || 'حدث خطأ أثناء الإضافة';
          }
          
          // Only show error if it's not an authentication error (auth errors redirect automatically)
          if (response.status === 401) {
            console.error('401 Unauthorized - Authentication error:', errorData);
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
        console.error('Error creating student:', err);
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
      console.log('openIstimaraPDF called with num_scolaire:', num_scolaire);
      
      if (!num_scolaire) {
        console.error('openIstimaraPDF: num_scolaire is missing');
        return;
      }
      
      console.log('Opening PDF route: /eleves/' + num_scolaire + '/istimara');
      
      // Open PDF in new tab with regenerate parameter to ensure fresh PDF
      const pdfUrl = `/eleves/${num_scolaire}/istimara?regenerate=1`;
      console.log('PDF URL:', pdfUrl);
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
        document.getElementById('view_relation_tuteur').value = eleve.relation_tuteur || '—';
        document.getElementById('view_nin_pere').value = eleve.nin_pere || '—';
        document.getElementById('view_nss_pere').value = eleve.nss_pere || '—';
        document.getElementById('view_classe_scol').value = eleve.classe_scol || '—';
        document.getElementById('view_sexe').value = eleve.sexe || '—';
        document.getElementById('view_handicap').value = (eleve.handicap === '1' || eleve.handicap === 1) ? 'نعم' : 'لا';
        document.getElementById('view_orphelin').value = (eleve.orphelin === '1' || eleve.orphelin === 1) ? 'نعم' : 'لا';
        
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
        console.error('Error loading student data:', error);
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
        
        const response = await fetch(`/eleves/${num_scolaire}/edit`);
        if (!response.ok) throw new Error('Failed to load student data');
        
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
                      console.error('Error loading schools:', err);
                    }
                  }, 300);
                }
              } catch (err) {
                console.error('Error loading communes:', err);
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
              console.error('Error loading birth communes:', err);
            }
          }, 300);
        }
        
        // Populate Step 2 fields
        document.getElementById('edit_nom').value = eleve.nom || '';
        document.getElementById('edit_prenom').value = eleve.prenom || '';
        document.getElementById('edit_nom_pere').value = eleve.nom_pere || '';
        document.getElementById('edit_prenom_pere').value = eleve.prenom_pere || '';
        document.getElementById('edit_date_naiss').value = eleve.date_naiss || '';
        document.getElementById('edit_relation_tuteur').value = eleve.relation_tuteur || '';
        document.getElementById('edit_nin_pere').value = eleve.nin_pere || '';
        document.getElementById('edit_nss_pere').value = eleve.nss_pere || '';
        
        // Set mother_id if available
        if (eleve.mother_id && editMotherSelect) {
          editMotherSelect.value = eleve.mother_id;
        }
        
        // Lock guardian identifiers from editing
        ['edit_nin_pere','edit_nss_pere'].forEach(id => {
          const el = document.getElementById(id);
          if (el) el.readOnly = true;
        });
        
        // Setup auto-fill for edit form relation change
        const editRelationSelect = document.getElementById('edit_relation_tuteur');
        const originalRelation = eleve.relation_tuteur || '';
        const editNinPere = document.getElementById('edit_nin_pere');
        const editNssPere = document.getElementById('edit_nss_pere');
        
        if (editRelationSelect) {
          // Remove old listener if exists
          const newEditRelationSelect = editRelationSelect.cloneNode(true);
          editRelationSelect.parentNode.replaceChild(newEditRelationSelect, editRelationSelect);
          
          // Preserve original relation (linked to account)
          newEditRelationSelect.value = originalRelation;
          newEditRelationSelect.addEventListener('change', () => {
            newEditRelationSelect.value = originalRelation;
          });

          // Prevent relation changes
          newEditRelationSelect.addEventListener('change', () => {
            newEditRelationSelect.value = originalRelation;
          });
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

        const editOrphelinYes = document.getElementById('edit_orphelinYes');
        const editOrphelinNo = document.getElementById('edit_orphelinNo');
        const isOrphelin = eleve.orphelin === '1' || eleve.orphelin === 1;
        if (editOrphelinYes && editOrphelinNo) {
          editOrphelinYes.checked = isOrphelin;
          editOrphelinNo.checked = !isOrphelin;
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
        console.error('Error loading student data:', error);
        Swal.fire('Error', 'Failed to load student data', 'error');
        const modal = bootstrap.Modal.getInstance(editChildModal);
        if (modal) modal.hide();
      }
    };

    // Edit modal events
    editChildModal.addEventListener('show.bs.modal', async () => {
      customOverlay.style.display = 'block';
      await loadMothers();
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
      const arabicInputs = ['prenom','nom','prenom_pere','nom_pere','prenom_mere','nom_mere'];
      arabicInputs.forEach(name => {
        const el = editForm.querySelector(`[name="${name}"]`);
        if (el && el.value.trim() && !/^[ء-ي\s]+$/.test(el.value)) {
          showError(el, 'يجب أن يكون النص بالعربية فقط');
          if (!firstError) firstError = el;
          hasError = true;
        }
      });

      const numericChecks = [
        { name: 'nin_pere', len: 18, label: 'NIN الأب' },
        { name: 'nin_mere', len: 18, label: 'NIN الأم' },
        { name: 'nss_pere', len: 12, label: 'NSS الأب' },
        { name: 'nss_mere', len: 12, label: 'NSS الأم' }
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
        const response = await fetch(`/eleves/${num_scolaire}`, {
          method: 'PUT',
          body: formData,
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
          },
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
      console.error('Error loading comments:', error);
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
            console.error(err);
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
