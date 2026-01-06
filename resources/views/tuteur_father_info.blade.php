@extends('layouts.main')

@section('title', 'معلومات الأب')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
.profile-container {
    direction: rtl;
    text-align: right;
    background: #f9f9fb;
    min-height: 100vh;
    padding: 40px;
}

.profile-card {
    background: white;
    border-radius: 15px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    padding: 30px;
    max-width: 900px;
    margin: auto;
}

.profile-card h3 {
    color: #0f033a;
    font-weight: bold;
    border-bottom: 2px solid #fdae4b;
    padding-bottom: 10px;
    margin-bottom: 30px;
}

.profile-info label {
    font-weight: bold;
    color: #0f033a;
}

.profile-info p {
    background: #f6f8fa;
    border-radius: 8px;
    padding: 8px 12px;
    margin-bottom: 0;
}

.btn-edit {
    background-color: #fdae4b;
    color: #0f033a;
    font-weight: bold;
    border: none;
    padding: 10px 30px;
    border-radius: 8px;
}

.btn-edit:hover {
    background-color: #f5a742;
    color: #0f033a;
}

.btn-soft {
    background: #f6f8fa;
    border: 1px solid #e6e9ef;
    color: #0f033a;
    font-weight: 700;
    border-radius: 10px;
    padding: 10px 18px;
}

.form-label.required::after {
    content: " *";
    color: #dc3545;
}
</style>
@endpush

