<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title>استمارة طلب الاستفادة من المنحة المدرسية الخاصة</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css" rel="stylesheet">

  <style>
    body {
      font-family: 'Amiri', 'DejaVu Sans', sans-serif;
      direction: rtl;
      text-align: right;
      line-height: 1.8;
      font-size: 15px;
      margin: 25px;
    }
    h2, h3 {
      text-align: center;
      margin-bottom: 5px;
      font-weight: bold;
    }
    .header {
      font-weight: bold;
      font-size: 14px;
      margin-bottom: 8px;
    }
    .section {
      border: 1px solid #000;
      border-radius: 4px;
      padding: 10px 12px;
      margin-top: 10px;
    }
    table {
      width: 100%;
    }
    td {
      padding: 3px 6px;
      vertical-align: top;
    }
    .label {
      font-weight: bold;
      width: 32%;
    }
    .signature {
      text-align: left;
      margin-top: 15px;
      font-weight: bold;
    }
    .footer-date {
      text-align: center;
      margin-top: 20px;
    }
    .checkbox-group {
      display: flex;
      gap: 20px;
      align-items: center;
    }
    .checkbox-group label {
      display: flex;
      align-items: center;
      gap: 5px;
    }
    

  .title-wrapper {
    text-align: center;
    margin: 15px 0;
  }

  .title-box {
    display: inline-block;
    border: 2px solid #000;
    border-radius: 6px;
    padding: 6px 25px;
    font-weight: bold;
    width: auto;
    white-space: nowrap; /* keeps it tight to text */
  }


  
  </style>
</head>

<body>

  <!-- Header -->
  <div class="header text-center">
    <div>الجمهورية الجزائرية الديمقراطية الشعبية</div>
    <div class="mt-1 text-start">
        الولاية :
        {{ $eleve->etablissement->commune->wilaya->lib_wil_ar
            ?? $eleve->communeResidence->wilaya->lib_wil_ar
            ?? '____________________' }}<br>
        البلدية :
        {{ $eleve->etablissement->commune->lib_comm_ar
            ?? $eleve->communeResidence->lib_comm_ar
            ?? '____________________' }}
    </div>

  </div>

  <div class="title-wrapper text-center">
  <span class="title-box">استمارة طلب الاستفادة من المنحة المدرسية الخاصة</span>
</div>


  <!-- معلومات التلميذ -->
  <div class="section">
    <h3 class="text-start">معلومات خاصة بالتلميذ</h3>
    <table class="table table-borderless mb-0">
      <tbody>
        <tr>
          <td class="label">المؤسسة العمومية للتربية و التعليم/المؤسسة العمومية للتربية و التعليم المتخصصة :</td>
          <td>{{ $eleve->etablissement->nom_etabliss ?? 'غير محددة' }}</td>
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
            {{ $eleve->communeNaissance->lib_comm_ar ?? '' }}
          </td>
        </tr>
        <tr>
          <td class="label">رقم التعريف المدرسي :</td>
          <td>{{ $eleve->num_scolaire }}</td>
        </tr>
      </tbody>
    </table>
    <div class="text-end mt-2">
      <p class="mb-0 fw-bold">مصادقة مدير مؤسسة التربية و التعليم العمومية</p>
      <p class="mb-0 fw-bold">مؤسسة التربية والتعليم المتخصصة</p>
    </div>
  </div>

  <!-- معلومات الولي -->
  <div class="section">
    <h3 class="text-end">معلومات خاصة بولي / وصي التلميذ</h3>

    <div class="checkbox-group mb-2">
      <label>
        <input type="checkbox" {{ $eleve->relation_tuteur == 'ولي' ? 'checked' : '' }}> ولي التلميذ
      </label>
      <label>
        <input type="checkbox" {{ $eleve->relation_tuteur == 'وصي' ? 'checked' : '' }}> وصي التلميذ
      </label>
    </div>

    <table class="table table-borderless mb-0">
      <tbody>
        <tr>
          <td class="label">اسم و لقب الولي / الوصي :</td>
          <td>{{ $eleve->tuteur->prenom_ar ?? '' }} {{ $eleve->tuteur->nom_ar ?? '' }}</td>
        </tr>
        <tr>
          <td class="label">تاريخ و مكان الميلاد :</td>
          <td>
            {{ $eleve->tuteur->date_naiss ?? '' }}
            {{ $eleve->tuteur->communeNaissance->lib_comm_ar ?? $eleve->tuteur->commune_naiss ?? '' }}
          </td>
        </tr>
        <tr>
          <td class="label">العنوان :</td>
          <td>{{ $eleve->tuteur->adresse ?? '' }}</td>
        </tr>
        <tr>
          <td class="label">رقم التعريف الوطني :</td>
          <td>{{ $eleve->tuteur->nin ?? '' }}</td>
        </tr>
        <tr>
          <td class="label">رقم الحساب البريدي الجاري :</td>
          <td>{{ $eleve->tuteur->num_cpt ?? '' }} - {{ $eleve->tuteur->cle_cpt ?? '' }}</td>
        </tr>
        <tr>
          <td class="label">رقم الضمان الاجتماعي :</td>
          <td>{{ $eleve->tuteur->nss ?? '' }}</td>
        </tr>
        <tr>
          <td class="label">الفئة الاجتماعية :</td>
          <td>{{ $eleve->tuteur->cats ?? '' }}</td>
        </tr>
        <tr>
          <td class="label">معلومات أخرى :</td>
          <td>{{ $eleve->tuteur->autr_info ?? '/' }}</td>
        </tr>
        <tr>
          <td class="label">عدد الأبناء المتمدرسين :</td>
          <td>{{ $eleve->tuteur->nbr_enfants_scolarise ?? 0 }}</td>
        </tr>
      </tbody>
    </table>

    <p class="fw-bold mt-2 mb-2">
      أصرح بشرفي بصحة المعلومات المذكورة في هذه الاستمارة، وبموافقتي الصريحة على معالجة معطياتي ذات الطابع الشخصي
      وفقا لأحكام القانون رقم 18-07 المؤرخ في 10 يونيو سنة 2018 والمتعلق بحماية الأشخاص الطبيعيين في مجال معالجة
      المعطيات ذات الطابع الشخصي.
    </p>

    <table class="table table-borderless mb-0">
      <tbody>
        <tr>
          <td class="label">رقم بطاقة التعريف البيومترية:</td>
          <td>{{ $eleve->tuteur->num_cni ?? '' }}</td>
        </tr>
        <tr>
          <td class="label">الصادرة بتاريخ:</td>
          <td>{{ $eleve->tuteur->date_cni ?? '' }}</td>
        </tr>
        <tr>
          <td class="label">عن:</td>
          <td>{{ $eleve->tuteur->lieu_cni ?? '' }}</td>
        </tr>
      </tbody>
    </table>

    <div class="signature">
      إمضاء ولي أو وصي التلميذ
    </div>
  </div>

  <p class="footer-date">التاريخ: {{ now()->format('Y-m-d') }}</p>
</body>
</html>
