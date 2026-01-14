<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<style>
body { font-family: DejaVu Sans; direction: rtl; text-align: right; line-height: 1.6; font-size: 12px; padding: 20px; }
.header { font-weight: bold; font-size: 13px; margin-bottom: 10px; text-align: center; direction: rtl; }
.header-left { text-align: right; margin-top: 5px; direction: rtl; }
.title-wrapper { text-align: center; margin: 15px 0; direction: rtl; }
.title-box { display: inline-block; border: 2px solid #000; border-radius: 4px; padding: 8px 20px; font-weight: bold; font-size: 14px; direction: rtl; }
.section { border: 1px solid #000; border-radius: 3px; padding: 12px; margin-top: 15px; direction: rtl; }
h3 { text-align: center; margin-bottom: 10px; font-weight: bold; font-size: 13px; direction: rtl; }
h3.left { text-align: right; direction: rtl; }
h3.right { text-align: right; direction: rtl; }
table { width: 100%; border-collapse: collapse; margin-top: 5px; direction: rtl; }
td { padding: 4px 6px; vertical-align: top; font-size: 11px; direction: rtl; text-align: right; }
.label { font-weight: bold; width: 35%; direction: rtl; text-align: right; }
.signature { text-align: right; margin-top: 15px; font-weight: bold; font-size: 11px; direction: rtl; }
.signature.text-left { text-align: left; }
.footer-date { text-align: center; margin-top: 20px; font-size: 11px; direction: rtl; }
.checkbox-group { margin: 10px 0; text-align: right; direction: rtl; }
.checkbox-group label { display: inline-block; margin-left: 20px; font-size: 11px; direction: rtl; }
.checkbox-item { margin: 5px 0; direction: rtl; text-align: right; }
.declaration { font-weight: bold; margin: 10px 0; font-size: 10px; line-height: 1.5; direction: rtl; text-align: right; }
.text-center { text-align: center; direction: rtl; }
.text-left { text-align: left; direction: rtl; }
.text-right { text-align: right; direction: rtl; }
.mt-2 { margin-top: 10px; }
.mb-0 { margin-bottom: 0; }
.mb-2 { margin-bottom: 10px; }
.guardianship-doc { display: inline-block; margin-right: 10px; font-size: 11px; }
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
<span class="title-box">استمارة طلب الاستفادة من المنحة المدرسية الخاصة</span>
</div>

<div class="section" dir="rtl">
<h3 class="left" dir="rtl">معلومات خاصة بالتلميذ:</h3>
<table>
<tr>
<td class="label">المؤسسة العمومية للتربية والتعليم / المؤسسة العمومية للتربية والتعليم المتخصصة:</td>
<td>{{ ($eleve->etablissement && is_object($eleve->etablissement)) ? ($eleve->etablissement->nom_etabliss ?? '...') : '...' }}</td>
</tr>
<tr>
<td class="label">السنة الدراسية:</td>
<td>{{ \Carbon\Carbon::now()->year }}/{{ \Carbon\Carbon::now()->year + 1 }}</td>
</tr>
<tr>
<td class="label">المستوى الدراسي:</td>
<td>{{ $eleve->classe_scol ?? $eleve->niv_scol ?? '...' }}</td>
</tr>
<tr>
<td class="label">لقب واسم التلميذ المستفيد:</td>
<td>{{ $eleve->nom_ar ?? $eleve->nom }} {{ $eleve->prenom_ar ?? $eleve->prenom }}</td>
</tr>
<tr>
<td class="label">ابن:</td>
<td>
@php
    $roleForParentsLine = (int)($eleve->relation_tuteur ?? 0);
@endphp
@if($eleve->father && is_object($eleve->father))
    {{ $eleve->father->prenom_ar ?? '' }}
@endif

@if($roleForParentsLine === 2)
    {{-- Role 2 = Mother is the tuteur --}}
    و {{ ($eleve->tuteur && is_object($eleve->tuteur)) ? (($eleve->tuteur->nom_ar ?? '') . ' ' . ($eleve->tuteur->prenom_ar ?? '')) : '' }}
    @if($eleve->tuteur && is_object($eleve->tuteur) && (($eleve->tuteur->nom_fr ?? null) || ($eleve->tuteur->prenom_fr ?? null)))
        ({{ $eleve->tuteur->prenom_fr ?? '' }} {{ $eleve->tuteur->nom_fr ?? '' }})
    @endif
@else
    @if($eleve->mother && is_object($eleve->mother))
        و {{ $eleve->mother->nom_ar ?? '' }} {{ $eleve->mother->prenom_ar ?? '' }}
        @if($eleve->mother->nom_fr || $eleve->mother->prenom_fr)
            ({{ $eleve->mother->prenom_fr ?? '' }} {{ $eleve->mother->nom_fr ?? '' }})
        @endif
    @endif
@endif
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
<div style="text-align: left; margin-top: 10px; direction: rtl;">
<p style="margin-bottom: 0;">مصادقة مدير المؤسسة العمومية للتربية والتعليم / المؤسسة العمومية للتربية والتعليم المتخصصة</p>
</div>
</div>

<div class="section" dir="rtl">
<h3 class="right" dir="rtl">معلومات خاصة بوالدي / وصي التلميذ:</h3>

<div class="checkbox-group">
@php
    $relation = (int)($eleve->relation_tuteur ?? 0);
    $isWali = ($relation === 1 || $relation === 2); // 1 = Father, 2 = Mother
    $isWasi = ($relation === 3); // 3 = Guardian
    $guardianDocValue = ($eleve->guardian_doc && !empty($eleve->guardian_doc)) ? $eleve->guardian_doc : '/';
@endphp
<label style="display: inline-block; margin-left: 20px; direction: rtl; text-align: right;">
ولي التلميذ @if($isWali)☑@else☐@endif
</label>
<label style="display: inline-block; margin-left: 20px; direction: rtl; text-align: right;">
وصي التلميذ @if($isWasi)☑@else☐@endif
</label>
<span class="guardianship-doc" style="margin-right: 10px; direction: rtl;">وثيقة إسناد الوصاية {{ $guardianDocValue }}</span>
</div>

<table>
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
    /
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
    /
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
    /
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
    /
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
    /
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
    /
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
    /
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
@endphp
<div class="checkbox-item" style="direction: rtl; text-align: right;">
{{ $noIncomeText }} @if($isNoIncome)☑@else☐@endif
</div>
<div class="checkbox-item" style="direction: rtl; text-align: right;">
{{ $lowIncomeText }} @if($isLowIncome)☑@else☐@endif
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
<tr>
<td class="label">رقم بطاقة الهوية البيومترية للولي / الوصي، طالب المنحة:</td>
<td>
@php
    $cniNumber = ($eleve->tuteur && is_object($eleve->tuteur)) ? ($eleve->tuteur->num_cni ?? null) : null;
@endphp
@if($cniNumber && $cniNumber !== '...' && trim($cniNumber) !== '')
    {{ $cniNumber }}
@else
    /
@endif
</td>
</tr>
<tr>
<td class="label">الصادرة بتاريخ:</td>
<td>
@php
    $cniDate = ($eleve->tuteur && is_object($eleve->tuteur)) ? ($eleve->tuteur->date_cni ?? '/') : '/';
    $cniPlace = '/';
    if ($eleve->tuteur && is_object($eleve->tuteur)) {
        if ($eleve->tuteur->communeCni && is_object($eleve->tuteur->communeCni)) {
            $communeName = $eleve->tuteur->communeCni->lib_comm_ar ?? ($eleve->tuteur->lieu_cni ?? '/');
            $wilayaName = '/';
            if (isset($eleve->tuteur->communeCni->wilaya) && is_object($eleve->tuteur->communeCni->wilaya)) {
                $wilayaName = $eleve->tuteur->communeCni->wilaya->lib_wil_ar ?? '/';
            }
            $cniPlace = $communeName . ' / ' . $wilayaName;
        } else {
            $cniPlace = $eleve->tuteur->lieu_cni ?? '/';
        }
    }
@endphp
{{ $cniDate }} عن: {{ $cniPlace }}
</td>
</tr>
</table>

<div style="text-align: left; margin-top: 15px; font-weight: bold; font-size: 11px; direction: rtl;">
إمضاء ولي / وصي التلميذ
____
</div>
</div>

</body>
</html>
