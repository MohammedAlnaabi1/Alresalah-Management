<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- ✅ SEO معلومات عامة -->
<meta name="description" content="مدرسة الرسالة لتحفيظ القرآن الكريم - تعليم وتربية على منهج القرآن في بيئة تربوية متميزة.">
<meta name="keywords" content="مدرسة الرسالة, تحفيظ القرآن, مدارس عمان, تعليم القرآن, الرسالة العامرات">
<meta name="author" content="مدرسة الرسالة">

<!-- ✅ العنوان الظاهر في محركات البحث -->
<title>مدرسة الرسالة لتحفيظ القرآن الكريم</title>

<!-- ✅ لجوجل وسوشيال ميديا -->
<meta property="og:title" content="مدرسة الرسالة لتحفيظ القرآن الكريم">
<meta property="og:description" content="تعليم وتربية على منهج القرآن في بيئة تربوية متميزة.">
<meta property="og:image" content="{{ asset('images/Logo.png') }}">
<meta property="og:url" content="https://alresalah-school.net">
<meta property="og:type" content="website">

<!-- ✅ أيقونة المتصفح -->
<link rel="icon" href="{{ asset('images/Logo.png') }}" type="image/png">

  <title>مدرسة الرسالة - الصفحة الرئيسية</title>

  <!-- Bootstrap + AOS + Google Fonts -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">

  <style>
    * { box-sizing: border-box; }

    html, body {
      overflow-x: hidden; /* ✅ يمنع التحريك الأفقي */
      width: 100%;
      max-width: 100%;
      font-family: 'Cairo', sans-serif;
      color: #333;
      background-color: #fff;
      scroll-behavior: smooth;
    }

    /* 🔶 شريط النسخة التجريبية */
    .beta-banner {
      background: linear-gradient(90deg, #f28c28, #f5a742);
      color: white;
      text-align: center;
      font-weight: 700;
      padding: 8px 0;
      font-size: 14px;
      letter-spacing: 1px;
      z-index: 2000;
      position: relative;
    }

    /* 🔸 Navbar */
    .navbar {
      background: transparent;
      position: absolute;
      width: 100%;
      z-index: 1000;
    }
    .navbar-brand {
      font-weight: 700;
      color: #fff !important;
    }
    .nav-link {
      color: #fff !important;
      font-weight: 600;
      margin-right: 15px;
      transition: color 0.3s ease;
    }
    .nav-link:hover {
      color: #ffd8b2 !important;
    }
    .btn-login {
      background-color: #F28C28;
      border: none;
      color: white;
      font-weight: 600;
      border-radius: 10px;
      transition: 0.3s;
    }
    .btn-login:hover {
      background-color: #e1780d;
    }

    /* 🔸 شعار مدرسة الرسالة */
    .brand {
      display: flex;
      align-items: center;
      gap: 8px;
    }
    .logo-bg {
      width: 55px;
      height: 55px;
      background: radial-gradient(circle at center, #fff 40%, #F28C28 90%);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
      transition: transform 0.3s ease;
    }
    .logo-bg img {
      width: 38px;
      height: 38px;
      object-fit: contain;
    }
    .logo-bg:hover { transform: scale(1.1); }
    .brand-text {
      font-size: 1.2rem;
      color: #fff;
    }

    /* 🔸 Hero Section */
    .hero {
      background: url('{{ asset('images/hero-bg.png') }}') center center / cover no-repeat;
      color: #fff;
      text-align: center;
      padding: 200px 20px 150px;
      position: relative;
      overflow: hidden;
      width: 100%; /* ✅ إصلاح */
    }
    .hero::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 0;
      width: 100%;
      height: 125px;
      background: linear-gradient(to top, #fff 0%, transparent 100%);
    }
    .hero h1 {
      font-size: 2.8rem;
      font-weight: 700;
      text-shadow: 0 3px 6px rgba(0,0,0,0.2);
    }
    .hero p {
      font-size: 1.2rem;
      margin-top: 15px;
      margin-bottom: 30px;
      opacity: 0.95;
    }

    /* 🔸 Section Style */
    .section { padding: 100px 0; }
    .section h2 {
      color: #F28C28;
      font-weight: 700;
      margin-bottom: 20px;
    }
    .section p {
      font-size: 1.1rem;
      color: #555;
      line-height: 1.8;
    }
    .section img {
      border-radius: 20px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
      transition: transform 0.4s ease;
      max-width: 100%; /* ✅ يمنع التمدد */
      height: auto;
    }
    .section img:hover { transform: scale(1.03); }

    /* 🔸 Contact Section */
    .contact-section {
      background: linear-gradient(135deg, #F28C28, #ffb347);
      color: #fff;
      padding: 80px 0;
      text-align: center;
      width: 100%; /* ✅ إصلاح */
    }
    .contact-section h2 {
      color: #fff;
      font-weight: 700;
      margin-bottom: 30px;
    }
    .contact-box-modern {
      background: #fff;
      border-radius: 20px;
      padding: 60px;
      box-shadow: 0 8px 25px rgba(0,0,0,0.15);
      color: #333;
    }

    /* 🔸 Footer */
    footer {
      background-color: #F28C28;
      color: white;
      text-align: center;
      padding: 30px 15px;
      width: 100%; /* ✅ إصلاح */
    }

    /* ✅ Responsive Fixes */
    @media (max-width: 768px) {
      .hero { padding: 160px 10px 120px; }
      .hero h1 { font-size: 2rem; }
      .section { padding: 70px 0; }
      .contact-box-modern { padding: 30px 20px; }
      .navbar-collapse {
        background-color: rgba(255, 255, 255, 0.97);
        position: absolute;
        top: 80px;
        right: 0;
        width: 100%;
        z-index: 9999;
        border-radius: 0 0 15px 15px;
        padding: 20px 0;
        text-align: center;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        animation: slideDown 0.3s ease;
      }
      .navbar-collapse .nav-link {
        color: #f28c28 !important;
        font-weight: 600;
        font-size: 18px;
        margin: 10px 0;
        display: block;
      }
      @keyframes slideDown {
        from {opacity: 0; transform: translateY(-10px);}
        to {opacity: 1; transform: translateY(0);}
      }
    }
  </style>
</head>
<body>

  <!-- 🔶 شريط النسخة التجريبية -->
  <div class="beta-banner">
    🚧 نسخة تجريبية - مدرسة الرسالة لتحفيظ القرآن الكريم 🚧
  </div>

  <!-- 🔹 Navbar -->
  <nav class="navbar navbar-expand-lg">
    <div class="container d-flex align-items-center justify-content-between">
      <div class="brand d-flex align-items-center">
        <div class="logo-bg">
          <img src="{{ asset('images/Logo.png') }}" alt="شعار الرسالة">
        </div>
        <span class="brand-text ms-2 fw-bold text-white">مدرسة الرسالة</span>
      </div>

      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
        <ul class="navbar-nav align-items-center">
          <li class="nav-item"><a class="nav-link" href="#about">عن المدرسة</a></li>
          <li class="nav-item"><a class="nav-link" href="#vision">رؤيتنا</a></li>
          <li class="nav-item"><a class="nav-link" href="#method">منهجنا</a></li>
          <li class="nav-item"><a class="nav-link" href="#leadership">القيادة</a></li>
          <li class="nav-item"><a class="nav-link" href="#contact">تواصل معنا</a></li>
        </ul>
        <a href="{{ route('login') }}" class="btn btn-login ms-3 px-3 py-2">تسجيل الدخول</a>
      </div>
    </div>
  </nav>

  <!-- ✅ Hero -->
  <section class="hero">
    <div class="container" data-aos="fade-up">
      <h1>جيل قرآني يصنع الفَرْق</h1>
      <p>نؤمن أن تعليم القرآن لا يقتصر على التلاوة، بل يشمل الفهم، والتطبيق، والعيش على نهجه.</p>
      <a href="#about" class="btn btn-light px-4 py-2 fw-bold">اكتشف المزيد</a>
    </div>
  </section>

  <!-- About -->
  <section id="about" class="section">
    <div class="container">
      <div class="row align-items-center">
        <div class="col-md-6 mb-4" data-aos="fade-left">
          <img src="{{ asset('images/about.png') }}" alt="عن المدرسة" class="img-fluid">
        </div>
        <div class="col-md-6" data-aos="fade-right">
          <h2>عن المدرسة</h2>
          <p>مدرسة الرسالة لتحفيظ القرآن الكريم مؤسسة تربوية تعليمية تهدف إلى غرس القيم القرآنية في نفوس الناشئة، وتنمية مهاراتهم الفكرية والقيادية.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Vision -->
  <section id="vision" class="section bg-light">
    <div class="container">
      <div class="row align-items-center">
        <div class="col-md-6 mb-4" data-aos="fade-left">
          <img src="{{ asset('images/vision.png') }}" alt="رؤيتنا" class="img-fluid">
        </div>
        <div class="col-md-6" data-aos="fade-right">
          <h2>رؤيتنا</h2>
          <p>جيل قرآني يصنع الفَرْق. نؤمن أن تعليم القرآن لا يقتصر على التلاوة بل يشمل الفهم، التطبيق، والعيش على نهجه.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Method -->
  <section id="method" class="section">
    <div class="container">
      <div class="row align-items-center">
        <div class="col-md-6 order-md-2 mb-4" data-aos="fade-left">
          <img src="{{ asset('images/method.png') }}" alt="منهجنا" class="img-fluid">
        </div>
        <div class="col-md-6 order-md-1" data-aos="fade-right">
          <h2>منهجنا</h2>
          <p>نُدرّس القرآن وعلومه بأساليب حديثة وتفاعلية في قاعات مجهزة وبيئة تربوية محفزة على التعلم والتميز.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Leadership -->
  <section id="leadership" class="section bg-light">
    <div class="container">
      <div class="row align-items-center">
        <div class="col-md-6 mb-4" data-aos="fade-left">
          <img src="{{ asset('images/leaders.png') }}" alt="القيادة" class="img-fluid">
        </div>
        <div class="col-md-6" data-aos="fade-right">
          <h2>القيادة والفعالية</h2>
          <p>نُخرّج قادة على نهج القرآن؛ قيادات فعالة تمتلك الإيمان والمعرفة لتبني مجتمعًا راقيًا ومؤثرًا.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Contact -->
  <section id="contact" class="contact-section">
    <div class="container" data-aos="fade-up">
      <h2 class="text-center fw-bold mb-5 text-white">تواصل معنا</h2>
      <div class="contact-box-modern">
        <div class="row align-items-center">
          <div class="col-lg-5 mb-4 mb-lg-0 text-start">
            <h4 class="fw-bold mb-4 text-orange">معلومات التواصل</h4>
            <p><i class="bi bi-envelope-fill me-2 text-orange"></i> <a href="mailto:alresalah112@gmail.com" class="text-dark text-decoration-none">alresalah112@gmail.com</a></p>
            <p><i class="bi bi-telephone-fill me-2 text-orange"></i> <a href="tel:+96877112660" class="text-dark text-decoration-none">77112660</a></p>
            <p><i class="bi bi-instagram me-2 text-orange"></i> <a href="https://www.instagram.com/alresalah_school1" target="_blank" class="text-dark text-decoration-none">@alresalah_school1</a></p>
          </div>

          <div class="col-lg-7">
            <form action="{{ route('contact.store') }}" method="POST" class="contact-form-modern">
              @csrf
              <div class="row g-3">
                <div class="col-md-6">
                  <input type="text" name="name" class="form-control" placeholder="الاسم الكامل" required>
                </div>
                <div class="col-md-6">
                  <input type="email" name="email" class="form-control" placeholder="البريد الإلكتروني" required>
                </div>
                <div class="col-12">
                  <textarea name="message" class="form-control" rows="4" placeholder="اكتب رسالتك هنا..." required></textarea>
                </div>
                <div class="col-12 text-end">
                  <button type="submit" class="btn btn-orange px-5 py-2 mt-2 fw-bold">إرسال الرسالة</button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer data-aos="fade-up">
    <p>© 2025 مدرسة الرسالة لتحفيظ القرآن الكريم. جميع الحقوق محفوظة.</p>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
  <script>
    AOS.init({
      duration: 1000,
      once: true
    });
  </script>
</body>
</html>
