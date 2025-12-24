@extends('layouts.main')

@section('title', 'لوحة الوصي/الولي')

@push('styles')
@vite(['resources/css/tuteur-dashboard.css'])
<!-- SweetAlert2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
@endpush

@section('content')
<div class="dashboard-container">

    <!-- Logout Button -->
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>

    <button class="logout-btn" onclick="confirmLogout()">
        <i class="fa-solid fa-right-from-bracket"></i>
        <span>تسجيل الخروج</span>
    </button>


    <!-- SweetAlert2 JS -->
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
                buttonsStyling: false // ✅ allows us to fully control the button design
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('logout-form').submit();
                }
            });
        }
    </script>



    <!-- Welcome header -->
    <div class="dashboard-header">
        @php
            $tuteur = session('tuteur');
            $nom = $tuteur['nom_ar'] ?? $tuteur['nom_fr'] ?? '';
            $prenom = $tuteur['prenom_ar'] ?? $tuteur['prenom_fr'] ?? '';
        @endphp

        <h2>مرحبًا بك، {{ trim($nom . ' ' . $prenom) ?: 'الوصي' }}</h2>
        <p>إدارة بياناتك وبيانات التلاميذ من خلال هذه الواجهة</p>
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
        <table class="children-table" >
            <thead>
                <tr>
                    <th>الإجراءات</th>
                    <th>المؤسسة التعليمية</th>
                    <th>المستوى الدراسي</th>
                    <th>تاريخ الميلاد</th>
                    <th>الاسم الكامل</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
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

                    <!-- نوع المدرسة + المستوى الدراسي -->
                    <div class="col-md-6">
                    <label class="form-label fw-bold required">نوع المدرسة</label>
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
                    <select class="form-select" name="ecole" required>
                        <option value="">اختر...</option>
                        <option>مدرسة النجاح</option>
                        <option>متوسطة المستقبل</option>
                        <option>ثانوية النور</option>
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
                <h5 class="fw-bold mb-3 text-center" style="color:#0f033a;">الخطوة 2: إدخال معلومات تلميذ</h5>

                <div class="row g-3">
                    <!-- 🆔 الرقم المدرسي -->
                    <div class="col-md-6">
                    <label class="form-label fw-bold required">الرقم المدرسي</label>
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

                    <div class="col-md-6">
                      <label class="form-label fw-bold required">لقب الأم بالعربية</label>
                      <input type="text" name="nom_mere" class="form-control" dir="rtl" required>
                    </div>
                    <div class="col-md-6">
                      <label class="form-label fw-bold required">اسم الأم بالعربية</label>
                      <input type="text" name="prenom_mere" class="form-control" dir="rtl" required>
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
                      <label class="form-label fw-bold required"> ماهي علاقتك بالتلميذ ؟</label>
                      <select name="relation_tuteur" class="form-select" required>
                          <option value="">اختر...</option>
                          <option value="ولي">ولي</option>
                          <option value="وصي">وصي</option>
                      </select>
                    </div>

                    <!-- الحالة الاجتماعية -->
                    <div class="col-md-4 d-flex align-items-center justify-content-end pe-0">
                      <label class="form-label fw-bold mb-0 ms-2">هل لديه إعاقة؟</label>
                      <div class="form-check mb-0 d-flex align-items-center">
                        <input class="form-check-input ms-2" type="checkbox" name="handicap" value="1" id="handicapCheck">
                        <label class="form-check-label" for="handicapCheck">نعم</label>
                      </div>
                    </div>

                    <div class="col-md-4 d-flex align-items-center justify-content-end pe-0">
                      <label class="form-label fw-bold mb-0 ms-2">هل هو يتيم؟</label>
                      <div class="form-check mb-0 d-flex align-items-center">
                        <input class="form-check-input ms-2" type="checkbox" name="orphelin" value="1" id="orphelinCheck">
                        <label class="form-check-label" for="orphelinCheck">نعم</label>
                      </div>
                    </div>

                    <!-- NIN + NSS -->
                    <div class="col-md-6">
                      <label class="form-label fw-bold">الرقم الوطني للأب (NIN)</label>
                      <input type="text" name="nin_pere" class="form-control" maxlength="18" minlength="18" pattern="\d{18}">
                    </div>
                    <div class="col-md-6">
                      <label class="form-label fw-bold">الرقم الوطني للأم (NIN)</label>
                      <input type="text" name="nin_mere" class="form-control" maxlength="18" minlength="18" pattern="\d{18}">
                    </div>

                    <div class="col-md-6">
                      <label class="form-label fw-bold">الرقم الوطني للضمان الاجتماعي للأب (NSS)</label>
                      <input type="text" name="nss_pere" class="form-control" maxlength="12" minlength="12" pattern="\d{12}">
                    </div>
                    <div class="col-md-6">
                      <label class="form-label fw-bold">الرقم الوطني للضمان الاجتماعي للأم (NSS)</label>
                      <input type="text" name="nss_mere" class="form-control" maxlength="12" minlength="12" pattern="\d{12}">
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
@php
    $tuteur = session('tuteur');
