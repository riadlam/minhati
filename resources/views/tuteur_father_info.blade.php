@extends('layouts.main')

@section('title', 'معلومات الأب')

@push('styles')
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
</style>
@endpush

@section('content')
<div class="tuteur-page">
    <div class="tuteur-card">
        <div class="tuteur-card__header">
            <div>
                <h3 class="tuteur-card__title"><i class="fa-solid fa-mars"></i>معلومات الأب</h3>
                <p class="tuteur-card__subtitle">عرض وتحديث معلومات الأب (نفس تصميم المنصة)</p>
            </div>
            <a href="{{ route('dashboard') }}" class="tuteur-btn tuteur-btn--soft">
                <i class="fa-solid fa-arrow-right"></i>عودة
            </a>
        </div>

        <div class="tuteur-card__body">

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
                <div class="tuteur-kv">
                    <div class="tuteur-kv__item"><div class="tuteur-kv__k">الرقم الوطني (NIN)</div><div class="tuteur-kv__v">{{ $father->nin ?? '—' }}</div></div>
                    <div class="tuteur-kv__item"><div class="tuteur-kv__k">رقم الضمان الاجتماعي (NSS)</div><div class="tuteur-kv__v">{{ $father->nss ?? '—' }}</div></div>
                    <div class="tuteur-kv__item"><div class="tuteur-kv__k">لقب الأب بالعربية</div><div class="tuteur-kv__v">{{ $father->nom_ar ?? '—' }}</div></div>
                    <div class="tuteur-kv__item"><div class="tuteur-kv__k">اسم الأب بالعربية</div><div class="tuteur-kv__v">{{ $father->prenom_ar ?? '—' }}</div></div>
                    <div class="tuteur-kv__item"><div class="tuteur-kv__k">لقب الأب بالفرنسية</div><div class="tuteur-kv__v">{{ $father->nom_fr ?? '—' }}</div></div>
                    <div class="tuteur-kv__item"><div class="tuteur-kv__k">اسم الأب بالفرنسية</div><div class="tuteur-kv__v">{{ $father->prenom_fr ?? '—' }}</div></div>
                    <div class="tuteur-kv__item"><div class="tuteur-kv__k">الفئة الاجتماعية</div><div class="tuteur-kv__v">{{ $father->categorie_sociale ?? 'غير محدد' }}</div></div>
                    <div class="tuteur-kv__item"><div class="tuteur-kv__k">مبلغ الدخل الشهري</div><div class="tuteur-kv__v">{{ $father->montant_s ? number_format($father->montant_s, 2) . ' دج' : 'غير محدد' }}</div></div>
                </div>

                <div class="d-flex gap-2 justify-content-center mt-4 flex-wrap">
                    <button type="button" class="tuteur-btn tuteur-btn--primary" id="toggleFatherEditBtn">
                        <i class="fa-solid fa-pen-to-square me-2"></i>تعديل
                    </button>
                    <a href="{{ route('dashboard') }}" class="tuteur-btn tuteur-btn--soft">عودة إلى اللوحة</a>
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
                        <button type="submit" class="tuteur-btn tuteur-btn--primary js-submit-btn">
                            <i class="fa-solid fa-floppy-disk me-2"></i>حفظ
                        </button>
                        <button type="button" class="tuteur-btn tuteur-btn--soft" id="cancelFatherEditBtn">إلغاء</button>
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
                        <button type="submit" class="tuteur-btn tuteur-btn--primary js-submit-btn">
                            <i class="fa-solid fa-floppy-disk me-2"></i>حفظ
                        </button>
                        <a href="{{ route('dashboard') }}" class="tuteur-btn tuteur-btn--soft">عودة إلى اللوحة</a>
                    </div>
                </form>
            </div>
        @endif
        </div>
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

