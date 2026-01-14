function addRequiredStars() {
    document.querySelectorAll("#signupForm .form-group").forEach(group => {
        // Vérifier si un input requis existe dans ce groupe
        const input = group.querySelector("input[required], select[required], textarea[required]");
        const label = group.querySelector("label");
        if (input && !input.disabled && label) {
            if (!label.querySelector(".text-danger")) {
                const star = document.createElement("span");
                star.className = "text-danger";
                star.textContent = " *";
                label.appendChild(star);
            }
        }
    });
}

const ACCESS_DEADLINE = new Date("2026-03-01T00:00:00");

function enforceAccessDeadline() {
    const form = document.getElementById("signupForm");
    const deadlineAlert = document.getElementById("deadlineAlert");
    if (!form) return;

    const now = new Date();
    const isClosed = now >= ACCESS_DEADLINE;

    if (deadlineAlert) {
        deadlineAlert.classList.toggle("d-none", !isClosed);
    }

    if (isClosed) {
        form.querySelectorAll("input, select, textarea, button").forEach((el) => {
            if (el.type === "hidden") return;
            el.disabled = true;
        });
    }
}

document.addEventListener("DOMContentLoaded", () => {
     /* === 🗺️ Chargement dynamique des wilayas et communes === */
    const wilayaSelect = document.getElementById("wilayaSelectSignup");
    const communeSelect = document.getElementById("communeSelectSignup");

    if (wilayaSelect && communeSelect) {
        // Charger les wilayas
        async function loadWilayas() {
            try {
                wilayaSelect.innerHTML = '<option value="">جارٍ التحميل...</option>';
                const res = await fetch('/api/wilayas');
                const responseData = await res.json();
                
                // Handle response structure: could be array directly or wrapped in {data: [...]}
                const wilayas = Array.isArray(responseData) ? responseData : (responseData.data || []);

                wilayaSelect.innerHTML = '<option value="">اختر...</option>';
                if (Array.isArray(wilayas)) {
                wilayas.forEach(w => {
                    wilayaSelect.innerHTML += `<option value="${w.code_wil}">${w.lib_wil_ar}</option>`;
                });
                }
            } catch (err) {
                console.error('خطأ في تحميل الولايات:', err);
                wilayaSelect.innerHTML = '<option value="">تعذر تحميل البيانات</option>';
            }
        }

        // Charger les communes selon la wilaya
        async function loadCommunes(codeWilaya) {
            try {
                communeSelect.innerHTML = '<option value="">جارٍ التحميل...</option>';
                communeSelect.disabled = true;

                const res = await fetch(`/api/communes/by-wilaya/${codeWilaya}`);
                const responseData = await res.json();
                
                // Handle response structure: could be array directly or wrapped in {data: [...]}
                const communes = Array.isArray(responseData) ? responseData : (responseData.data || []);

                communeSelect.innerHTML = '<option value="">اختر...</option>';
                if (Array.isArray(communes)) {
                communes.forEach(c => {
                    communeSelect.innerHTML += `<option value="${c.code_comm}">${c.lib_comm_ar}</option>`;
                });
                }

                communeSelect.disabled = false;
            } catch (err) {
                console.error('خطأ في تحميل البلديات:', err);
                communeSelect.innerHTML = '<option value="">تعذر تحميل البيانات</option>';
            }
        }

        // Quand on choisit une wilaya → charger les communes
        wilayaSelect.addEventListener("change", (e) => {
            const codeWilaya = e.target.value;
            if (codeWilaya) {
                loadCommunes(codeWilaya);
            } else {
                communeSelect.innerHTML = '<option value="">اختر الولاية أولا...</option>';
                communeSelect.disabled = true;
            }
        });

        // Charger la liste dès le chargement de la page
        loadWilayas();
    }

    /* === 🪪 Wilaya et commune d’émission de la carte === */
const wilayaCarte = document.getElementById("wilaya_carte");
const communeCarte = document.getElementById("commune_carte");

if (wilayaCarte && communeCarte) {
    async function loadWilayasCarte() {
        try {
            wilayaCarte.innerHTML = '<option value="">جارٍ التحميل...</option>';
            const res = await fetch('/api/wilayas');
            const responseData = await res.json();
            
            // Handle response structure: could be array directly or wrapped in {data: [...]}
            const wilayas = Array.isArray(responseData) ? responseData : (responseData.data || []);

            wilayaCarte.innerHTML = '<option value="">-- اختر الولاية --</option>';
            if (Array.isArray(wilayas)) {
            wilayas.forEach(w =>
                wilayaCarte.innerHTML += `<option value="${w.code_wil}">${w.lib_wil_ar}</option>`
            );
            }
        } catch (err) {
            console.error('خطأ في تحميل ولايات البطاقة:', err);
            wilayaCarte.innerHTML = '<option value="">تعذر تحميل البيانات</option>';
        }
    }

    async function loadCommunesCarte(codeWilaya) {
        try {
            communeCarte.innerHTML = '<option value="">جارٍ التحميل...</option>';
            communeCarte.disabled = true;

            const res = await fetch(`/api/communes/by-wilaya/${codeWilaya}`);
            const responseData = await res.json();
            
            // Handle response structure: could be array directly or wrapped in {data: [...]}
            const communes = Array.isArray(responseData) ? responseData : (responseData.data || []);

            communeCarte.innerHTML = '<option value="">-- اختر البلدية --</option>';
            if (Array.isArray(communes)) {
            communes.forEach(c =>
                communeCarte.innerHTML += `<option value="${c.code_comm}">${c.lib_comm_ar}</option>`
            );
            }

            communeCarte.disabled = false;
        } catch (err) {
            console.error('خطأ في تحميل بلديات البطاقة:', err);
            communeCarte.innerHTML = '<option value="">تعذر تحميل البيانات</option>';
        }
    }

    wilayaCarte.addEventListener("change", (e) => {
        const codeWilaya = e.target.value;
        if (codeWilaya) {
            loadCommunesCarte(codeWilaya);
        } else {
            communeCarte.innerHTML = '<option value="">اختر الولاية أولا...</option>';
            communeCarte.disabled = true;
        }
    });

    // Charger la liste dès le chargement de la page
    loadWilayasCarte();
}

    /* === 💰 Tuteur Social Category & Monthly Income Logic === */
    const tuteurCategorieSelect = document.getElementById('categorie_sociale');
    const tuteurMontantWrapper = document.getElementById('montant_s_wrapper');
    const tuteurMontantInput = document.getElementById('montant_s');
    const certificateOfNoneIncomeWrapper = document.getElementById('certificate_of_none_income_wrapper');
    const certificateOfNoneIncomeInput = document.getElementById('Certificate_of_none_income');
    const certificateOfNonAffiliationWrapper = document.getElementById('certificate_of_non_affiliation_wrapper');
    const certificateOfNonAffiliationInput = document.getElementById('Certificate_of_non_affiliation_to_social_security');
    const crossedCcpWrapper = document.getElementById('crossed_ccp_wrapper');
    const crossedCcpInput = document.getElementById('crossed_ccp');
    
    function updateFileUploadFields() {
        const selectedValue = tuteurCategorieSelect ? tuteurCategorieSelect.value : '';
        
        if (selectedValue === 'عديم الدخل') {
            // Show: Certificate_of_none_income, Certificate_of_non_affiliation_to_social_security
            if (certificateOfNoneIncomeWrapper) certificateOfNoneIncomeWrapper.style.display = 'block';
            if (certificateOfNoneIncomeInput) certificateOfNoneIncomeInput.setAttribute('required', 'required');
            
            if (certificateOfNonAffiliationWrapper) certificateOfNonAffiliationWrapper.style.display = 'block';
            if (certificateOfNonAffiliationInput) certificateOfNonAffiliationInput.setAttribute('required', 'required');
            
            // Hide: crossed_ccp
            if (crossedCcpWrapper) crossedCcpWrapper.style.display = 'none';
            if (crossedCcpInput) {
                crossedCcpInput.removeAttribute('required');
                crossedCcpInput.value = '';
            }
        } else if (selectedValue === 'الدخل الشهري أقل أو يساوي مبلغ الأجر الوطني الأدنى المضمون') {
            // Show: crossed_ccp
            if (crossedCcpWrapper) crossedCcpWrapper.style.display = 'block';
            if (crossedCcpInput) crossedCcpInput.setAttribute('required', 'required');
            
            // Hide: Certificate_of_none_income, Certificate_of_non_affiliation_to_social_security
            if (certificateOfNoneIncomeWrapper) certificateOfNoneIncomeWrapper.style.display = 'none';
            if (certificateOfNoneIncomeInput) {
                certificateOfNoneIncomeInput.removeAttribute('required');
                certificateOfNoneIncomeInput.value = '';
            }
            
            if (certificateOfNonAffiliationWrapper) certificateOfNonAffiliationWrapper.style.display = 'none';
            if (certificateOfNonAffiliationInput) {
                certificateOfNonAffiliationInput.removeAttribute('required');
                certificateOfNonAffiliationInput.value = '';
            }
        } else {
            // Hide all conditional fields
            if (certificateOfNoneIncomeWrapper) certificateOfNoneIncomeWrapper.style.display = 'none';
            if (certificateOfNoneIncomeInput) {
                certificateOfNoneIncomeInput.removeAttribute('required');
                certificateOfNoneIncomeInput.value = '';
            }
            
            if (certificateOfNonAffiliationWrapper) certificateOfNonAffiliationWrapper.style.display = 'none';
            if (certificateOfNonAffiliationInput) {
                certificateOfNonAffiliationInput.removeAttribute('required');
                certificateOfNonAffiliationInput.value = '';
            }
            
            if (crossedCcpWrapper) crossedCcpWrapper.style.display = 'none';
            if (crossedCcpInput) {
                crossedCcpInput.removeAttribute('required');
                crossedCcpInput.value = '';
            }
        }
    }
    
    if (tuteurCategorieSelect && tuteurMontantWrapper && tuteurMontantInput) {
        tuteurCategorieSelect.addEventListener('change', function() {
            if (this.value === 'الدخل الشهري أقل أو يساوي مبلغ الأجر الوطني الأدنى المضمون') {
                tuteurMontantWrapper.style.display = 'block';
                tuteurMontantInput.required = true;
            } else {
                tuteurMontantWrapper.style.display = 'none';
                tuteurMontantInput.required = false;
                tuteurMontantInput.value = '';
            }
            updateFileUploadFields();
        });
        
        // Initialize on page load
        updateFileUploadFields();
    }

    addRequiredStars();
    const formSteps = document.querySelectorAll(".form-step");
    const nextBtns = document.querySelectorAll(".next-step");
    const prevBtns = document.querySelectorAll(".prev-step");
    const progress = document.getElementById("progress");
    const progressSteps = document.querySelectorAll(".progress-step");
    let formStepIndex = 0;

    /* === 🔄 Mise à jour des étapes === */
    function updateFormSteps() {
        formSteps.forEach((step, index) => step.classList.toggle("active", index === formStepIndex));
        progressSteps.forEach((step, index) => step.classList.toggle("active", index <= formStepIndex));
        progress.style.width = (formStepIndex / (progressSteps.length - 1)) * 100 + "%";
        attachValidationListeners();
        checkCategorieInitial();
    }

    

    /* === 🧾 Gestion des messages d’erreur === */
    function showError(input, message) {
        removeError(input);
        if (!message) return;

        const error = document.createElement("div");
        error.className = "error-message";
        error.textContent = message;

        const wrapper = input.closest(".password-wrapper") || input;
        wrapper.insertAdjacentElement("afterend", error);

        input.classList.add("invalid");
        input.classList.remove("valid");
    }

    function showSuccess(input) {
        removeError(input);
        input.classList.add("valid");
        input.classList.remove("invalid");
    }

    function removeError(input) {
        const wrapper = input.closest(".password-wrapper") || input;
        const existing = wrapper.parentNode.querySelectorAll(".error-message");
        existing.forEach(e => e.remove());
    }
    
    // Helper function to setup Arabic validation for mother name fields
    window.setupMotherArabicValidation = function(motherIndex) {
        const nomArInput = document.getElementById(`mother_${motherIndex}_nom_ar`);
        const prenomArInput = document.getElementById(`mother_${motherIndex}_prenom_ar`);
        
        [nomArInput, prenomArInput].forEach(input => {
            if (!input) return;
            
            const arabicRegex = /^[\u0600-\u06FF\s]+$/;
            
            // Block non-Arabic characters on keypress
            input.addEventListener('keypress', function(e) {
                const char = e.key;
                if (!/^[\u0600-\u06FF\s]$/.test(char)) {
                    e.preventDefault();
                    showError(this, 'يرجى الإدخال باللغة العربية فقط');
                    this.classList.add('invalid');
                    this.classList.remove('valid');
                }
            });
            
            // Real-time validation on input
            input.addEventListener('input', function() {
                const value = this.value.trim();
                if (value === '') {
                    removeError(this);
                    this.classList.remove('valid', 'invalid');
                    return;
                }
                
                if (arabicRegex.test(value)) {
                    removeError(this);
                    this.classList.add('valid');
                    this.classList.remove('invalid');
                } else {
                    showError(this, 'يرجى الإدخال باللغة العربية فقط');
                    this.classList.add('invalid');
                    this.classList.remove('valid');
                }
            });
            
            // Validation on blur
            input.addEventListener('blur', function() {
                const value = this.value.trim();
                if (value === '') {
                    removeError(this);
                    this.classList.remove('valid', 'invalid');
                    return;
                }
                
                if (!arabicRegex.test(value)) {
                    showError(this, 'يرجى الإدخال باللغة العربية فقط');
                    this.classList.add('invalid');
                    this.classList.remove('valid');
                }
            });
        });
    };
    
    // Helper function to setup NIN validation for mother (18 digits)
    window.setupMotherNINValidation = function(motherIndex) {
        const ninInput = document.getElementById(`mother_${motherIndex}_nin`);
        if (!ninInput) return;
        
        let checkTimeout = null;
        
        // Prevent typing more than 18 digits
        ninInput.addEventListener('keypress', function(e) {
            // Allow: backspace, delete, tab, escape, enter, and numbers
            if ([8, 9, 27, 13, 46].indexOf(e.keyCode) !== -1 ||
                // Allow Ctrl+A, Ctrl+C, Ctrl+V, Ctrl+X
                (e.keyCode === 65 && e.ctrlKey === true) ||
                (e.keyCode === 67 && e.ctrlKey === true) ||
                (e.keyCode === 86 && e.ctrlKey === true) ||
                (e.keyCode === 88 && e.ctrlKey === true) ||
                // Allow: home, end, left, right
                (e.keyCode >= 35 && e.keyCode <= 39)) {
                return;
            }
            // Ensure that it is a number and stop if already 18 digits
            if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
                e.preventDefault();
            }
            // Prevent typing if already 18 digits
            if (this.value.length >= 18) {
                e.preventDefault();
            }
        });
        
        // Only allow digits and limit to 18 characters
        ninInput.addEventListener('input', function() {
            // Remove any non-digit characters
            this.value = this.value.replace(/\D/g, '');
            
            // Strictly limit to 18 digits - prevent any more from being entered
            if (this.value.length > 18) {
                this.value = this.value.slice(0, 18);
            }
            
            const value = this.value.trim();
            
            // Clear previous timeout
            if (checkTimeout) {
                clearTimeout(checkTimeout);
            }
            
            // Real-time validation
            if (value === '') {
                removeError(this);
                this.classList.remove('valid', 'invalid');
                return;
            }
            
            if (value.length === 18) {
                // Check for duplicates after a short delay
                checkTimeout = setTimeout(async () => {
                    try {
                        const response = await fetch('/api/check/mother/nin', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                            },
                            body: JSON.stringify({ nin: value })
                        });
                        
                        const data = await response.json();
                        
                        if (data.exists) {
                            showError(ninInput, 'هذا الرقم الوطني موجود بالفعل');
                            ninInput.classList.add('invalid');
                            ninInput.classList.remove('valid');
                        } else {
                            removeError(ninInput);
                            ninInput.classList.add('valid');
                            ninInput.classList.remove('invalid');
                        }
                    } catch (error) {
                        console.error('Error checking NIN:', error);
                        // Don't show error if check fails, just mark as valid for length
                        removeError(ninInput);
                        ninInput.classList.add('valid');
                        ninInput.classList.remove('invalid');
                    }
                }, 500);
                
                // Show progress while checking
                removeError(ninInput);
                ninInput.classList.remove('invalid');
            } else {
                showError(this, `الرقم الوطني يجب أن يحتوي على 18 رقمًا (${value.length}/18)`);
                this.classList.add('invalid');
                this.classList.remove('valid');
            }
        });
        
        // Validation on blur
        ninInput.addEventListener('blur', async function() {
            const value = this.value.trim();
            if (value === '') {
                removeError(this);
                this.classList.remove('valid', 'invalid');
                return;
            }
            
            if (value.length !== 18) {
                showError(this, 'الرقم الوطني يجب أن يحتوي على 18 رقمًا');
                this.classList.add('invalid');
                this.classList.remove('valid');
            } else {
                // Check for duplicates
                try {
                    const response = await fetch('/api/check/mother/nin', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                        },
                        body: JSON.stringify({ nin: value })
                    });
                    
                    const data = await response.json();
                    
                    if (data.exists) {
                        showError(this, 'هذا الرقم الوطني موجود بالفعل');
                        this.classList.add('invalid');
                        this.classList.remove('valid');
                    } else {
                        removeError(this);
                        this.classList.add('valid');
                        this.classList.remove('invalid');
                    }
                } catch (error) {
                    console.error('Error checking NIN:', error);
                    // If check fails, still validate length
                    removeError(this);
                    this.classList.add('valid');
                    this.classList.remove('invalid');
                }
            }
        });
    };
    
    // Helper function to setup NSS validation for mother (12 digits)
    window.setupMotherNSSValidation = function(motherIndex) {
        const nssInput = document.getElementById(`mother_${motherIndex}_nss`);
        if (!nssInput) return;
        
        let checkTimeout = null;
        
        // Prevent typing more than 12 digits
        nssInput.addEventListener('keypress', function(e) {
            // Allow: backspace, delete, tab, escape, enter, and numbers
            if ([8, 9, 27, 13, 46].indexOf(e.keyCode) !== -1 ||
                // Allow Ctrl+A, Ctrl+C, Ctrl+V, Ctrl+X
                (e.keyCode === 65 && e.ctrlKey === true) ||
                (e.keyCode === 67 && e.ctrlKey === true) ||
                (e.keyCode === 86 && e.ctrlKey === true) ||
                (e.keyCode === 88 && e.ctrlKey === true) ||
                // Allow: home, end, left, right
                (e.keyCode >= 35 && e.keyCode <= 39)) {
                return;
            }
            // Ensure that it is a number and stop if already 12 digits
            if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
                e.preventDefault();
            }
            // Prevent typing if already 12 digits
            if (this.value.length >= 12) {
                e.preventDefault();
            }
        });
        
        // Prevent paste of more than 12 digits
        nssInput.addEventListener('paste', function(e) {
            e.preventDefault();
            const pastedText = (e.clipboardData || window.clipboardData).getData('text');
            const digitsOnly = pastedText.replace(/\D/g, '').slice(0, 12);
            this.value = digitsOnly;
            this.dispatchEvent(new Event('input'));
        });
        
        // Only allow digits and limit to 12 characters
        nssInput.addEventListener('input', function() {
            // Remove any non-digit characters
            this.value = this.value.replace(/\D/g, '');
            
            // Limit to 12 digits
            if (this.value.length > 12) {
                this.value = this.value.slice(0, 12);
            }
            
            const value = this.value.trim();
            
            // Clear previous timeout
            if (checkTimeout) {
                clearTimeout(checkTimeout);
            }
            
            // Real-time validation
            if (value === '') {
                removeError(this);
                this.classList.remove('valid', 'invalid');
                return;
            }
            
            if (value.length === 12) {
                // Check for duplicates after a short delay
                checkTimeout = setTimeout(async () => {
                    try {
                        const response = await fetch('/api/check/mother/nss', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                            },
                            body: JSON.stringify({ nss: value })
                        });
                        
                        const data = await response.json();
                        
                        if (data.exists) {
                            showError(nssInput, 'هذا رقم الضمان الاجتماعي موجود بالفعل');
                            nssInput.classList.add('invalid');
                            nssInput.classList.remove('valid');
                        } else {
                            removeError(nssInput);
                            nssInput.classList.add('valid');
                            nssInput.classList.remove('invalid');
                        }
                    } catch (error) {
                        console.error('Error checking NSS:', error);
                        // Don't show error if check fails, just mark as valid for length
                        removeError(nssInput);
                        nssInput.classList.add('valid');
                        nssInput.classList.remove('invalid');
                    }
                }, 500);
                
                // Show progress while checking
                removeError(nssInput);
                nssInput.classList.remove('invalid');
            } else {
                showError(this, `رقم الضمان الاجتماعي يجب أن يحتوي على 12 رقمًا (${value.length}/12)`);
                this.classList.add('invalid');
                this.classList.remove('valid');
            }
        });
        
        // Validation on blur
        nssInput.addEventListener('blur', async function() {
            const value = this.value.trim();
            if (value === '') {
                removeError(this);
                this.classList.remove('valid', 'invalid');
                return;
            }
            
            if (value.length !== 12) {
                showError(this, 'رقم الضمان الاجتماعي يجب أن يحتوي على 12 رقمًا');
                this.classList.add('invalid');
                this.classList.remove('valid');
            } else {
                // Check for duplicates
                try {
                    const response = await fetch('/api/check/mother/nss', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                        },
                        body: JSON.stringify({ nss: value })
                    });
                    
                    const data = await response.json();
                    
                    if (data.exists) {
                        showError(this, 'هذا رقم الضمان الاجتماعي موجود بالفعل');
                        this.classList.add('invalid');
                        this.classList.remove('valid');
                    } else {
                        removeError(this);
                        this.classList.add('valid');
                        this.classList.remove('invalid');
                    }
                } catch (error) {
                    console.error('Error checking NSS:', error);
                    // If check fails, still validate length
                    removeError(this);
                    this.classList.add('valid');
                    this.classList.remove('invalid');
                }
            }
        });
    };

        /* === ✅ Validation champ === */
