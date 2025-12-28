<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<style>
body { font-family: DejaVu Sans; direction: rtl; text-align: right; line-height: 1.6; font-size: 12px; padding: 20px; }
.header { font-weight: bold; font-size: 13px; margin-bottom: 10px; text-align: center; }
.header-left { text-align: left; margin-top: 5px; }
.title-wrapper { text-align: center; margin: 15px 0; }
.title-box { display: inline-block; border: 2px solid #000; border-radius: 4px; padding: 8px 20px; font-weight: bold; font-size: 14px; }
.section { border: 1px solid #000; border-radius: 3px; padding: 12px; margin-top: 15px; }
h3 { text-align: center; margin-bottom: 10px; font-weight: bold; font-size: 13px; }
h3.left { text-align: left; }
h3.right { text-align: right; }
table { width: 100%; border-collapse: collapse; margin-top: 5px; }
td { padding: 4px 6px; vertical-align: top; font-size: 11px; }
.label { font-weight: bold; width: 35%; }
.signature { text-align: left; margin-top: 15px; font-weight: bold; font-size: 11px; }
.footer-date { text-align: center; margin-top: 20px; font-size: 11px; }
.checkbox-group { margin: 10px 0; text-align: right; }
.checkbox-group label { display: inline-block; margin-left: 20px; font-size: 11px; }
.declaration { font-weight: bold; margin: 10px 0; font-size: 10px; line-height: 1.5; }
.text-center { text-align: center; }
.text-left { text-align: left; }
.text-right { text-align: right; }
.mt-2 { margin-top: 10px; }
.mb-0 { margin-bottom: 0; }
.mb-2 { margin-bottom: 10px; }
</style>
</head>
<body>
<div class="header">
<div>الجمهورية الجزائرية الديمقراطية الشعبية</div>
<div class="header-left">
الولاية :
@php
    $wilayaName = '____________________';
    if ($eleve->etablissement && isset($eleve->etablissement->commune) && is_object($eleve->etablissement->commune) && isset($eleve->etablissement->commune->wilaya) && is_object($eleve->etablissement->commune->wilaya)) {
        $wilayaName = $eleve->etablissement->commune->wilaya->lib_wil_ar ?? '____________________';
    } elseif ($eleve->communeResidence && is_object($eleve->communeResidence) && isset($eleve->communeResidence->wilaya) && is_object($eleve->communeResidence->wilaya)) {
        $wilayaName = $eleve->communeResidence->wilaya->lib_wil_ar ?? '____________________';
    }
@endphp
{{ $wilayaName }}<br>
البلدية :
@php
    $communeName = '____________________';
    if ($eleve->etablissement && isset($eleve->etablissement->commune) && is_object($eleve->etablissement->commune)) {
        $communeName = $eleve->etablissement->commune->lib_comm_ar ?? '____________________';
    } elseif ($eleve->communeResidence && is_object($eleve->communeResidence)) {
        $communeName = $eleve->communeResidence->lib_comm_ar ?? '____________________';
    }
@endphp
{{ $communeName }}
</div>
</div>

<div class="title-wrapper">
<span class="title-box">استمارة طلب الاستفادة من المنحة المدرسية الخاصة</span>
</div>

<div class="section">
<h3 class="left">معلومات خاصة بالتلميذ</h3>
<table>
<tr>
<td class="label">المؤسسة العمومية للتربية و التعليم/المؤسسة العمومية للتربية و التعليم المتخصصة :</td>
<td>{{ ($eleve->etablissement && is_object($eleve->etablissement)) ? ($eleve->etablissement->nom_etabliss ?? 'غير محددة') : 'غير محددة' }}</td>
</tr>
<tr>
<td class="label">السنة الدراسية :</td>
<td>
{{ now()->year }}/{{ now()->year + 1 }}
{{ $eleve->classe_scol ?? '' }} : المستوى /
</td>
</tr>
<tr>
<td class="label">اسم و لقب التلميذ المستفيد :</td>
<td>{{ $eleve->prenom_ar ?? $eleve->prenom }} {{ $eleve->nom_ar ?? $eleve->nom }}</td>
</tr>
<tr>
<td class="label">ابن:</td>
<td>{{ $eleve->prenom_pere ?? '' }} و {{ $eleve->nom_mere ?? '' }} {{ $eleve->prenom_mere ?? '' }}</td>
</tr>
<tr>
<td class="label">تاريخ و مكان الميلاد :</td>
<td>
{{ $eleve->date_naiss ? \Carbon\Carbon::parse($eleve->date_naiss)->format('Y-m-d') : '' }}
@if($eleve->communeNaissance && is_object($eleve->communeNaissance))
    {{ $eleve->communeNaissance->lib_comm_ar ?? '' }}
@endif
</td>
</tr>
<tr>
<td class="label">رقم التعريف المدرسي :</td>
<td>{{ $eleve->num_scolaire }}</td>
</tr>
</table>
<div class="text-right mt-2">
<p class="mb-0">مصادقة مدير مؤسسة التربية و التعليم العمومية</p>
<p class="mb-0">مؤسسة التربية والتعليم المتخصصة</p>
</div>
</div>

<div class="section">
<h3 class="right">معلومات خاصة بولي / وصي التلميذ</h3>

<div class="checkbox-group">
<label>
<input type="checkbox" {{ ($eleve->relation_tuteur ?? '') == 'ولي' ? 'checked' : '' }}> ولي التلميذ
</label>
<label>
<input type="checkbox" {{ ($eleve->relation_tuteur ?? '') == 'وصي' ? 'checked' : '' }}> وصي التلميذ
</label>
</div>

<table>
<tr>
<td class="label">اسم و لقب الولي / الوصي :</td>
<td>{{ ($eleve->tuteur && is_object($eleve->tuteur)) ? (($eleve->tuteur->prenom_ar ?? '') . ' ' . ($eleve->tuteur->nom_ar ?? '')) : '' }}</td>
</tr>
<tr>
<td class="label">تاريخ و مكان الميلاد :</td>
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
<td class="label">العنوان :</td>
<td>{{ ($eleve->tuteur && is_object($eleve->tuteur)) ? ($eleve->tuteur->adresse ?? '') : '' }}</td>
</tr>
<tr>
<td class="label">رقم التعريف الوطني :</td>
<td>{{ ($eleve->tuteur && is_object($eleve->tuteur)) ? ($eleve->tuteur->nin ?? '') : '' }}</td>
</tr>
<tr>
<td class="label">رقم الحساب البريدي الجاري :</td>
<td>
@if($eleve->tuteur && is_object($eleve->tuteur))
    {{ ($eleve->tuteur->num_cpt ?? '') . ' - ' . ($eleve->tuteur->cle_cpt ?? '') }}
@endif
</td>
</tr>
<tr>
<td class="label">رقم الضمان الاجتماعي :</td>
<td>{{ ($eleve->tuteur && is_object($eleve->tuteur)) ? ($eleve->tuteur->nss ?? '') : '' }}</td>
</tr>
<tr>
<td class="label">الفئة الاجتماعية :</td>
<td>{{ ($eleve->tuteur && is_object($eleve->tuteur)) ? ($eleve->tuteur->cats ?? '') : '' }}</td>
</tr>
<tr>
<td class="label">معلومات أخرى :</td>
<td>{{ ($eleve->tuteur && is_object($eleve->tuteur)) ? ($eleve->tuteur->autr_info ?? '/') : '/' }}</td>
</tr>
<tr>
<td class="label">عدد الأبناء المتمدرسين :</td>
<td>{{ ($eleve->tuteur && is_object($eleve->tuteur)) ? ($eleve->tuteur->nbr_enfants_scolarise ?? 0) : 0 }}</td>
</tr>
</table>

<div class="declaration">
أصرح بشرفي بصحة المعلومات المذكورة في هذه الاستمارة، وبموافقتي الصريحة على معالجة معطياتي ذات الطابع الشخصي
وفقا لأحكام القانون رقم 18-07 المؤرخ في 10 يونيو سنة 2018 والمتعلق بحماية الأشخاص الطبيعيين في مجال معالجة
المعطيات ذات الطابع الشخصي.
</div>

<table>
<tr>
<td class="label">رقم بطاقة التعريف البيومترية:</td>
<td>{{ ($eleve->tuteur && is_object($eleve->tuteur)) ? ($eleve->tuteur->num_cni ?? '') : '' }}</td>
</tr>
<tr>
<td class="label">الصادرة بتاريخ:</td>
<td>{{ ($eleve->tuteur && is_object($eleve->tuteur)) ? ($eleve->tuteur->date_cni ?? '') : '' }}</td>
</tr>
<tr>
<td class="label">عن:</td>
<td>{{ ($eleve->tuteur && is_object($eleve->tuteur)) ? ($eleve->tuteur->lieu_cni ?? '') : '' }}</td>
</tr>
</table>

<div class="signature">
إمضاء ولي أو وصي التلميذ
</div>
</div>

<div class="footer-date">التاريخ: {{ now()->format('Y-m-d') }}</div>
</body>
</html>
