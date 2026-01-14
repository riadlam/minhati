@extends('layouts.main')

@section('title', 'معلومات الأب')

@push('styles')
<style>
/* Enhanced Father Page Styling */
.father-page-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    padding: 1.5rem 0;
    margin-bottom: 1.5rem;
    border-bottom: 2px solid rgba(253, 174, 75, 0.2);
}

.father-page-header h2 {
    margin: 0;
    font-weight: 900;
    color: var(--bg-dark);
    font-size: 1.75rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.father-page-header h2 i {
    color: #fdae4b;
    font-size: 1.5rem;
}

.father-action-bar {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    flex-wrap: wrap;
}

.father-info-badge {
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

.father-card {
    background: var(--white);
    border: 1px solid var(--border-light);
    border-radius: var(--radius-xl);
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    box-shadow: var(--shadow-md);
    transition: all var(--transition-base);
}

.father-card:hover {
    box-shadow: var(--shadow-lg);
    transform: translateY(-2px);
}

.father-card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    padding-bottom: 1rem;
    margin-bottom: 1rem;
    border-bottom: 2px solid var(--border-light);
    flex-wrap: wrap;
}

.father-card-title {
    margin: 0;
    font-weight: 900;
    color: var(--bg-dark);
    font-size: 1.25rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.father-card-title i {
    color: #fdae4b;
}

.father-card-actions {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.father-view-mode {
    animation: fadeInUp 0.4s ease-out;
}

.father-edit-mode {
    animation: fadeInUp 0.4s ease-out;
    background: linear-gradient(135deg, rgba(253, 174, 75, 0.02) 0%, rgba(255, 199, 107, 0.02) 100%);
    border: 2px solid rgba(253, 174, 75, 0.3);
    border-radius: var(--radius-xl);
    padding: 1.5rem;
    margin-top: 1rem;
}

.add-father-section {
    background: linear-gradient(135deg, rgba(253, 174, 75, 0.05) 0%, rgba(255, 199, 107, 0.05) 100%);
    border: 2px dashed rgba(253, 174, 75, 0.4);
    border-radius: var(--radius-xl);
    padding: 2rem;
    margin-top: 2rem;
}

.add-father-section-title {
    margin: 0 0 1.5rem 0;
    font-weight: 900;
    color: var(--bg-dark);
    font-size: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.add-father-section-title i {
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
    .father-page-header {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .father-card-header {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .father-card-actions {
        width: 100%;
    }
    
    .father-card-actions .btn {
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
                    <i class="fa-solid fa-mars"></i>
                    معلومات الآباء
                </h3>
                <p class="tuteur-card__subtitle">إدارة وعرض معلومات الآباء بشكل منظم</p>
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

            {{-- All Users: Multiple Fathers Interface --}}
            <div class="father-page-header">
                <h2><i class="fa-solid fa-mars"></i>معلومات الآباء</h2>
                <div class="father-action-bar">
                    <div class="father-info-badge">
                        <i class="fa-solid fa-circle-info"></i>
                        يمكنك إضافة/تعديل/حذف الآباء
                    </div>
                </div>
            </div>
            
            @if($fathers && $fathers->count() > 0)
                @foreach($fathers as $father)
                    <div class="father-card" id="fatherCard-{{ $father->id }}">
                        <div class="father-card-header">
                            <h4 class="father-card-title">
                                <i class="fa-solid fa-user"></i>
                                الأب {{ $loop->iteration }}
                            </h4>
                            <div class="father-card-actions">
                                <button type="button" class="tuteur-btn tuteur-btn--primary" data-toggle="edit-father" data-id="{{ $father->id }}">
                                    <i class="fa-solid fa-pen-to-square"></i>تعديل
                                </button>
                                <form method="POST" action="{{ route('tuteur.fathers.destroy', $father) }}" class="js-delete-father-form" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="tuteur-btn" style="background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); color: white;">
                                        <i class="fa-solid fa-trash"></i>حذف
                                    </button>
                                </form>
                            </div>
                        </div>

                        {{-- View Mode --}}
                        <div class="father-view-mode" id="fatherView-{{ $father->id }}">
                            <div class="tuteur-kv">
                                <div class="tuteur-kv__item">
                                    <div class="tuteur-kv__k">الرقم الوطني (NIN)</div>
                                    <div class="tuteur-kv__v">{{ $father->nin ?? '—' }}</div>
                                </div>
                                <div class="tuteur-kv__item">
                                    <div class="tuteur-kv__k">رقم الضمان الاجتماعي (NSS)</div>
                                    <div class="tuteur-kv__v">{{ $father->nss ?? '—' }}</div>
                                </div>
                                <div class="tuteur-kv__item">
                                    <div class="tuteur-kv__k">لقب الأب بالعربية</div>
                                    <div class="tuteur-kv__v">{{ $father->nom_ar ?? '—' }}</div>
                                </div>
                                <div class="tuteur-kv__item">
                                    <div class="tuteur-kv__k">اسم الأب بالعربية</div>
                                    <div class="tuteur-kv__v">{{ $father->prenom_ar ?? '—' }}</div>
                                </div>
                                <div class="tuteur-kv__item">
                                    <div class="tuteur-kv__k">لقب الأب بالفرنسية</div>
                                    <div class="tuteur-kv__v">{{ $father->nom_fr ?? '—' }}</div>
                                </div>
                                <div class="tuteur-kv__item">
                                    <div class="tuteur-kv__k">اسم الأب بالفرنسية</div>
                                    <div class="tuteur-kv__v">{{ $father->prenom_fr ?? '—' }}</div>
                                </div>
                                <div class="tuteur-kv__item">
                                    <div class="tuteur-kv__k">الفئة الاجتماعية</div>
                                    <div class="tuteur-kv__v">{{ $father->categorie_sociale ?? 'غير محدد' }}</div>
                                </div>
                                <div class="tuteur-kv__item">
                                    <div class="tuteur-kv__k">مبلغ الدخل الشهري</div>
                                    <div class="tuteur-kv__v">{{ $father->montant_s ? number_format($father->montant_s, 2) . ' دج' : 'غير محدد' }}</div>
                                </div>
                            </div>
                        </div>

                        {{-- Edit Mode --}}
                        <div class="father-edit-mode d-none" id="fatherEdit-{{ $father->id }}">
                            <form method="POST" action="{{ route('tuteur.fathers.update', $father) }}" novalidate class="js-swal-submit" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label required">الرقم الوطني للأب (NIN)</label>
                                        <input type="text" name="nin" class="form-control @error('nin') is-invalid @enderror" maxlength="18" inputmode="numeric" pattern="\d{18}" required value="{{ old('nin', $father->nin) }}">
                                        @error('nin')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">رقم الضمان الاجتماعي للأب (NSS)</label>
                                        <input type="text" name="nss" class="form-control @error('nss') is-invalid @enderror" maxlength="12" inputmode="numeric" pattern="\d{12}" value="{{ old('nss', $father->nss) }}">
                                        @error('nss')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row g-3 mt-2">
                                    <div class="col-md-6">
                                        <label class="form-label required">لقب الأب بالعربية</label>
                                        <input type="text" name="nom_ar" class="form-control @error('nom_ar') is-invalid @enderror" required value="{{ old('nom_ar', $father->nom_ar) }}">
                                        @error('nom_ar')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label required">اسم الأب بالعربية</label>
                                        <input type="text" name="prenom_ar" class="form-control @error('prenom_ar') is-invalid @enderror" required value="{{ old('prenom_ar', $father->prenom_ar) }}">
                                        @error('prenom_ar')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row g-3 mt-2">
                                    <div class="col-md-6">
                                        <label class="form-label">لقب الأب بالفرنسية</label>
                                        <input type="text" name="nom_fr" class="form-control @error('nom_fr') is-invalid @enderror" value="{{ old('nom_fr', $father->nom_fr) }}">
                                        @error('nom_fr')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">اسم الأب بالفرنسية</label>
                                        <input type="text" name="prenom_fr" class="form-control @error('prenom_fr') is-invalid @enderror" value="{{ old('prenom_fr', $father->prenom_fr) }}">
                                        @error('prenom_fr')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                @php
                                    $lowIncome = 'الدخل الشهري أقل أو يساوي مبلغ الأجر الوطني الأدنى المضمون';
                                    $catsOld = old('categorie_sociale', $father->categorie_sociale);
                                @endphp
                                <div class="row g-3 mt-2">
                                    <div class="col-md-6">
                                        <label class="form-label">الفئة الاجتماعية</label>
                                        <select name="categorie_sociale" class="form-select fatherCats @error('categorie_sociale') is-invalid @enderror" data-id="{{ $father->id }}">
                                            <option value="">—</option>
                                            <option value="عديم الدخل" {{ $catsOld === 'عديم الدخل' ? 'selected' : '' }}>عديم الدخل</option>
                                            <option value="{{ $lowIncome }}" {{ $catsOld === $lowIncome ? 'selected' : '' }}>{{ $lowIncome }}</option>
                                        </select>
                                        @error('categorie_sociale')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 fatherMontantWrap" id="fatherMontantWrap-{{ $father->id }}">
                                        <label class="form-label">مبلغ الدخل الشهري</label>
                                        <input type="number" name="montant_s" class="form-control @error('montant_s') is-invalid @enderror" step="0.01" min="0" value="{{ old('montant_s', $father->montant_s) }}">
                                        @error('montant_s')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- File Upload Fields --}}
                                <div class="row g-3 mt-2">
                                    <div class="col-md-6">
                                        <label class="form-label required">بطاقة الهوية البيومترية (الوجه الأمامي)</label>
                                        <input type="file" name="biometric_id" class="form-control @error('biometric_id') is-invalid @enderror" accept=".pdf,.jpg,.jpeg,.png">
                                        <small class="form-text text-muted">الحد الأقصى: 5 ميجابايت | الصيغ المسموحة: PDF, JPG, JPEG, PNG</small>
                                        @error('biometric_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label required">بطاقة الهوية البيومترية (الوجه الخلفي)</label>
                                        <input type="file" name="biometric_id_back" class="form-control @error('biometric_id_back') is-invalid @enderror" accept=".pdf,.jpg,.jpeg,.png">
                                        <small class="form-text text-muted">الحد الأقصى: 5 ميجابايت | الصيغ المسموحة: PDF, JPG, JPEG, PNG</small>
                                        @error('biometric_id_back')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row g-3 mt-2" id="fatherCertificateOfNoneIncomeWrap-{{ $father->id }}" style="display: none;">
                                    <div class="col-md-6">
                                        <label class="form-label">شهادة عدم الدخل</label>
                                        <input type="file" name="Certificate_of_none_income" class="form-control @error('Certificate_of_none_income') is-invalid @enderror" accept=".pdf,.jpg,.jpeg,.png">
                                        <small class="form-text text-muted">الحد الأقصى: 5 ميجابايت | الصيغ المسموحة: PDF, JPG, JPEG, PNG</small>
                                        @error('Certificate_of_none_income')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">شهادة عدم الانتساب للضمان الاجتماعي</label>
                                        <input type="file" name="Certificate_of_non_affiliation_to_social_security" class="form-control @error('Certificate_of_non_affiliation_to_social_security') is-invalid @enderror" accept=".pdf,.jpg,.jpeg,.png">
                                        <small class="form-text text-muted">الحد الأقصى: 5 ميجابايت | الصيغ المسموحة: PDF, JPG, JPEG, PNG</small>
                                        @error('Certificate_of_non_affiliation_to_social_security')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row g-3 mt-2" id="fatherCrossedCcpWrap-{{ $father->id }}" style="display: none;">
                                    <div class="col-md-6">
                                        <label class="form-label">صك بريدي مشطوب</label>
                                        <input type="file" name="crossed_ccp" class="form-control @error('crossed_ccp') is-invalid @enderror" accept=".pdf,.jpg,.jpeg,.png">
                                        <small class="form-text text-muted">الحد الأقصى: 5 ميجابايت | الصيغ المسموحة: PDF, JPG, JPEG, PNG</small>
                                        @error('crossed_ccp')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="d-flex gap-2 mt-4 flex-wrap">
                                    <button type="submit" class="tuteur-btn tuteur-btn--primary js-submit-btn">
                                        <i class="fa-solid fa-floppy-disk"></i>حفظ التغييرات
                                    </button>
                                    <button type="button" class="tuteur-btn tuteur-btn--soft" data-cancel="edit-father" data-id="{{ $father->id }}">
                                        <i class="fa-solid fa-times"></i>إلغاء
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endforeach
                
                {{-- Add New Father Section --}}
                <div class="add-father-section">
                    <h4 class="add-father-section-title">
                        <i class="fa-solid fa-plus-circle"></i>
                        إضافة أب جديد
                    </h4>
                    <form method="POST" action="{{ route('tuteur.fathers.store') }}" class="js-swal-submit" novalidate>
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label required">الرقم الوطني للأب (NIN)</label>
                                <input type="text" name="nin" class="form-control @error('nin') is-invalid @enderror" maxlength="18" inputmode="numeric" pattern="\d{18}" required value="{{ old('nin') }}">
                                @error('nin')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">رقم الضمان الاجتماعي للأب (NSS)</label>
                                <input type="text" name="nss" class="form-control @error('nss') is-invalid @enderror" maxlength="12" inputmode="numeric" pattern="\d{12}" value="{{ old('nss') }}">
                                @error('nss')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row g-3 mt-2">
                            <div class="col-md-6">
                                <label class="form-label required">لقب الأب بالعربية</label>
                                <input type="text" name="nom_ar" class="form-control @error('nom_ar') is-invalid @enderror" required value="{{ old('nom_ar') }}">
                                @error('nom_ar')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label required">اسم الأب بالعربية</label>
                                <input type="text" name="prenom_ar" class="form-control @error('prenom_ar') is-invalid @enderror" required value="{{ old('prenom_ar') }}">
                                @error('prenom_ar')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row g-3 mt-2">
                            <div class="col-md-6">
                                <label class="form-label">لقب الأب بالفرنسية</label>
                                <input type="text" name="nom_fr" class="form-control @error('nom_fr') is-invalid @enderror" value="{{ old('nom_fr') }}">
                                @error('nom_fr')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">اسم الأب بالفرنسية</label>
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
                                <select name="categorie_sociale" id="newFatherCats" class="form-select @error('categorie_sociale') is-invalid @enderror">
                                    <option value="">—</option>
                                    <option value="عديم الدخل" {{ $catsOld === 'عديم الدخل' ? 'selected' : '' }}>عديم الدخل</option>
                                    <option value="{{ $lowIncome }}" {{ $catsOld === $lowIncome ? 'selected' : '' }}>{{ $lowIncome }}</option>
                                </select>
                                @error('categorie_sociale')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6" id="newFatherMontantWrap">
                                <label class="form-label">مبلغ الدخل الشهري</label>
                                <input type="number" name="montant_s" class="form-control @error('montant_s') is-invalid @enderror" step="0.01" min="0" value="{{ old('montant_s') }}">
                                @error('montant_s')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- File Upload Fields --}}
                        <div class="row g-3 mt-2">
                            <div class="col-md-6">
                                <label class="form-label required">بطاقة الهوية البيومترية (الوجه الأمامي)</label>
                                <input type="file" name="biometric_id" class="form-control @error('biometric_id') is-invalid @enderror" accept=".pdf,.jpg,.jpeg,.png" required>
                                <small class="form-text text-muted">الحد الأقصى: 5 ميجابايت | الصيغ المسموحة: PDF, JPG, JPEG, PNG</small>
                                @error('biometric_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label required">بطاقة الهوية البيومترية (الوجه الخلفي)</label>
                                <input type="file" name="biometric_id_back" class="form-control @error('biometric_id_back') is-invalid @enderror" accept=".pdf,.jpg,.jpeg,.png" required>
                                <small class="form-text text-muted">الحد الأقصى: 5 ميجابايت | الصيغ المسموحة: PDF, JPG, JPEG, PNG</small>
                                @error('biometric_id_back')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row g-3 mt-2" id="newFatherCertificateOfNoneIncomeWrap" style="display: none;">
                            <div class="col-md-6">
                                <label class="form-label">شهادة عدم الدخل</label>
                                <input type="file" name="Certificate_of_none_income" class="form-control @error('Certificate_of_none_income') is-invalid @enderror" accept=".pdf,.jpg,.jpeg,.png">
                                <small class="form-text text-muted">الحد الأقصى: 5 ميجابايت | الصيغ المسموحة: PDF, JPG, JPEG, PNG</small>
                                @error('Certificate_of_none_income')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">شهادة عدم الانتساب للضمان الاجتماعي</label>
                                <input type="file" name="Certificate_of_non_affiliation_to_social_security" class="form-control @error('Certificate_of_non_affiliation_to_social_security') is-invalid @enderror" accept=".pdf,.jpg,.jpeg,.png">
                                <small class="form-text text-muted">الحد الأقصى: 5 ميجابايت | الصيغ المسموحة: PDF, JPG, JPEG, PNG</small>
                                @error('Certificate_of_non_affiliation_to_social_security')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row g-3 mt-2" id="newFatherCrossedCcpWrap" style="display: none;">
                            <div class="col-md-6">
                                <label class="form-label">صك بريدي مشطوب</label>
                                <input type="file" name="crossed_ccp" class="form-control @error('crossed_ccp') is-invalid @enderror" accept=".pdf,.jpg,.jpeg,.png">
                                <small class="form-text text-muted">الحد الأقصى: 5 ميجابايت | الصيغ المسموحة: PDF, JPG, JPEG, PNG</small>
                                @error('crossed_ccp')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex gap-2 mt-4 flex-wrap">
                            <button type="submit" class="tuteur-btn tuteur-btn--primary js-submit-btn">
                                <i class="fa-solid fa-plus"></i>إضافة الأب
                            </button>
                        </div>
                    </form>
                </div>
            @else
                {{-- Empty State --}}
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <i class="fa-solid fa-mars"></i>
                    </div>
                    <div class="empty-state-text">لا توجد آباء مسجلين</div>
                    <div class="empty-state-subtext">ابدأ بإضافة أب جديد باستخدام النموذج أدناه</div>
                </div>

                {{-- Add New Father Section (Empty State) --}}
                <div class="add-father-section">
                    <h4 class="add-father-section-title">
                        <i class="fa-solid fa-plus-circle"></i>
                        إضافة أب جديد
                    </h4>
                    <form method="POST" action="{{ route('tuteur.fathers.store') }}" class="js-swal-submit" novalidate enctype="multipart/form-data">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label required">الرقم الوطني للأب (NIN)</label>
                                <input type="text" name="nin" class="form-control @error('nin') is-invalid @enderror" maxlength="18" inputmode="numeric" pattern="\d{18}" required value="{{ old('nin') }}">
                                @error('nin')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">رقم الضمان الاجتماعي للأب (NSS)</label>
                                <input type="text" name="nss" class="form-control @error('nss') is-invalid @enderror" maxlength="12" inputmode="numeric" pattern="\d{12}" value="{{ old('nss') }}">
                                @error('nss')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="row g-3 mt-2">
                            <div class="col-md-6">
                                <label class="form-label required">لقب الأب بالعربية</label>
                                <input type="text" name="nom_ar" class="form-control @error('nom_ar') is-invalid @enderror" required value="{{ old('nom_ar') }}">
                                @error('nom_ar')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label required">اسم الأب بالعربية</label>
                                <input type="text" name="prenom_ar" class="form-control @error('prenom_ar') is-invalid @enderror" required value="{{ old('prenom_ar') }}">
                                @error('prenom_ar')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="row g-3 mt-2">
                            <div class="col-md-6">
                                <label class="form-label">لقب الأب بالفرنسية</label>
                                <input type="text" name="nom_fr" class="form-control @error('nom_fr') is-invalid @enderror" value="{{ old('nom_fr') }}">
                                @error('nom_fr')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">اسم الأب بالفرنسية</label>
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
                                <select name="categorie_sociale" id="newFatherCats" class="form-select @error('categorie_sociale') is-invalid @enderror">
                                    <option value="">—</option>
                                    <option value="عديم الدخل" {{ $catsOld === 'عديم الدخل' ? 'selected' : '' }}>عديم الدخل</option>
                                    <option value="{{ $lowIncome }}" {{ $catsOld === $lowIncome ? 'selected' : '' }}>{{ $lowIncome }}</option>
                                </select>
                                @error('categorie_sociale')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6" id="newFatherMontantWrap">
                                <label class="form-label">مبلغ الدخل الشهري</label>
                                <input type="number" name="montant_s" class="form-control @error('montant_s') is-invalid @enderror" step="0.01" min="0" value="{{ old('montant_s') }}">
                                @error('montant_s')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- File Upload Fields --}}
                        <div class="row g-3 mt-2">
                            <div class="col-md-6">
                                <label class="form-label required">بطاقة الهوية البيومترية (الوجه الأمامي)</label>
                                <input type="file" name="biometric_id" class="form-control @error('biometric_id') is-invalid @enderror" accept=".pdf,.jpg,.jpeg,.png" required>
                                <small class="form-text text-muted">الحد الأقصى: 5 ميجابايت | الصيغ المسموحة: PDF, JPG, JPEG, PNG</small>
                                @error('biometric_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label required">بطاقة الهوية البيومترية (الوجه الخلفي)</label>
                                <input type="file" name="biometric_id_back" class="form-control @error('biometric_id_back') is-invalid @enderror" accept=".pdf,.jpg,.jpeg,.png" required>
                                <small class="form-text text-muted">الحد الأقصى: 5 ميجابايت | الصيغ المسموحة: PDF, JPG, JPEG, PNG</small>
                                @error('biometric_id_back')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row g-3 mt-2" id="newFatherCertificateOfNoneIncomeWrap" style="display: none;">
                            <div class="col-md-6">
                                <label class="form-label">شهادة عدم الدخل</label>
                                <input type="file" name="Certificate_of_none_income" class="form-control @error('Certificate_of_none_income') is-invalid @enderror" accept=".pdf,.jpg,.jpeg,.png">
                                <small class="form-text text-muted">الحد الأقصى: 5 ميجابايت | الصيغ المسموحة: PDF, JPG, JPEG, PNG</small>
                                @error('Certificate_of_none_income')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">شهادة عدم الانتساب للضمان الاجتماعي</label>
                                <input type="file" name="Certificate_of_non_affiliation_to_social_security" class="form-control @error('Certificate_of_non_affiliation_to_social_security') is-invalid @enderror" accept=".pdf,.jpg,.jpeg,.png">
                                <small class="form-text text-muted">الحد الأقصى: 5 ميجابايت | الصيغ المسموحة: PDF, JPG, JPEG, PNG</small>
                                @error('Certificate_of_non_affiliation_to_social_security')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row g-3 mt-2" id="newFatherCrossedCcpWrap" style="display: none;">
                            <div class="col-md-6">
                                <label class="form-label">صك بريدي مشطوب</label>
                                <input type="file" name="crossed_ccp" class="form-control @error('crossed_ccp') is-invalid @enderror" accept=".pdf,.jpg,.jpeg,.png">
                                <small class="form-text text-muted">الحد الأقصى: 5 ميجابايت | الصيغ المسموحة: PDF, JPG, JPEG, PNG</small>
                                @error('crossed_ccp')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex gap-2 mt-4 flex-wrap">
                            <button type="submit" class="tuteur-btn tuteur-btn--primary js-submit-btn">
                                <i class="fa-solid fa-plus"></i>إضافة الأب
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

    // Toggle edit per father
    document.querySelectorAll('[data-toggle="edit-father"]').forEach(btn => {
        btn.addEventListener('click', () => {
            const id = btn.getAttribute('data-id');
            const view = document.getElementById('fatherView-' + id);
            const edit = document.getElementById('fatherEdit-' + id);
            if (!view || !edit) return;
            view.classList.add('d-none');
            edit.classList.remove('d-none');
            // Scroll to edit form
            edit.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        });
    });
    
    document.querySelectorAll('[data-cancel="edit-father"]').forEach(btn => {
        btn.addEventListener('click', () => {
            const id = btn.getAttribute('data-id');
            const view = document.getElementById('fatherView-' + id);
            const edit = document.getElementById('fatherEdit-' + id);
            if (!view || !edit) return;
            edit.classList.add('d-none');
            view.classList.remove('d-none');
            // Scroll to view
            view.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        });
    });

    // Conditional montant and file fields per existing father edit
    const syncWrap = (selectEl, wrapEl) => {
        if (!selectEl || !wrapEl) return;
        wrapEl.style.display = (selectEl.value === lowIncome) ? 'block' : 'none';
    };
    
    const syncFileFields = (selectEl, fatherId) => {
        if (!selectEl) return;
        const noIncomeWrap = document.getElementById('fatherCertificateOfNoneIncomeWrap-' + fatherId);
        const crossedCcpWrap = document.getElementById('fatherCrossedCcpWrap-' + fatherId);
        const value = selectEl.value;
        
        if (value === 'عديم الدخل') {
            if (noIncomeWrap) noIncomeWrap.style.display = 'block';
            if (crossedCcpWrap) crossedCcpWrap.style.display = 'none';
        } else if (value === lowIncome) {
            if (noIncomeWrap) noIncomeWrap.style.display = 'none';
            if (crossedCcpWrap) crossedCcpWrap.style.display = 'block';
        } else {
            if (noIncomeWrap) noIncomeWrap.style.display = 'none';
            if (crossedCcpWrap) crossedCcpWrap.style.display = 'none';
        }
    };
    
    document.querySelectorAll('select.fatherCats').forEach(sel => {
        const id = sel.getAttribute('data-id');
        const wrap = document.getElementById('fatherMontantWrap-' + id);
        const doSync = () => {
            syncWrap(sel, wrap);
            syncFileFields(sel, id);
        };
        sel.addEventListener('change', doSync);
        doSync();
    });

    // Conditional montant and file fields for new father form
    const newCats = document.getElementById('newFatherCats');
    const newWrap = document.getElementById('newFatherMontantWrap');
    const newNoIncomeWrap = document.getElementById('newFatherCertificateOfNoneIncomeWrap');
    const newCrossedCcpWrap = document.getElementById('newFatherCrossedCcpWrap');
    
    const syncNewFileFields = () => {
        if (!newCats) return;
        const value = newCats.value;
        
        if (value === 'عديم الدخل') {
            if (newNoIncomeWrap) newNoIncomeWrap.style.display = 'block';
            if (newCrossedCcpWrap) newCrossedCcpWrap.style.display = 'none';
        } else if (value === lowIncome) {
            if (newNoIncomeWrap) newNoIncomeWrap.style.display = 'none';
            if (newCrossedCcpWrap) newCrossedCcpWrap.style.display = 'block';
        } else {
            if (newNoIncomeWrap) newNoIncomeWrap.style.display = 'none';
            if (newCrossedCcpWrap) newCrossedCcpWrap.style.display = 'none';
        }
    };
    
    if (newCats && newWrap) {
        const doSync = () => {
            syncWrap(newCats, newWrap);
            syncNewFileFields();
        };
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
        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            const btn = form.querySelector('.js-submit-btn');
            if (btn) {
                btn.disabled = true;
                btn.dataset.originalHtml = btn.innerHTML;
                btn.innerHTML = `<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>جارٍ الحفظ...`;
            }

            if (window.Swal) {
                Swal.fire({
                    title: 'جارٍ الحفظ...',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    didOpen: () => Swal.showLoading(),
                });
            }

            try {
                // Determine if this is create or update
                const methodInput = form.querySelector('input[name="_method"]');
                const isUpdate = methodInput && methodInput.value === 'PUT';
                
                // Get API credentials
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                const apiToken = localStorage.getItem('api_token');
                const tokenType = localStorage.getItem('token_type') || 'Bearer';
                
                // Determine API endpoint
                let apiUrl, method;
                if (isUpdate) {
                    // Extract ID from parent container's ID attribute (e.g., "fatherEdit-123" -> "123")
                    let fatherId = null;
                    let parent = form.parentElement;
                    while (parent && !fatherId) {
                        if (parent.id && parent.id.startsWith('fatherEdit-')) {
                            fatherId = parent.id.replace('fatherEdit-', '');
                            break;
                        }
                        parent = parent.parentElement;
                    }
                    if (!fatherId) {
                        throw new Error('Unable to determine father ID from form');
                    }
                    apiUrl = `/api/fathers/${fatherId}`;
                    method = 'PUT';
                } else {
                    apiUrl = '/api/fathers';
                    method = 'POST';
                }
                
                // For PUT requests, check if we have file uploads
                const hasFiles = form.querySelector('input[type="file"]') && Array.from(form.querySelectorAll('input[type="file"]')).some(input => input.files && input.files.length > 0);
                
                let body, headers;
                
                if (hasFiles || !isUpdate) {
                    // Use FormData for file uploads or POST requests
                    const formData = new FormData(form);
                    console.log('Using FormData - entries:');
                    for (let pair of formData.entries()) {
                        console.log(pair[0] + ': ' + (pair[1] instanceof File ? pair[1].name : pair[1]));
                    }
                    body = formData;
                    headers = {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest'
                    };
                    // Don't set Content-Type for FormData - browser will set it with boundary
                } else {
                    // For PUT without files, use JSON
                    const formData = new FormData(form);
                    const data = {};
                    for (let [key, value] of formData.entries()) {
                        // Skip _method, _token, and file inputs
                        if (key !== '_method' && key !== '_token' && !(value instanceof File)) {
                            data[key] = value;
                        }
                    }
                    console.log('Using JSON - data:', data);
                    body = JSON.stringify(data);
                    headers = {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest'
                    };
                }
                
                if (apiToken) {
                    headers['Authorization'] = `${tokenType} ${apiToken}`;
                }

                const response = await fetch(apiUrl, {
                    method: method,
                    headers: headers,
                    body: body,
                    credentials: 'include'
                });

                // Check if response is JSON
                const contentType = response.headers.get('content-type');
                let result;
                if (contentType && contentType.includes('application/json')) {
                    result = await response.json();
                    console.log('API Response:', result);
                } else {
                    const text = await response.text();
                    console.error('Non-JSON response:', text);
                    throw new Error(`Server returned non-JSON response: ${response.status} ${response.statusText}`);
                }

                if (response.ok) {
                    console.log('Update successful:', result);
                    // Only reload if we actually got a success response with data
                    if (result && result.message) {
                        if (window.Swal) {
                            Swal.fire({
                                icon: 'success',
                                title: 'تم بنجاح',
                                text: isUpdate ? 'تم تحديث معلومات الأب بنجاح' : 'تمت إضافة الأب بنجاح',
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            window.location.reload();
                        }
                    } else {
                        console.error('Unexpected response format:', result);
                        Swal.fire({
                            icon: 'warning',
                            title: 'تحذير',
                            text: 'تم إرسال الطلب ولكن لم يتم تأكيد التحديث. يرجى التحقق من البيانات.'
                        });
                    }
                } else {
                    // Handle validation errors
                    if (btn) {
                        btn.disabled = false;
                        btn.innerHTML = btn.dataset.originalHtml;
                    }

                    let errorMessage = result.message || 'حدث خطأ أثناء الحفظ';
                    let errorHtml = '';

                    if (result.errors) {
                        const errorList = Object.values(result.errors).flat();
                        errorHtml = `<ul style="text-align:right; direction:rtl; padding-right:18px; margin:0;">
                            ${errorList.map(err => `<li>${err}</li>`).join('')}
                        </ul>`;
                    }

                    if (window.Swal) {
                        Swal.fire({
                            icon: 'error',
                            title: 'تحقق من المعلومات',
                            html: errorHtml || errorMessage,
                            confirmButtonText: 'حسنًا'
                        });
                    } else {
                        alert(errorMessage);
                    }
                }
            } catch (error) {
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = btn.dataset.originalHtml;
                }

                if (window.Swal) {
                    Swal.fire({
                        icon: 'error',
                        title: 'خطأ',
                        text: 'حدث خطأ في الاتصال. يرجى المحاولة مرة أخرى.'
                    });
                } else {
                    alert('حدث خطأ في الاتصال');
                }
            }
        });
    });

    // Delete confirmation using API
    document.querySelectorAll('.js-delete-father-form').forEach(form => {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            if (!window.Swal) {
                if (!confirm('هل أنت متأكد من الحذف؟')) {
                    return;
                }
            } else {
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
            }

            // Get father ID from form action
            const actionUrl = form.getAttribute('action');
            const fatherId = actionUrl.split('/').pop();
            
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                                 document.querySelector('input[name="_token"]')?.value;
                
                // Get API token from localStorage
                const apiToken = localStorage.getItem('api_token');
                const tokenType = localStorage.getItem('token_type') || 'Bearer';
                
                const headers = {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                };
                
                // Add Authorization header if API token exists
                if (apiToken) {
                    headers['Authorization'] = `${tokenType} ${apiToken}`;
                }
                
                const response = await fetch(`/api/fathers/${fatherId}`, {
                    method: 'DELETE',
                    headers: headers,
                    credentials: 'include'
                });

                if (response.ok) {
                    // Remove the card from DOM
                    const fatherCard = document.getElementById(`fatherCard-${fatherId}`);
                    if (fatherCard) {
                        fatherCard.style.transition = 'opacity 0.3s, transform 0.3s';
                        fatherCard.style.opacity = '0';
                        fatherCard.style.transform = 'scale(0.95)';
                        setTimeout(() => {
                            fatherCard.remove();
                            
                            // Check if there are no more fathers, reload page to show empty state
                            if (!document.querySelector('.father-card')) {
                                window.location.reload();
                            }
                        }, 300);
                    }
                    
                    if (window.Swal) {
                        Swal.fire({
                            icon: 'success',
                            title: 'تم الحذف بنجاح',
                            text: 'تم حذف الأب بنجاح',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    } else {
                        toast('success', 'تم حذف الأب بنجاح');
                    }
                } else {
                    const errorData = await response.json();
                    let errorMessage = 'حدث خطأ أثناء الحذف';
                    if (errorData.message) {
                        errorMessage = errorData.message;
                    }
                    
                    if (window.Swal) {
                        Swal.fire({
                            icon: 'error',
                            title: 'خطأ',
                            text: errorMessage
                        });
                    } else {
                        alert(errorMessage);
                    }
                }
            } catch (error) {
                console.error('Delete error:', error);
                if (window.Swal) {
                    Swal.fire({
                        icon: 'error',
                        title: 'خطأ',
                        text: 'حدث خطأ في الاتصال'
                    });
                } else {
                    alert('حدث خطأ في الاتصال');
                }
            }
        });
    });
})();
</script>
@endsection