function validateField(input, showMessage = true) {
    // Ignorer les champs désactivés
    if (input.disabled) {
        removeError(input);
        input.classList.remove("invalid", "valid");
        return true;
    }

    const value = input.value.trim();
    const id = input.id;
    const type = input.type;
    const name = input.name;

    removeError(input);
    let valid = true;
    let message = "";

   if (type === "radio") {
    const radioGroup = document.querySelectorAll(`input[name="${name}"]`);
    const checked = document.querySelector(`input[name="${name}"]:checked`);
    const wrapper = input.closest(".form-group");

    // 🔹 Supprimer tout ancien message avant d'en ajouter un nouveau
    if (wrapper) {
        wrapper.querySelectorAll(".error-message").forEach(e => e.remove());
    }

    if (!checked) {
        valid = false;
        message = "هذا الحقل مطلوب";

        if (showMessage && wrapper) {
            const error = document.createElement("div");
            error.className = "error-message";
            error.textContent = message;
            wrapper.appendChild(error);
        }
    }

    // ✅ Marquer tous les boutons du groupe comme valides ou invalides
    radioGroup.forEach(radio => {
        radio.classList.toggle("valid", valid);
        radio.classList.toggle("invalid", !valid);
    });

    return valid;
}

    // ✅ Validation des selects
    if (input.tagName.toLowerCase() === "select" && input.hasAttribute("required")) {
        if (value === "" || value === "none") {
            valid = false;
            message = "هذا الحقل مطلوب";
        }
    }

    // ✅ Validation des champs requis (autres types)
    if (input.hasAttribute("required") && value === "") {
        valid = false;
        message = "هذا الحقل مطلوب";
    }

    // ✅ Validation spécifique selon ID
    if (valid && value !== "") {
        switch (id) {
            case "nin":
                if (!/^\d{18}$/.test(value)) {
                    valid = false;
                    message = "يجب أن يحتوي الرقم التعريفي الوطني على 18 رقمًا بالضبط";
                }
                break;

            case "email":
                if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) {
                    valid = false;
                    message = "البريد الإلكتروني غير صالح";
                }
                break;

            case "phone":
                if (!/^\d{10}$/.test(value)) {
                    valid = false;
                    message = "رقم الهاتف يجب أن يحتوي على 10 أرقام";
                }
                break;

            case "password":
                if (!/^(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).{8,}$/.test(value)) {
                    valid = false;
                    message = "كلمة المرور ضعيفة، يجب أن تحتوي على حرف كبير، رقم، ورمز خاص";
                }
                break;

            case "confirm_password":
                const password = document.getElementById("password").value;
                if (value !== password) {
                    valid = false;
                    message = "كلمة المرور غير مطابقة";
                }
                break;

            case "date_naissance":
                const today = new Date();
                const selectedDate = new Date(value);
                if (selectedDate > today) {
                    valid = false;
                    message = "تاريخ الميلاد لا يمكن أن يكون في المستقبل";
                }
                break;

            case "num_carte":
                if (!/^\d{9}$/.test(value)) {
                    valid = false;
                    message = "يجب أن يحتوي رقم بطاقة التعريف الوطنية على 9 أرقام";
                }
                break;

            case "date_carte":
                const today2 = new Date();
                today2.setHours(0, 0, 0, 0);
                const selectedCarteDate = new Date(value);
                
                // Check if date is in the future
                if (selectedCarteDate > today2) {
                    valid = false;
                    message = "تاريخ إصدار البطاقة يجب أن يكون قبل أو يساوي تاريخ اليوم";
                }
                // Check if date is more than 10 years old
                else {
                    const tenYearsAgo = new Date();
                    tenYearsAgo.setFullYear(today2.getFullYear() - 10);
                    tenYearsAgo.setHours(0, 0, 0, 0);
                    
                    if (selectedCarteDate < tenYearsAgo) {
                        valid = false;
                        message = "تاريخ إصدار البطاقة يجب ألا يتجاوز 10 سنوات من تاريخ اليوم";
                    }
                }
                break;

            case "nss":
                if (!/^\d{12}$/.test(value)) {
                    valid = false;
                    message = "رقم الضمان الاجتماعي يجب أن يحتوي على 12 رقمًا بالضبط";
                }
                break;

            case "montant_s":
                const categorie = document.getElementById("categorie_sociale").value;
                if (categorie !== "عديم الدخل") { // uniquement si revenu attendu
                    const num = parseFloat(value);
                    if (isNaN(num) || num <= 0) {
                        valid = false;
                        message = "يرجى إدخال مبلغ صالح (أكبر من 0)";
                    } else {
                        input.value = num; // normalisation
                    }
                } else {
                    // si catégorie = 2, montant est 0 et valid
                    input.value = "0";
                    valid = true;
                    removeError(input);
                }
                break;


            case "num_cp":
                if (!/^\d{10}$/.test(value)) {
                    valid = false;
                    message = "رقم الحساب البريدي يجب أن يحتوي على 10 أرقام";
                }
                break;

            case "cle_ccp":
                if (!/^\d{2}$/.test(value)) {
                    valid = false;
                    message = "الرقم المفتاح يجب أن يحتوي على رقمين اثنين";
                }
                break;

            case "adresse":
                const arabicRegex = /^[\u0600-\u06FF\s]+$/;
                if (!arabicRegex.test(value)) {
                    valid = false;
                    message = "يرجى الإدخال باللغة العربية فقط";
                }
                break;
        }
    }

    if (valid) {
        showSuccess(input);
    } else if (showMessage) {
        showError(input, message);
    } else {
        input.classList.remove("valid", "invalid");
    }

    return valid;
}


    /* === 📞 Validation du téléphone (10 chiffres seulement) === */
    const phoneInput = document.getElementById("phone");
    if (phoneInput) {
        phoneInput.addEventListener("input", function () {
            const onlyNumbers = this.value.replace(/\D/g, "");

            if (this.value !== onlyNumbers) {
                showError(this, "يجب إدخال أرقام فقط");
            } else if (onlyNumbers.length > 0 && onlyNumbers.length !== 10) {
                showError(this, "رقم الهاتف يجب أن يحتوي على 10 أرقام");
            } else {
                removeError(this);
                this.classList.add("valid");
                this.classList.remove("invalid");
            }

            this.value = onlyNumbers.slice(0, 10);
        });
    }

