@extends('layouts.main')

@section('title', 'إنشاء حساب جديد')

@vite(['resources/css/global.css', 'resources/css/signup.css', 'resources/js/signup.js'])

@section('content')
<div class="signup-container">
     {{-- 🔗 Lien vers la connexion --}}
    <div class="top-left-link">
        <span>لديك حساب ؟</span>
        <a href="{{ route('login') }}">تسجيل الدخول</a>
    </div>

    <h2>إنشاء حساب جديد</h2>

    {{-- ✅ Barre de progression --}}
    <div class="progressbar-wrapper">
        <div class="progressbar">
            <div class="progress" id="progress"></div>
            <div class="progress-step active" data-title="معلومات الحساب"></div>
            <div class="progress-step" data-title="معلومات شخصية"></div>
            <div class="progress-step" data-title="معلومات أخرى"></div>
        </div>
    </div>

    <div class="signup-card">
        <form id="signupForm" novalidate enctype="multipart/form-data">
            @csrf

            <div id="deadlineAlert" class="alert alert-warning d-none" role="alert" style="direction: rtl; text-align: right;">
                تم غلق المنصة بعد 1 مارس 2026. يرجى الاتصال بالإدارة لمزيد من المعلومات.
            </div>

            {{-- === الخطوة 1: معلومات الحساب === --}}
            <div class="form-step active">

                 <div class="form-group">
                        <label for="nin">الرقم التعريفي الوطني (NIN)</label>
                        <input 
                            type="number" 
                            id="nin" 
                            name="nin" 
                            required 
                            maxlength="18" 
                            inputmode="numeric"
                            autocomplete="off"
                            onpaste="return false;"
                            ondrop="return false;">
                    </div>
                <div class="form-row">
                    

                    <div class="form-group">
                        <label for="email">البريد الإلكتروني</label>
                        <input type="email" id="email" name="email" required
                            title="يرجى إدخال بريد إلكتروني صالح"
                            autocomplete="off"
                            onpaste="return false;"
                            ondrop="return false;">
                    </div>

                    <div class="form-group">
                        <label for="phone">رقم الهاتف</label>
                        <input 
                            type="tel" 
                            id="phone" 
                            name="phone" 
                            required 
                            inputmode="numeric"
                            pattern="\d{10}"
                            maxlength="10"
                            title="يجب أن يحتوي رقم الهاتف على 10 أرقام"
                            autocomplete="off"
                            onpaste="return false;"
                            ondrop="return false;">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="password">كلمة المرور</label>
                        <div class="password-wrapper">
                            <input type="password" id="password" name="password" required
                                pattern="(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).{8,}"
                                title="يجب أن تحتوي كلمة المرور على حرف كبير، رقم، ورمز خاص، و8 أحرف على الأقل"
                                autocomplete="off"
                                onpaste="return false;"
                                ondrop="return false;">
                            <i class="toggle-password fa fa-eye"></i>
                        </div>
                        <!-- le message d’erreur sera inséré ici en dehors du wrapper -->
                    </div>

                    <div class="form-group">
                        <label for="confirm_password">تأكيد كلمة المرور</label>
                        <div class="password-wrapper">
                            <input type="password" id="confirm_password" name="confirm_password" required
                                title="يرجى تأكيد كلمة المرور بشكل مطابق"
                                autocomplete="off"
                                onpaste="return false;"
                                ondrop="return false;">
                            <i class="toggle-password fa fa-eye"></i>
                        </div>
                    </div>
                </div>


                <div class="btn-group">
                    <button type="button" class="btn next-step">التالي</button>
                </div>
            </div>


            {{-- === الخطوة 2: المعلومات الشخصية === --}} 
            <div class="form-step">

                {{-- 🔹 اللقب والاسم بالعربية --}}
                <div class="form-row">
                    <div class="form-group">
                        <label for="nom_ar">اللقب (بالعربية)</label>
                        <input type="text" id="nom_ar" name="nom_ar" required autocomplete="off" onpaste="return false;" ondrop="return false;">
                    </div>
                    <div class="form-group">
                        <label for="prenom_ar">الاسم (بالعربية)</label>
                        <input type="text" id="prenom_ar" name="prenom_ar" required autocomplete="off" onpaste="return false;" ondrop="return false;">
                    </div>
                </div>

                {{-- 🔹 اللقب والاسم باللاتينية --}}
                <div class="form-row">
                    <div class="form-group">
                        <label for="nom_fr">اللقب (باللاتينية)</label>
                        <input type="text" id="nom_fr" name="nom_fr" required autocomplete="off" onpaste="return false;" ondrop="return false;">
                    </div>
                    <div class="form-group">
                        <label for="prenom_fr">الاسم (باللاتينية)</label>
                        <input type="text" id="prenom_fr" name="prenom_fr" required autocomplete="off" onpaste="return false;" ondrop="return false;">
                    </div>
                </div>

                {{-- 🔹 الجنس + تاريخ الميلاد + تاريخ ميلاد تقريبي --}}
                <div class="form-row">
    
                <!-- ✅ الجنس -->
                <div class="form-group radio-inline">
                    <label>الجنس</label>
                    <div class="radio-group">
                        <label><input type="radio" name="gender" value="male" required> ذكر</label>
                        <label><input type="radio" name="gender" value="female" required> أنثى</label>
                    </div>
                </div>

                <!-- ✅ تاريخ الميلاد (avec wrapper) -->
                <div class="form-group">
                    <div class="date-wrapper"> <!-- ✅ wrapper ajouté -->
                        <label for="date_naiss">تاريخ الميلاد</label>
                        <input type="date" id="date_naissance" name="date_naissance" required max="{{ date('Y-m-d') }}" autocomplete="off" onpaste="return false;" ondrop="return false;">
                    </div>
                </div>

                <!-- ✅ Checkbox reste sur la même ligne -->
                <div class="form-group checkbox-inline">
                    <label for="presume">تاريخ ميلاد مفترض</label>
                    <input type="checkbox" id="presume" name="presume">
                </div>

            </div>

                {{-- 🔹 الولاية والبلدية (مكان الميلاد) --}}
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label fw-bold">ولاية الميلاد</label>
                        <select id="wilayaSelectSignup" name="ولاية الميلاد" class="form-select" required autocomplete="off">
                            <option value="">اختر...</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label fw-bold">بلدية الميلاد</label>
                        <select id="communeSelectSignup" name="بلدية الميلاد" class="form-select" required disabled autocomplete="off">
                            <option value="">اختر الولاية أولا...</option>
                        </select>
                    </div>
                </div>

                {{-- 🔹 العنوان وعدد الأطفال في نفس السطر --}}
                <div class="form-row">
                    <div class="form-group">
                        <label for="adresse">العنوان</label>
                        <input type="text" id="adresse" name="adresse" required autocomplete="off" onpaste="return false;" ondrop="return false;">
                    </div>
                    <div class="form-group">
                        <label for="nbr_enfants">عدد الأطفال المتمدرسين</label>
                        <input type="number" id="nbr_enfants" name="nbr_enfants" min="0" required autocomplete="off" onpaste="return false;" ondrop="return false;">
                    </div>
                </div>

                {{-- 🔹 رقم البطاقة + تاريخ الإصدار --}}
                <div class="form-row">
                    <div class="form-group">
                        <label for="num_carte">رقم بطاقة التعريف الوطنية</label>
                        <input type="text" id="num_carte" name="num_carte" required autocomplete="off" onpaste="return false;" ondrop="return false;">
                    </div>
                    <div class="form-group">
                        <label for="date_carte">تاريخ إصدار البطاقة</label>
                        <input type="date" id="date_carte" name="date_carte" required 
                               max="{{ date('Y-m-d') }}" 
                               min="{{ date('Y-m-d', strtotime('-10 years')) }}"
                               autocomplete="off"
                               onpaste="return false;"
                               ondrop="return false;">
                    </div>
                </div>

                {{-- 🔹 ولاية وبلدية إصدار البطاقة --}}
                <div class="form-row">
                    <div class="form-group">
                        <label for="wilaya_carte">ولاية إصدار البطاقة </label>
                        <select id="wilaya_carte" name="ولاية إصدار البطاقة " class="form-select" required autocomplete="off">
                            <option value="">-- اختر الولاية --</option>
                            {{-- Les options seront chargées dynamiquement via JS --}}
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="commune_carte">بلدية إصدار البطاقة </label>
                        <select id="commune_carte" name="بلدية إصدار البطاقة " class="form-select" required disabled autocomplete="off">
                            <option value="">-- اختر الولاية أولا --</option>
                            {{-- Les options seront chargées dynamiquement selon الولاية --}}
                        </select>
                    </div>
                </div>

                <div class="btn-group">
                    <button type="button" class="btn prev-step">السابق</button>
                    <button type="button" class="btn next-step">التالي</button>
                </div>
            </div>


            {{-- === الخطوة 3: المعلومات المالية === --}}
            <div class="form-step">
                <div class="form-group">
                    <label for="nss">رقم الضمان الاجتماعي</label>
                    <input type="text" id="nss" name="nss" autocomplete="off" onpaste="return false;" ondrop="return false;">
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="num_cp">رقم الحساب البريدي (CCP)</label>
                        <input type="text" id="num_cp" name="num_cp" required autocomplete="off" onpaste="return false;" ondrop="return false;">
                    </div>
                    <div class="form-group small-input">
                        <label for="cle_ccp">الرقم المفتاح (Clé CCP)</label>
                        <input type="text" id="cle_ccp" name="cle_ccp" maxlength="2" required autocomplete="off" onpaste="return false;" ondrop="return false;">
                    </div>
                </div>
                <div class="form-group">
                    <label for="categorie_sociale">الفئة الاجتماعية <span class="text-danger">*</span></label>
                    <select id="categorie_sociale" name="categorie_sociale" required autocomplete="off">
                        <option value="" disabled selected>اختر الفئة الاجتماعية</option>
                        <option value="عديم الدخل">عديم الدخل</option>
                        <option value="الدخل الشهري أقل أو يساوي مبلغ الأجر الوطني الأدنى المضمون">الدخل الشهري أقل أو يساوي مبلغ الأجر الوطني الأدنى المضمون</option>
                    </select>
                </div>
                <div class="form-group" id="montant_s_wrapper" style="display: none;">
                    <label for="montant_s">مبلغ الدخل الشهري <span class="text-danger">*</span></label>
                    <input type="number" id="montant_s" name="montant_s" min="0" max="24000" step="0.01" autocomplete="off" onpaste="return false;" ondrop="return false;">
                </div>
                
                {{-- File Upload Fields --}}
                <div class="form-group">
                    <label for="biometric_id">بطاقة الهوية البيومترية (الوجه الأمامي) <span class="text-danger">*</span></label>
                    <input type="file" id="biometric_id" name="biometric_id" accept=".pdf,.jpg,.jpeg,.png" required>
                    <small class="form-text text-muted">الحد الأقصى: 5 ميجابايت | الصيغ المسموحة: PDF, JPG, JPEG, PNG</small>
                </div>
                
                <div class="form-group">
                    <label for="biometric_id_back">بطاقة الهوية البيومترية (الوجه الخلفي) <span class="text-danger">*</span></label>
                    <input type="file" id="biometric_id_back" name="biometric_id_back" accept=".pdf,.jpg,.jpeg,.png" required>
                    <small class="form-text text-muted">الحد الأقصى: 5 ميجابايت | الصيغ المسموحة: PDF, JPG, JPEG, PNG</small>
                </div>
                
                <div class="form-group" id="certificate_of_none_income_wrapper" style="display: none;">
                    <label for="Certificate_of_none_income">شهادة عدم الدخل <span class="text-danger">*</span></label>
                    <input type="file" id="Certificate_of_none_income" name="Certificate_of_none_income" accept=".pdf,.jpg,.jpeg,.png">
                    <small class="form-text text-muted">الحد الأقصى: 5 ميجابايت | الصيغ المسموحة: PDF, JPG, JPEG, PNG</small>
                </div>
                
                <div class="form-group" id="certificate_of_non_affiliation_wrapper" style="display: none;">
                    <label for="Certificate_of_non_affiliation_to_social_security">شهادة عدم الانتساب للضمان الاجتماعي <span class="text-danger">*</span></label>
                    <input type="file" id="Certificate_of_non_affiliation_to_social_security" name="Certificate_of_non_affiliation_to_social_security" accept=".pdf,.jpg,.jpeg,.png">
                    <small class="form-text text-muted">الحد الأقصى: 5 ميجابايت | الصيغ المسموحة: PDF, JPG, JPEG, PNG</small>
                </div>
                
                <div class="form-group" id="crossed_ccp_wrapper" style="display: none;">
                    <label for="crossed_ccp">صك بريدي مشطوب <span class="text-danger">*</span></label>
                    <input type="file" id="crossed_ccp" name="crossed_ccp" accept=".pdf,.jpg,.jpeg,.png">
                    <small class="form-text text-muted">الحد الأقصى: 5 ميجابايت | الصيغ المسموحة: PDF, JPG, JPEG, PNG</small>
                </div>
                
                <div class="form-group" id="salary_certificate_wrapper" style="display: none;">
                    <label for="salary_certificate">شهادة الراتب <span class="text-danger">*</span></label>
                    <input type="file" id="salary_certificate" name="salary_certificate" accept=".pdf,.jpg,.jpeg,.png">
                    <small class="form-text text-muted">الحد الأقصى: 5 ميجابايت | الصيغ المسموحة: PDF, JPG, JPEG, PNG</small>
                </div>
                
                <div class="form-group">
                    <label for="autre_info">ﻣﻌﻠﻮﻣﺎﺕ ﺃﺧﺮﻯ ﺣﻮﻝ ﺍﳊﺎﻟﺔ ﺍﻻﺟﺘﻤﺎﻋﻴﺔ ﻟﻮﺍﻟﺪﻱ / ﻭﺻﻲ ﺍﻟﺘﻠﻤﻴﺬ</label>
                    <textarea id="autre_info" name="autre_info" rows="3"></textarea>
                </div>

                <div class="form-group agreement-group" style="margin-top: 1.5rem;">
                    <div class="agreement-wrapper">
                        <input type="checkbox" id="agreement_checkbox" name="agreement" required class="agreement-checkbox">
                        <div class="agreement-content">
                            <label for="agreement_checkbox" class="agreement-label">
                                <span class="agreement-text-short">
                                    أقر بأنّني قد اطّلعت على شروط وكيفيات الاستفادة من المنحة المدرسية الخاصّة المنصوص عليها في المرسوم التنفيذي رقم <strong>25-168</strong> المؤرخ في 22 جوان 2025، وأصرّح بموافقتي على معالجة معطياتي ذات الطابع الشخصي...
                                </span>
                                <span class="agreement-text-full" style="display: none;">
                                    أقر بأنّني قد اطّلعت على شروط وكيفيات الاستفادة من المنحة المدرسية الخاصّة المنصوص عليها في المرسوم التنفيذي رقم <strong>25-168</strong> المؤرخ في 22 جوان 2025، وأصرّح بموافقتي على معالجة معطياتي ذات الطابع الشخصي في هذا الشأن، طبقاً لأحكام القانون رقم <strong>18-07</strong> المؤرخ في 25 رمضان عام 1439 الموافق 10 يونيو سنة 2018، المتعلق بحماية الأشخاص الطبيعيين في مجال معالجة المعطيات ذات الطابع الشخصي، المعدّل والمتمّم.
                                </span>
                                <span class="text-danger"> *</span>
                            </label>
                            <button type="button" class="read-more-btn" onclick="toggleAgreementText()">
                                <span class="read-more-text">قراءة المزيد</span>
                                <span class="read-less-text" style="display: none;">قراءة أقل</span>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="btn-group">
                    <button type="button" class="btn prev-step">السابق</button>
                    <button type="submit" class="btn">تسجيل</button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection
