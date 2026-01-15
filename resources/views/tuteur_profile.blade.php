@extends('layouts.main')

@section('title', 'معلوماتي الشخصية')

@section('content')
<div class="tuteur-page">
    <div class="tuteur-card">
        <div class="tuteur-card__header">
            <div>
                <h3 class="tuteur-card__title"><i class="fa-solid fa-user"></i>معلوماتي الشخصية (الولي / الوصي)</h3>
                <p class="tuteur-card__subtitle" id="mode-subtitle">عرض وتعديل معلومات الحساب</p>
            </div>
            <div style="display: flex; gap: 10px;">
                <button id="edit-btn" class="tuteur-btn tuteur-btn--primary">
                    <i class="fa-solid fa-edit"></i>تعديل
                </button>
                <button id="save-btn" class="tuteur-btn tuteur-btn--success" style="display: none;">
                    <i class="fa-solid fa-save"></i>حفظ التغييرات
                </button>
                <button id="cancel-btn" class="tuteur-btn tuteur-btn--secondary" style="display: none;">
                    <i class="fa-solid fa-times"></i>إلغاء
                </button>
                <a href="{{ route('dashboard') }}" class="tuteur-btn tuteur-btn--soft">
                    <i class="fa-solid fa-arrow-right"></i>عودة
                </a>
            </div>
        </div>

        <div class="tuteur-card__body">
            <form id="profile-form">
                <div class="tuteur-kv">
                    <!-- Non-editable fields -->
                    <div class="tuteur-kv__item">
                        <div class="tuteur-kv__k">رقم التعريف الوطني (NIN)</div>
                        <div class="tuteur-kv__v">{{ $tuteur->nin ?? '—' }}</div>
                    </div>
                    <div class="tuteur-kv__item">
                        <div class="tuteur-kv__k">الجنس</div>
                        <div class="tuteur-kv__v">{{ $tuteur->sexe ?? '—' }}</div>
                    </div>

                    <!-- Editable fields -->
                    <div class="tuteur-kv__item">
                        <div class="tuteur-kv__k">اللقب بالعربية <span class="text-danger">*</span></div>
                        <div class="tuteur-kv__v">
                            <span class="view-mode">{{ $tuteur->nom_ar ?? '—' }}</span>
                            <input type="text" name="nom_ar" class="edit-mode form-control" value="{{ $tuteur->nom_ar }}" style="display: none;" required>
                            <small class="text-danger error-msg" data-field="nom_ar"></small>
                        </div>
                    </div>

                    <div class="tuteur-kv__item">
                        <div class="tuteur-kv__k">الاسم بالعربية <span class="text-danger">*</span></div>
                        <div class="tuteur-kv__v">
                            <span class="view-mode">{{ $tuteur->prenom_ar ?? '—' }}</span>
                            <input type="text" name="prenom_ar" class="edit-mode form-control" value="{{ $tuteur->prenom_ar }}" style="display: none;" required>
                            <small class="text-danger error-msg" data-field="prenom_ar"></small>
                        </div>
                    </div>

                    <div class="tuteur-kv__item">
                        <div class="tuteur-kv__k">اللقب باللاتينية</div>
                        <div class="tuteur-kv__v">
                            <span class="view-mode">{{ $tuteur->nom_fr ?? '—' }}</span>
                            <input type="text" name="nom_fr" class="edit-mode form-control" value="{{ $tuteur->nom_fr }}" style="display: none;">
                            <small class="text-danger error-msg" data-field="nom_fr"></small>
                        </div>
                    </div>

                    <div class="tuteur-kv__item">
                        <div class="tuteur-kv__k">الاسم باللاتينية</div>
                        <div class="tuteur-kv__v">
                            <span class="view-mode">{{ $tuteur->prenom_fr ?? '—' }}</span>
                            <input type="text" name="prenom_fr" class="edit-mode form-control" value="{{ $tuteur->prenom_fr }}" style="display: none;">
                            <small class="text-danger error-msg" data-field="prenom_fr"></small>
                        </div>
                    </div>

                    <div class="tuteur-kv__item">
                        <div class="tuteur-kv__k">تاريخ الميلاد</div>
                        <div class="tuteur-kv__v">
                            <span class="view-mode">{{ $tuteur->date_naiss ?? '—' }}</span>
                            <input type="date" name="date_naiss" class="edit-mode form-control" value="{{ $tuteur->date_naiss }}" style="display: none;">
                            <small class="text-danger error-msg" data-field="date_naiss"></small>
                        </div>
                    </div>

                    <div class="tuteur-kv__item">
                        <div class="tuteur-kv__k">العنوان</div>
                        <div class="tuteur-kv__v">
                            <span class="view-mode">{{ $tuteur->adresse ?? '—' }}</span>
                            <input type="text" name="adresse" class="edit-mode form-control" value="{{ $tuteur->adresse }}" style="display: none;">
                            <small class="text-danger error-msg" data-field="adresse"></small>
                        </div>
                    </div>

                    <div class="tuteur-kv__item">
                        <div class="tuteur-kv__k">رقم الهاتف</div>
                        <div class="tuteur-kv__v">
                            <span class="view-mode">{{ $tuteur->tel ?? '—' }}</span>
                            <input type="text" name="tel" class="edit-mode form-control" value="{{ $tuteur->tel }}" style="display: none;" pattern="[0-9]{10}" maxlength="10" placeholder="مثال: 0555123456">
                            <small class="text-danger error-msg" data-field="tel"></small>
                        </div>
                    </div>

                    <div class="tuteur-kv__item">
                        <div class="tuteur-kv__k">البريد الإلكتروني</div>
                        <div class="tuteur-kv__v">
                            <span class="view-mode">{{ $tuteur->email ?? '—' }}</span>
                            <input type="email" name="email" class="edit-mode form-control" value="{{ $tuteur->email }}" style="display: none;">
                            <small class="text-danger error-msg" data-field="email"></small>
                        </div>
                    </div>

                    <div class="tuteur-kv__item">
                        <div class="tuteur-kv__k">رقم بطاقة التعريف الوطنية</div>
                        <div class="tuteur-kv__v">
                            <span class="view-mode">{{ $tuteur->num_cni ?? '—' }}</span>
                            <input type="text" name="num_cni" class="edit-mode form-control" value="{{ $tuteur->num_cni }}" style="display: none;" maxlength="10">
                            <small class="text-danger error-msg" data-field="num_cni"></small>
                        </div>
                    </div>

                    <div class="tuteur-kv__item">
                        <div class="tuteur-kv__k">تاريخ إصدار البطاقة</div>
                        <div class="tuteur-kv__v">
                            <span class="view-mode">{{ $tuteur->date_cni ?? '—' }}</span>
                            <input type="date" name="date_cni" class="edit-mode form-control" value="{{ $tuteur->date_cni }}" style="display: none;">
                            <small class="text-danger error-msg" data-field="date_cni"></small>
                        </div>
                    </div>

                    <div class="tuteur-kv__item">
                        <div class="tuteur-kv__k">رقم الضمان الاجتماعي</div>
                        <div class="tuteur-kv__v">
                            <span class="view-mode">{{ $tuteur->nss ?? '—' }}</span>
                            <input type="text" name="nss" class="edit-mode form-control" value="{{ $tuteur->nss }}" style="display: none;" pattern="[0-9]{12}" maxlength="12" placeholder="12 رقمًا">
                            <small class="text-danger error-msg" data-field="nss"></small>
                        </div>
                    </div>

                    <div class="tuteur-kv__item">
                        <div class="tuteur-kv__k">رقم الحساب البريدي (CCP)</div>
                        <div class="tuteur-kv__v">
                            <span class="view-mode">{{ $tuteur->num_cpt ?? '—' }}</span>
                            <input type="text" name="num_cpt" class="edit-mode form-control" value="{{ $tuteur->num_cpt }}" style="display: none;" pattern="[0-9]{12}" maxlength="12" placeholder="12 رقمًا">
                            <small class="text-danger error-msg" data-field="num_cpt"></small>
                        </div>
                    </div>

                    <div class="tuteur-kv__item">
                        <div class="tuteur-kv__k">مفتاح الحساب البريدي</div>
                        <div class="tuteur-kv__v">
                            <span class="view-mode">{{ $tuteur->cle_cpt ?? '—' }}</span>
                            <input type="text" name="cle_cpt" class="edit-mode form-control" value="{{ $tuteur->cle_cpt }}" style="display: none;" pattern="[0-9]{2}" maxlength="2" placeholder="رقمان">
                            <small class="text-danger error-msg" data-field="cle_cpt"></small>
                        </div>
                    </div>

                    <!-- Password change section (only in edit mode) -->
                    <div class="tuteur-kv__item edit-mode" style="display: none;">
                        <div class="tuteur-kv__k">كلمة المرور الجديدة</div>
                        <div class="tuteur-kv__v">
                            <input type="password" name="password" class="form-control" placeholder="اتركه فارغًا إذا كنت لا تريد تغييره" minlength="8">
                            <small class="text-muted">الحد الأدنى 8 أحرف</small>
                            <small class="text-danger error-msg" data-field="password"></small>
                        </div>
                    </div>

                    <div class="tuteur-kv__item edit-mode" style="display: none;">
                        <div class="tuteur-kv__k">تأكيد كلمة المرور</div>
                        <div class="tuteur-kv__v">
                            <input type="password" name="password_confirmation" class="form-control" placeholder="أعد إدخال كلمة المرور الجديدة">
                            <small class="text-danger error-msg" data-field="password_confirmation"></small>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.tuteur-kv__v input.form-control {
    padding: 8px 12px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
    width: 100%;
    transition: border-color 0.3s;
}