/* === 🇦🇪 Validation nom_ar & prenom_ar & adresse (arabe uniquement et blocage français) === */
const arabicNameFields = ["nom_ar", "prenom_ar", "adresse"];
arabicNameFields.forEach((id) => {
    const input = document.getElementById(id);
    if (!input) return;

    const arabicRegex = /^[\u0600-\u06FF\s]+$/;

    // 🔹 Bloquer la saisie non arabe
    input.addEventListener("keypress", function (e) {
        const char = e.key;
        // si le caractère n'est pas arabe ni espace, on bloque
        if (!/^[\u0600-\u06FF\s]$/.test(char)) {
            e.preventDefault();
            showError(this, "يرجى الإدخال باللغة العربية فقط");
            this.classList.add("invalid");
            this.classList.remove("valid");
        }
    });

    // 🔹 Prevent pasting non-Arabic text
    input.addEventListener("paste", function (e) {
        e.preventDefault();
        const pastedText = (e.clipboardData || window.clipboardData).getData('text');
        const arabicOnly = pastedText.replace(/[^\u0600-\u06FF\s]/g, '');
        this.value = arabicOnly;
        this.dispatchEvent(new Event('input'));
    });

    // 🔹 Validation visuelle dynamique
    input.addEventListener("input", function () {
        const value = this.value.trim();
        if (value === "") {
            removeError(this);
            this.classList.remove("valid", "invalid");
            return;
        }

        if (arabicRegex.test(value)) {
            removeError(this);
            this.classList.add("valid");
            this.classList.remove("invalid");
        } else {
            showError(this, "يرجى الإدخال باللغة العربية فقط");
            this.classList.add("invalid");
            this.classList.remove("valid");
        }
    });
    
    // 🔹 Validation on blur
    input.addEventListener("blur", function () {
        const value = this.value.trim();
        if (value === "") {
            removeError(this);
            this.classList.remove("valid", "invalid");
            return;
        }
        
        if (!arabicRegex.test(value)) {
            showError(this, "يرجى الإدخال باللغة العربية فقط");
            this.classList.add("invalid");
            this.classList.remove("valid");
        }
    });
});
/* === 🇫🇷 Validation nom_fr & prenom_fr (latin uniquement et blocage arabe) === */
const frenchNameFields = ["nom_fr", "prenom_fr"];
frenchNameFields.forEach((id) => {
    const input = document.getElementById(id);
    if (!input) return;

    const latinRegex = /^[A-Za-zÀ-ÿ\s]+$/; // accepte les lettres accentuées et espaces

    // 🔹 Bloquer la saisie arabe
    input.addEventListener("keypress", function (e) {
        const char = e.key;
        // bloque toute lettre arabe
        if (/^[\u0600-\u06FF]$/.test(char)) {
            e.preventDefault();
            showError(this, "يرجى الإدخال بالحروف اللاتينية فقط");
            this.classList.add("invalid");
            this.classList.remove("valid");
        }
    });

    // 🔹 Vérification visuelle dynamique
    input.addEventListener("input", function () {
        const value = this.value.trim();
        if (value === "") {
            removeError(this);
            this.classList.remove("valid", "invalid");
            return;
        }

        if (latinRegex.test(value)) {
            removeError(this);
            this.classList.add("valid");
            this.classList.remove("invalid");
        } else {
            showError(this, "يرجى الإدخال بالحروف اللاتينية فقط");
            this.classList.add("invalid");
            this.classList.remove("valid");
        }
    });
});

 function attachValidationListeners() {
const activeInputs = document.querySelectorAll(".form-step.active input, .form-step.active select");
    const categorieSelect = document.getElementById("categorie_sociale");
    const montantInput = document.getElementById("montant_s");

    // === 🎯 Gestion catégorie sociale & montant mensuel ===
    const montantWrapper = document.getElementById('montant_s_wrapper');
    if (categorieSelect && montantInput && montantWrapper) {
        // Remove old handler if exists
        if (categorieSelect._changeHandler) {
        categorieSelect.removeEventListener("change", categorieSelect._changeHandler);
        }

        categorieSelect._changeHandler = function () {
            const certificateOfNoneIncomeWrapper = document.getElementById('certificate_of_none_income_wrapper');
            const certificateOfNoneIncomeInput = document.getElementById('Certificate_of_none_income');
            const certificateOfNonAffiliationWrapper = document.getElementById('certificate_of_non_affiliation_wrapper');
            const certificateOfNonAffiliationInput = document.getElementById('Certificate_of_non_affiliation_to_social_security');
            const crossedCcpWrapper = document.getElementById('crossed_ccp_wrapper');
            const crossedCcpInput = document.getElementById('crossed_ccp');
            
            if (this.value === "الدخل الشهري أقل أو يساوي مبلغ الأجر الوطني الأدنى المضمون") {
                // Second option → show and require montant_s
                montantWrapper.style.display = 'block';
                montantInput.removeAttribute("disabled");
                montantInput.setAttribute("required", "required");
                montantInput.value = "";
                
                // Show crossed_ccp
                if (crossedCcpWrapper) crossedCcpWrapper.style.display = 'block';
                if (crossedCcpInput) crossedCcpInput.setAttribute("required", "required");
                
                // Hide certificate fields
                if (certificateOfNoneIncomeWrapper) certificateOfNoneIncomeWrapper.style.display = 'none';
                if (certificateOfNoneIncomeInput) {
                    certificateOfNoneIncomeInput.removeAttribute("required");
                    certificateOfNoneIncomeInput.value = '';
                }
                if (certificateOfNonAffiliationWrapper) certificateOfNonAffiliationWrapper.style.display = 'none';
                if (certificateOfNonAffiliationInput) {
                    certificateOfNonAffiliationInput.removeAttribute("required");
                    certificateOfNonAffiliationInput.value = '';
                }
            } else if (this.value === "عديم الدخل") {
                // First option → hide montant_s
                montantWrapper.style.display = 'none';
                montantInput.value = "";
                montantInput.removeAttribute("required");
                montantInput.removeAttribute("disabled");
                montantInput.classList.remove("valid", "invalid");
                removeError(montantInput);
                
                // Show certificate fields
                if (certificateOfNoneIncomeWrapper) certificateOfNoneIncomeWrapper.style.display = 'block';
                if (certificateOfNoneIncomeInput) certificateOfNoneIncomeInput.setAttribute("required", "required");
                
                if (certificateOfNonAffiliationWrapper) certificateOfNonAffiliationWrapper.style.display = 'block';
                if (certificateOfNonAffiliationInput) certificateOfNonAffiliationInput.setAttribute("required", "required");
                
                // Hide crossed_ccp
                if (crossedCcpWrapper) crossedCcpWrapper.style.display = 'none';
                if (crossedCcpInput) {
                    crossedCcpInput.removeAttribute("required");
                    crossedCcpInput.value = '';
                }
            } else {
                // Empty → hide and clear all
                montantWrapper.style.display = 'none';
                montantInput.value = "";
                montantInput.removeAttribute("required");
                montantInput.removeAttribute("disabled");
                montantInput.classList.remove("valid", "invalid");
                removeError(montantInput);
                
                // Hide all file fields
                if (certificateOfNoneIncomeWrapper) certificateOfNoneIncomeWrapper.style.display = 'none';
                if (certificateOfNoneIncomeInput) {
                    certificateOfNoneIncomeInput.removeAttribute("required");
                    certificateOfNoneIncomeInput.value = '';
                }
                if (certificateOfNonAffiliationWrapper) certificateOfNonAffiliationWrapper.style.display = 'none';
                if (certificateOfNonAffiliationInput) {
                    certificateOfNonAffiliationInput.removeAttribute("required");
                    certificateOfNonAffiliationInput.value = '';
                }
                if (crossedCcpWrapper) crossedCcpWrapper.style.display = 'none';
                if (crossedCcpInput) {
                    crossedCcpInput.removeAttribute("required");
                    crossedCcpInput.value = '';
                }
            }
        };

        categorieSelect.addEventListener("change", categorieSelect._changeHandler);

        // Vérification initiale au chargement
        categorieSelect._changeHandler();
    }

    activeInputs.forEach(input => {
        input.removeEventListener("input", input._inputHandler);
        input.removeEventListener("blur", input._blurHandler);

        // limiter le NIN
        if (input.id === "nin") {
            input.addEventListener("input", () => {
                input.value = input.value.replace(/\D/g, "").slice(0, 18);
            });
        }

        if (input.id === "num_carte") {
            input.addEventListener("input", () => {
                input.value = input.value.replace(/\D/g, "").slice(0, 9);
            });
        }

        if (input.id === "nss") {
            input.addEventListener("input", () => {
                input.value = input.value.replace(/\D/g, "").slice(0, 12);
            });
        }

        if (input.id === "montant_s") {
            input.addEventListener("input", () => {
                if (parseFloat(input.value) < 0) {
                    input.value = "";
                }
            });
        }

        if (input.id === "num_cp") {
            input.addEventListener("input", () => {
                input.value = input.value.replace(/\D/g, "").slice(0, 10);
            });
        }

        if (input.id === "cle_ccp") {
            input.addEventListener("input", () => {
                input.value = input.value.replace(/\D/g, "").slice(0, 2);
            });
        }

        // Special handling for date_carte to show instant error feedback
        if (input.id === "date_carte") {
            input.addEventListener("change", () => {
                if (input.value.trim() !== "") {
                    validateField(input, true); // Show error message immediately
                }
            });
            // Override the general input handler for date_carte to show instant feedback
            input._inputHandler = () => {
                if (input.value.trim() !== "") {
                    validateField(input, true); // Show error message immediately on input
                }
            };
        } else {
        input._inputHandler = () => {
            if (input.value.trim() !== "") validateField(input, false);
        };
        }
        input.addEventListener("input", input._inputHandler);

        input._blurHandler = () => {
            if (input.value.trim() !== "") validateField(input, true);
        };
        input.addEventListener("blur", input._blurHandler);
    });
}


