@extends('layouts.main')

@section('title', 'معلومات الأم')

@push('styles')
<style>
/* Enhanced Mother Page Styling */
.mother-page-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    padding: 1.5rem 0;
    margin-bottom: 1.5rem;
    border-bottom: 2px solid rgba(253, 174, 75, 0.2);
}

.mother-page-header h2 {
    margin: 0;
    font-weight: 900;
    color: var(--bg-dark);
    font-size: 1.75rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.mother-page-header h2 i {
    color: #fdae4b;
    font-size: 1.5rem;
}

.mother-action-bar {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    flex-wrap: wrap;
}

.mother-info-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    border-radius: 999px;
    background: linear-gradient(135deg, rgba(253, 174, 75, 0.1) 0%, rgba(255, 199, 107, 0.1) 100%);
    border: 1px solid rgba(253, 174, 75, 0.3);
    font-weight: 700;
    color: var(--bg-dark);
    font-size: 0.875rem;
}

.mother-card {
    background: var(--white);
    border: 1px solid var(--border-light);
    border-radius: var(--radius-xl);
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    box-shadow: var(--shadow-md);
    transition: all var(--transition-base);
}

.mother-card:hover {
    box-shadow: var(--shadow-lg);
    transform: translateY(-2px);
}

.mother-card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    padding-bottom: 1rem;
    margin-bottom: 1rem;
    border-bottom: 2px solid var(--border-light);
    flex-wrap: wrap;
}

