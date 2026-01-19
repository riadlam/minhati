<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<style>
/* IMPORTANT: mPDF uses the registered font key (cairo). Using 'Cairo' can prevent bold face from applying. */
body { font-family: cairo !important; direction: rtl; text-align: right; line-height: 1.5; font-size: 11px; padding: 20px; }
* { font-family: cairo !important; }
.header { font-weight: 700; font-size: 12px; margin-bottom: 10px; text-align: center; direction: rtl; }
.header-left { text-align: right; margin-top: 5px; direction: rtl; font-size: 11px; }
.title-wrapper { text-align: center; margin: 15px 0; direction: rtl; }
/* mPDF reliably respects padding on table cells (span padding is often ignored) */
.title-table { margin: 0 auto; border-collapse: separate; border-spacing: 0; direction: rtl; }
.title-table td { border: 2px solid #000; border-radius: 4px; padding: 7px 51px; font-weight: 700; font-size: 11px; white-space: nowrap; }
.section { border: 1px solid #000; border-radius: 3px; padding: 12px; margin-top: 15px; direction: rtl; overflow: visible; }
h3 { text-align: center; margin-bottom: 10px; font-weight: bold; font-size: 13.2px; direction: rtl; }
h3.left { text-align: right; direction: rtl; }
h3.right { text-align: right; direction: rtl; }
table { width: 100%; border-collapse: collapse; border-spacing: 0; margin-top: 5px; direction: rtl; overflow: visible; table-layout: fixed; }
col.label-col { width: 30%; }
col.value-col { width: 70%; }
td { padding: 4px 2px; vertical-align: top; font-size: 10px; direction: rtl; text-align: right; }
td.label { font-weight: 700; direction: rtl; text-align: right; white-space: nowrap; word-break: keep-all; overflow: visible; font-size: 12px; color: #000; padding-right: 0px; padding-left: 0px; }
td:not(.label) { padding-left: 3px; padding-right: 0px; }
.signature { text-align: right; margin-top: 15px; font-weight: 600; font-size: 10px; direction: rtl; }
.signature.text-left { text-align: left; }
.footer-date { text-align: center; margin-top: 20px; font-size: 10px; direction: rtl; }
.checkbox-group { margin: 10px 0; text-align: right; direction: rtl; }
.checkbox-group label { display: inline-block; margin-left: 20px; font-size: 10px; direction: rtl; }
.checkbox-item { margin: 5px 0; direction: rtl; text-align: right; font-size: 10px; }
/* mPDF-safe checkbox: use plain text [ ] / [x] */
.chk { display: inline-block; min-width: 18px; text-align: center; direction: ltr; }
.declaration { font-weight: 700; margin: 10px 0; font-size: 10.5px; line-height: 1.4; direction: rtl; text-align: right; }
.text-center { text-align: center; direction: rtl; }
.text-left { text-align: left; direction: rtl; }
.text-right { text-align: right; direction: rtl; }
.mt-2 { margin-top: 10px; }
.mb-0 { margin-bottom: 0; }
.mb-2 { margin-bottom: 10px; }
.guardianship-doc { display: inline-block; margin-right: 10px; font-size: 10px; }
</style>
</head>
<body>
<div class="header" dir="rtl">
<div>الجمهورية الجزائرية الديمقراطية الشعبية</div>
<div class="header-left" dir="rtl">
ولاية:
@php
    $wilayaName = '...';
    if ($eleve->etablissement && isset($eleve->etablissement->commune) && is_object($eleve->etablissement->commune) && isset($eleve->etablissement->commune->wilaya) && is_object($eleve->etablissement->commune->wilaya)) {
        $wilayaName = $eleve->etablissement->commune->wilaya->lib_wil_ar ?? '...';
    } elseif ($eleve->communeResidence && is_object($eleve->communeResidence) && isset($eleve->communeResidence->wilaya) && is_object($eleve->communeResidence->wilaya)) {
        $wilayaName = $eleve->communeResidence->wilaya->lib_wil_ar ?? '...';
    }
@endphp
{{ $wilayaName }}<br>
دائرة:
@php
    $dairaName = '...';
    if ($eleve->etablissement && isset($eleve->etablissement->commune) && is_object($eleve->etablissement->commune) && property_exists($eleve->etablissement->commune, 'lib_daira_ar')) {
        $dairaName = $eleve->etablissement->commune->lib_daira_ar ?? '...';
    } elseif ($eleve->communeResidence && is_object($eleve->communeResidence) && property_exists($eleve->communeResidence, 'lib_daira_ar')) {
        $dairaName = $eleve->communeResidence->lib_daira_ar ?? '...';
    }
@endphp
{{ $dairaName }}<br>
بلدية:
@php
    $communeName = '...';
    if ($eleve->etablissement && isset($eleve->etablissement->commune) && is_object($eleve->etablissement->commune)) {
        $communeName = $eleve->etablissement->commune->lib_comm_ar ?? '...';
    } elseif ($eleve->communeResidence && is_object($eleve->communeResidence)) {
        $communeName = $eleve->communeResidence->lib_comm_ar ?? '...';
    }
@endphp
{{ $communeName }}
</div>
</div>

<div class="title-wrapper">
<table class="title-table"><tr><td>استمارة طلب الاستفادة من المنحة المدرسية الخاصة</td></tr></table>
</div>

<div class="section" dir="rtl">
<h3 class="left" dir="rtl">معلومات خاصة بالتلميذ:</h3>
<table style="width: 100%; border-collapse: collapse; border-spacing: 0;">
<colgroup>
<col style="width: auto;">
<col style="width: *;">
</colgroup>
<tr>
<td class="label" style="font-weight: 700; font-size: 12px; color: #000; padding: 4px 0px 4px 0px; text-align: right; white-space: nowrap; width: 1%;">المؤسسة العمومية للتربية والتعليم / المؤسسة العمومية للتربية والتعليم المتخصصة:</td>
<td style="padding: 4px 0px 4px 5px; font-size: 10px;">{{ ($eleve->etablissement && is_object($eleve->etablissement)) ? ($eleve->etablissement->nom_etabliss ?? '...') : '...' }}</td>
</tr>
<tr>
<td class="label" style="font-weight: 700; font-size: 12px; color: #000; padding: 4px 0px 4px 0px; text-align: right; white-space: nowrap; width: 1%;">السنة الدراسية:</td>
<td style="padding: 4px 0px 4px 5px; font-size: 10px;">{{ \Carbon\Carbon::now()->year }}/{{ \Carbon\Carbon::now()->year + 1 }}</td>
</tr>
<tr>
<td class="label" style="font-weight: 700; font-size: 12px; color: #000; padding: 4px 0px 4px 0px; text-align: right; white-space: nowrap; width: 1%;">المستوى الدراسي:</td>
<td style="padding: 4px 0px 4px 5px; font-size: 10px;">{{ $eleve->classe_scol ?? $eleve->niv_scol ?? '...' }}</td>
</tr>
<tr>
<td class="label" style="font-weight: 700; font-size: 12px; color: #000; padding: 4px 0px 4px 0px; text-align: right; white-space: nowrap; width: 1%;">لقب واسم التلميذ المستفيد:</td>
<td style="padding: 4px 0px 4px 5px; font-size: 10px;">{{ $eleve->nom_ar ?? $eleve->nom }} {{ $eleve->prenom_ar ?? $eleve->prenom }}</td>
</tr>
<tr>
<td class="label" style="font-weight: 700; font-size: 12px; color: #000; padding: 4px 0px 4px 0px; text-align: right; white-space: nowrap; width: 1%;">ابن:</td>
<td>
@php
    $fatherName = '';
    $motherName = '';
    
    // Always get father's Arabic name from father relationship (regardless of relation_tuteur)
    if ($eleve->father && is_object($eleve->father)) {
        $fatherName = trim(($eleve->father->nom_ar ?? $eleve->father->nom_fr ?? '') . ' ' . ($eleve->father->prenom_ar ?? $eleve->father->prenom_fr ?? ''));
    }
    
    // Always get mother's Arabic name from mother relationship (regardless of relation_tuteur)
    if ($eleve->mother && is_object($eleve->mother)) {
        $motherName = trim(($eleve->mother->nom_ar ?? $eleve->mother->nom_fr ?? '') . ' ' . ($eleve->mother->prenom_ar ?? $eleve->mother->prenom_fr ?? ''));
    }
    
    // Build the full name string with "و" separator - always show both if available
    $fullParentName = '';
    if (!empty($fatherName) && !empty($motherName)) {
        // Both exist: show "Father و Mother"
        $fullParentName = $fatherName . ' و ' . $motherName;
    } elseif (!empty($fatherName)) {
        // Only father exists
        $fullParentName = $fatherName;
    } elseif (!empty($motherName)) {
        // Only mother exists
        $fullParentName = $motherName;
    } else {
        // Neither exists
        $fullParentName = '...';
    }
@endphp
{{ $fullParentName }}
</td>
</tr>
<tr>
<td class="label">تاريخ ومكان الازدياد:</td>
<td>
{{ $eleve->date_naiss ? \Carbon\Carbon::parse($eleve->date_naiss)->format('Y-m-d') : '' }}
@if($eleve->communeNaissance && is_object($eleve->communeNaissance))
    {{ $eleve->communeNaissance->lib_comm_ar ?? '' }}
@endif
</td>
</tr>
<tr>
<td class="label">رقم التعريف المدرسي:</td>
<td>{{ $eleve->num_scolaire }}</td>
</tr>
</table>
<div style="text-align: left; margin-top: 10px; direction: rtl; position: relative;">
<div style="margin-bottom: 0; line-height: 2; padding-top: 4px;">
<span>مصادقة مدير المؤسسة العمومية للتربية والتعليم</span>
</div>
<div style="margin-top: -0.7em; margin-bottom: 0; line-height: 1.5; text-align: left; direction: rtl; padding-top: 2px;">
<span style="display: inline-block; padding-right: 0;">المؤسسة العمومية للتربية والتعليم المتخصصة</span>
</div>
</div>
</div>

<div class="section" dir="rtl">
<h3 class="right" dir="rtl">معلومات خاصة بوالدي / وصي التلميذ:</h3>

<div class="checkbox-group">
@php
    $relation = (int)($eleve->relation_tuteur ?? 0);
    $isWali = ($relation === 1 || $relation === 2); // 1 = Father, 2 = Mother
    $isWasi = ($relation === 3); // 3 = Guardian
    $guardianDocValue = ($eleve->guardian_doc && !empty($eleve->guardian_doc)) ? $eleve->guardian_doc : '//';
    $waliBox = $isWali ? '[x]' : '[ ]';
    $wasiBox = $isWasi ? '[x]' : '[ ]';
@endphp
<label style="display: inline-block; margin-left: 20px; direction: rtl; text-align: right;">
ولي التلميذ <span class="chk">{{ $waliBox }}</span>
</label>
<label style="display: inline-block; margin-left: 20px; direction: rtl; text-align: right;">
وصي التلميذ <span class="chk">{{ $wasiBox }}</span>
</label>
<span class="guardianship-doc" style="margin-right: 10px; direction: rtl;">وثيقة إسناد الوصاية</span>
</div>

<table>
<colgroup>
<col class="label-col" style="width: 30%;">
<col class="value-col" style="width: 70%;">
</colgroup>
<tr>
<td class="label">اسم ولقب ولي / وصي التلميذ:</td>
<td>{{ ($eleve->tuteur && is_object($eleve->tuteur)) ? (($eleve->tuteur->nom_ar ?? '') . ' ' . ($eleve->tuteur->prenom_ar ?? '')) : '...' }}</td>
</tr>
<tr>
<td class="label">تاريخ ومكان ميلاد الولي أو وصي التلميذ:</td>
<td>
{{ ($eleve->tuteur && is_object($eleve->tuteur)) ? ($eleve->tuteur->date_naiss ?? '') : '' }}
@if($eleve->tuteur && is_object($eleve->tuteur))
    @if($eleve->tuteur->communeNaissance && is_object($eleve->tuteur->communeNaissance))
        {{ $eleve->tuteur->communeNaissance->lib_comm_ar ?? '' }}
    @else
        {{ $eleve->tuteur->commune_naiss ?? '' }}
    @endif
@endif
</td>
</tr>
<tr>
<td class="label">العنوان:</td>
<td>{{ ($eleve->tuteur && is_object($eleve->tuteur)) ? ($eleve->tuteur->adresse ?? '...') : '...' }}</td>
</tr>
<tr>
<td class="label">رقم التعريف الوطني الوحيد لولي التلميذ:</td>
<td>
@php
    $waliNin = ($eleve->tuteur && is_object($eleve->tuteur) && (in_array((int)($eleve->relation_tuteur ?? 0), [1, 2]))) ? ($eleve->tuteur->nin ?? null) : null;
@endphp
@if($waliNin && $waliNin !== '...')
    {{ $waliNin }}
@else
    //
@endif
</td>
</tr>
<tr>
<td class="label">رقم التعريف الوطني الوحيد لأم التلميذ:</td>
@php
    $roleForMotherNin = (int)($eleve->relation_tuteur ?? 0);
    $motherNin = null;
    if ($roleForMotherNin === 2) {
        $motherNin = ($eleve->tuteur && is_object($eleve->tuteur)) ? ($eleve->tuteur->nin ?? null) : null;
    } else {
        $motherNin = ($eleve->mother && is_object($eleve->mother)) ? ($eleve->mother->nin ?? null) : null;
    }
@endphp
<td>
@if($motherNin && $motherNin !== '...' && trim($motherNin) !== '')
    {{ $motherNin }}
@else
    //
@endif
</td>
</tr>
<tr>
<td class="label">رقم التعريف الوطني الوحيد لوصي التلميذ:</td>
<td>
@php
    $wasiNin = ($eleve->tuteur && is_object($eleve->tuteur) && ((int)($eleve->relation_tuteur ?? 0) === 3)) ? ($eleve->tuteur->nin ?? null) : null;
@endphp
@if($wasiNin && $wasiNin !== '...')
    {{ $wasiNin }}
@else
    //
@endif
</td>
</tr>
<tr>
<td class="label">رقم الحساب البريدي الجاري للولي أو وصي التلميذ:</td>
<td>
@php
    $ccpNumber = ($eleve->tuteur && is_object($eleve->tuteur)) ? ($eleve->tuteur->num_cpt ?? null) : null;
    $ccpKey = ($eleve->tuteur && is_object($eleve->tuteur)) ? ($eleve->tuteur->cle_cpt ?? null) : null;
@endphp
@if($ccpNumber && $ccpKey && trim($ccpNumber) !== '' && trim($ccpKey) !== '')
    {{ $ccpNumber }} المفتاح {{ $ccpKey }}
@else
    //
@endif
</td>
</tr>
<tr>
<td class="label">رقم الضمان الاجتماعي لولي التلميذ:</td>
<td>
@php
    $waliNss = ($eleve->tuteur && is_object($eleve->tuteur) && (in_array((int)($eleve->relation_tuteur ?? 0), [1, 2]))) ? ($eleve->tuteur->nss ?? null) : null;
@endphp
@if($waliNss && $waliNss !== '...' && trim($waliNss) !== '')
    {{ $waliNss }}
@else
    //
@endif
</td>
</tr>
<tr>
<td class="label">رقم الضمان الاجتماعي لأم التلميذ:</td>
@php
    $roleForMotherNss = (int)($eleve->relation_tuteur ?? 0);
    $motherNss = null;
    if ($roleForMotherNss === 2) {
        $motherNss = ($eleve->tuteur && is_object($eleve->tuteur)) ? ($eleve->tuteur->nss ?? null) : null;
    } else {
        $motherNss = ($eleve->mother && is_object($eleve->mother)) ? ($eleve->mother->nss ?? null) : null;
    }
@endphp
<td>
@if($motherNss && $motherNss !== '...' && trim($motherNss) !== '')
    {{ $motherNss }}
@else
    //
@endif
</td>
</tr>
<tr>
<td class="label">رقم الضمان الاجتماعي لوصي التلميذ:</td>
<td>
@php
    $wasiNss = ($eleve->tuteur && is_object($eleve->tuteur) && ((int)($eleve->relation_tuteur ?? 0) === 3)) ? ($eleve->tuteur->nss ?? null) : null;
@endphp
@if($wasiNss && $wasiNss !== '...' && trim($wasiNss) !== '')
    {{ $wasiNss }}
@else
    //
@endif
</td>
</tr>
<tr>
<td class="label">الفئة الاجتماعية: ضع علامة (x) أمام العبارة المناسبة:</td>
<td>
@php
    $tuteurCats = $eleve->tuteur && is_object($eleve->tuteur) ? ($eleve->tuteur->cats ?? '') : '';
    $relation = (int)($eleve->relation_tuteur ?? 0);
    $isWali = (in_array($relation, [1, 2])); // 1 = Father, 2 = Mother
    $isWasi = ($relation === 3); // 3 = Guardian
    
    // Map signup form values to PDF checkbox values
    $isNoIncome = ($tuteurCats === 'عديم الدخل');
    $isLowIncome = ($tuteurCats === 'الدخل الشهري أقل أو يساوي مبلغ الأجر الوطني الأدنى المضمون');
    
    // Determine text based on relationship
    if ($isWali) {
        $noIncomeText = 'منحدر من عائلة معوزة لا يتوفر والداه على أي دخل';
        $lowIncomeText = 'يقل أو يساوي الدخل الشهري لكل من والديه مبلغ الأجر الوطني الأدنى المضمون';
    } elseif ($isWasi) {
        $noIncomeText = 'منحدر من عائلة معوزة لا يتوفر وصيه على أي دخل';
        $lowIncomeText = 'يقل أو يساوي الدخل الشهري لوصيه مبلغ الأجر الوطني الأدنى المضمون';
    } else {
        $noIncomeText = 'منحدر من عائلة معوزة لا يتوفر والداه أو وصيه على أي دخل';
        $lowIncomeText = 'يقل أو يساوي الدخل الشهري لكل من والديه أو وصيه مبلغ الأجر الوطني الأدنى المضمون';
    }
    $noIncomeCheckmark = $isNoIncome ? 'x' : '';
    $lowIncomeCheckmark = $isLowIncome ? 'x' : '';
@endphp
<div class="checkbox-item" style="direction: rtl; text-align: right;">
{{ $noIncomeText }} <span class="chk">{{ $isNoIncome ? '[x]' : '[ ]' }}</span>
</div>
<div class="checkbox-item" style="direction: rtl; text-align: right;">
{{ $lowIncomeText }} <span class="chk">{{ $isLowIncome ? '[x]' : '[ ]' }}</span>
</div>
</td>
</tr>
<tr>
<td class="label">معلومات أخرى متعلقة بالحالة الاجتماعية :</td>
<td>{{ ($eleve->tuteur && is_object($eleve->tuteur)) ? ($eleve->tuteur->autr_info ?? '...') : '...' }}</td>
</tr>
<tr>
<td class="label">عدد أبناء الولي أو الوصي المتمدرسين:</td>
<td>{{ ($eleve->tuteur && is_object($eleve->tuteur)) ? ($eleve->tuteur->nbr_enfants_scolarise ?? 0) : 0 }}</td>
</tr>
</table>

<div class="declaration">
أصرح بشرفي، بصحة المعلومات المذكورة في هذه الاستمارة، وبموافقتي الصريحة على معالجة معطياتي ذات الطابع الشخصي طبقا لأحكام القانون رقم 18-07 المؤرخ في 25 رمضان عام 1439 الموافق 10 يونيو سنة 2018 والمتعلق بحماية الأشخاص الطبيعيين في مجال معالجة المعطيات ذات الطابع الشخصي.
</div>

<table>
<colgroup>
<col class="label-col" style="width: 30%;">
<col class="value-col" style="width: 70%;">
</colgroup>
<tr>
<td class="label">رقم بطاقة الهوية البيومترية للولي / الوصي، طالب المنحة:</td>
<td>
@php
    $cniNumber = ($eleve->tuteur && is_object($eleve->tuteur)) ? ($eleve->tuteur->num_cni ?? null) : null;
@endphp
@if($cniNumber && $cniNumber !== '...' && trim($cniNumber) !== '')
    {{ $cniNumber }}
@else
    //
@endif
</td>
</tr>
<tr>
<td class="label">الصادرة بتاريخ:</td>
<td>
@php
    $cniDate = ($eleve->tuteur && is_object($eleve->tuteur)) ? ($eleve->tuteur->date_cni ?? '//') : '//';
    $cniPlace = '//';
    if ($eleve->tuteur && is_object($eleve->tuteur)) {
        if ($eleve->tuteur->communeCni && is_object($eleve->tuteur->communeCni)) {
            $communeName = $eleve->tuteur->communeCni->lib_comm_ar ?? ($eleve->tuteur->lieu_cni ?? '//');
            $wilayaName = '//';
            if (isset($eleve->tuteur->communeCni->wilaya) && is_object($eleve->tuteur->communeCni->wilaya)) {
                $wilayaName = $eleve->tuteur->communeCni->wilaya->lib_wil_ar ?? '//';
            }
            $cniPlace = $communeName . ' / ' . $wilayaName;
        } else {
            $cniPlace = $eleve->tuteur->lieu_cni ?? '//';
        }
    }
@endphp
{{ $cniDate }} عن: {{ $cniPlace }}
</td>
</tr>
</table>

<div style="text-align: left; margin-top: 15px; font-weight: bold; font-size: 11px; direction: rtl;">
إمضاء ولي / وصي التلميذ
</div>
</div>

</body>
</html>
