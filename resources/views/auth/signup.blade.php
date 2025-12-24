@extends('layouts.main')

@section('title', 'إنشاء حساب جديد')

@vite(['resources/css/signup.css', 'resources/js/signup.js'])

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
        <form id="signupForm" novalidate>
            @csrf

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
                            inputmode="numeric">
                    </div>
                <div class="form-row">
                    

                    <div class="form-group">
                        <label for="email">البريد الإلكتروني</label>
                        <input type="email" id="email" name="email" required
                            title="يرجى إدخال بريد إلكتروني صالح">
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
                            title="يجب أن يحتوي رقم الهاتف على 10 أرقام">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="password">كلمة المرور</label>
                        <div class="password-wrapper">
                            <input type="password" id="password" name="password" required
                                pattern="(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).{8,}"
                                title="يجب أن تحتوي كلمة المرور على حرف كبير، رقم، ورمز خاص، و8 أحرف على الأقل">
                            <i class="toggle-password fa fa-eye"></i>
                        </div>
                        <!-- le message d’erreur sera inséré ici en dehors du wrapper -->
                    </div>

                    <div class="form-group">
                        <label for="confirm_password">تأكيد كلمة المرور</label>
                        <div class="password-wrapper">
                            <input type="password" id="confirm_password" name="confirm_password" required
                                title="يرجى تأكيد كلمة المرور بشكل مطابق">
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
                        <input type="text" id="nom_ar" name="nom_ar" required>
                    </div>
                    <div class="form-group">
                        <label for="prenom_ar">الاسم (بالعربية)</label>
                        <input type="text" id="prenom_ar" name="prenom_ar" required>
                    </div>
                </div>

                {{-- 🔹 اللقب والاسم باللاتينية --}}
                <div class="form-row">
                    <div class="form-group">
                        <label for="nom_fr">اللقب (باللاتينية)</label>
                        <input type="text" id="nom_fr" name="nom_fr" required>
                    </div>
                    <div class="form-group">
                        <label for="prenom_fr">الاسم (باللاتينية)</label>
                        <input type="text" id="prenom_fr" name="prenom_fr" required>
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
                        <input type="date" id="date_naissance" name="date_naissance" required max="{{ date('Y-m-d') }}">
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
                        <select id="wilayaSelectSignup" name="ولاية الميلاد" class="form-select" required>
                            <option value="">اختر...</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label fw-bold">بلدية الميلاد</label>
                        <select id="communeSelectSignup" name="بلدية الميلاد" class="form-select" required disabled>
                            <option value="">اختر الولاية أولا...</option>
                        </select>
                    </div>
                </div>

                {{-- 🔹 العنوان وعدد الأطفال في نفس السطر --}}
                <div class="form-row">
                    <div class="form-group">
                        <label for="adresse">العنوان</label>
                        <input type="text" id="adresse" name="adresse" required>
                    </div>
                    <div class="form-group">
                        <label for="nbr_enfants">عدد الأطفال المتمدرسين</label>
                        <input type="number" id="nbr_enfants" name="nbr_enfants" min="0" required>
                    </div>
                </div>

                {{-- 🔹 رقم البطاقة + تاريخ الإصدار --}}
                <div class="form-row">
                    <div class="form-group">
                        <label for="num_carte">رقم بطاقة التعريف الوطنية</label>
                        <input type="text" id="num_carte" name="num_carte" required>
                    </div>
                    <div class="form-group">
                        <label for="date_carte">تاريخ إصدار البطاقة</label>
                        <input type="date" id="date_carte" name="date_carte" required max="{{ date('Y-m-d') }}">
                    </div>
                </div>

                {{-- 🔹 ولاية وبلدية إصدار البطاقة --}}
                <div class="form-row">
                    <div class="form-group">
                        <label for="wilaya_carte">ولاية إصدار البطاقة </label>
                        <select id="wilaya_carte" name="ولاية إصدار البطاقة " class="form-select" required>
                            <option value="">-- اختر الولاية --</option>
                            {{-- Les options seront chargées dynamiquement via JS --}}
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="commune_carte">بلدية إصدار البطاقة </label>
                        <select id="commune_carte" name="بلدية إصدار البطاقة " class="form-select" required disabled>
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
                    <label for="nss">الرقم الوطني للضمان الاجتماعي (NSS)</label>
                    <input type="text" id="nss" name="nss" required>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="num_cp">رقم الحساب البريدي (CCP)</label>
                        <input type="text" id="num_cp" name="num_cp" required>
                    </div>
                    <div class="form-group small-input">
                        <label for="cle_ccp">الرقم المفتاح (Clé CCP)</label>
                        <input type="text" id="cle_ccp" name="cle_ccp" maxlength="2" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="categorie_sociale">الفئة الاجتماعية</label>
                    <select id="categorie_sociale" name="categorie_sociale" required>
                        <option value="" disabled selected>اختر الفئة الاجتماعية</option>
                        <option value="الدخل الشهري أقل أو يساوي مبلغ الأجر الوطني الأدنى المضمون">الدخل الشهري أقل أو يساوي مبلغ الأجر الوطني الأدنى المضمون</option>
                        <option value="عديم الدخل">عديم الدخل</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="montant_s">المبلغ السنوي</label>
                    <input type="number" id="montant_s" name="montant_s" required>
                </div>
                <div class="form-group">
                    <label for="autre_info">معلومات إضافية</label>
                    <textarea id="autre_info" name="autre_info" rows="3"></textarea>
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