@section('content')
<div class="profile-container">
    <div class="profile-card">
        <h3><i class="fa-solid fa-mars me-2"></i>معلومات الأب</h3>

        {{-- Fallback (no JS) --}}
        <noscript>
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </noscript>

        @if($father)
            <!-- View mode -->
            <div id="fatherView">
                <div class="profile-info row g-3">
                    <div class="col-md-6">
                        <label>الرقم الوطني (NIN):</label>
                        <p>{{ $father->nin ?? '—' }}</p>
                    </div>
                    <div class="col-md-6">
                        <label>رقم الضمان الاجتماعي (NSS):</label>
                        <p>{{ $father->nss ?? '—' }}</p>
                    </div>

                    <div class="col-md-6">
                        <label>لقب الأب بالعربية:</label>
                        <p>{{ $father->nom_ar ?? '—' }}</p>
                    </div>
                    <div class="col-md-6">
                        <label>اسم الأب بالعربية:</label>
                        <p>{{ $father->prenom_ar ?? '—' }}</p>
                    </div>

                    <div class="col-md-6">
                        <label>لقب الأب بالفرنسية:</label>
                        <p>{{ $father->nom_fr ?? '—' }}</p>
                    </div>
                    <div class="col-md-6">
                        <label>اسم الأب بالفرنسية:</label>
                        <p>{{ $father->prenom_fr ?? '—' }}</p>
                    </div>

                    <div class="col-md-6">
                        <label>الفئة الاجتماعية:</label>
                        <p>{{ $father->categorie_sociale ?? 'غير محدد' }}</p>
                    </div>
                    <div class="col-md-6">
                        <label>مبلغ الدخل الشهري:</label>
                        <p>{{ $father->montant_s ? number_format($father->montant_s, 2) . ' دج' : 'غير محدد' }}</p>
                    </div>
                </div>

                <div class="d-flex gap-2 justify-content-center mt-4 flex-wrap">
                    <button type="button" class="btn btn-edit" id="toggleFatherEditBtn">
                        <i class="fa-solid fa-pen-to-square me-2"></i>تعديل
                    </button>
                    <a href="{{ route('dashboard') }}" class="btn btn-soft">عودة إلى اللوحة</a>
                </div>
            </div>

            <!-- Edit mode -->
            <div id="fatherEdit" class="d-none mt-4">
                <form method="POST" action="{{ route('tuteur.father.update') }}" novalidate class="js-swal-submit">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label required">الرقم الوطني للأب (NIN)</label>
                            <input type="text" name="nin" class="form-control" maxlength="18" inputmode="numeric" pattern="\d{18}" required value="{{ old('nin', $father->nin) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">رقم الضمان الاجتماعي للأب (NSS)</label>
                            <input type="text" name="nss" class="form-control" maxlength="12" inputmode="numeric" pattern="\d{12}" value="{{ old('nss', $father->nss) }}">
                        </div>
                    </div>

                    <div class="row g-3 mt-1">
                        <div class="col-md-6">
                            <label class="form-label required">لقب الأب بالعربية</label>
                            <input type="text" name="nom_ar" class="form-control" required value="{{ old('nom_ar', $father->nom_ar) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required">اسم الأب بالعربية</label>
                            <input type="text" name="prenom_ar" class="form-control" required value="{{ old('prenom_ar', $father->prenom_ar) }}">
                        </div>
                    </div>

                    <div class="row g-3 mt-1">
                        <div class="col-md-6">
                            <label class="form-label">لقب الأب بالفرنسية</label>
                            <input type="text" name="nom_fr" class="form-control" value="{{ old('nom_fr', $father->nom_fr) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">اسم الأب بالفرنسية</label>
                            <input type="text" name="prenom_fr" class="form-control" value="{{ old('prenom_fr', $father->prenom_fr) }}">
                        </div>
                    </div>

                    @php
                        $lowIncome = 'الدخل الشهري أقل أو يساوي مبلغ الأجر الوطني الأدنى المضمون';
                        $catsOld = old('categorie_sociale', $father->categorie_sociale);
                    @endphp
                    <div class="row g-3 mt-1">
                        <div class="col-md-6">
                            <label class="form-label">الفئة الاجتماعية</label>
                            <select name="categorie_sociale" id="fatherCats" class="form-select">
                                <option value="">—</option>
                                <option value="عديم الدخل" {{ $catsOld === 'عديم الدخل' ? 'selected' : '' }}>عديم الدخل</option>
                                <option value="{{ $lowIncome }}" {{ $catsOld === $lowIncome ? 'selected' : '' }}>{{ $lowIncome }}</option>
                            </select>
                        </div>
                        <div class="col-md-6" id="fatherMontantWrap">
                            <label class="form-label">مبلغ الدخل الشهري</label>
                            <input type="number" name="montant_s" class="form-control" step="0.01" min="0" value="{{ old('montant_s', $father->montant_s) }}">
                        </div>
                    </div>

                    <div class="d-flex gap-2 justify-content-center mt-4 flex-wrap">
                        <button type="submit" class="btn btn-edit js-submit-btn">
                            <i class="fa-solid fa-floppy-disk me-2"></i>حفظ
                        </button>
                        <button type="button" class="btn btn-soft" id="cancelFatherEditBtn">إلغاء</button>
                    </div>
                </form>
            </div>
        @else
            <div class="alert alert-info text-center">
                <p class="mb-0">لا توجد معلومات الأب مسجلة</p>
            </div>
            <div class="mt-4">
                <form method="POST" action="{{ route('tuteur.father.store') }}" novalidate class="js-swal-submit">
                    @csrf

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label required">الرقم الوطني للأب (NIN)</label>
                            <input type="text" name="nin" class="form-control" maxlength="18" inputmode="numeric" pattern="\d{18}" required value="{{ old('nin') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">رقم الضمان الاجتماعي للأب (NSS)</label>
                            <input type="text" name="nss" class="form-control" maxlength="12" inputmode="numeric" pattern="\d{12}" value="{{ old('nss') }}">
                        </div>
                    </div>

                    <div class="row g-3 mt-1">
                        <div class="col-md-6">
                            <label class="form-label required">لقب الأب بالعربية</label>
                            <input type="text" name="nom_ar" class="form-control" required value="{{ old('nom_ar') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required">اسم الأب بالعربية</label>
                            <input type="text" name="prenom_ar" class="form-control" required value="{{ old('prenom_ar') }}">
                        </div>
                    </div>

                    <div class="row g-3 mt-1">
                        <div class="col-md-6">
                            <label class="form-label">لقب الأب بالفرنسية</label>
                            <input type="text" name="nom_fr" class="form-control" value="{{ old('nom_fr') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">اسم الأب بالفرنسية</label>
                            <input type="text" name="prenom_fr" class="form-control" value="{{ old('prenom_fr') }}">
                        </div>
                    </div>

                    @php
                        $lowIncome = 'الدخل الشهري أقل أو يساوي مبلغ الأجر الوطني الأدنى المضمون';
                        $catsOld = old('categorie_sociale');
                    @endphp
                    <div class="row g-3 mt-1">
                        <div class="col-md-6">
                            <label class="form-label">الفئة الاجتماعية</label>
                            <select name="categorie_sociale" id="fatherCats" class="form-select">
                                <option value="">—</option>
                                <option value="عديم الدخل" {{ $catsOld === 'عديم الدخل' ? 'selected' : '' }}>عديم الدخل</option>
                                <option value="{{ $lowIncome }}" {{ $catsOld === $lowIncome ? 'selected' : '' }}>{{ $lowIncome }}</option>
                            </select>
                        </div>
                        <div class="col-md-6" id="fatherMontantWrap">
                            <label class="form-label">مبلغ الدخل الشهري</label>
                            <input type="number" name="montant_s" class="form-control" step="0.01" min="0" value="{{ old('montant_s') }}">
                        </div>
                    </div>

                    <div class="d-flex gap-2 justify-content-center mt-4 flex-wrap">
                        <button type="submit" class="btn btn-edit js-submit-btn">
                            <i class="fa-solid fa-floppy-disk me-2"></i>حفظ
                        </button>
                        <a href="{{ route('dashboard') }}" class="btn btn-soft">عودة إلى اللوحة</a>
                    </div>
                </form>
            </div>
        @endif
    </div>
