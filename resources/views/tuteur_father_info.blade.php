@extends('layouts.main')

@section('title', 'معلومات الأب')

@push('styles')
<style>
.profile-container {
    direction: rtl;
    text-align: right;
    background: #f9f9fb;
    min-height: 100vh;
    padding: 40px;
}

.page-hero {
    max-width: 900px;
    margin: 0 auto 16px auto;
    background: linear-gradient(135deg, rgba(15,3,58,1) 0%, rgba(38,13,110,1) 55%, rgba(253,174,75,1) 180%);
    border-radius: 18px;
    padding: 18px 18px;
    color: #fff;
    box-shadow: 0 14px 40px rgba(15, 3, 58, 0.15);
    position: relative;
    overflow: hidden;
}
.page-hero::after{
    content: "";
    position: absolute;
    inset: -60px -60px auto auto;
    width: 220px;
    height: 220px;
    background: rgba(255,255,255,.08);
    border-radius: 50%;
    transform: rotate(20deg);
}
.page-hero h2{
    margin: 0;
    font-weight: 800;
    letter-spacing: .2px;
    font-size: 22px;
}
.page-hero p{
    margin: 6px 0 0 0;
    opacity: .88;
    font-weight: 600;
    font-size: 13px;
}

.profile-card {
    background: white;
    border-radius: 15px;
    box-shadow: 0 14px 40px rgba(15, 3, 58, 0.10);
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

.kv {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 14px;
}
@media (max-width: 768px) {
    .profile-container { padding: 18px; }
    .kv { grid-template-columns: 1fr; }
}
.kv-item {
    background: #f6f8fa;
    border: 1px solid #e6e9ef;
    border-radius: 14px;
    padding: 12px 14px;
}
.kv-item .k {
    color: rgba(15,3,58,.75);
    font-weight: 800;
    font-size: 12px;
    margin-bottom: 6px;
}
.kv-item .v {
    color: #0f033a;
    font-weight: 800;
    font-size: 14px;
    word-break: break-word;
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

.form-control, .form-select{
    border-radius: 12px;
    border: 1px solid #e6e9ef;
    padding: 12px 12px;
    font-weight: 700;
}
.form-control:focus, .form-select:focus{
    border-color: rgba(253,174,75,.9);
    box-shadow: 0 0 0 .2rem rgba(253,174,75,.25);
}
.invalid-feedback{
    display:block;
    font-weight:700;
}
</style>
@endpush

@section('content')
<div class="profile-container">
    <div class="page-hero">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 position-relative" style="z-index:1;">
            <div>
                <h2><i class="fa-solid fa-mars me-2"></i>معلومات الأب</h2>
                <p>واجهة حديثة: بطاقات واضحة + تعديل سلس + رسائل أخطاء على مستوى الحقول</p>
            </div>
            <a href="{{ route('dashboard') }}" class="btn btn-soft">عودة إلى اللوحة</a>
        </div>
    </div>
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
                <div class="kv">
                    <div class="kv-item"><div class="k">الرقم الوطني (NIN)</div><div class="v">{{ $father->nin ?? '—' }}</div></div>
                    <div class="kv-item"><div class="k">رقم الضمان الاجتماعي (NSS)</div><div class="v">{{ $father->nss ?? '—' }}</div></div>
                    <div class="kv-item"><div class="k">لقب الأب بالعربية</div><div class="v">{{ $father->nom_ar ?? '—' }}</div></div>
                    <div class="kv-item"><div class="k">اسم الأب بالعربية</div><div class="v">{{ $father->prenom_ar ?? '—' }}</div></div>
                    <div class="kv-item"><div class="k">لقب الأب بالفرنسية</div><div class="v">{{ $father->nom_fr ?? '—' }}</div></div>
                    <div class="kv-item"><div class="k">اسم الأب بالفرنسية</div><div class="v">{{ $father->prenom_fr ?? '—' }}</div></div>
                    <div class="kv-item"><div class="k">الفئة الاجتماعية</div><div class="v">{{ $father->categorie_sociale ?? 'غير محدد' }}</div></div>
                    <div class="kv-item"><div class="k">مبلغ الدخل الشهري</div><div class="v">{{ $father->montant_s ? number_format($father->montant_s, 2) . ' دج' : 'غير محدد' }}</div></div>
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
                    <input type="hidden" name="form_context" value="father_update">

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label required">الرقم الوطني للأب (NIN)</label>
                            <input type="text" name="nin" class="form-control @error('nin') is-invalid @enderror" maxlength="18" inputmode="numeric" pattern="\d{18}" required value="{{ old('nin', $father->nin) }}">
                            @error('nin') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">رقم الضمان الاجتماعي للأب (NSS)</label>
                            <input type="text" name="nss" class="form-control @error('nss') is-invalid @enderror" maxlength="12" inputmode="numeric" pattern="\d{12}" value="{{ old('nss', $father->nss) }}">
                            @error('nss') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="row g-3 mt-1">
                        <div class="col-md-6">
                            <label class="form-label required">لقب الأب بالعربية</label>
                            <input type="text" name="nom_ar" class="form-control @error('nom_ar') is-invalid @enderror" required value="{{ old('nom_ar', $father->nom_ar) }}">
                            @error('nom_ar') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required">اسم الأب بالعربية</label>
                            <input type="text" name="prenom_ar" class="form-control @error('prenom_ar') is-invalid @enderror" required value="{{ old('prenom_ar', $father->prenom_ar) }}">
                            @error('prenom_ar') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="row g-3 mt-1">
                        <div class="col-md-6">
                            <label class="form-label">لقب الأب بالفرنسية</label>
                            <input type="text" name="nom_fr" class="form-control @error('nom_fr') is-invalid @enderror" value="{{ old('nom_fr', $father->nom_fr) }}">
                            @error('nom_fr') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">اسم الأب بالفرنسية</label>
                            <input type="text" name="prenom_fr" class="form-control @error('prenom_fr') is-invalid @enderror" value="{{ old('prenom_fr', $father->prenom_fr) }}">
                            @error('prenom_fr') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    @php
                        $lowIncome = 'الدخل الشهري أقل أو يساوي مبلغ الأجر الوطني الأدنى المضمون';
                        $catsOld = old('categorie_sociale', $father->categorie_sociale);
                    @endphp
                    <div class="row g-3 mt-1">
                        <div class="col-md-6">
                            <label class="form-label">الفئة الاجتماعية</label>
                            <select name="categorie_sociale" id="fatherCats" class="form-select @error('categorie_sociale') is-invalid @enderror">
                                <option value="">—</option>
                                <option value="عديم الدخل" {{ $catsOld === 'عديم الدخل' ? 'selected' : '' }}>عديم الدخل</option>
                                <option value="{{ $lowIncome }}" {{ $catsOld === $lowIncome ? 'selected' : '' }}>{{ $lowIncome }}</option>
                            </select>
                            @error('categorie_sociale') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6" id="fatherMontantWrap">
                            <label class="form-label">مبلغ الدخل الشهري</label>
                            <input type="number" name="montant_s" class="form-control @error('montant_s') is-invalid @enderror" step="0.01" min="0" value="{{ old('montant_s', $father->montant_s) }}">
                            @error('montant_s') <div class="invalid-feedback">{{ $message }}</div> @enderror
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
                    <input type="hidden" name="form_context" value="father_create">

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label required">الرقم الوطني للأب (NIN)</label>
                            <input type="text" name="nin" class="form-control @error('nin') is-invalid @enderror" maxlength="18" inputmode="numeric" pattern="\d{18}" required value="{{ old('nin') }}">
                            @error('nin') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">رقم الضمان الاجتماعي للأب (NSS)</label>
                            <input type="text" name="nss" class="form-control @error('nss') is-invalid @enderror" maxlength="12" inputmode="numeric" pattern="\d{12}" value="{{ old('nss') }}">
                            @error('nss') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="row g-3 mt-1">
                        <div class="col-md-6">
                            <label class="form-label required">لقب الأب بالعربية</label>
                            <input type="text" name="nom_ar" class="form-control @error('nom_ar') is-invalid @enderror" required value="{{ old('nom_ar') }}">
                            @error('nom_ar') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required">اسم الأب بالعربية</label>
                            <input type="text" name="prenom_ar" class="form-control @error('prenom_ar') is-invalid @enderror" required value="{{ old('prenom_ar') }}">
                            @error('prenom_ar') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="row g-3 mt-1">
                        <div class="col-md-6">
                            <label class="form-label">لقب الأب بالفرنسية</label>
                            <input type="text" name="nom_fr" class="form-control @error('nom_fr') is-invalid @enderror" value="{{ old('nom_fr') }}">
                            @error('nom_fr') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">اسم الأب بالفرنسية</label>
                            <input type="text" name="prenom_fr" class="form-control @error('prenom_fr') is-invalid @enderror" value="{{ old('prenom_fr') }}">
                            @error('prenom_fr') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    @php
                        $lowIncome = 'الدخل الشهري أقل أو يساوي مبلغ الأجر الوطني الأدنى المضمون';
                        $catsOld = old('categorie_sociale');
                    @endphp
                    <div class="row g-3 mt-1">
                        <div class="col-md-6">
                            <label class="form-label">الفئة الاجتماعية</label>
                            <select name="categorie_sociale" id="fatherCats" class="form-select @error('categorie_sociale') is-invalid @enderror">
                                <option value="">—</option>
                                <option value="عديم الدخل" {{ $catsOld === 'عديم الدخل' ? 'selected' : '' }}>عديم الدخل</option>
                                <option value="{{ $lowIncome }}" {{ $catsOld === $lowIncome ? 'selected' : '' }}>{{ $lowIncome }}</option>
                            </select>
                            @error('categorie_sociale') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6" id="fatherMontantWrap">
                            <label class="form-label">مبلغ الدخل الشهري</label>
                            <input type="number" name="montant_s" class="form-control @error('montant_s') is-invalid @enderror" step="0.01" min="0" value="{{ old('montant_s') }}">
                            @error('montant_s') <div class="invalid-feedback">{{ $message }}</div> @enderror
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

    // If validation errors happened on submit, auto-open edit/create form
    const formContext = @json(old('form_context'));
    const hasErrors = @json($errors->any());
    if ((hasErrors || formContext === 'father_update' || formContext === 'father_create') && view && edit) {
        view.classList.add('d-none');
        edit.classList.remove('d-none');
        const firstInvalid = edit.querySelector('.is-invalid') || edit.querySelector('input,select,textarea');
        if (firstInvalid) firstInvalid.focus({ preventScroll: true });
        window.scrollTo({ top: 0, behavior: 'smooth' });
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