@endphp

<script>
  window.currentUserNIN = "{{ $tuteur['nin'] ?? '' }}";
  window.currentUserNSS = "{{ $tuteur['nss'] ?? '' }}";
  window.currentUserSexe = "{{ $tuteur['sexe'] ?? '' }}";
</script>
<script>
document.addEventListener("DOMContentLoaded", async () => {
  /* ===============================
     🧒 Load children list
  =============================== */
  async function loadChildrenList() {
    const tableBody = document.querySelector(".children-table tbody");
    tableBody.innerHTML = `<tr><td colspan="5">جارٍ تحميل البيانات...</td></tr>`;

    try {
      const nin = "{{ session('tuteur.nin') }}";
      const response = await fetch(`/tuteur/${nin}/eleves`);
      const data = await response.json();

      if (!response.ok || !Array.isArray(data) || data.length === 0) {
        tableBody.innerHTML = `
          <tr>
            <td colspan="5" style="color:#777; padding:20px; text-align:center;">
              لا يوجد تلاميذ مسجلين بعد.
            </td>
          </tr>
        `;
      } else {
        // Smooth fade transition ✨
        tableBody.style.opacity = 0;
        setTimeout(() => {
          tableBody.innerHTML = data.map(eleve => `
            <tr>
              <td style="text-align:center;">
              <a href="/eleves/${eleve.num_scolaire}/istimara" target="_blank" class="btn btn-outline-danger btn-sm">
                <i class="fa-solid fa-file-pdf"></i>
              </a>
                <button class="btn-delete" data-id="${eleve.num_scolaire}">
                  <i class="fa-solid fa-trash"></i> حذف
                </button>
                <a href="/eleves/${eleve.num_scolaire}" class="btn-view">
                  <i class="fa-solid fa-eye"></i> عرض
                </a>
                <a href="/eleves/${eleve.num_scolaire}/edit" class="btn-edit">
                  <i class="fa-solid fa-pen"></i> تعديل
                </a>
              </td>
              <td>${eleve.etablissement?.nom_etabliss ?? '—'}</td>
              <td>${eleve.classe_scol ?? '—'}</td>
              <td>${eleve.date_naiss ?? '—'}</td>
              <td>${eleve.nom ?? ''} ${eleve.prenom ?? ''}</td>
            </tr>
          `).join('');
          tableBody.style.transition = "opacity 0.4s ease";
          tableBody.style.opacity = 1;
        }, 150);
      }
    } catch (error) {
      console.error(error);
      tableBody.innerHTML = `
        <tr>
          <td colspan="5" style="color:red; padding:20px; text-align:center;">
            حدث خطأ أثناء تحميل البيانات.
          </td>
        </tr>
      `;
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

  // Select elements
  const wilayaSelect = document.getElementById('wilayaSelect');
  const communeSelect = document.getElementById('communeSelect');
  const typeSelect = document.querySelector('select[name="type_ecole"]');
  const niveauSelect = document.querySelector('select[name="niveau"]');
  const ecoleSelect = document.querySelector('select[name="ecole"]');
  const wilayaNaiss = document.getElementById('wilayaNaiss');
  const communeNaiss = document.getElementById('communeNaiss');
  const nomEleve = form.querySelector('[name="nom"]');
  const nomPere = form.querySelector('[name="nom_pere"]');


  // When modal opens → load wilayas
  const addChildModal = document.getElementById('addChildModal');
  addChildModal.addEventListener('show.bs.modal', async () => {
    await loadWilayasGeneric(wilayaSelect, communeSelect);      // Step 1
    await loadWilayasGeneric(wilayaNaiss, communeNaiss); 
  });

  /* 🟢 Load wilayas from DB */
    /* ===============================
    🧩 Generic Wilaya / Commune Loader
    =============================== */
  async function loadWilayasGeneric(wilayaSelectEl, communeSelectEl) {
    try {
      wilayaSelectEl.innerHTML = '<option value="">جارٍ التحميل...</option>';
      const res = await fetch('/api/wilayas');
      const wilayas = await res.json();

      wilayaSelectEl.innerHTML = '<option value="">اختر...</option>';
      wilayas.forEach(w => {
        wilayaSelectEl.innerHTML += `<option value="${w.code_wil}">${w.lib_wil_ar}</option>`;
      });

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
          const communes = await res.json();

          communeSelectEl.innerHTML = '<option value="">اختر...</option>';
          communes.forEach(c => {
            communeSelectEl.innerHTML += `<option value="${c.code_comm}">${c.lib_comm_ar}</option>`;
          });
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
      ecoleSelectEl.innerHTML = '<option value="">اختر البلدية أولا...</option>';
      ecoleSelectEl.disabled = true;
    }

    if (!wilayaCode) {
      communeSelectEl.innerHTML = '<option value="">اختر الولاية أولا...</option>';
      return;
    }

    try {
      const res = await fetch(`/api/communes/by-wilaya/${wilayaCode}`);
      const communes = await res.json();

      communeSelectEl.innerHTML = '<option value="">اختر...</option>';
      communes.forEach(c => {
        communeSelectEl.innerHTML += `<option value="${c.code_comm}">${c.lib_comm_ar}</option>`;
      });
      communeSelectEl.disabled = false;
    } catch (err) {
      console.error('⚠️ خطأ في تحميل البلديات:', err);
      communeSelectEl.innerHTML = '<option value="">تعذر تحميل البيانات</option>';
    }
  }
  wilayaSelect.addEventListener('change', () => handleWilayaChange(wilayaSelect, communeSelect, ecoleSelect));
  wilayaNaiss.addEventListener('change', () => handleWilayaChange(wilayaNaiss, communeNaiss));

  /* 🟢 Load établissements dynamically when commune + niveau + type are selected */
  [typeSelect, niveauSelect, communeSelect].forEach(sel => {
    sel.addEventListener('change', loadEtablissements);
  });

  async function loadEtablissements() {
    const code_commune = communeSelect.value;
    const niveau = niveauSelect.value;
    const nature = typeSelect.value;

    ecoleSelect.innerHTML = '<option value="">جارٍ التحميل...</option>';
    ecoleSelect.disabled = true;

    // Make sure all are chosen
    if (!code_commune || !niveau || !nature) {
      ecoleSelect.innerHTML = '<option value="">اختر كل المعايير أولا...</option>';
      return;
    }

    try {
      const url = `/api/etablissements?code_commune=${code_commune}&niveau=${niveau}&nature=${nature}`;
      const res = await fetch(url);

      if (!res.ok) {
        ecoleSelect.innerHTML = '<option value="">لم يتم العثور على مؤسسات</option>';
        ecoleSelect.disabled = true;
        return;
      }

      const etabs = await res.json();
      ecoleSelect.innerHTML = '<option value="">اختر...</option>';

      etabs.forEach(e => {
        ecoleSelect.innerHTML += `<option value="${e.code_etabliss}">${e.nom_etabliss}</option>`;
      });

      ecoleSelect.disabled = false;
    } catch (err) {
      console.error('خطأ في تحميل المؤسسات:', err);
      ecoleSelect.innerHTML = '<option value="">تعذر تحميل البيانات</option>';
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

    ecoleSelect.innerHTML = '<option value="">اختر كل المعايير أولا...</option>';
    ecoleSelect.disabled = true;
  }

  // 🔁 “إعادة تعيين” button click
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
    fullReset();
  });

  /* ===============================
    🟠 Go to Step 2 — but validate first
  =============================== */
  nextStep.addEventListener('click', () => {
    const requiredFields = [
      { el: typeSelect, name: 'نوع المدرسة' },
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

  relationSelect.addEventListener('change', () => {
    const relation = relationSelect.value;

    // Reset fields first
    [ninPere, nssPere, ninMere, nssMere].forEach(f => {
      f.value = '';
      f.removeAttribute('readonly');
    });

    // Auto-fill if relation is "ولي"
    if (relation === 'ولي') {
      const sexeTuteur = window.currentUserSexe?.trim();
      const userNIN = window.currentUserNIN;
      const userNSS = window.currentUserNSS;

      if (sexeTuteur === 'ذكر') {
        ninPere.value = userNIN;
        nssPere.value = userNSS;
        ninPere.setAttribute('readonly', true);
        nssPere.setAttribute('readonly', true);
      } else if (sexeTuteur === 'أنثى') {
        ninMere.value = userNIN;
        nssMere.value = userNSS;
        ninMere.setAttribute('readonly', true);
        nssMere.setAttribute('readonly', true);
      }
    }
  });
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
        { name: 'num_scolaire', len: 16, label: 'الرقم المدرسي' },
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
            showError(el, 'الرقم المدرسي موجود مسبقًا');
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
        const response = await fetch('/eleves', {
          method: 'POST',
          body: formData,
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
          },
        });
        if (!response.ok) throw new Error('خطأ أثناء الإضافة');

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

