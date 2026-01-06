@extends('layouts.main')

@section('title', 'معلومات الأم')

@push('styles')
<style>
.pill {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 6px 12px;
    border-radius: 999px;
    background: var(--secondary-light);
    border: 1px solid var(--border-light);
    font-weight: 800;
    color: var(--bg-dark);
    font-size: 12px;
}
</style>
@endpush

@section('content')
<div class="tuteur-page">
    <div class="tuteur-card">
        <div class="tuteur-card__header">
            <div>
                <h3 class="tuteur-card__title"><i class="fa-solid fa-venus"></i>{{ $tuteur->relation_tuteur == 1 ? 'معلومات الأمهات' : 'معلومات الأم' }}</h3>
                <p class="tuteur-card__subtitle">صفحة منظمة ومتناغمة مع تصميم المنصة</p>
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

        @if($tuteur->relation_tuteur == 1)
            <h3><i class="fa-solid fa-venus me-2"></i>معلومات الأمهات</h3>
            <div class="mb-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div class="pill"><i class="fa-solid fa-circle-info"></i>يمكنك إضافة/تعديل/حذف الأمهات</div>
                <a href="{{ route('dashboard') }}" class="tuteur-btn tuteur-btn--soft">عودة إلى اللوحة</a>
            </div>
            
            @if($mothers && $mothers->count() > 0)
                @foreach($mothers as $mother)
                    <div class="tuteur-subcard">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <h5 class="mb-0">الأم {{ $loop->iteration }}</h5>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-sm btn-primary" data-toggle="edit-mother" data-id="{{ $mother->id }}">
                                    <i class="fa-solid fa-pen-to-square me-1"></i>تعديل
                                </button>
                                <form method="POST" action="{{ route('tuteur.mothers.destroy', $mother) }}" onsubmit="return confirm('هل أنت متأكد من الحذف؟');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger js-delete-mother-btn">
                                        <i class="fa-solid fa-trash me-1"></i>حذف
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- View -->
                        <div class="profile-info row g-3 mt-2" id="motherView-{{ $mother->id }}">
                            <div class="col-md-6">
                                <label>الرقم الوطني (NIN):</label>
                                <p>{{ $mother->nin ?? '—' }}</p>
                            </div>
                            <div class="col-md-6">
                                <label>رقم الضمان الاجتماعي (NSS):</label>
                                <p>{{ $mother->nss ?? '—' }}</p>
                            </div>
                            <div class="col-md-6">
                                <label>لقب الأم بالعربية:</label>
                                <p>{{ $mother->nom_ar ?? '—' }}</p>
                            </div>
                            <div class="col-md-6">
                                <label>اسم الأم بالعربية:</label>
                                <p>{{ $mother->prenom_ar ?? '—' }}</p>
                            </div>
                            <div class="col-md-6">
                                <label>لقب الأم بالفرنسية:</label>
                                <p>{{ $mother->nom_fr ?? '—' }}</p>
                            </div>
                            <div class="col-md-6">
                                <label>اسم الأم بالفرنسية:</label>
                                <p>{{ $mother->prenom_fr ?? '—' }}</p>
                            </div>
                            <div class="col-md-6">
                                <label>الفئة الاجتماعية:</label>
                                <p>{{ $mother->categorie_sociale ?? 'غير محدد' }}</p>
                            </div>
                            <div class="col-md-6">
                                <label>مبلغ الدخل الشهري:</label>
                                <p>{{ $mother->montant_s ? number_format($mother->montant_s, 2) . ' دج' : 'غير محدد' }}</p>
                            </div>
                        </div>

                        <!-- Edit -->
                        <div class="mt-3 d-none" id="motherEdit-{{ $mother->id }}">
                            <form method="POST" action="{{ route('tuteur.mothers.update', $mother) }}" novalidate class="js-swal-submit">
                                @csrf
                                @method('PUT')

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label required">الرقم الوطني للأم (NIN)</label>
                                        <input type="text" name="nin" class="form-control" maxlength="18" inputmode="numeric" required value="{{ old('nin', $mother->nin) }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">رقم الضمان الاجتماعي للأم (NSS)</label>
                                        <input type="text" name="nss" class="form-control" maxlength="12" inputmode="numeric" value="{{ old('nss', $mother->nss) }}">
                                    </div>
                                </div>

                                <div class="row g-3 mt-1">
                                    <div class="col-md-6">
                                        <label class="form-label required">لقب الأم بالعربية</label>
                                        <input type="text" name="nom_ar" class="form-control" required value="{{ old('nom_ar', $mother->nom_ar) }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label required">اسم الأم بالعربية</label>
                                        <input type="text" name="prenom_ar" class="form-control" required value="{{ old('prenom_ar', $mother->prenom_ar) }}">
                                    </div>
                                </div>

                                <div class="row g-3 mt-1">
                                    <div class="col-md-6">
                                        <label class="form-label">لقب الأم بالفرنسية</label>
                                        <input type="text" name="nom_fr" class="form-control" value="{{ old('nom_fr', $mother->nom_fr) }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">اسم الأم بالفرنسية</label>
                                        <input type="text" name="prenom_fr" class="form-control" value="{{ old('prenom_fr', $mother->prenom_fr) }}">
                                    </div>
                                </div>

                                @php
                                    $lowIncome = 'الدخل الشهري أقل أو يساوي مبلغ الأجر الوطني الأدنى المضمون';
                                    $catsOld = old('categorie_sociale', $mother->categorie_sociale);
                                @endphp
                                <div class="row g-3 mt-1">
                                    <div class="col-md-6">
                                        <label class="form-label">الفئة الاجتماعية</label>
                                        <select name="categorie_sociale" class="form-select motherCats" data-id="{{ $mother->id }}">
                                            <option value="">—</option>
                                            <option value="عديم الدخل" {{ $catsOld === 'عديم الدخل' ? 'selected' : '' }}>عديم الدخل</option>
                                            <option value="{{ $lowIncome }}" {{ $catsOld === $lowIncome ? 'selected' : '' }}>{{ $lowIncome }}</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 motherMontantWrap" id="motherMontantWrap-{{ $mother->id }}">
                                        <label class="form-label">مبلغ الدخل الشهري</label>
                                        <input type="number" name="montant_s" class="form-control" step="0.01" min="0" value="{{ old('montant_s', $mother->montant_s) }}">
                                    </div>
                                </div>

                                <div class="d-flex gap-2 mt-3 flex-wrap">
                                    <button type="submit" class="btn btn-edit js-submit-btn">
                                        <i class="fa-solid fa-floppy-disk me-2"></i>حفظ
                                    </button>
                                    <button type="button" class="btn btn-soft" data-cancel="edit-mother" data-id="{{ $mother->id }}">إلغاء</button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endforeach
                
                <h5 class="fw-bold mt-4" style="color:#0f033a;">إضافة أم جديدة</h5>
                <form method="POST" action="{{ route('tuteur.mothers.store') }}" class="mt-3 js-swal-submit" novalidate>
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label required">الرقم الوطني للأم (NIN)</label>
                            <input type="text" name="nin" class="form-control" maxlength="18" inputmode="numeric" required value="{{ old('nin') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">رقم الضمان الاجتماعي للأم (NSS)</label>
                            <input type="text" name="nss" class="form-control" maxlength="12" inputmode="numeric" value="{{ old('nss') }}">
                        </div>
                    </div>

                    <div class="row g-3 mt-1">
                        <div class="col-md-6">
                            <label class="form-label required">لقب الأم بالعربية</label>
                            <input type="text" name="nom_ar" class="form-control" required value="{{ old('nom_ar') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required">اسم الأم بالعربية</label>
                            <input type="text" name="prenom_ar" class="form-control" required value="{{ old('prenom_ar') }}">
                        </div>
                    </div>

                    <div class="row g-3 mt-1">
                        <div class="col-md-6">
                            <label class="form-label">لقب الأم بالفرنسية</label>
                            <input type="text" name="nom_fr" class="form-control" value="{{ old('nom_fr') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">اسم الأم بالفرنسية</label>
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
                            <select name="categorie_sociale" id="newMotherCats" class="form-select">
                                <option value="">—</option>
                                <option value="عديم الدخل" {{ $catsOld === 'عديم الدخل' ? 'selected' : '' }}>عديم الدخل</option>
                                <option value="{{ $lowIncome }}" {{ $catsOld === $lowIncome ? 'selected' : '' }}>{{ $lowIncome }}</option>
                            </select>
                        </div>
                        <div class="col-md-6" id="newMotherMontantWrap">
                            <label class="form-label">مبلغ الدخل الشهري</label>
                            <input type="number" name="montant_s" class="form-control" step="0.01" min="0" value="{{ old('montant_s') }}">
                        </div>
                    </div>

                    <div class="d-flex gap-2 mt-4 flex-wrap">
                        <button type="submit" class="tuteur-btn tuteur-btn--primary js-submit-btn">
                            <i class="fa-solid fa-plus me-2"></i>إضافة
                        </button>
                    </div>
                </form>
            @else
                <div class="alert alert-info text-center">
                    <p class="mb-3">لا توجد أمهات مسجلة</p>
                </div>
                <h5 class="fw-bold mt-4" style="color:#0f033a;">إضافة أم جديدة</h5>
                <form method="POST" action="{{ route('tuteur.mothers.store') }}" class="mt-3 js-swal-submit" novalidate>
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label required">الرقم الوطني للأم (NIN)</label>
                            <input type="text" name="nin" class="form-control" maxlength="18" inputmode="numeric" required value="{{ old('nin') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">رقم الضمان الاجتماعي للأم (NSS)</label>
                            <input type="text" name="nss" class="form-control" maxlength="12" inputmode="numeric" value="{{ old('nss') }}">
                        </div>
                    </div>
                    <div class="row g-3 mt-1">
                        <div class="col-md-6">
                            <label class="form-label required">لقب الأم بالعربية</label>
                            <input type="text" name="nom_ar" class="form-control" required value="{{ old('nom_ar') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required">اسم الأم بالعربية</label>
                            <input type="text" name="prenom_ar" class="form-control" required value="{{ old('prenom_ar') }}">
                        </div>
                    </div>
                    <div class="row g-3 mt-1">
                        <div class="col-md-6">
                            <label class="form-label">لقب الأم بالفرنسية</label>
                            <input type="text" name="nom_fr" class="form-control" value="{{ old('nom_fr') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">اسم الأم بالفرنسية</label>
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
                            <select name="categorie_sociale" id="newMotherCats" class="form-select">
                                <option value="">—</option>
                                <option value="عديم الدخل" {{ $catsOld === 'عديم الدخل' ? 'selected' : '' }}>عديم الدخل</option>
                                <option value="{{ $lowIncome }}" {{ $catsOld === $lowIncome ? 'selected' : '' }}>{{ $lowIncome }}</option>
                            </select>
                        </div>
                        <div class="col-md-6" id="newMotherMontantWrap">
                            <label class="form-label">مبلغ الدخل الشهري</label>
                            <input type="number" name="montant_s" class="form-control" step="0.01" min="0" value="{{ old('montant_s') }}">
                        </div>
                    </div>
                    <div class="d-flex gap-2 mt-4 flex-wrap">
                        <button type="submit" class="tuteur-btn tuteur-btn--primary js-submit-btn">
                            <i class="fa-solid fa-plus me-2"></i>إضافة
                        </button>
                        <a href="{{ route('dashboard') }}" class="tuteur-btn tuteur-btn--soft">عودة إلى اللوحة</a>
                    </div>
                </form>
            @endif
        @else
            <h3><i class="fa-solid fa-venus me-2"></i>معلومات الأم</h3>
            
            @if($mother)
                <div class="mb-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
                    
                    <a href="{{ route('dashboard') }}" class="tuteur-btn tuteur-btn--soft">عودة إلى اللوحة</a>
                </div>

                <div id="singleMotherView">
                    <div class="profile-info row g-3">
                        <div class="col-md-6">
                            <label>الرقم الوطني (NIN):</label>
                            <p>{{ $mother->nin ?? '—' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label>رقم الضمان الاجتماعي (NSS):</label>
                            <p>{{ $mother->nss ?? '—' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label>لقب الأم بالعربية:</label>
                            <p>{{ $mother->nom_ar ?? '—' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label>اسم الأم بالعربية:</label>
                            <p>{{ $mother->prenom_ar ?? '—' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label>لقب الأم بالفرنسية:</label>
                            <p>{{ $mother->nom_fr ?? '—' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label>اسم الأم بالفرنسية:</label>
                            <p>{{ $mother->prenom_fr ?? '—' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label>الفئة الاجتماعية:</label>
                            <p>{{ $mother->categorie_sociale ?? 'غير محدد' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label>مبلغ الدخل الشهري:</label>
                            <p>{{ $mother->montant_s ? number_format($mother->montant_s, 2) . ' دج' : 'غير محدد' }}</p>
                        </div>
                    </div>
                    <div class="d-flex gap-2 justify-content-center mt-4 flex-wrap">
                        <button type="button" class="tuteur-btn tuteur-btn--primary" id="toggleSingleMotherEditBtn">
                            <i class="fa-solid fa-pen-to-square me-2"></i>تعديل
                        </button>
                        <a href="{{ route('dashboard') }}" class="tuteur-btn tuteur-btn--soft">عودة إلى اللوحة</a>
                    </div>
                </div>

                <div id="singleMotherEdit" class="d-none mt-4">
                    <form method="POST" action="{{ route('tuteur.mother.update') }}" novalidate class="js-swal-submit">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label required">الرقم الوطني للأم (NIN)</label>
                                <input type="text" name="nin" class="form-control" maxlength="18" inputmode="numeric" required value="{{ old('nin', $mother->nin) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">رقم الضمان الاجتماعي للأم (NSS)</label>
                                <input type="text" name="nss" class="form-control" maxlength="12" inputmode="numeric" value="{{ old('nss', $mother->nss) }}">
                            </div>
                        </div>

                        <div class="row g-3 mt-1">
                            <div class="col-md-6">
                                <label class="form-label required">لقب الأم بالعربية</label>
                                <input type="text" name="nom_ar" class="form-control" required value="{{ old('nom_ar', $mother->nom_ar) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label required">اسم الأم بالعربية</label>
                                <input type="text" name="prenom_ar" class="form-control" required value="{{ old('prenom_ar', $mother->prenom_ar) }}">
                            </div>
                        </div>

                        <div class="row g-3 mt-1">
                            <div class="col-md-6">
                                <label class="form-label">لقب الأم بالفرنسية</label>
                                <input type="text" name="nom_fr" class="form-control" value="{{ old('nom_fr', $mother->nom_fr) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">اسم الأم بالفرنسية</label>
                                <input type="text" name="prenom_fr" class="form-control" value="{{ old('prenom_fr', $mother->prenom_fr) }}">
                            </div>
                        </div>

                        @php
                            $lowIncome = 'الدخل الشهري أقل أو يساوي مبلغ الأجر الوطني الأدنى المضمون';
                            $catsOld = old('categorie_sociale', $mother->categorie_sociale);
                        @endphp
                        <div class="row g-3 mt-1">
                            <div class="col-md-6">
                                <label class="form-label">الفئة الاجتماعية</label>
                                <select name="categorie_sociale" id="singleMotherCats" class="form-select">
                                    <option value="">—</option>
                                    <option value="عديم الدخل" {{ $catsOld === 'عديم الدخل' ? 'selected' : '' }}>عديم الدخل</option>
                                    <option value="{{ $lowIncome }}" {{ $catsOld === $lowIncome ? 'selected' : '' }}>{{ $lowIncome }}</option>
                                </select>
                            </div>
                            <div class="col-md-6" id="singleMotherMontantWrap">
                                <label class="form-label">مبلغ الدخل الشهري</label>
                                <input type="number" name="montant_s" class="form-control" step="0.01" min="0" value="{{ old('montant_s', $mother->montant_s) }}">
                            </div>
                        </div>

                        <div class="d-flex gap-2 justify-content-center mt-4 flex-wrap">
                            <button type="submit" class="tuteur-btn tuteur-btn--primary js-submit-btn">
                                <i class="fa-solid fa-floppy-disk me-2"></i>حفظ
                            </button>
                            <button type="button" class="tuteur-btn tuteur-btn--soft" id="cancelSingleMotherEditBtn">إلغاء</button>
                        </div>
                    </form>
                </div>
            @else
                <div class="alert alert-info text-center">
                    <p class="mb-3">لا توجد معلومات أم مسجلة</p>
                </div>
                <div class="text-center mt-4">
                    <a href="{{ route('dashboard') }}" class="tuteur-btn tuteur-btn--soft">عودة إلى اللوحة</a>
                </div>
            @endif
        @endif
        </div>
    </div>
</div>

<script>
(function () {
    const lowIncome = 'الدخل الشهري أقل أو يساوي مبلغ الأجر الوطني الأدنى المضمون';
    const onlyDigits = (el, maxLen) => {
        el.addEventListener('input', () => {
            el.value = (el.value || '').replace(/\D/g, '').slice(0, maxLen);
        });
    };

    // Digits restriction
    document.querySelectorAll('input[name="nin"]').forEach(i => onlyDigits(i, 18));
    document.querySelectorAll('input[name="nss"]').forEach(i => onlyDigits(i, 12));

    // Toggle edit per mother (role 1)
    document.querySelectorAll('[data-toggle="edit-mother"]').forEach(btn => {
        btn.addEventListener('click', () => {
            const id = btn.getAttribute('data-id');
            const view = document.getElementById('motherView-' + id);
            const edit = document.getElementById('motherEdit-' + id);
            if (!view || !edit) return;
            view.classList.add('d-none');
            edit.classList.remove('d-none');
        });
    });
    document.querySelectorAll('[data-cancel="edit-mother"]').forEach(btn => {
        btn.addEventListener('click', () => {
            const id = btn.getAttribute('data-id');
            const view = document.getElementById('motherView-' + id);
            const edit = document.getElementById('motherEdit-' + id);
            if (!view || !edit) return;
            edit.classList.add('d-none');
            view.classList.remove('d-none');
        });
    });

    // Conditional montant per existing mother edit
    const syncWrap = (selectEl, wrapEl) => {
        if (!selectEl || !wrapEl) return;
        wrapEl.style.display = (selectEl.value === lowIncome) ? 'block' : 'none';
    };
    document.querySelectorAll('select.motherCats').forEach(sel => {
        const id = sel.getAttribute('data-id');
        const wrap = document.getElementById('motherMontantWrap-' + id);
        const doSync = () => syncWrap(sel, wrap);
        sel.addEventListener('change', doSync);
        doSync();
    });

    // Conditional montant for new mother form (role 1)
    const newCats = document.getElementById('newMotherCats');
    const newWrap = document.getElementById('newMotherMontantWrap');
    if (newCats && newWrap) {
        const doSync = () => syncWrap(newCats, newWrap);
        newCats.addEventListener('change', doSync);
        doSync();
    }

    // Single mother toggle (role 3)
    const toggleSingle = document.getElementById('toggleSingleMotherEditBtn');
    const cancelSingle = document.getElementById('cancelSingleMotherEditBtn');
    const singleView = document.getElementById('singleMotherView');
    const singleEdit = document.getElementById('singleMotherEdit');
    if (toggleSingle && cancelSingle && singleView && singleEdit) {
        toggleSingle.addEventListener('click', () => {
            singleView.classList.add('d-none');
            singleEdit.classList.remove('d-none');
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
        cancelSingle.addEventListener('click', () => {
            singleEdit.classList.add('d-none');
            singleView.classList.remove('d-none');
        });
    }

    // Conditional montant for single mother edit (role 3)
    const singleCats = document.getElementById('singleMotherCats');
    const singleWrap = document.getElementById('singleMotherMontantWrap');
    if (singleCats && singleWrap) {
        const doSync = () => syncWrap(singleCats, singleWrap);
        singleCats.addEventListener('change', doSync);
        doSync();
    }

    // SweetAlert2 UX (toast + confirm + loading)
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
            if (!window.Swal) return;
            e.preventDefault();

            const btn = form.querySelector('.js-submit-btn');
            if (btn) {
                btn.disabled = true;
                btn.dataset.originalHtml = btn.innerHTML;
                btn.innerHTML = `<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>جارٍ الحفظ...`;
            }

            Swal.fire({
                title: 'جارٍ الحفظ...',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => Swal.showLoading(),
            });
            setTimeout(() => form.submit(), 60);
        });
    });

    // Delete confirmation (role 1)
    document.querySelectorAll('form[action*=\"/tuteur/mothers/\"][method=\"POST\"]').forEach(form => {
        const methodInput = form.querySelector('input[name=\"_method\"]');
        if (!methodInput || methodInput.value !== 'DELETE') return;
        form.addEventListener('submit', async (e) => {
            if (!window.Swal) return; // keep native confirm fallback in markup
            e.preventDefault();
            const res = await Swal.fire({
                icon: 'warning',
                title: 'تأكيد الحذف',
                text: 'هل أنت متأكد؟ لا يمكن التراجع عن هذه العملية.',
                showCancelButton: true,
                confirmButtonText: 'نعم، احذف',
                cancelButtonText: 'إلغاء',
                reverseButtons: true,
            });
            if (!res.isConfirmed) return;

            Swal.fire({
                title: 'جارٍ الحذف...',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => Swal.showLoading(),
            });
            setTimeout(() => form.submit(), 60);
        });
    });
})();
</script>
@endsection