// ✅ Vérification initiale de la catégorie sociale au chargement de la page
function checkCategorieInitial() {
    const categorie = document.getElementById("categorie_sociale");
    const montant = document.getElementById("montant_s");
    const montantWrapper = document.getElementById("montant_s_wrapper");
    const certificateOfNoneIncomeWrapper = document.getElementById('certificate_of_none_income_wrapper');
    const certificateOfNoneIncomeInput = document.getElementById('Certificate_of_none_income');
    const certificateOfNonAffiliationWrapper = document.getElementById('certificate_of_non_affiliation_wrapper');
    const certificateOfNonAffiliationInput = document.getElementById('Certificate_of_non_affiliation_to_social_security');
    const crossedCcpWrapper = document.getElementById('crossed_ccp_wrapper');
    const crossedCcpInput = document.getElementById('crossed_ccp');

    if (categorie && montant && montantWrapper) {
        if (categorie.value === "عديم الدخل" || categorie.value === "") {
            // First option or empty → hide montant_s
            montantWrapper.style.display = 'none';
            montant.value = "";
            montant.removeAttribute("required");
            removeError(montant);
            
            // Show certificate fields if "عديم الدخل"
            if (categorie.value === "عديم الدخل") {
                if (certificateOfNoneIncomeWrapper) certificateOfNoneIncomeWrapper.style.display = 'block';
                if (certificateOfNoneIncomeInput) certificateOfNoneIncomeInput.setAttribute("required", "required");
                if (certificateOfNonAffiliationWrapper) certificateOfNonAffiliationWrapper.style.display = 'block';
                if (certificateOfNonAffiliationInput) certificateOfNonAffiliationInput.setAttribute("required", "required");
            } else {
                if (certificateOfNoneIncomeWrapper) certificateOfNoneIncomeWrapper.style.display = 'none';
                if (certificateOfNoneIncomeInput) certificateOfNoneIncomeInput.removeAttribute("required");
                if (certificateOfNonAffiliationWrapper) certificateOfNonAffiliationWrapper.style.display = 'none';
                if (certificateOfNonAffiliationInput) certificateOfNonAffiliationInput.removeAttribute("required");
            }
            
            // Hide crossed_ccp
            if (crossedCcpWrapper) crossedCcpWrapper.style.display = 'none';
            if (crossedCcpInput) crossedCcpInput.removeAttribute("required");
        } else if (categorie.value === "الدخل الشهري أقل أو يساوي مبلغ الأجر الوطني الأدنى المضمون") {
            // Second option → show and require montant_s
            montantWrapper.style.display = 'block';
            montant.removeAttribute("disabled");
            montant.setAttribute("required", "required");
            if (montant.value === "0") montant.value = "";
            
            // Show crossed_ccp
            if (crossedCcpWrapper) crossedCcpWrapper.style.display = 'block';
            if (crossedCcpInput) crossedCcpInput.setAttribute("required", "required");
            
            // Hide certificate fields
            if (certificateOfNoneIncomeWrapper) certificateOfNoneIncomeWrapper.style.display = 'none';
            if (certificateOfNoneIncomeInput) certificateOfNoneIncomeInput.removeAttribute("required");
            if (certificateOfNonAffiliationWrapper) certificateOfNonAffiliationWrapper.style.display = 'none';
            if (certificateOfNonAffiliationInput) certificateOfNonAffiliationInput.removeAttribute("required");
        }
    }
}


    /* === 🧱 Validation avant suivant === */
    function validateStep() {
    const activeStep = document.querySelector(".form-step.active");
    const inputs = activeStep.querySelectorAll("input[required], select[required]");
    let allValid = true;
    let missingFields = [];

    // Supprimer anciens messages
    activeStep.querySelectorAll(".error-message").forEach(e => e.remove());

    inputs.forEach(input => {
        // Skip hidden fields (like montant_s when not required)
        const wrapper = input.closest('#montant_s_wrapper');
        if (wrapper && window.getComputedStyle(wrapper).display === 'none') {
            return;
        }
        if (!validateField(input, true)) {
            allValid = false;
            const label = input.closest(".form-group")?.querySelector("label")?.textContent || input.name;
            missingFields.push(label);
        }
    });

    inputs.forEach(input => {
        // Inclure les champs disabled qui doivent être obligatoires
        if (input.disabled && input.id !== "montant_s") return; // ignore les autres disabled
        if (!validateField(input, true)) {
            allValid = false;
            const label = input.closest(".form-group")?.querySelector("label")?.textContent || input.name;
            if (!missingFields.includes(label)) missingFields.push(label);
        }
    });
    if (!allValid && typeof Swal !== "undefined") {
        Swal.fire({
            icon: "warning",
            title: "يرجى إكمال البيانات",
            html: `الحقول التالية مطلوبة أو تحتوي على أخطاء:<br><b>${missingFields.join("<br>")}</b>`,
            confirmButtonText: "حسنًا",
        });
    }

    return allValid;
}

    /* === ⏭ Navigation === */
    nextBtns.forEach(btn => {
        btn.addEventListener("click", () => {

            // ✅ Validation Étape 1 et Étape 2
            if ((formStepIndex === 0 || formStepIndex === 1) && !validateStep()) return;

            if (formStepIndex < formSteps.length - 1) {
                formStepIndex++;
                updateFormSteps();
            }
        });
    });

    prevBtns.forEach(btn => {
        btn.addEventListener("click", () => {
            if (formStepIndex > 0) {
                formStepIndex--;
                updateFormSteps();
            }
        });
    });
