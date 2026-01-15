@extends('layouts.main')

@section('title', 'إضافة تلميذ جديد - لوحة التحكم')

@vite(['resources/css/dashboard.css'])

@push('styles')
<style>
    .required::after {
        content: " *";
        color: red;
    }
    .step-content {
        display: block;
    }
    .step-content.d-none {
        display: none;
    }
    .form-label {
        font-weight: 600;
        margin-bottom: 0.5rem;
    }
    .btn-primary-custom {
        background-color: #0f033a;
        color: white;
        border: none;
    }
    .btn-primary-custom:hover {
        background-color: #1a0f4a;
        color: white;
    }
    .btn-warning-custom {
        background-color: #fdae4b;
        color: #0f033a;
        font-weight: bold;
        border: none;
    }
    .btn-warning-custom:hover {
        background-color: #f5a02b;
        color: #0f033a;
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
                <li class="sidebar-item">
                    <a href="{{ route('user.students.list') }}" class="sidebar-link">
                        <i class="fa-solid fa-user-graduate"></i>
                        <span>التلاميذ</span>
                    </a>
                </li>
                <li class="sidebar-item active">
                    <a href="{{ route('user.add.student') }}" class="sidebar-link">
                        <i class="fa-solid fa-user-plus"></i>
                        <span>إضافة تلميذ جديد</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="{{ route('user.pending.requests') }}" class="sidebar-link">
                        <i class="fa-solid fa-file-check"></i>
                        <span>الطلبات المعلقة</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="{{ route('user.approved.requests') }}" class="sidebar-link">
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
                <h2>إضافة تلميذ جديد</h2>
                <p>إضافة تلميذ جديد إلى النظام</p>
            </div>

            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body p-4">
                    <form id="adminAddStudentForm" enctype="multipart/form-data">
                        @csrf
                        
                        <!-- === STEP 1: Tuteur Selection === -->
                        <div id="step1" class="step-content">
                            <h5 class="fw-bold mb-4 text-center" style="color:#0f033a;">الخطوة 1: اختيار الولي</h5>
                            
                            <div class="row g-3 mb-4">
                                <div class="col-md-12">
                                    <label class="form-label fw-bold required">الولي (NIN)</label>
                                    <div class="d-flex gap-2 align-items-start">
                                        <input type="text" 
                                               id="tuteurNIN" 
                                               name="tuteur_nin" 
                                               class="form-control" 
                                               maxlength="18" 
                                               minlength="18" 
                                               pattern="\d{18}"
                                               placeholder="أدخل الرقم التعريفي الوطني للولي (18 رقمًا)"
                                               required>
                                        <button type="button" 
                                                id="checkTuteurBtn" 
                                                class="btn btn-primary-custom px-4 flex-shrink-0">
                                            <i class="fa-solid fa-search me-1"></i> التحقق
                                        </button>
                                    </div>
                                    <small class="form-text text-muted d-block mt-2">
                                        <i class="fa-solid fa-info-circle"></i> أدخل الرقم التعريفي الوطني للولي. إذا لم يكن موجودًا، سيتم إنشاؤه.
                                    </small>
                                    <div id="tuteurInfo" class="mt-3 p-3 bg-light rounded d-none">
                                        <h6 class="fw-bold mb-2">معلومات الولي:</h6>
                                        <p class="mb-1" id="tuteurName"></p>
                                        <p class="mb-0 text-muted" id="tuteurNINDisplay"></p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mt-4">
                                <button type="button" 
                                        class="btn btn-warning-custom px-4" 
                                        id="nextToStep2">
                                    التالي <i class="fa-solid fa-arrow-left ms-1"></i>
                                </button>
                            </div>
                        </div>

                        <!-- === STEP 2: School Selection === -->
                        <div id="step2" class="step-content d-none">
                            <h5 class="fw-bold mb-4 text-center" style="color:#0f033a;">الخطوة 2: اختيار المؤسسة التعليمية</h5>
                            
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold required">مؤسسة التربية والتعليم</label>
                                    <select class="form-select" name="type_ecole" id="typeEcole" required>
                                        <option value="">اختر...</option>
                                        <option value="عمومية">عمومية</option>
                                        <option value="متخصصة">متخصصة عمومية</option>
                                    </select>
                                </div>
                                
                                <div class="col-md-6">
                                    <label class="form-label fw-bold required">المستوى الدراسي</label>
                                    <select class="form-select" name="niveau" id="niveau" required>
                                        <option value="">اختر...</option>
                                        <option value="ابتدائي">ابتدائي</option>
                                        <option value="متوسط">متوسط</option>
                                        <option value="ثانوي">ثانوي</option>
                                    </select>
                                </div>
                                
                                <div class="col-md-6">
                                    <label class="form-label fw-bold required">الولاية</label>
                                    <select class="form-select" name="wilaya_id" id="wilayaSelect" required>
                                        <option value="">اختر...</option>
                                        @foreach($wilayas as $wilaya)
                                            <option value="{{ $wilaya->code_wil }}">{{ $wilaya->lib_wil_ar }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div class="col-md-6">
                                    <label class="form-label fw-bold required">البلدية</label>
                                    <select class="form-select" name="commune_id" id="communeSelect" required disabled>
                                        <option value="">اختر الولاية أولا...</option>
                                    </select>
                                </div>
                                
                                <div class="col-md-12">
                                    <label class="form-label fw-bold required">المؤسسة التعليمية</label>
                                    <select class="form-select" name="ecole" id="ecoleSelect" required disabled>
                                        <option value="">اختر كل المعايير أولا (مؤسسة التربية والتعليم، المستوى الدراسي، البلدية)</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mt-4">
                                <button type="button" 
                                        class="btn btn-warning-custom px-4" 
                                        id="nextToStep3">
                                    التالي <i class="fa-solid fa-arrow-left ms-1"></i>
                                </button>
                                <button type="button" 
                                        class="btn btn-outline-secondary px-4" 
                                        id="prevToStep1">
                                    <i class="fa-solid fa-arrow-right me-1"></i> العودة
                                </button>
                            </div>
                        </div>

                        <!-- === STEP 3: Student Info === -->
                        <div id="step3" class="step-content d-none">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="fw-bold mb-0" style="color:#0f033a;">الخطوة 3: إدخال معلومات التلميذ</h5>
                                <button type="button" id="clearStep3Btn" class="btn btn-outline-danger btn-sm">
                                    <i class="bi bi-arrow-clockwise"></i> مسح الكل والبدء من جديد
                                </button>
                            </div>
                            
                            <div class="row g-3">
                                <!-- الرقم التعريفي المدرسي -->
                                <div class="col-md-12">
                                    <label class="form-label fw-bold required">الرقم التعريفي المدرسي</label>
                                    <input type="text" name="num_scolaire" class="form-control" maxlength="16" minlength="16" pattern="\d{16}" placeholder="16 رقمًا" required>
                                </div>
                                
                                <!-- الاسم واللقب - Student (on same row) -->
                                <div class="col-md-6">
                                    <label class="form-label fw-bold required">اللقب بالعربية</label>
                                    <input type="text" name="nom" class="form-control" dir="rtl" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold required">الاسم بالعربية</label>
                                    <input type="text" name="prenom" class="form-control" dir="rtl" required>
                                </div>
                                
                                <!-- صفة طالب المنحة -->
                                <div class="col-md-6">
                                    <label class="form-label fw-bold required">صفة طالب المنحة</label>
                                    <select name="relation_tuteur" id="relationSelect" class="form-select" required>
                                        <option value="">اختر...</option>
                                        <option value="1" id="waliOption">الولي (الأب)</option>
                                        <option value="2" id="waliMotherOption">الولي (الأم)</option>
                                        <option value="3" id="wasiyOption">وصي</option>
                                    </select>
                                </div>
                                
                                <!-- الأم/الزوجة - For Guardian Role (NIN Input) -->
                                <div class="col-md-6" id="motherNINWrapper" style="display: none;">
                                    <label class="form-label fw-bold required" id="motherNINLabel">الرقم الوطني للأم (NIN)</label>
                                    <div class="d-flex gap-2 align-items-start">
                                        <input type="text" 
                                               id="motherNIN" 
                                               class="form-control" 
                                               maxlength="18" 
                                               minlength="18" 
                                               pattern="\d{18}"
                                               placeholder="18 رقمًا">
                                        <button type="button" 
                                                id="checkMotherNINBtn" 
                                                class="btn btn-primary-custom px-3 flex-shrink-0">
                                            <i class="fa-solid fa-search"></i>
                                        </button>
                                    </div>
                                    <small class="form-text text-muted d-block mt-2">
                                        <i class="fa-solid fa-info-circle"></i> أدخل الرقم الوطني للأم. إذا لم يكن موجودًا، سيتم إنشاؤه.
                                    </small>
                                    <div id="motherNameDisplay" class="text-success fw-bold mt-2" style="display: none;">
                                        <i class="fa-solid fa-check-circle"></i> <span id="motherNameText"></span>
                                    </div>
                                    <input type="hidden" id="motherID" name="mother_id">
                                </div>
                                
                                <!-- الأب - For Guardian Role (NIN Input) -->
                                <div class="col-md-6" id="fatherNINWrapper" style="display: none;">
                                    <label class="form-label fw-bold required" id="fatherNINLabel">الرقم الوطني للأب (NIN)</label>
                                    <div class="d-flex gap-2 align-items-start">
                                        <input type="text" 
                                               id="fatherNIN" 
                                               class="form-control" 
                                               maxlength="18" 
                                               minlength="18" 
                                               pattern="\d{18}"
                                               placeholder="18 رقمًا">
                                        <button type="button" 
                                                id="checkFatherNINBtn" 
                                                class="btn btn-primary-custom px-3 flex-shrink-0">
                                            <i class="fa-solid fa-search"></i>
                                        </button>
                                    </div>
                                    <small class="form-text text-muted d-block mt-2">
                                        <i class="fa-solid fa-info-circle"></i> أدخل الرقم الوطني للأب. إذا لم يكن موجودًا، سيتم إنشاؤه.
                                    </small>
                                    <div id="fatherNameDisplay" class="text-success fw-bold mt-2" style="display: none;">
                                        <i class="fa-solid fa-check-circle"></i> <span id="fatherNameText"></span>
                                    </div>
                                    <input type="hidden" id="fatherID" name="father_id">
                                </div>
                                
                                <!-- الأب/الأم/الوصي - Last name and First name (on same row) -->
                                <div class="col-md-6" id="nomPereWrapper" style="display: none;">
                                    <label class="form-label fw-bold required" id="nomPereLabel">لقب الأب بالعربية</label>
                                    <input type="text" name="nom_pere" id="nomPere" class="form-control" dir="rtl">
                                </div>
                                <div class="col-md-6" id="prenomPereWrapper" style="display: none;">
                                    <label class="form-label fw-bold required" id="prenomPereLabel">اسم الأب بالعربية</label>
                                    <input type="text" name="prenom_pere" id="prenomPere" class="form-control" dir="rtl">
                                </div>
                                
                                <!-- الميلاد -->
                                <div class="col-md-6">
                                    <label class="form-label fw-bold required">تاريخ الميلاد</label>
                                    <input type="date" name="date_naiss" class="form-control" required>
                                </div>
                                
                                <div class="col-md-3">
                                    <label class="form-label fw-bold required">ولاية الميلاد</label>
                                    <select name="wilaya_naiss" id="wilayaNaiss" class="form-select" required>
                                        <option value="">اختر...</option>
                                        @foreach($wilayas as $wilaya)
                                            <option value="{{ $wilaya->code_wil }}">{{ $wilaya->lib_wil_ar }}</option>
                                        @endforeach
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
                                    <label class="form-label fw-bold required">طبيعة الإعاقة</label>
                                    <select name="handicap_nature" id="handicapNature" class="form-select">
                                        <option value="">اختر...</option>
                                        <option value="بصريا">بصريا</option>
                                        <option value="حركيا">حركيا</option>
                                        <option value="سمعيا">سمعيا</option>
                                        <option value="متعدد">متعدد</option>
                                        <option value="صم بكم">صم بكم</option>
                                    </select>
                                </div>
                                <div class="col-md-6 handicap-details d-none" id="handicapPercentageWrapper">
                                    <label class="form-label fw-bold required">نسبة الإعاقة (%)</label>
                                    <input type="number" name="handicap_percentage" id="handicapPercentage" class="form-control" min="50" max="100" step="0.1" placeholder="50 - 100">
                                    <small class="form-text text-muted">الحد الأدنى: 50% | الحد الأقصى: 100%</small>
                                </div>
                                
                                <!-- NIN + NSS for Father/Mother/Guardian (will be shown based on relation) -->
                                <div class="col-md-6" id="ninPereWrapper" style="display: none;">
                                    <label class="form-label fw-bold">الرقم الوطني للأب (NIN)</label>
                                    <input type="text" id="ninPere" class="form-control" readonly style="background-color: #f8f9fa;">
                                </div>
                                
                                <div class="col-md-6" id="nssPereWrapper" style="display: none;">
                                    <label class="form-label fw-bold">رقم الضمان الاجتماعي للأب (NSS)</label>
                                    <input type="text" id="nssPere" class="form-control" readonly style="background-color: #f8f9fa;">
                                </div>
                                
                                <div class="col-md-6" id="ninMereWrapper" style="display: none;">
                                    <label class="form-label fw-bold">الرقم الوطني للأم (NIN)</label>
                                    <input type="text" name="nin_mere" id="ninMere" class="form-control" maxlength="18" minlength="18" pattern="\d{18}" readonly style="background-color: #f8f9fa;">
                                </div>
                                
                                <div class="col-md-6" id="nssMereWrapper" style="display: none;">
                                    <label class="form-label fw-bold">رقم الضمان الاجتماعي للأم (NSS)</label>
                                    <input type="text" name="nss_mere" id="nssMere" class="form-control" maxlength="12" minlength="12" pattern="\d{12}" readonly style="background-color: #f8f9fa;">
                                </div>
                                
                                <div class="col-md-6" id="ninGuardianWrapper" style="display: none;">
                                    <label class="form-label fw-bold">الرقم الوطني للوصي (NIN)</label>
                                    <input type="text" name="nin_guardian" id="ninGuardian" class="form-control" maxlength="18" minlength="18" pattern="\d{18}" readonly style="background-color: #f8f9fa;">
                                </div>
                                
                                <div class="col-md-6" id="nssGuardianWrapper" style="display: none;">
                                    <label class="form-label fw-bold">رقم الضمان الاجتماعي للوصي (NSS)</label>
                                    <input type="text" name="nss_guardian" id="nssGuardian" class="form-control" maxlength="12" minlength="12" pattern="\d{12}" readonly style="background-color: #f8f9fa;">
                                </div>
                                
                                <!-- وثيقة إسناد الوصاية (for Guardian role only) -->
                                <div class="col-md-12" id="guardianDocWrapper" style="display: none;">
                                    <label class="form-label fw-bold required">وثيقة إسناد الوصاية</label>
                                    <input type="file" name="guardian_doc" id="guardianDoc" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                                    <small class="form-text text-muted d-block mt-1">
                                        <i class="fa-solid fa-info-circle"></i> يُسمح برفع ملفات PDF أو صور (JPG, JPEG, PNG) فقط. الحد الأقصى للحجم: 5 ميجابايت
                                    </small>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mt-4">
                                <button type="submit" class="btn btn-warning-custom px-4">
                                    إضافة <i class="fa-solid fa-check ms-1"></i>
                                </button>
                                <button type="button" class="btn btn-outline-secondary px-4" id="prevToStep2">
                                    <i class="fa-solid fa-arrow-right me-1"></i> العودة
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
</div>

<!-- Tuteur Creation Modal -->
<div class="modal fade" id="tuteurCreationModal" tabindex="-1" aria-labelledby="tuteurCreationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header" style="background-color:#0f033a; color:white;">
                <h5 class="modal-title" id="tuteurCreationModalLabel">
                    <i class="fa-solid fa-user-plus me-2 text-warning"></i> إنشاء ولي جديد
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="إغلاق"></button>
            </div>
            <div class="modal-body p-4">
                <form id="tuteurCreationForm">
                    @csrf
                    <!-- This form will be populated with signup form fields via JavaScript -->
                    <div id="tuteurFormContent">
                        <!-- Form fields will be loaded here -->
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <button type="button" class="btn btn-warning-custom" id="submitTuteurForm">إنشاء الولي</button>
            </div>
        </div>
    </div>
</div>

<!-- Mother Creation Modal -->
<div class="modal fade" id="motherCreationModal" tabindex="-1" aria-labelledby="motherCreationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header" style="background-color:#0f033a; color:white;">
                <h5 class="modal-title" id="motherCreationModalLabel">
                    <i class="fa-solid fa-venus me-2 text-warning"></i> إنشاء أم جديدة
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="إغلاق"></button>
            </div>
            <div class="modal-body p-4">
                <form id="motherCreationForm">
                    @csrf
                    <input type="hidden" id="motherCreationNIN" name="nin">
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold required">الرقم الوطني للأم (NIN)</label>
                            <input type="text" id="mother_modal_nin" name="nin" class="form-control" maxlength="18" minlength="18" pattern="\d{18}" readonly style="background-color: #f8f9fa;">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">رقم الضمان الاجتماعي للأم (NSS)</label>
                            <input type="text" id="mother_modal_nss" name="nss" class="form-control" maxlength="12" minlength="12" pattern="\d{12}">
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label fw-bold required">اللقب بالعربية</label>
                            <input type="text" id="mother_modal_nom_ar" name="nom_ar" class="form-control" dir="rtl" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold required">الاسم بالعربية</label>
                            <input type="text" id="mother_modal_prenom_ar" name="prenom_ar" class="form-control" dir="rtl" required>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label fw-bold">اللقب باللاتينية</label>
                            <input type="text" id="mother_modal_nom_fr" name="nom_fr" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">الاسم باللاتينية</label>
                            <input type="text" id="mother_modal_prenom_fr" name="prenom_fr" class="form-control">
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label fw-bold">الفئة الاجتماعية</label>
                            <select id="mother_modal_categorie_sociale" name="categorie_sociale" class="form-select">
                                <option value="">اختر الفئة الاجتماعية</option>
                                <option value="عديم الدخل">عديم الدخل</option>
                                <option value="الدخل الشهري أقل أو يساوي مبلغ الأجر الوطني الأدنى المضمون">الدخل الشهري أقل أو يساوي مبلغ الأجر الوطني الأدنى المضمون</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <div id="mother_modal_montant_wrapper" style="display: none;">
                                <label class="form-label fw-bold">مبلغ الدخل الشهري</label>
                                <input type="number" id="mother_modal_montant_s" name="montant_s" class="form-control" step="0.01" min="0">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <button type="button" class="btn btn-warning-custom" id="submitMotherForm">إنشاء الأم</button>
            </div>
        </div>
    </div>
</div>

<!-- Father Creation Modal -->
<div class="modal fade" id="fatherCreationModal" tabindex="-1" aria-labelledby="fatherCreationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header" style="background-color:#0f033a; color:white;">
                <h5 class="modal-title" id="fatherCreationModalLabel">
                    <i class="fa-solid fa-mars me-2 text-warning"></i> إنشاء أب جديد
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="إغلاق"></button>
            </div>
            <div class="modal-body p-4">
                <form id="fatherCreationForm">
                    @csrf
                    <input type="hidden" id="fatherCreationNIN" name="nin">
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold required">الرقم الوطني للأب (NIN)</label>
                            <input type="text" id="father_modal_nin" name="nin" class="form-control" maxlength="18" minlength="18" pattern="\d{18}" readonly style="background-color: #f8f9fa;">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">رقم الضمان الاجتماعي للأب (NSS)</label>
                            <input type="text" id="father_modal_nss" name="nss" class="form-control" maxlength="12" minlength="12" pattern="\d{12}">
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label fw-bold required">اللقب بالعربية</label>
                            <input type="text" id="father_modal_nom_ar" name="nom_ar" class="form-control" dir="rtl" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold required">الاسم بالعربية</label>
                            <input type="text" id="father_modal_prenom_ar" name="prenom_ar" class="form-control" dir="rtl" required>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label fw-bold">اللقب باللاتينية</label>
                            <input type="text" id="father_modal_nom_fr" name="nom_fr" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">الاسم باللاتينية</label>
                            <input type="text" id="father_modal_prenom_fr" name="prenom_fr" class="form-control">
                        </div>
                        
                        <div class="col-md-12">
                            <label class="form-label fw-bold">الفئة الاجتماعية</label>
                            <select id="father_modal_categorie_sociale" name="categorie_sociale" class="form-select">
                                <option value="">اختر الفئة الاجتماعية</option>
                                <option value="عديم الدخل">عديم الدخل</option>
                                <option value="الدخل الشهري أقل أو يساوي مبلغ الأجر الوطني الأدنى المضمون">الدخل الشهري أقل أو يساوي مبلغ الأجر الوطني الأدنى المضمون</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <button type="button" class="btn btn-warning-custom" id="submitFatherForm">إنشاء الأب</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
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

document.addEventListener('DOMContentLoaded', function() {
    // Get CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    
    // Helper function for API calls (no auth needed for admin)
    async function apiFetch(url, options = {}) {
        const defaultHeaders = {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        };
        
        const mergedHeaders = { ...defaultHeaders, ...(options.headers || {}) };
        
        // For FormData, remove Content-Type to let browser set it with boundary
        if (options.body instanceof FormData) {
            delete mergedHeaders['Content-Type'];
        }
        
        const response = await fetch(url, {
            ...options,
            headers: mergedHeaders,
        });
        
        if (!response.ok) {
            const errorData = await response.json().catch(() => ({ message: 'حدث خطأ' }));
            
            // Handle validation errors (422 status)
            if (response.status === 422 && errorData.errors) {
                // Create a formatted error message from validation errors
                const errorMessages = Object.values(errorData.errors).flat();
                const formattedMessage = errorMessages.join('\n');
                const error = new Error(formattedMessage);
                error.status = 422;
                error.errors = errorData.errors;
                error.rawData = errorData;
                throw error;
            }
            
            throw new Error(errorData.message || 'حدث خطأ');
        }
        
        const data = await response.json();
        // If response is already an array, return it directly
        // Otherwise, check for common response wrappers
        if (Array.isArray(data)) {
            return data;
        }
        // Handle Laravel collection responses or other formats
        return data.data || data.communes || data;
    }
    
    // Step navigation
    const step1 = document.getElementById('step1');
    const step2 = document.getElementById('step2');
    const step3 = document.getElementById('step3');
    const nextToStep2 = document.getElementById('nextToStep2');
    const nextToStep3 = document.getElementById('nextToStep3');
    const prevToStep1 = document.getElementById('prevToStep1');
    const prevToStep2 = document.getElementById('prevToStep2');
    
    // Tuteur lookup
    const tuteurNIN = document.getElementById('tuteurNIN');
    const checkTuteurBtn = document.getElementById('checkTuteurBtn');
    const tuteurInfo = document.getElementById('tuteurInfo');
    const tuteurName = document.getElementById('tuteurName');
    const tuteurNINDisplay = document.getElementById('tuteurNINDisplay');
    let selectedTuteurNIN = null;
    let selectedTuteurData = null; // Store tuteur data
    
    checkTuteurBtn.addEventListener('click', async function() {
        const nin = tuteurNIN.value.trim();
        
        if (!nin || nin.length !== 18) {
            Swal.fire({
                icon: 'error',
                title: 'خطأ',
                text: 'الرجاء إدخال رقم وطني صحيح (18 رقمًا)',
                confirmButtonText: 'حسنًا'
            });
            return;
        }
        
        try {
            checkTuteurBtn.disabled = true;
            checkTuteurBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> جاري التحقق...';
            
            const response = await apiFetch('/api/check/tuteur/exists', {
                method: 'POST',
                body: JSON.stringify({ nin: nin })
            });
            
            if (response.exists && response.tuteur) {
                // Tuteur exists
                selectedTuteurNIN = nin;
                selectedTuteurData = response.tuteur; // Store tuteur data
                tuteurName.textContent = `${response.tuteur.nom_ar || ''} ${response.tuteur.prenom_ar || ''}`.trim();
                tuteurNINDisplay.textContent = `NIN: ${nin}`;
                tuteurInfo.classList.remove('d-none');
                tuteurNIN.setAttribute('readonly', true);
                tuteurNIN.style.backgroundColor = '#f8f9fa';
                
                Swal.fire({
                    icon: 'success',
                    title: 'تم العثور على الولي',
                    text: `الولي: ${tuteurName.textContent}`,
                    confirmButtonText: 'حسنًا'
                });
            } else {
                // Tuteur doesn't exist - show creation modal
                selectedTuteurNIN = nin;
                tuteurNIN.setAttribute('readonly', true);
                tuteurNIN.style.backgroundColor = '#f8f9fa';
                
                // Show modal with signup form
                showTuteurCreationModal(nin);
            }
        } catch (error) {
            Swal.fire({
                icon: 'error',
                title: 'خطأ',
                text: error.message || 'حدث خطأ أثناء التحقق من الولي',
                confirmButtonText: 'حسنًا'
            });
        } finally {
            checkTuteurBtn.disabled = false;
            checkTuteurBtn.innerHTML = '<i class="fa-solid fa-search me-1"></i> التحقق';
        }
    });
    
    // Step navigation handlers
    nextToStep2.addEventListener('click', function() {
        if (!selectedTuteurNIN) {
            Swal.fire({
                icon: 'warning',
                title: 'تحذير',
                text: 'الرجاء التحقق من الولي أولاً',
                confirmButtonText: 'حسنًا'
            });
            return;
        }
        step1.classList.add('d-none');
        step2.classList.remove('d-none');
    });
    
    nextToStep3.addEventListener('click', function() {
        // Validate step 2 fields
        const typeEcole = document.getElementById('typeEcole').value;
        const niveau = document.getElementById('niveau').value;
        const wilaya = document.getElementById('wilayaSelect').value;
        const commune = document.getElementById('communeSelect').value;
        const ecole = document.getElementById('ecoleSelect').value;
        
        if (!typeEcole || !niveau || !wilaya || !commune || !ecole) {
            Swal.fire({
                icon: 'warning',
                title: 'تحذير',
                text: 'الرجاء ملء جميع الحقول في الخطوة 2',
                confirmButtonText: 'حسنًا'
            });
            return;
        }
        
        step2.classList.add('d-none');
        step3.classList.remove('d-none');
        updateClasseOptions();
    });
    
    prevToStep1.addEventListener('click', function() {
        step2.classList.add('d-none');
        step1.classList.remove('d-none');
    });
    
    prevToStep2.addEventListener('click', function() {
        step3.classList.add('d-none');
        step2.classList.remove('d-none');
    });
    
    // School selection logic (similar to tuteur dashboard)
    const wilayaSelect = document.getElementById('wilayaSelect');
    const communeSelect = document.getElementById('communeSelect');
    const ecoleSelect = document.getElementById('ecoleSelect');
    const typeEcole = document.getElementById('typeEcole');
    const niveau = document.getElementById('niveau');
    
    wilayaSelect.addEventListener('change', async function() {
        const wilayaCode = this.value;
        communeSelect.innerHTML = '<option value="">اختر...</option>';
        communeSelect.disabled = !wilayaCode;
        ecoleSelect.innerHTML = '<option value="">اختر...</option>';
        ecoleSelect.disabled = true;
        
        if (wilayaCode) {
            try {
                const response = await apiFetch(`/api/communes/by-wilaya/${wilayaCode}`);
                // Handle response - it might be an array or wrapped in an object
                const communes = Array.isArray(response) ? response : (response.data || response.communes || []);
                communes.forEach(commune => {
                    const option = document.createElement('option');
                    option.value = commune.code_comm;
                    option.textContent = commune.lib_comm_ar;
                    communeSelect.appendChild(option);
                });
                communeSelect.disabled = false;
            } catch (error) {
                console.error('Error loading communes:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ',
                    text: 'حدث خطأ أثناء تحميل البلديات',
                    confirmButtonText: 'حسنًا'
                });
            }
        }
    });
    
    // Load schools when all criteria are selected
    function loadSchools() {
        const typeEcoleVal = typeEcole.value;
        const niveauVal = niveau.value;
        const communeVal = communeSelect.value;
        
        ecoleSelect.innerHTML = '<option value="">اختر...</option>';
        ecoleSelect.disabled = true;
        
        if (typeEcoleVal && niveauVal && communeVal) {
            const filters = {
                type_ecole: typeEcoleVal,
                niveau: niveauVal,
                code_commune: communeVal
            };
            
            apiFetch(`/api/etablissements?${new URLSearchParams(filters)}`)
                .then(response => {
                    if (response && response.length > 0) {
                        response.forEach(ecole => {
                            const option = document.createElement('option');
                            option.value = ecole.code_etabliss;
                            option.textContent = ecole.nom_etabliss;
                            ecoleSelect.appendChild(option);
                        });
                        ecoleSelect.disabled = false;
                    }
                })
                .catch(error => {
                    console.error('Error loading schools:', error);
                });
        }
    }
    
    typeEcole.addEventListener('change', loadSchools);
    niveau.addEventListener('change', loadSchools);
    communeSelect.addEventListener('change', loadSchools);
    
    // Update classe options based on niveau
    function updateClasseOptions() {
        const niveauVal = niveau.value;
        const classeSelect = document.getElementById('classeSelect');
        classeSelect.innerHTML = '<option value="">اختر...</option>';
        
        let options = [];
        if (niveauVal === 'ابتدائي') {
            options = ['السنة الأولى ابتدائي', 'السنة الثانية ابتدائي', 'السنة الثالثة ابتدائي', 'السنة الرابعة ابتدائي', 'السنة الخامسة ابتدائي'];
        } else if (niveauVal === 'متوسط') {
            options = ['السنة الأولى متوسط', 'السنة الثانية متوسط', 'السنة الثالثة متوسط', 'السنة الرابعة متوسط'];
        } else if (niveauVal === 'ثانوي') {
            options = ['السنة الأولى ثانوي', 'السنة الثانية ثانوي', 'السنة الثالثة ثانوي'];
        }
        
        options.forEach(opt => {
            const option = document.createElement('option');
            option.value = opt;
            option.textContent = opt;
            classeSelect.appendChild(option);
        });
    }
    
    niveau.addEventListener('change', updateClasseOptions);
    
    // Commune naissance logic
    const wilayaNaiss = document.getElementById('wilayaNaiss');
    const communeNaiss = document.getElementById('communeNaiss');
    
    wilayaNaiss.addEventListener('change', async function() {
        const wilayaCode = this.value;
        communeNaiss.innerHTML = '<option value="">اختر...</option>';
        communeNaiss.disabled = !wilayaCode;
        
        if (wilayaCode) {
            try {
                const response = await apiFetch(`/api/communes/by-wilaya/${wilayaCode}`);
                // Handle response - it might be an array or wrapped in an object
                const communes = Array.isArray(response) ? response : (response.data || response.communes || []);
                communes.forEach(commune => {
                    const option = document.createElement('option');
                    option.value = commune.code_comm;
                    option.textContent = commune.lib_comm_ar;
                    communeNaiss.appendChild(option);
                });
                communeNaiss.disabled = false;
            } catch (error) {
                console.error('Error loading communes:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ',
                    text: 'حدث خطأ أثناء تحميل البلديات',
                    confirmButtonText: 'حسنًا'
                });
            }
        }
    });
    
    // Relation tuteur logic (same as dashboard)
    const relationSelect = document.getElementById('relationSelect');
    const nomPereWrapper = document.getElementById('nomPereWrapper');
    const prenomPereWrapper = document.getElementById('prenomPereWrapper');
    const motherSelectWrapper = document.getElementById('motherSelectWrapper');
    const ninPereWrapper = document.getElementById('ninPereWrapper');
    const nssPereWrapper = document.getElementById('nssPereWrapper');
    const ninMereWrapper = document.getElementById('ninMereWrapper');
    const nssMereWrapper = document.getElementById('nssMereWrapper');
    const ninGuardianWrapper = document.getElementById('ninGuardianWrapper');
    const nssGuardianWrapper = document.getElementById('nssGuardianWrapper');
    const guardianDocWrapper = document.getElementById('guardianDocWrapper');
    
    // Get mother/father NIN wrappers
    const motherNINWrapper = document.getElementById('motherNINWrapper');
    const fatherNINWrapper = document.getElementById('fatherNINWrapper');
    const motherNIN = document.getElementById('motherNIN');
    const fatherNIN = document.getElementById('fatherNIN');
    
    function updateFormForRelation(relation) {
        // Hide all wrappers first (with null checks)
        if (nomPereWrapper) nomPereWrapper.style.display = 'none';
        if (prenomPereWrapper) prenomPereWrapper.style.display = 'none';
        if (motherSelectWrapper) motherSelectWrapper.style.display = 'none';
        if (motherNINWrapper) motherNINWrapper.style.display = 'none';
        if (fatherNINWrapper) fatherNINWrapper.style.display = 'none';
        if (ninPereWrapper) ninPereWrapper.style.display = 'none';
        if (nssPereWrapper) nssPereWrapper.style.display = 'none';
        if (ninMereWrapper) ninMereWrapper.style.display = 'none';
        if (nssMereWrapper) nssMereWrapper.style.display = 'none';
        if (ninGuardianWrapper) ninGuardianWrapper.style.display = 'none';
        if (nssGuardianWrapper) nssGuardianWrapper.style.display = 'none';
        if (guardianDocWrapper) guardianDocWrapper.style.display = 'none';
        
        // Clear values
        if (motherNIN) motherNIN.value = '';
        if (fatherNIN) fatherNIN.value = '';
        const motherIDEl = document.getElementById('motherID');
        const fatherIDEl = document.getElementById('fatherID');
        if (motherIDEl) motherIDEl.value = '';
        if (fatherIDEl) fatherIDEl.value = '';
        
        // Clear name displays
        const motherNameDisplay = document.getElementById('motherNameDisplay');
        const fatherNameDisplay = document.getElementById('fatherNameDisplay');
        if (motherNameDisplay) motherNameDisplay.style.display = 'none';
        if (fatherNameDisplay) fatherNameDisplay.style.display = 'none';
        
        // Show fields based on relation
        if (relation === '1') {
            // ولي (الأب)
            if (nomPereWrapper) nomPereWrapper.style.display = 'block';
            if (prenomPereWrapper) prenomPereWrapper.style.display = 'block';
            const nomPereLabel = document.getElementById('nomPereLabel');
            const prenomPereLabel = document.getElementById('prenomPereLabel');
            if (nomPereLabel) nomPereLabel.textContent = 'لقب الأب بالعربية';
            if (prenomPereLabel) prenomPereLabel.textContent = 'اسم الأب بالعربية';
            
            // Auto-fill from tuteur data
            if (selectedTuteurData && nomPere && prenomPere) {
                nomPere.value = selectedTuteurData.nom_ar || '';
                prenomPere.value = selectedTuteurData.prenom_ar || '';
                nomPere.setAttribute('readonly', true);
                prenomPere.setAttribute('readonly', true);
                nomPere.style.backgroundColor = '#f8f9fa';
                prenomPere.style.backgroundColor = '#f8f9fa';
            }
        } else if (relation === '2') {
            // ولي (الأم)
            if (nomPereWrapper) nomPereWrapper.style.display = 'block';
            if (prenomPereWrapper) prenomPereWrapper.style.display = 'block';
            const nomPereLabel = document.getElementById('nomPereLabel');
            const prenomPereLabel = document.getElementById('prenomPereLabel');
            if (nomPereLabel) nomPereLabel.textContent = 'لقب الأم بالعربية';
            if (prenomPereLabel) prenomPereLabel.textContent = 'اسم الأم بالعربية';
            
            // Auto-fill from tuteur data
            if (selectedTuteurData && nomPere && prenomPere) {
                nomPere.value = selectedTuteurData.nom_ar || '';
                prenomPere.value = selectedTuteurData.prenom_ar || '';
                nomPere.setAttribute('readonly', true);
                prenomPere.setAttribute('readonly', true);
                nomPere.style.backgroundColor = '#f8f9fa';
                prenomPere.style.backgroundColor = '#f8f9fa';
            }
        } else if (relation === '3') {
            // وصي - Show mother and father NIN inputs
            if (nomPereWrapper) nomPereWrapper.style.display = 'block';
            if (prenomPereWrapper) prenomPereWrapper.style.display = 'block';
            const nomPereLabel = document.getElementById('nomPereLabel');
            const prenomPereLabel = document.getElementById('prenomPereLabel');
            if (nomPereLabel) nomPereLabel.textContent = 'لقب الوصي بالعربية';
            if (prenomPereLabel) prenomPereLabel.textContent = 'اسم الوصي بالعربية';
            
            // Auto-fill from tuteur data
            if (selectedTuteurData && nomPere && prenomPere) {
                nomPere.value = selectedTuteurData.nom_ar || '';
                prenomPere.value = selectedTuteurData.prenom_ar || '';
                nomPere.setAttribute('readonly', true);
                prenomPere.setAttribute('readonly', true);
                nomPere.style.backgroundColor = '#f8f9fa';
                prenomPere.style.backgroundColor = '#f8f9fa';
            }
            
            // Show mother and father NIN inputs
            if (motherNINWrapper) motherNINWrapper.style.display = 'block';
            if (fatherNINWrapper) fatherNINWrapper.style.display = 'block';
            
            // Show guardian NIN/NSS (from tuteur)
            if (ninGuardianWrapper) ninGuardianWrapper.style.display = 'block';
            if (nssGuardianWrapper) nssGuardianWrapper.style.display = 'block';
            if (guardianDocWrapper) guardianDocWrapper.style.display = 'block';
            
            // Fill guardian NIN/NSS from tuteur
            if (selectedTuteurNIN) {
                const ninGuardianEl = document.getElementById('ninGuardian');
                if (ninGuardianEl) ninGuardianEl.value = selectedTuteurNIN;
                // NSS will be loaded from tuteur data if available
            }
        } else {
            // Clear and make editable if no relation selected
            if (nomPere && prenomPere) {
                nomPere.value = '';
                prenomPere.value = '';
                nomPere.removeAttribute('readonly');
                prenomPere.removeAttribute('readonly');
                nomPere.style.backgroundColor = '';
                prenomPere.style.backgroundColor = '';
            }
        }
    }
    
    relationSelect.addEventListener('change', function() {
        updateFormForRelation(this.value);
    });
    
    // Clear mother/father name displays when NIN changes
    if (motherNIN) {
        motherNIN.addEventListener('input', function() {
            const motherNameDisplay = document.getElementById('motherNameDisplay');
            const motherID = document.getElementById('motherID');
            if (motherNameDisplay) motherNameDisplay.style.display = 'none';
            if (motherID) motherID.value = '';
        });
    }
    
    if (fatherNIN) {
        fatherNIN.addEventListener('input', function() {
            const fatherNameDisplay = document.getElementById('fatherNameDisplay');
            const fatherID = document.getElementById('fatherID');
            if (fatherNameDisplay) fatherNameDisplay.style.display = 'none';
            if (fatherID) fatherID.value = '';
        });
    }
    
    // Mother NIN check
    const checkMotherNINBtn = document.getElementById('checkMotherNINBtn');
    if (checkMotherNINBtn && motherNIN) {
        checkMotherNINBtn.addEventListener('click', async function() {
            const nin = motherNIN.value.trim();
            
            if (!nin || nin.length !== 18) {
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ',
                    text: 'الرجاء إدخال رقم وطني صحيح (18 رقمًا)',
                    confirmButtonText: 'حسنًا'
                });
                return;
            }
            
            try {
                checkMotherNINBtn.disabled = true;
                checkMotherNINBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';
                
                const response = await apiFetch('/api/check/mother/nin', {
                    method: 'POST',
                    body: JSON.stringify({ nin: nin })
                });
                
                if (response && response.exists && response.mother) {
                    // Mother exists - use the data directly from the response
                    const mother = response.mother;
                    
                    document.getElementById('motherID').value = mother.id;
                    document.getElementById('ninMere').value = mother.nin;
                    document.getElementById('nssMere').value = mother.nss || '';
                    document.getElementById('ninMereWrapper').style.display = 'block';
                    document.getElementById('nssMereWrapper').style.display = 'block';
                    
                    // Display mother name
                    const motherNameDisplay = document.getElementById('motherNameDisplay');
                    const motherNameText = document.getElementById('motherNameText');
                    if (motherNameDisplay && motherNameText) {
                        const fullName = `${mother.nom_ar || ''} ${mother.prenom_ar || ''}`.trim();
                        motherNameText.textContent = fullName;
                        motherNameDisplay.style.display = 'block';
                    }
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'تم العثور على الأم',
                        text: `${mother.nom_ar || ''} ${mother.prenom_ar || ''}`,
                        confirmButtonText: 'حسنًا'
                    });
                } else {
                    // Mother doesn't exist - show creation modal
                    document.getElementById('mother_modal_nin').value = nin;
                    document.getElementById('motherCreationNIN').value = nin;
                    const modal = new bootstrap.Modal(document.getElementById('motherCreationModal'));
                    modal.show();
                }
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ',
                    text: error.message || 'حدث خطأ أثناء التحقق من الأم',
                    confirmButtonText: 'حسنًا'
                });
            } finally {
                checkMotherNINBtn.disabled = false;
                checkMotherNINBtn.innerHTML = '<i class="fa-solid fa-search"></i>';
            }
        });
    }
    
    // Father NIN check
    const checkFatherNINBtn = document.getElementById('checkFatherNINBtn');
    if (checkFatherNINBtn && fatherNIN) {
        checkFatherNINBtn.addEventListener('click', async function() {
            const nin = fatherNIN.value.trim();
            
            if (!nin || nin.length !== 18) {
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ',
                    text: 'الرجاء إدخال رقم وطني صحيح (18 رقمًا)',
                    confirmButtonText: 'حسنًا'
                });
                return;
            }
            
            try {
                checkFatherNINBtn.disabled = true;
                checkFatherNINBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';
                
                const response = await apiFetch('/api/check/father/nin', {
                    method: 'POST',
                    body: JSON.stringify({ nin: nin })
                });
                
                if (response && response.exists && response.father) {
                    // Father exists - use the data directly from the response
                    const father = response.father;
                    
                    document.getElementById('fatherID').value = father.id;
                    document.getElementById('ninPere').value = father.nin;
                    document.getElementById('nssPere').value = father.nss || '';
                    document.getElementById('ninPereWrapper').style.display = 'block';
                    document.getElementById('nssPereWrapper').style.display = 'block';
                    
                    // Display father name
                    const fatherNameDisplay = document.getElementById('fatherNameDisplay');
                    const fatherNameText = document.getElementById('fatherNameText');
                    if (fatherNameDisplay && fatherNameText) {
                        const fullName = `${father.nom_ar || ''} ${father.prenom_ar || ''}`.trim();
                        fatherNameText.textContent = fullName;
                        fatherNameDisplay.style.display = 'block';
                    }
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'تم العثور على الأب',
                        text: `${father.nom_ar || ''} ${father.prenom_ar || ''}`,
                        confirmButtonText: 'حسنًا'
                    });
                } else {
                    // Father doesn't exist - show creation modal
                    document.getElementById('father_modal_nin').value = nin;
                    document.getElementById('fatherCreationNIN').value = nin;
                    const modal = new bootstrap.Modal(document.getElementById('fatherCreationModal'));
                    modal.show();
                }
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ',
                    text: error.message || 'حدث خطأ أثناء التحقق من الأب',
                    confirmButtonText: 'حسنًا'
                });
            } finally {
                checkFatherNINBtn.disabled = false;
                checkFatherNINBtn.innerHTML = '<i class="fa-solid fa-search"></i>';
            }
        });
    }
    
    // Handle categorie_sociale dropdown change for mother modal
    const motherModalCategorieSelect = document.getElementById('mother_modal_categorie_sociale');
    const motherModalMontantWrapper = document.getElementById('mother_modal_montant_wrapper');
    const motherModalMontantInput = document.getElementById('mother_modal_montant_s');
    
    if (motherModalCategorieSelect && motherModalMontantWrapper && motherModalMontantInput) {
        motherModalCategorieSelect.addEventListener('change', function() {
            if (this.value === 'الدخل الشهري أقل أو يساوي مبلغ الأجر الوطني الأدنى المضمون') {
                motherModalMontantWrapper.style.display = 'block';
                motherModalMontantInput.required = true;
            } else {
                motherModalMontantWrapper.style.display = 'none';
                motherModalMontantInput.required = false;
                motherModalMontantInput.value = '';
            }
        });
    }
    
    // Submit mother creation form
    const submitMotherForm = document.getElementById('submitMotherForm');
    if (submitMotherForm) {
        submitMotherForm.addEventListener('click', async function() {
            const form = document.getElementById('motherCreationForm');
            const formData = new FormData(form);
            const data = Object.fromEntries(formData);
            
            // Add tuteur_nin
            data.tuteur_nin = selectedTuteurNIN;
            
            try {
                submitMotherForm.disabled = true;
                submitMotherForm.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> جاري الإنشاء...';
                
                const response = await apiFetch('/api/admin/mothers', {
                    method: 'POST',
                    body: JSON.stringify(data)
                });
                
                // Close modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('motherCreationModal'));
                modal.hide();
                
                // Fill mother data in form
                document.getElementById('motherID').value = response.id || response.data?.id;
                document.getElementById('ninMere').value = data.nin;
                document.getElementById('nssMere').value = data.nss || '';
                document.getElementById('ninMereWrapper').style.display = 'block';
                document.getElementById('nssMereWrapper').style.display = 'block';
                
                // Display mother name
                const motherNameDisplay = document.getElementById('motherNameDisplay');
                const motherNameText = document.getElementById('motherNameText');
                if (motherNameDisplay && motherNameText) {
                    const fullName = `${data.nom_ar || ''} ${data.prenom_ar || ''}`.trim();
                    motherNameText.textContent = fullName;
                    motherNameDisplay.style.display = 'block';
                }
                
                // Clear modal form
                form.reset();
                
                Swal.fire({
                    icon: 'success',
                    title: 'نجح!',
                    text: 'تم إنشاء الأم بنجاح',
                    confirmButtonText: 'حسنًا'
                });
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ',
                    text: error.message || 'حدث خطأ أثناء إنشاء الأم',
                    confirmButtonText: 'حسنًا'
                });
            } finally {
                submitMotherForm.disabled = false;
                submitMotherForm.innerHTML = 'إنشاء الأم';
            }
        });
    }
    
    // Submit father creation form
    const submitFatherForm = document.getElementById('submitFatherForm');
    if (submitFatherForm) {
        submitFatherForm.addEventListener('click', async function() {
            const form = document.getElementById('fatherCreationForm');
            const formData = new FormData(form);
            const data = Object.fromEntries(formData);
            
            // Add tuteur_nin
            data.tuteur_nin = selectedTuteurNIN;
            
            try {
                submitFatherForm.disabled = true;
                submitFatherForm.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> جاري الإنشاء...';
                
                const response = await apiFetch('/api/admin/fathers', {
                    method: 'POST',
                    body: JSON.stringify(data)
                });
                
                // Close modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('fatherCreationModal'));
                modal.hide();
                
                // Fill father data in form
                document.getElementById('fatherID').value = response.id || response.data?.id;
                document.getElementById('ninPere').value = data.nin;
                document.getElementById('nssPere').value = data.nss || '';
                document.getElementById('ninPereWrapper').style.display = 'block';
                document.getElementById('nssPereWrapper').style.display = 'block';
                
                // Display father name
                const fatherNameDisplay = document.getElementById('fatherNameDisplay');
                const fatherNameText = document.getElementById('fatherNameText');
                if (fatherNameDisplay && fatherNameText) {
                    const fullName = `${data.nom_ar || ''} ${data.prenom_ar || ''}`.trim();
                    fatherNameText.textContent = fullName;
                    fatherNameDisplay.style.display = 'block';
                }
                
                // Clear modal form
                form.reset();
                
                Swal.fire({
                    icon: 'success',
                    title: 'نجح!',
                    text: 'تم إنشاء الأب بنجاح',
                    confirmButtonText: 'حسنًا'
                });
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ',
                    text: error.message || 'حدث خطأ أثناء إنشاء الأب',
                    confirmButtonText: 'حسنًا'
                });
            } finally {
                submitFatherForm.disabled = false;
                submitFatherForm.innerHTML = 'إنشاء الأب';
            }
        });
    }
    
    // Handicap toggle
    const handicapYes = document.getElementById('handicapYes');
    const handicapNo = document.getElementById('handicapNo');
    const handicapNatureWrapper = document.getElementById('handicapNatureWrapper');
    const handicapPercentageWrapper = document.getElementById('handicapPercentageWrapper');
    
    function toggleHandicapDetails(show) {
        handicapNatureWrapper.classList.toggle('d-none', !show);
        handicapPercentageWrapper.classList.toggle('d-none', !show);
        
        // Handle required attribute to prevent HTML5 validation errors on hidden fields
        const handicapNatureInput = document.getElementById('handicapNature');
        const handicapPercentageInput = document.getElementById('handicapPercentage');
        
        if (handicapNatureInput) {
            if (show) {
                handicapNatureInput.setAttribute('required', 'required');
            } else {
                handicapNatureInput.removeAttribute('required');
                handicapNatureInput.value = '';
            }
        }
        if (handicapPercentageInput) {
            if (show) {
                handicapPercentageInput.setAttribute('required', 'required');
            } else {
                handicapPercentageInput.removeAttribute('required');
                handicapPercentageInput.value = '';
            }
        }
    }
    
    handicapYes.addEventListener('change', () => toggleHandicapDetails(true));
    handicapNo.addEventListener('change', () => toggleHandicapDetails(false));
    
    // Form submission
    const adminAddStudentForm = document.getElementById('adminAddStudentForm');
    adminAddStudentForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        // Get selected relation_tuteur
        const selectedRelation = relationSelect.value;
        
        // Set relation_tuteur, mother_id, and father_id based on selected relation (same as dashboard)
        if (selectedRelation === '1') {
            // الولي (الأب): Set mother_id, relation_tuteur = 1, no father_id
            const motherID = document.getElementById('motherID').value;
            if (motherID) {
                formData.set('mother_id', motherID);
            }
            formData.set('relation_tuteur', '1');
            formData.delete('father_id');
        } else if (selectedRelation === '2') {
            // الولي (الأم): Set father_id, relation_tuteur = 2, no mother_id
            const fatherID = document.getElementById('fatherID').value;
            if (fatherID) {
                formData.set('father_id', fatherID);
            }
            formData.set('relation_tuteur', '2');
            formData.delete('mother_id');
        } else if (selectedRelation === '3') {
            // وصي: Set both mother_id and father_id, relation_tuteur = 3
            const motherID = document.getElementById('motherID').value;
            const fatherID = document.getElementById('fatherID').value;
            if (motherID) {
                formData.set('mother_id', motherID);
            }
            if (fatherID) {
                formData.set('father_id', fatherID);
            }
            formData.set('relation_tuteur', '3');
        }
        
        // Remove NIN fields from submission (they're only for verification)
        formData.delete('mother_nin');
        formData.delete('father_nin');
        
        // Add tuteur_nin for admin route
        if (selectedTuteurNIN) {
            formData.append('tuteur_nin', selectedTuteurNIN);
        }
        
        try {
            const response = await apiFetch('/api/admin/eleves', {
                method: 'POST',
                body: formData
            });
            
            Swal.fire({
                icon: 'success',
                title: 'نجح!',
                text: 'تم إضافة التلميذ بنجاح',
                confirmButtonText: 'حسنًا'
            }).then(() => {
                // Reset form or redirect
                window.location.reload();
            });
        } catch (error) {
            // Handle validation errors (422)
            if (error.status === 422 && error.errors) {
                const form = document.getElementById('adminAddStudentForm');
                
                // Clear previous errors
                if (form) {
                    form.querySelectorAll('.error-msg').forEach(e => e.remove());
                    form.querySelectorAll('.is-invalid').forEach(e => e.classList.remove('is-invalid'));
                    
                    // Display errors on corresponding fields
                    Object.keys(error.errors).forEach(fieldName => {
                        const field = form.querySelector(`[name="${fieldName}"]`);
                        if (field) {
                            field.classList.add('is-invalid');
                            const errorMsg = document.createElement('div');
                            errorMsg.className = 'error-msg text-danger small mt-1';
                            errorMsg.textContent = error.errors[fieldName][0]; // Show first error message
                            field.parentElement.appendChild(errorMsg);
                        }
                    });
                    
                    // Scroll to first error
                    const firstErrorField = form.querySelector('.is-invalid');
                    if (firstErrorField) {
                        firstErrorField.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                }
                
                // Show error message with all validation errors
                const errorMessages = Object.values(error.errors).flat();
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ في التحقق من البيانات',
                    html: errorMessages.map(msg => `<div style="text-align: right; direction: rtl;">${msg}</div>`).join('<br>'),
                    confirmButtonText: 'حسنًا'
                });
            } else {
                // Other errors
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ',
                    text: error.message || 'حدث خطأ أثناء إضافة التلميذ',
                    confirmButtonText: 'حسنًا'
                });
            }
        }
    });
    
    // Tuteur creation modal (simplified - will load signup form fields)
    function showTuteurCreationModal(nin) {
        const modal = new bootstrap.Modal(document.getElementById('tuteurCreationModal'));
        document.getElementById('tuteurFormContent').innerHTML = `
            <p class="text-center mb-4">الولي بالرقم الوطني <strong>${nin}</strong> غير موجود. يرجى ملء المعلومات التالية لإنشاء الولي:</p>
            <div class="alert alert-info">
                <i class="fa-solid fa-info-circle"></i> سيتم استخدام نفس نموذج التسجيل لإنشاء الولي.
            </div>
            <p class="text-center">
                <a href="/signup?nin=${nin}" target="_blank" class="btn btn-warning-custom">
                    <i class="fa-solid fa-external-link me-1"></i> فتح نموذج التسجيل في نافذة جديدة
                </a>
            </p>
        `;
        modal.show();
    }
    
    // If NIN is provided in URL, auto-fill and check
    const urlParams = new URLSearchParams(window.location.search);
    const ninParam = urlParams.get('nin');
    if (ninParam && ninParam.length === 18) {
        tuteurNIN.value = ninParam;
        setTimeout(() => checkTuteurBtn.click(), 500);
    }
});
</script>
@endpush

@endsection