</div>

<script>
(function () {
    const onlyDigits = (el, maxLen) => {
        el.addEventListener('input', () => {
            el.value = (el.value || '').replace(/\D/g, '').slice(0, maxLen);
        });
    };

    // View/Edit toggle
    const toggleBtn = document.getElementById('toggleFatherEditBtn');
    const cancelBtn = document.getElementById('cancelFatherEditBtn');
    const view = document.getElementById('fatherView');
    const edit = document.getElementById('fatherEdit');

    if (toggleBtn && cancelBtn && view && edit) {
        toggleBtn.addEventListener('click', () => {
            view.classList.add('d-none');
            edit.classList.remove('d-none');
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
        cancelBtn.addEventListener('click', () => {
            edit.classList.add('d-none');
            view.classList.remove('d-none');
        });
    }

    // Digits restriction
    document.querySelectorAll('input[name="nin"]').forEach(i => onlyDigits(i, 18));
    document.querySelectorAll('input[name="nss"]').forEach(i => onlyDigits(i, 12));

    // Conditional montant_s
    const lowIncome = 'الدخل الشهري أقل أو يساوي مبلغ الأجر الوطني الأدنى المضمون';
    const cats = document.getElementById('fatherCats');
    const wrap = document.getElementById('fatherMontantWrap');
    if (cats && wrap) {
        const sync = () => {
            wrap.style.display = (cats.value === lowIncome) ? 'block' : 'none';
        };
        cats.addEventListener('change', sync);
        sync();
    }

    // SweetAlert2 UX (toast + loading)
    const toast = (icon, title) => {
        if (!window.Swal) return;
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon,
            title,
            showConfirmButton: false,
            timer: 3200,
            timerProgressBar: true
        });
    };

    @if(session('success'))
        toast('success', @json(session('success')));
    @endif
    @if(session('error'))
        toast('error', @json(session('error')));
    @endif
    @if($errors->any())
        if (window.Swal) {
            Swal.fire({
                icon: 'error',
                title: 'تحقق من المعلومات',
                html: `<ul style="text-align:right; direction:rtl; padding-right:18px; margin:0;">{!! implode('', array_map(fn($e) => '<li>'.e($e).'</li>', $errors->all())) !!}</ul>`,
                confirmButtonText: 'حسنًا',
            });
        }
    @endif

    document.querySelectorAll('form.js-swal-submit').forEach(form => {
        form.addEventListener('submit', (e) => {
            // If SweetAlert is not available, keep default behavior
            if (!window.Swal) return;
            e.preventDefault();

            // Disable submit button + spinner
            const btn = form.querySelector('.js-submit-btn');
            if (btn) {
                btn.disabled = true;
                const original = btn.innerHTML;
                btn.dataset.originalHtml = original;
                btn.innerHTML = `<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>جارٍ الحفظ...`;
            }

            Swal.fire({
                title: 'جارٍ الحفظ...',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => Swal.showLoading(),
            });

            // Let the modal render then submit
            setTimeout(() => form.submit(), 60);
        });
    });
})();
</script>
@endsection

