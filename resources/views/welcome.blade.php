<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>المنصة الرقمية لطلب المنحة المدرسية الخاصة</title>

  <!-- Bootstrap & Font Awesome -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

@vite(['resources/css/global.css', 'resources/css/welcome.css', 'resources/js/welcome.js'])
</head>

<body>
  <!-- Navbar -->
  <nav class="main-navbar">
    <div class="navbar-left">
      <img src="{{ asset('images/ministere1.png') }}" class="nav-logo" alt="Logo gauche">
      <button class="mobile-menu-toggle" id="mobileMenuToggle" aria-label="Toggle menu">
        <i class="fa-solid fa-bars"></i>
      </button>
      <div class="navbar-links" id="navbarLinks">
        <a href="#hero" class="active">الرئيسية</a>
        <a href="#features">المميزات</a>
        <a href="#how-it-works">كيف تعمل</a>
        <a href="#about">حول المنصة</a>
        <a href="#faq">الأسئلة الشائعة</a>
        <a href="#contact">إتصل بنا</a>
      </div>
    </div>

    <div class="navbar-right">
      <img src="{{ asset('images/LOGO_ads.png') }}" class="nav-logo" alt="Logo droite">
    </div>
  </nav>
  
  <!-- Mobile Menu Overlay -->
  <div class="mobile-menu-overlay" id="mobileMenuOverlay"></div>

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

  <!-- Features Section -->
  <section id="features" class="features-section">
    <div class="container">
      <div class="section-header">
        <h2>مميزات المنصة الرقمية</h2>
        <p>منصة حديثة وسهلة الاستخدام لتقديم ومتابعة طلبات المنحة المدرسية</p>
      </div>
      <div class="row g-4">
        <div class="col-md-6 col-lg-4">
          <div class="feature-card">
            <div class="feature-icon">
              <i class="fa-solid fa-laptop"></i>
            </div>
            <h4>منصة رقمية متكاملة</h4>
            <p>تقديم الطلبات ومتابعتها إلكترونياً دون الحاجة للتنقل</p>
          </div>
        </div>
        <div class="col-md-6 col-lg-4">
          <div class="feature-card">
            <div class="feature-icon">
              <i class="fa-solid fa-shield-halved"></i>
            </div>
            <h4>آمنة ومحمية</h4>
            <p>حماية كاملة لبياناتك الشخصية وفق أعلى معايير الأمان</p>
          </div>
        </div>
        <div class="col-md-6 col-lg-4">
          <div class="feature-card">
            <div class="feature-icon">
              <i class="fa-solid fa-clock"></i>
            </div>
            <h4>متاحة 24/7</h4>
            <p>يمكنك الوصول إلى المنصة في أي وقت ومن أي مكان</p>
          </div>
        </div>
        <div class="col-md-6 col-lg-4">
          <div class="feature-card">
            <div class="feature-icon">
              <i class="fa-solid fa-chart-line"></i>
            </div>
            <h4>متابعة مباشرة</h4>
            <p>تتبع حالة طلبك في الوقت الفعلي عبر لوحة التحكم</p>
          </div>
        </div>
        <div class="col-md-6 col-lg-4">
          <div class="feature-card">
            <div class="feature-icon">
              <i class="fa-solid fa-file-circle-check"></i>
            </div>
            <h4>سهولة التقديم</h4>
            <p>عملية تقديم بسيطة وسريعة بخطوات واضحة ومباشرة</p>
          </div>
        </div>
        <div class="col-md-6 col-lg-4">
          <div class="feature-card">
            <div class="feature-icon">
              <i class="fa-solid fa-users-line"></i>
            </div>
            <h4>إدارة متعددة</h4>
            <p>إمكانية إضافة ومتابعة طلبات جميع أبنائك من حساب واحد</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- How It Works Section -->
  <section id="how-it-works" class="how-it-works-section">
    <div class="container">
      <div class="section-header">
        <h2>كيف تعمل المنصة</h2>
        <p>خطوات بسيطة للحصول على المنحة المدرسية</p>
      </div>
      <div class="steps-container">
        <div class="step-item">
          <div class="step-number">1</div>
          <div class="step-content">
            <div class="step-icon">
              <i class="fa-solid fa-user-plus"></i>
            </div>
            <h4>إنشاء حساب</h4>
            <p>قم بإنشاء حساب جديد</p>
          </div>
        </div>
        <div class="step-connector">
          <i class="fa-solid fa-arrow-left"></i>
        </div>
        <div class="step-item">
          <div class="step-number">2</div>
          <div class="step-content">
            <div class="step-icon">
              <i class="fa-solid fa-user-pen"></i>
            </div>
            <h4>إكمال البيانات</h4>
            <p>املأ معلوماتك الشخصية ومعلومات أبنائك المتمدرسين</p>
          </div>
        </div>
        <div class="step-connector">
          <i class="fa-solid fa-arrow-left"></i>
        </div>
        <div class="step-item">
          <div class="step-number">3</div>
          <div class="step-content">
            <div class="step-icon">
              <i class="fa-solid fa-file-upload"></i>
            </div>
            <h4>تقديم الطلب</h4>
            <p>أرسل طلب المنحة بعد التأكد من صحة جميع البيانات</p>
          </div>
        </div>
        <div class="step-connector">
          <i class="fa-solid fa-arrow-left"></i>
        </div>
        <div class="step-item">
          <div class="step-number">4</div>
          <div class="step-content">
            <div class="step-icon">
              <i class="fa-solid fa-eye"></i>
            </div>
            <h4>متابعة الطلب</h4>
            <p>تابع حالة طلبك وتمتع بالشفافية الكاملة</p>
          </div>
        </div>
      </div>
    </div>
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
            <li>
  <i class="fa-solid fa-money-bill"></i>
  يقل أو يساوي الدخل الشهري لكل من والديه أو وصيه مبلغ الأجر الوطني الأدنى المضمون (دج).