.mother-card-title {
    margin: 0;
    font-weight: 900;
    color: var(--bg-dark);
    font-size: 1.25rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.mother-card-title i {
    color: #fdae4b;
}

.mother-card-actions {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.mother-view-mode {
    animation: fadeInUp 0.4s ease-out;
}

.mother-edit-mode {
    animation: fadeInUp 0.4s ease-out;
    background: linear-gradient(135deg, rgba(253, 174, 75, 0.02) 0%, rgba(255, 199, 107, 0.02) 100%);
    border: 2px solid rgba(253, 174, 75, 0.3);
    border-radius: var(--radius-xl);
    padding: 1.5rem;
    margin-top: 1rem;
}

.add-mother-section {
    background: linear-gradient(135deg, rgba(253, 174, 75, 0.05) 0%, rgba(255, 199, 107, 0.05) 100%);
    border: 2px dashed rgba(253, 174, 75, 0.4);
    border-radius: var(--radius-xl);
    padding: 2rem;
    margin-top: 2rem;
}

.add-mother-section-title {
    margin: 0 0 1.5rem 0;
    font-weight: 900;
    color: var(--bg-dark);
    font-size: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.add-mother-section-title i {
    color: #fdae4b;
}

.form-label {
    font-weight: 700;
    color: var(--bg-dark);
    margin-bottom: 0.5rem;
    font-size: 0.9rem;
}

.form-label.required::after {
    content: ' *';
    color: #dc3545;
}

.form-control, .form-select {
    border: 2px solid var(--border-light);
    border-radius: var(--radius-lg);
    padding: 0.75rem 1rem;
    font-size: 0.95rem;
    transition: all var(--transition-base);
    background: var(--white);
}

.form-control:focus, .form-select:focus {
    border-color: #fdae4b;
    box-shadow: 0 0 0 3px rgba(253, 174, 75, 0.1);
    outline: none;
}

.form-control.is-invalid, .form-select.is-invalid {
    border-color: #dc3545;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath d='m5.8 3.6 .4.4.4-.4m0 4.8-.4-.4-.4.4'/%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right 0.75rem center;
    background-size: 1rem 1rem;
    padding-right: 2.5rem;
}

.invalid-feedback {
    display: block;
    width: 100%;
    margin-top: 0.5rem;
    font-size: 0.875rem;
    color: #dc3545;
    font-weight: 600;
}

.empty-state {
    text-align: center;
    padding: 3rem 2rem;
    background: linear-gradient(135deg, rgba(253, 174, 75, 0.05) 0%, rgba(255, 199, 107, 0.05) 100%);
    border: 2px dashed rgba(253, 174, 75, 0.3);
    border-radius: var(--radius-xl);
    margin: 2rem 0;
}

.empty-state-icon {
    font-size: 4rem;
    color: rgba(253, 174, 75, 0.5);
    margin-bottom: 1rem;
}

.empty-state-text {
    font-weight: 700;
    color: var(--bg-dark);
    font-size: 1.1rem;
    margin-bottom: 0.5rem;
}

.empty-state-subtext {
    color: var(--text-secondary);
    font-size: 0.95rem;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@media (max-width: 768px) {
    .mother-page-header {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .mother-card-header {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .mother-card-actions {
        width: 100%;
    }
    
    .mother-card-actions .btn {
        flex: 1;
    }
}
</style>
@endpush

@section('content')
<div class="tuteur-page">
    <div class="tuteur-card">
        <div class="tuteur-card__header">
            <div>
                <h3 class="tuteur-card__title">
                    <i class="fa-solid fa-venus"></i>
                    معلومات الأمهات
                </h3>
                <p class="tuteur-card__subtitle">إدارة وعرض معلومات الأمهات بشكل منظم</p>
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

            {{-- All Users: Multiple Mothers Interface --}}
            <div class="mother-page-header">
                <h2><i class="fa-solid fa-venus"></i>معلومات الأمهات</h2>
                <div class="mother-action-bar">
                    <div class="mother-info-badge">
                        <i class="fa-solid fa-circle-info"></i>
                        يمكنك إضافة/تعديل/حذف الأمهات
                    </div>
                </div>
            </div>
            
            @if($mothers && $mothers->count() > 0)
                    @foreach($mothers as $mother)
                        <div class="mother-card" id="motherCard-{{ $mother->id }}">
                            <div class="mother-card-header">
                                <h4 class="mother-card-title">
                                    <i class="fa-solid fa-user"></i>
                                    الأم {{ $loop->iteration }}
                                </h4>
                                <div class="mother-card-actions">
                                    <button type="button" class="tuteur-btn tuteur-btn--primary" data-toggle="edit-mother" data-id="{{ $mother->id }}">
                                        <i class="fa-solid fa-pen-to-square"></i>تعديل
                                    </button>
                                    <form method="POST" action="{{ route('tuteur.mothers.destroy', $mother) }}" class="js-delete-mother-form" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="tuteur-btn" style="background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); color: white;">
                                            <i class="fa-solid fa-trash"></i>حذف
                                        </button>
                                    </form>
                                </div>
                            </div>

                            {{-- View Mode --}}
                            <div class="mother-view-mode" id="motherView-{{ $mother->id }}">
                                <div class="tuteur-kv">
                                    <div class="tuteur-kv__item">
                                        <div class="tuteur-kv__k">الرقم الوطني (NIN)</div>
                                        <div class="tuteur-kv__v">{{ $mother->nin ?? '—' }}</div>
                                    </div>
                                    <div class="tuteur-kv__item">
                                        <div class="tuteur-kv__k">رقم الضمان الاجتماعي (NSS)</div>
                                        <div class="tuteur-kv__v">{{ $mother->nss ?? '—' }}</div>
                                    </div>
                                    <div class="tuteur-kv__item">
                                        <div class="tuteur-kv__k">لقب الأم بالعربية</div>
                                        <div class="tuteur-kv__v">{{ $mother->nom_ar ?? '—' }}</div>
                                    </div>
                                    <div class="tuteur-kv__item">
                                        <div class="tuteur-kv__k">اسم الأم بالعربية</div>
                                        <div class="tuteur-kv__v">{{ $mother->prenom_ar ?? '—' }}</div>
                                    </div>
                                    <div class="tuteur-kv__item">
                                        <div class="tuteur-kv__k">لقب الأم بالفرنسية</div>
                                        <div class="tuteur-kv__v">{{ $mother->nom_fr ?? '—' }}</div>
                                    </div>
                                    <div class="tuteur-kv__item">
                                        <div class="tuteur-kv__k">اسم الأم بالفرنسية</div>
                                        <div class="tuteur-kv__v">{{ $mother->prenom_fr ?? '—' }}</div>
                                    </div>
                                    <div class="tuteur-kv__item">
                                        <div class="tuteur-kv__k">الفئة الاجتماعية</div>
                                        <div class="tuteur-kv__v">{{ $mother->categorie_sociale ?? 'غير محدد' }}</div>
                                    </div>
                                    <div class="tuteur-kv__item">
                                        <div class="tuteur-kv__k">مبلغ الدخل الشهري</div>
                                        <div class="tuteur-kv__v">{{ $mother->montant_s ? number_format($mother->montant_s, 2) . ' دج' : 'غير محدد' }}</div>
                                    </div>
                                </div>
                            </div>

                            {{-- Edit Mode --}}
                            <div class="mother-edit-mode d-none" id="motherEdit-{{ $mother->id }}">
                                <form method="POST" action="{{ route('tuteur.mothers.update', $mother) }}" novalidate class="js-swal-submit">
                                    @csrf
                                    @method('PUT')

                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label required">الرقم الوطني للأم (NIN)</label>
                                            <input type="text" name="nin" class="form-control @error('nin') is-invalid @enderror" maxlength="18" inputmode="numeric" pattern="\d{18}" required value="{{ old('nin', $mother->nin) }}">
                                            @error('nin')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">رقم الضمان الاجتماعي للأم (NSS)</label>
                                            <input type="text" name="nss" class="form-control @error('nss') is-invalid @enderror" maxlength="12" inputmode="numeric" pattern="\d{12}" value="{{ old('nss', $mother->nss) }}">
                                            @error('nss')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="row g-3 mt-2">
                                        <div class="col-md-6">
                                            <label class="form-label required">لقب الأم بالعربية</label>
                                            <input type="text" name="nom_ar" class="form-control @error('nom_ar') is-invalid @enderror" required value="{{ old('nom_ar', $mother->nom_ar) }}">
                                            @error('nom_ar')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label required">اسم الأم بالعربية</label>
                                            <input type="text" name="prenom_ar" class="form-control @error('prenom_ar') is-invalid @enderror" required value="{{ old('prenom_ar', $mother->prenom_ar) }}">
                                            @error('prenom_ar')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="row g-3 mt-2">
                                        <div class="col-md-6">
                                            <label class="form-label">لقب الأم بالفرنسية</label>
                                            <input type="text" name="nom_fr" class="form-control @error('nom_fr') is-invalid @enderror" value="{{ old('nom_fr', $mother->nom_fr) }}">
                                            @error('nom_fr')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">اسم الأم بالفرنسية</label>
                                            <input type="text" name="prenom_fr" class="form-control @error('prenom_fr') is-invalid @enderror" value="{{ old('prenom_fr', $mother->prenom_fr) }}">
                                            @error('prenom_fr')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    @php
                                        $lowIncome = 'الدخل الشهري أقل أو يساوي مبلغ الأجر الوطني الأدنى المضمون';
                                        $catsOld = old('categorie_sociale', $mother->categorie_sociale);
                                    @endphp
                                    <div class="row g-3 mt-2">
                                        <div class="col-md-6">
                                            <label class="form-label">الفئة الاجتماعية</label>
                                            <select name="categorie_sociale" class="form-select motherCats @error('categorie_sociale') is-invalid @enderror" data-id="{{ $mother->id }}">
                                                <option value="">—</option>
                                                <option value="عديم الدخل" {{ $catsOld === 'عديم الدخل' ? 'selected' : '' }}>عديم الدخل</option>
                                                <option value="{{ $lowIncome }}" {{ $catsOld === $lowIncome ? 'selected' : '' }}>{{ $lowIncome }}</option>
                                            </select>
                                            @error('categorie_sociale')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 motherMontantWrap" id="motherMontantWrap-{{ $mother->id }}">
                                            <label class="form-label">مبلغ الدخل الشهري</label>
                                            <input type="number" name="montant_s" class="form-control @error('montant_s') is-invalid @enderror" step="0.01" min="0" value="{{ old('montant_s', $mother->montant_s) }}">
                                            @error('montant_s')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="d-flex gap-2 mt-4 flex-wrap">
                                        <button type="submit" class="tuteur-btn tuteur-btn--primary js-submit-btn">
                                            <i class="fa-solid fa-floppy-disk"></i>حفظ التغييرات
                                        </button>
                                        <button type="button" class="tuteur-btn tuteur-btn--soft" data-cancel="edit-mother" data-id="{{ $mother->id }}">
                                            <i class="fa-solid fa-times"></i>إلغاء
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endforeach
                    
                    {{-- Add New Mother Section --}}
                    <div class="add-mother-section">
                        <h4 class="add-mother-section-title">
                            <i class="fa-solid fa-plus-circle"></i>
                            إضافة أم جديدة
                        </h4>
                        <form method="POST" action="{{ route('tuteur.mothers.store') }}" class="js-swal-submit" novalidate>
                            @csrf
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label required">الرقم الوطني للأم (NIN)</label>
                                    <input type="text" name="nin" class="form-control @error('nin') is-invalid @enderror" maxlength="18" inputmode="numeric" pattern="\d{18}" required value="{{ old('nin') }}">
                                    @error('nin')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">رقم الضمان الاجتماعي للأم (NSS)</label>
                                    <input type="text" name="nss" class="form-control @error('nss') is-invalid @enderror" maxlength="12" inputmode="numeric" pattern="\d{12}" value="{{ old('nss') }}">
                                    @error('nss')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row g-3 mt-2">
                                <div class="col-md-6">
                                    <label class="form-label required">لقب الأم بالعربية</label>
                                    <input type="text" name="nom_ar" class="form-control @error('nom_ar') is-invalid @enderror" required value="{{ old('nom_ar') }}">
                                    @error('nom_ar')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label required">اسم الأم بالعربية</label>
                                    <input type="text" name="prenom_ar" class="form-control @error('prenom_ar') is-invalid @enderror" required value="{{ old('prenom_ar') }}">
                                    @error('prenom_ar')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row g-3 mt-2">
                                <div class="col-md-6">
                                    <label class="form-label">لقب الأم بالفرنسية</label>
                                    <input type="text" name="nom_fr" class="form-control @error('nom_fr') is-invalid @enderror" value="{{ old('nom_fr') }}">
                                    @error('nom_fr')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">اسم الأم بالفرنسية</label>
                                    <input type="text" name="prenom_fr" class="form-control @error('prenom_fr') is-invalid @enderror" value="{{ old('prenom_fr') }}">
                                    @error('prenom_fr')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            @php
                                $lowIncome = 'الدخل الشهري أقل أو يساوي مبلغ الأجر الوطني الأدنى المضمون';
                                $catsOld = old('categorie_sociale');
                            @endphp
                            <div class="row g-3 mt-2">
                                <div class="col-md-6">
                                    <label class="form-label">الفئة الاجتماعية</label>
                                    <select name="categorie_sociale" id="newMotherCats" class="form-select @error('categorie_sociale') is-invalid @enderror">
                                        <option value="">—</option>
                                        <option value="عديم الدخل" {{ $catsOld === 'عديم الدخل' ? 'selected' : '' }}>عديم الدخل</option>
                                        <option value="{{ $lowIncome }}" {{ $catsOld === $lowIncome ? 'selected' : '' }}>{{ $lowIncome }}</option>
                                    </select>
                                    @error('categorie_sociale')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6" id="newMotherMontantWrap">
                                    <label class="form-label">مبلغ الدخل الشهري</label>
                                    <input type="number" name="montant_s" class="form-control @error('montant_s') is-invalid @enderror" step="0.01" min="0" value="{{ old('montant_s') }}">
                                    @error('montant_s')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="d-flex gap-2 mt-4 flex-wrap">
                                <button type="submit" class="tuteur-btn tuteur-btn--primary js-submit-btn">
                                    <i class="fa-solid fa-plus"></i>إضافة الأم
                                </button>
                            </div>
                        </form>
                    </div>
                @else
                    {{-- Empty State --}}
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i class="fa-solid fa-venus"></i>
                        </div>
                        <div class="empty-state-text">لا توجد أمهات مسجلة</div>
                        <div class="empty-state-subtext">ابدأ بإضافة أم جديدة باستخدام النموذج أدناه</div>
                    </div>

                    {{-- Add New Mother Section (Empty State) --}}
                    <div class="add-mother-section">
                        <h4 class="add-mother-section-title">
                            <i class="fa-solid fa-plus-circle"></i>
                            إضافة أم جديدة
                        </h4>
                        <form method="POST" action="{{ route('tuteur.mothers.store') }}" class="js-swal-submit" novalidate>
                            @csrf
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label required">الرقم الوطني للأم (NIN)</label>
                                    <input type="text" name="nin" class="form-control @error('nin') is-invalid @enderror" maxlength="18" inputmode="numeric" pattern="\d{18}" required value="{{ old('nin') }}">
                                    @error('nin')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">رقم الضمان الاجتماعي للأم (NSS)</label>
                                    <input type="text" name="nss" class="form-control @error('nss') is-invalid @enderror" maxlength="12" inputmode="numeric" pattern="\d{12}" value="{{ old('nss') }}">
                                    @error('nss')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="row g-3 mt-2">
                                <div class="col-md-6">
                                    <label class="form-label required">لقب الأم بالعربية</label>
                                    <input type="text" name="nom_ar" class="form-control @error('nom_ar') is-invalid @enderror" required value="{{ old('nom_ar') }}">
                                    @error('nom_ar')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label required">اسم الأم بالعربية</label>
                                    <input type="text" name="prenom_ar" class="form-control @error('prenom_ar') is-invalid @enderror" required value="{{ old('prenom_ar') }}">
                                    @error('prenom_ar')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="row g-3 mt-2">
                                <div class="col-md-6">
                                    <label class="form-label">لقب الأم بالفرنسية</label>
                                    <input type="text" name="nom_fr" class="form-control @error('nom_fr') is-invalid @enderror" value="{{ old('nom_fr') }}">
                                    @error('nom_fr')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">اسم الأم بالفرنسية</label>
                                    <input type="text" name="prenom_fr" class="form-control @error('prenom_fr') is-invalid @enderror" value="{{ old('prenom_fr') }}">
                                    @error('prenom_fr')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            @php
                                $lowIncome = 'الدخل الشهري أقل أو يساوي مبلغ الأجر الوطني الأدنى المضمون';
                                $catsOld = old('categorie_sociale');
                            @endphp
                            <div class="row g-3 mt-2">
                                <div class="col-md-6">
                                    <label class="form-label">الفئة الاجتماعية</label>
                                    <select name="categorie_sociale" id="newMotherCats" class="form-select @error('categorie_sociale') is-invalid @enderror">
                                        <option value="">—</option>
                                        <option value="عديم الدخل" {{ $catsOld === 'عديم الدخل' ? 'selected' : '' }}>عديم الدخل</option>
                                        <option value="{{ $lowIncome }}" {{ $catsOld === $lowIncome ? 'selected' : '' }}>{{ $lowIncome }}</option>
                                    </select>
                                    @error('categorie_sociale')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6" id="newMotherMontantWrap">
                                    <label class="form-label">مبلغ الدخل الشهري</label>
                                    <input type="number" name="montant_s" class="form-control @error('montant_s') is-invalid @enderror" step="0.01" min="0" value="{{ old('montant_s') }}">
                                    @error('montant_s')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="d-flex gap-2 mt-4 flex-wrap">
                                <button type="submit" class="tuteur-btn tuteur-btn--primary js-submit-btn">
                                    <i class="fa-solid fa-plus"></i>إضافة الأم
                                </button>
                            </div>
                        </form>
                    </div>
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
            // Scroll to edit form
            edit.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
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
            // Scroll to view
            view.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
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

    // Delete confirmation
    document.querySelectorAll('.js-delete-mother-form').forEach(form => {
        form.addEventListener('submit', async (e) => {
            if (!window.Swal) {
                if (!confirm('هل أنت متأكد من الحذف؟')) {
                    e.preventDefault();
                }
                return;
            }
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
