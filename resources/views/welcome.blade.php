<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>المنصة الرقمية لطلب المنحة المدرسية الخاصة</title>

  <!-- Bootstrap & Font Awesome -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;900&display=swap" rel="stylesheet">

@vite(['resources/css/welcome.css', 'resources/js/welcome.js'])
</head>

<body>
  <!-- Navbar -->
  <nav class="main-navbar">
    <div class="navbar-left">
      <img src="{{ asset('images/ministere1.png') }}" class="nav-logo" alt="Logo gauche">
      <div class="navbar-links">
        <a href="#hero" class="active">الرئيسية</a>
        <a href="#about">حول المنصة</a>
        <a href="#contact">إتصل بنا</a>
      </div>
    </div>

    <div class="nav-title">
      <span>الجمهورية الجزائرية الديمقراطية الشعبية</span>
      <span class="ministry-title">وزارة التضامن الوطني والأسرة وقضايا المرأة</span>
      <span>وكالة التنمية الإجتماعية</span>
    </div>

    <div class="navbar-right">
      <img src="{{ asset('images/LOGO_ads.png') }}" class="nav-logo" alt="Logo droite">
    </div>
  </nav>

  <!-- Hero -->
  <section id="hero" class="hero">
    <div class="hero-bg active" style="background-image: url('{{ asset('images/back1.jpg') }}');"></div>
    <div class="hero-bg" style="background-image: url('{{ asset('images/back2.jpg') }}');"></div>
    <div class="overlay"></div>

    <div class="hero-content">
      <h1>المنصة الرقمية لطلب المنحة المدرسية الخاصة</h1>
      <h3>السنة الدراسية 2025 / 2026</h3>
      <p>منصة رقمية رسمية لتقديم ومتابعة طلبات المنحة المدرسية الخاصة للعام الدراسي الجديد.</p>
      <div class="d-flex justify-content-center gap-3 flex-wrap">
        <a href="{{ route('login.form') }}" class="btn btn-primary">
          <i class="fa-solid fa-right-to-bracket ms-2"></i> تسجيل الدخول
        </a>
        <a href="{{ route('signup') }}" class="btn btn-light border">
          <i class="fa-solid fa-user-plus ms-2"></i> إنشاء حساب
        </a>
      </div>
    </div>

    <!-- Arrow Buttons -->
    <button class="hero-arrow left"><i class="fa-solid fa-arrow-left"></i></button>
    <button class="hero-arrow right"><i class="fa-solid fa-arrow-right"></i></button>
  </section>

  <!-- About -->
  <section id="about" class="about-section">
    <div class="container about-container">
      <div class="row align-items-center g-4">
        <div class="col-lg-7 about-text">
          <h2>شروط و طريقة التسجيل للإستفادة من المنحة المدرسية الخاصة</h2>
          <p>تمنح المنحة المدرسية الخاصة، مرة واحدة قبل بداية كل سنة دراسية، لكل تلميذ مسجل بصفة نظامية بمؤسسة التربية و التعليم العمومية أو بمؤسسة التربية والتعليم المتخصصة العمومية، وينتمي إلى إحدى الفئات الإجتماعية المذكورة أدناه:</p>
          <ul>
            <li><i class="fa-solid fa-users"></i>منحدر من عائلة معوزة لا يتوفر والداه أو وصيه على أي دخل.</li>
            <li><i class="fa-solid fa-dollar-sign"></i>يقل أو يساوي الدخل الشهري لكل من والديه أو وصيه مبلغ الأجر الوطني الأدنى المضمون.</li>
          </ul>

          <button id="toggle-details" class="btn btn-primary mt-3">
            <i class="fa-solid fa-circle-info ms-2 info-icon"></i> المزيد من التفاصيل
          </button>

          <!-- Hidden details -->
          <div id="details-content" class="details-content mt-4">
            <h4>📋 خطوات استعمال المنصة الرقمية</h4>
            <ol class="mt-2">
              <li>إنشاء حساب جديد لوليّ التلميذ عبر الضغط على زر <strong>“إنشاء حساب”</strong>.</li>
              <li>ملء جميع المعلومات الضرورية الخاصة بوليّ التلميذ (الوليّ أو الوصيّ).</li>
              <li>تسجيل الدخول باستعمال رقم التعريف الوطني وكلمة المرور التي تم إنشاؤها.</li>
              <li>من خلال لوحة التحكم، يمكن إضافة الأبناء المتمدرسين وملء بياناتهم الدراسية.</li>
              <li>إرسال طلب المنحة ومتابعة حالة الطلب عبر المنصة.</li>
            </ol>
          </div>
        </div>

        <div class="col-lg-5 text-center about-image">
          <img src="{{ asset('images/terms-and-conditions.png') }}" 
               alt="شروط المنحة" 
               class="img-fluid rounded shadow">
        </div>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer id="contact">
    <div class="container">
      <div class="row g-5">
        <div class="col-md-6 col-lg-4">
          <div class="footer-logo">
            <img src="{{ asset('images/LOGO_ads.png') }}" alt="Logo">
          </div>
          <p class="small">المنصة الرقمية الرسمية لتقديم طلبات المنحة المدرسية الخاصة تحت إشراف وكالة التنمية الاجتماعية.</p>
        </div>

        <div class="col-md-6 col-lg-4">
          <h5>روابط مفيدة</h5>
          <ul class="list-unstyled">
            <li><a href="https://www.msnfcf.gov.dz" target="_blank"><i class="fas fa-angle-left ms-2"></i> وزارة التضامن الوطني و الأسرة وقضايا المرأة</a></li>
            <li><a href="https://www.ads.dz" target="_blank"><i class="fas fa-angle-left ms-2"></i> وكالة التنمية الاجتماعية</a></li>
            <li><a href="https://www.interieur.gov.dz/" target="_blank"><i class="fas fa-angle-left ms-2"></i> وزارة الداخلية</a></li>
          </ul>
        </div>

        <div class="col-md-6 col-lg-4">
          <h5>اتصل بنا</h5>
          <p><i class="fas fa-map-marker-alt text-warning ms-2"></i> حي كناب عمارة رقم 02، البساتين، بئر مراد رايس – الجزائر</p>
          <p><i class="fas fa-envelope text-warning ms-2"></i> communication@ads.dz</p>
          <p><i class="fa fa-phone-alt text-warning ms-2"></i> (+213) 23 55 04 25 / 26</p>
        </div>
      </div>

      <div class="bottom-bar">
         المنصة الرقمية لطلب المنحة المدرسية الخاصة — جميع الحقوق محفوظة {{ date('Y') }} © 
      </div>
    </div>
  </footer>

  <!-- JS local -->
  @vite(['resources/js/welcome.js'])
</body>
</html>