</li>

          </ul>

          <button id="toggle-details" class="btn btn-primary mt-3">
            <i class="fa-solid fa-circle-info ms-2 info-icon"></i> المزيد من التفاصيل
          </button>

          <!-- Hidden details -->
          <div id="details-content" class="details-content mt-4">
            <h4>📋 خطوات استعمال المنصة الرقمية</h4>
            <ol class="mt-3">
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

  <!-- FAQ Section -->
  <section id="faq" class="faq-section">
    <div class="container">
      <div class="section-header">
        <h2>الأسئلة الشائعة</h2>
        <p>إجابات على أكثر الأسئلة شيوعاً حول المنحة المدرسية</p>
      </div>
      <div class="row justify-content-center">
        <div class="col-lg-10">
          <div class="faq-container">
            <div class="faq-item">
              <div class="faq-question">
                <h5>من يمكنه الاستفادة من المنحة المدرسية الخاصة؟</h5>
                <i class="fa-solid fa-chevron-down"></i>
              </div>
              <div class="faq-answer">
                <p>يمكن الاستفادة من المنحة لكل تلميذ مسجل بصفة نظامية في مؤسسة التربية والتعليم العمومية أو المتخصصة العمومية، وينتمي إلى عائلة معوزة لا يتوفر والداه أو وصيه على أي دخل، أو يقل أو يساوي الدخل الشهري مبلغ الأجر الوطني الأدنى المضمون.</p>
              </div>
            </div>
            <div class="faq-item">
              <div class="faq-question">
                <h5>متى يمكن تقديم طلب المنحة؟</h5>
                <i class="fa-solid fa-chevron-down"></i>
              </div>
              <div class="faq-answer">
                <p>يمكن تقديم طلب المنحة المدرسية الخاصة مرة واحدة قبل بداية كل سنة دراسية. للموسم الدراسي 2025/2026، يمكنك تقديم طلبك الآن عبر المنصة الرقمية.</p>
              </div>
            </div>
            <div class="faq-item">
              <div class="faq-question">
                <h5>ما هي الوثائق المطلوبة لتقديم الطلب؟</h5>
                <i class="fa-solid fa-chevron-down"></i>
              </div>
              <div class="faq-answer">
                <p>يجب إحضار جميع الوثائق المطلوبة مثل شهادة التسجيل المدرسي، وثيقة الهوية الوطنية، وكشف الدخل أو شهادة العوز. يمكنك الاطلاع على القائمة الكاملة للوثائق المطلوبة بعد تسجيل الدخول.</p>
              </div>
            </div>
            <div class="faq-item">
              <div class="faq-question">
                <h5>كيف يمكنني متابعة حالة طلبي؟</h5>
                <i class="fa-solid fa-chevron-down"></i>
              </div>
              <div class="faq-answer">
                <p>بعد تسجيل الدخول إلى حسابك، يمكنك متابعة حالة طلبك مباشرة من لوحة التحكم. ستجد حالة الطلب (قيد المراجعة، مقبول، مرفوض) مع إمكانية الاطلاع على التفاصيل.</p>
              </div>
            </div>
            <div class="faq-item">
              <div class="faq-question">
                <h5>ماذا أفعل إذا نسيت كلمة المرور؟</h5>
                <i class="fa-solid fa-chevron-down"></i>
              </div>
              <div class="faq-answer">
                <p>يمكنك استعادة كلمة المرور من صفحة تسجيل الدخول عبر النقر على "نسيت كلمة المرور" وإدخال رقم التعريف الوطني. سيتم إرسال رابط إعادة تعيين كلمة المرور إلى بريدك الإلكتروني.</p>
              </div>
            </div>
            <div class="faq-item">
              <div class="faq-question">
                <h5>هل يمكنني إضافة أكثر من تلميذ في نفس الحساب؟</h5>
                <i class="fa-solid fa-chevron-down"></i>
              </div>
              <div class="faq-answer">
                <p>نعم، يمكنك إضافة جميع أبنائك المتمدرسين في حساب واحد وتقديم طلبات منحة لكل منهم من خلال لوحة التحكم الخاصة بك.</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Call to Action Section -->
  <section class="cta-section">
    <div class="container">
      <div class="cta-content">
        <h2>ابدأ رحلتك الآن</h2>
        <p>انضم إلى آلاف العائلات التي استفادت من المنحة المدرسية الخاصة</p>
        <div class="cta-buttons">
          <a href="{{ route('signup') }}" class="btn btn-primary btn-lg">
            <i class="fa-solid fa-user-plus ms-2"></i> إنشاء حساب جديد
          </a>
          <a href="{{ route('login.form') }}" class="btn btn-light btn-lg">
            <i class="fa-solid fa-right-to-bracket ms-2"></i> تسجيل الدخول
          </a>
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
          <p dir="ltr" style="text-align: right;"><i class="fas fa-envelope text-warning me-2"></i> communication@ads.dz</p>
          <p dir="ltr" style="text-align: right;"><i class="fa fa-phone-alt text-warning me-2"></i> (+213) 23 55 04 25 / 26</p>
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