.tuteur-kv__v input.form-control:focus {
    outline: none;
    border-color: #4CAF50;
    box-shadow: 0 0 0 2px rgba(76, 175, 80, 0.1);
}

.tuteur-kv__v input.form-control.is-invalid {
    border-color: #dc3545;
}

.error-msg {
    display: block;
    margin-top: 4px;
    font-size: 12px;
}

.text-danger {
    color: #dc3545;
}

.text-muted {
    color: #6c757d;
    font-size: 12px;
    display: block;
    margin-top: 4px;
}
</style>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const editBtn = document.getElementById('edit-btn');
    const saveBtn = document.getElementById('save-btn');
    const cancelBtn = document.getElementById('cancel-btn');
    const profileForm = document.getElementById('profile-form');
    const modeSubtitle = document.getElementById('mode-subtitle');
    
    let originalData = {};
    let isEditMode = false;

    // Helper function to get API headers with token
    function getApiHeaders(includeCSRF = true) {
        const headers = {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
        };
        
        if (includeCSRF) {
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (csrfToken) {
                headers['X-CSRF-TOKEN'] = csrfToken.content;
            }
        }
        
        const token = localStorage.getItem('api_token');
        const tokenType = localStorage.getItem('token_type') || 'Bearer';
        if (token) {
            headers['Authorization'] = `${tokenType} ${token}`;
        }
        
        return headers;
    }

    // Save original data
    function saveOriginalData() {
        originalData = {};
        const inputs = profileForm.querySelectorAll('input[name]');
        inputs.forEach(input => {
            originalData[input.name] = input.value;
        });
    }

    // Restore original data
    function restoreOriginalData() {
        Object.keys(originalData).forEach(name => {
            const input = profileForm.querySelector(`input[name="${name}"]`);
            if (input) {
                input.value = originalData[name];
            }
        });
    }

    // Clear errors
    function clearErrors() {
        document.querySelectorAll('.error-msg').forEach(el => el.textContent = '');
        document.querySelectorAll('.form-control').forEach(el => el.classList.remove('is-invalid'));
    }

    // Show errors
    function showErrors(errors) {
        clearErrors();
        Object.keys(errors).forEach(field => {
            const errorEl = document.querySelector(`.error-msg[data-field="${field}"]`);
            const inputEl = document.querySelector(`input[name="${field}"]`);
            if (errorEl && errors[field]) {
                errorEl.textContent = Array.isArray(errors[field]) ? errors[field][0] : errors[field];
            }
            if (inputEl) {
                inputEl.classList.add('is-invalid');
            }
        });
    }

    // Toggle edit mode
    function toggleEditMode(enable) {
        isEditMode = enable;
        
        if (enable) {
            saveOriginalData();
            modeSubtitle.textContent = 'قم بتعديل معلوماتك ثم احفظ التغييرات';
            editBtn.style.display = 'none';
            saveBtn.style.display = 'inline-block';
            cancelBtn.style.display = 'inline-block';
            
            document.querySelectorAll('.view-mode').forEach(el => el.style.display = 'none');
            document.querySelectorAll('.edit-mode').forEach(el => el.style.display = 'block');
        } else {
            modeSubtitle.textContent = 'عرض وتعديل معلومات الحساب';
            editBtn.style.display = 'inline-block';
            saveBtn.style.display = 'none';
            cancelBtn.style.display = 'none';
            
            document.querySelectorAll('.view-mode').forEach(el => el.style.display = 'block');
            document.querySelectorAll('.edit-mode').forEach(el => el.style.display = 'none');
            
            clearErrors();
        }
    }

    // Edit button click
    editBtn.addEventListener('click', function() {
        toggleEditMode(true);
    });

    // Cancel button click
    cancelBtn.addEventListener('click', function() {
        restoreOriginalData();
        toggleEditMode(false);
    });

    // Save button click
    saveBtn.addEventListener('click', async function() {
        clearErrors();

        // Prepare form data
        const formData = new FormData(profileForm);
        const data = {};
        
        // Only send fields that have values or have been changed
        for (let [key, value] of formData.entries()) {
            if (value !== null && value !== '') {
                data[key] = value;
            }
        }

        // Show loading
        Swal.fire({
            title: 'جارٍ الحفظ...',
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        try {
            const nin = '{{ $tuteur->nin }}';
            const response = await fetch(`/api/tuteurs/${nin}`, {
                method: 'PUT',
                headers: getApiHeaders(),
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (response.ok) {
                await Swal.fire({
                    icon: 'success',
                    title: 'تم الحفظ بنجاح!',
                    text: result.message || 'تم تحديث معلوماتك الشخصية',
                    confirmButtonText: 'حسنًا',
                    customClass: {
                        confirmButton: 'swal-confirm-btn'
                    },
                    buttonsStyling: false
                });

                // Update view mode with new values
                Object.keys(data).forEach(key => {
                    if (key !== 'password' && key !== 'password_confirmation') {
                        const viewEl = document.querySelector(`.tuteur-kv__item:has(input[name="${key}"]) .view-mode`);
                        if (viewEl && data[key]) {
                            viewEl.textContent = data[key];
                        }
                    }
                });

                // Clear password fields
                profileForm.querySelector('input[name="password"]').value = '';
                profileForm.querySelector('input[name="password_confirmation"]').value = '';

                toggleEditMode(false);
            } else {
                Swal.close();
                
                if (result.errors) {
                    showErrors(result.errors);
                    Swal.fire({
                        icon: 'error',
                        title: 'خطأ في البيانات',
                        text: result.message || 'يرجى التحقق من البيانات المدخلة',
                        confirmButtonText: 'حسنًا',
                        customClass: {
                            confirmButton: 'swal-confirm-btn'
                        },
                        buttonsStyling: false
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'حدث خطأ',
                        text: result.message || 'فشل في حفظ التغييرات',
                        confirmButtonText: 'حسنًا',
                        customClass: {
                            confirmButton: 'swal-confirm-btn'
                        },
                        buttonsStyling: false
                    });
                }
            }
        } catch (error) {
            Swal.close();
            Swal.fire({
                icon: 'error',
                title: 'خطأ في الاتصال',
                text: 'حدث خطأ أثناء الاتصال بالخادم',
                confirmButtonText: 'حسنًا',
                customClass: {
                    confirmButton: 'swal-confirm-btn'
                },
                buttonsStyling: false
            });
        }
    });
});
</script>
@endsection