const form = document.getElementById("signupForm");

function verifierRIP(ccp, cle) {
    ccp = ccp.trim();
    cle = cle.trim();

    if (!/^\d+$/.test(ccp) || !/^\d+$/.test(cle)) return false;

    const R1 = parseInt(ccp, 10) * 100;
    const R2 = R1 % 97;
    const R3 = (R2 + 85 > 97) ? (R2 + 85 - 97) : (R2 + 85);
    const clerr = (97 - R3).toString().padStart(2, "0");

    return cle === clerr;
}


if (form) {
    // Désactive fermement la validation HTML native du navigateur
    form.noValidate = true; // équivalent à l'attribut HTML novalidate

    form.addEventListener("submit", async (e) => {
        e.preventDefault();

        let allValid = true;
        let missingFields = [];

        // 🧹 Remove old error messages
        document.querySelectorAll(".error-message").forEach(el => el.remove());

        // ✅ Validate all required fields
        const allRequiredInputs = form.querySelectorAll("input[required], select[required]");
        allRequiredInputs.forEach(input => {
            if (!validateField(input, true)) {
                allValid = false;
                const label = input.closest(".form-group, .col-md-3")?.querySelector("label")?.textContent || input.name;
                if (!missingFields.includes(label)) missingFields.push(label);
            }
        });

        // ✅ Validate gender
        const genderChecked = form.querySelector('input[name="gender"]:checked');
        if (!genderChecked) {
            allValid = false;
            if (!missingFields.includes("الجنس")) missingFields.push("الجنس");
        }

        // ✅ Validate agreement checkbox
        const agreementCheckbox = document.getElementById('agreement_checkbox');
        if (!agreementCheckbox || !agreementCheckbox.checked) {
            allValid = false;
            if (!missingFields.includes("الموافقة على القوانين")) missingFields.push("الموافقة على القوانين");
            // Highlight the checkbox
            if (agreementCheckbox) {
                agreementCheckbox.style.outline = '2px solid #ef4444';
                agreementCheckbox.style.outlineOffset = '2px';
            }
        } else if (agreementCheckbox) {
            // Remove highlight if checked
            agreementCheckbox.style.outline = '';
            agreementCheckbox.style.outlineOffset = '';
        }

        // ✅ Validate file uploads
        const fileInputs = form.querySelectorAll('input[type="file"]');
        const allowedTypes = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'];
        const maxSize = 5 * 1024 * 1024; // 5MB in bytes
        
        for (const fileInput of fileInputs) {
            // Skip validation if field is hidden (not required)
            const wrapper = fileInput.closest('.form-group');
            if (wrapper && window.getComputedStyle(wrapper).display === 'none') {
                continue;
            }
            
            if (fileInput.hasAttribute('required') && (!fileInput.files || fileInput.files.length === 0)) {
                allValid = false;
                const label = wrapper?.querySelector('label')?.textContent || fileInput.name;
                if (!missingFields.includes(label)) missingFields.push(label);
                continue;
            }
            
            if (fileInput.files && fileInput.files.length > 0) {
                const file = fileInput.files[0];
                
                // Check file type
                if (!allowedTypes.includes(file.type)) {
                    allValid = false;
                    const label = wrapper?.querySelector('label')?.textContent || fileInput.name;
                    showError(fileInput, 'نوع الملف غير مسموح. يجب أن يكون PDF, JPG, JPEG, أو PNG');
                    if (!missingFields.includes(label)) missingFields.push(label);
                    continue;
                }
                
                // Check file size
                if (file.size > maxSize) {
                    allValid = false;
                    const label = wrapper?.querySelector('label')?.textContent || fileInput.name;
                    showError(fileInput, 'حجم الملف كبير جداً. الحد الأقصى: 5 ميجابايت');
                    if (!missingFields.includes(label)) missingFields.push(label);
                    continue;
                }
                
                // Clear any previous errors
                removeError(fileInput);
            }
        }

        // ❌ Stop if validation failed
        if (!allValid) {
            Swal.fire({
                icon: "warning",
                title: "يرجى إكمال البيانات",
                html: `الحقول التالية مطلوبة أو تحتوي على أخطاء:<br><b>${missingFields.join("<br>")}</b>`,
                confirmButtonText: "حسنًا",
            });
            return;
        }

        // ✅ Gather raw form data first
        const rawData = Object.fromEntries(new FormData(form).entries());

        // 🧠 Map frontend names → backend expected keys
        const mappedData = {
            nin: rawData.nin,
            email: rawData.email,
            tel: rawData.phone,
            password: rawData.password,
            nom_ar: rawData.nom_ar,
            prenom_ar: rawData.prenom_ar,
            nom_fr: rawData.nom_fr,
            prenom_fr: rawData.prenom_fr,
            sexe: genderChecked.value === "male" ? "ذكر" : "أنثى",
            date_naiss: rawData.date_naissance,
            presume: rawData.presume ? "1" : "0",
            commune_naiss: document.getElementById("communeSelectSignup")?.value || null,
            adresse: rawData.adresse,
            nbr_enfants_scolarise: rawData.nbr_enfants,
            num_cni: rawData.num_carte,
            date_cni: rawData.date_carte,
            lieu_cni: document.getElementById("commune_carte")?.value || null,
            nss: rawData.nss,
            num_cpt: rawData.num_cp,
            cle_cpt: rawData.cle_ccp,
            cats: rawData.categorie_sociale,
            montant_s: (rawData.categorie_sociale === 'الدخل الشهري أقل أو يساوي مبلغ الأجر الوطني الأدنى المضمون') ? (rawData.montant_s || null) : null,
            autr_info: rawData.autre_info || "",
            code_commune: document.getElementById("communeSelectSignup")?.value || null,
        };

        // ✅ Check CCP / Cle before sending
        if (!verifierRIP(mappedData.num_cpt, mappedData.cle_cpt)) {
            Swal.fire({
                icon: "warning",
                title: " CCP خطأ في",
                text: "رقم CCP أو مفتاح CCP غير صحيح. يرجى التحقق.",
                confirmButtonText: "حسنًا"
            });
            return; // stop submission
        }

        // ✅ Convert mappedData into FormData
        const postData = new FormData();
        for (const key in mappedData) {
            if (mappedData[key] !== undefined && mappedData[key] !== null) {
                postData.append(key, mappedData[key]);
            }
        }
        
        // ✅ Append file uploads
        const fileFields = ['biometric_id', 'biometric_id_back', 'Certificate_of_none_income', 'Certificate_of_non_affiliation_to_social_security', 'crossed_ccp', 'salary_certificate'];
        fileFields.forEach(fieldName => {
            const fileInput = document.getElementById(fieldName);
            if (fileInput && fileInput.files && fileInput.files.length > 0) {
                postData.append(fieldName, fileInput.files[0]);
            }
        });

        try {
            // 🕒 Show loading
            Swal.fire({
                title: "جاري الإرسال...",
                text: "الرجاء الانتظار قليلاً",
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading(),
            });

            // ✅ Submit to backend API
            const response = await fetch("/api/tuteurs", {
                method: "POST",
                body: postData,
                headers: {
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
                    "Accept": "application/json",
                },
            });

            const data = await response.json();
            Swal.close(); // remove loading

            // ⚠️ Validation errors
            if (response.status === 422) {
                const errorMessages = Object.values(data.errors || {}).flat().join("<br>");
                Swal.fire({
                    icon: "warning",
                    title: "تحقق من البيانات",
                    html: errorMessages,
                    confirmButtonText: "حسنًا",
                    customClass: { confirmButton: "swal-confirm-btn" },
                    buttonsStyling: false,
                });
                return;
            }

            // ❌ Other server errors
            if (!response.ok) {
                Swal.fire({
                    icon: "error",
                    title: "حدث خطأ أثناء التسجيل",
                    text: data.message || "يرجى المحاولة لاحقًا.",
                    confirmButtonText: "حسنًا",
                    customClass: { confirmButton: "swal-confirm-btn" },
                    buttonsStyling: false,
                });
                return;
            }

            // 🎉 Success
            Swal.fire({
                icon: "success",
                title: "✅ تم التسجيل بنجاح!",
                text: "يمكنك الآن تسجيل الدخول إلى حسابك.",
                confirmButtonText: "حسنًا",
                customClass: { confirmButton: "swal-confirm-btn" },
                buttonsStyling: false,
            }).then(() => {
                window.location.href = "/login";
            });

        } catch (error) {
            console.error("❌ Fetch error:", error);
            Swal.close();
            Swal.fire({
                icon: "error",
                title: "خطأ في الاتصال بالخادم",
                text: "تحقق من الاتصال بالخادم المحلي أو الشبكة.",
            });
        }
    });

}

    enforceAccessDeadline();
    attachValidationListeners();
    updateFormSteps();

});

/* === 👁️ Toggle Password Visibility === */
document.querySelectorAll(".toggle-password").forEach(icon => {
    icon.addEventListener("click", () => {
        const input = icon.closest(".password-wrapper").querySelector("input");
        if (input.type === "password") {
            input.type = "text";
            icon.classList.replace("fa-eye", "fa-eye-slash");
        } else {
            input.type = "password";
            icon.classList.replace("fa-eye-slash", "fa-eye");
        }
    });
});

/* === 📖 Toggle Agreement Text === */
window.toggleAgreementText = function() {
    const shortText = document.querySelector('.agreement-text-short');
    const fullText = document.querySelector('.agreement-text-full');
    const readMoreText = document.querySelector('.read-more-text');
    const readLessText = document.querySelector('.read-less-text');
    const readMoreBtn = document.querySelector('.read-more-btn');
    
    if (shortText && fullText && readMoreText && readLessText && readMoreBtn) {
        const isFullTextVisible = fullText.style.display !== 'none' && fullText.style.display !== '';
        
        if (!isFullTextVisible) {
            // Show full text
            shortText.style.display = 'none';
            fullText.style.display = 'block';
            readMoreText.style.display = 'none';
            readLessText.style.display = 'inline';
            readMoreBtn.classList.add('active');
        } else {
            // Show short text
            shortText.style.display = 'block';
            fullText.style.display = 'none';
            readMoreText.style.display = 'inline';
            readLessText.style.display = 'none';
            readMoreBtn.classList.remove('active');
        }
    }
};
