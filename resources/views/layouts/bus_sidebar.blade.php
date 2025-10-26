<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  
  <title>@yield('title', 'نظام إدارة الحافلات - مدرسة الرسالة')</title>

  <!-- Bootstrap & Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600&display=swap" rel="stylesheet">

  <style>
    :root {
      --orange: #f18b22;
      --blue: #006b8f;
      --light-bg: #f5f6fa;
    }

    body {
      background-color: var(--light-bg);
      font-family: 'Cairo', sans-serif;
      margin: 0;
      padding: 0;
      overflow-x: hidden;
    }

    /* 🔸 الشريط الجانبي */
    .sidebar {
      width: 250px;
      height: 100vh;
      position: fixed;
      right: 0;
      top: 0;
      background: linear-gradient(180deg, var(--orange) 0%, #f3a447 100%);
      color: #fff;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      padding: 20px 15px;
      box-shadow: -2px 0 8px rgba(0, 0, 0, 0.1);
      z-index: 1000;
    }

    .sidebar .brand {
      text-align: center;
      margin-bottom: 30px;
    }

    .sidebar .brand img {
      width: 70px;
      background: #fff;
      border-radius: 50%;
      padding: 5px;
      margin-bottom: 8px;
      transition: transform 0.3s;
    }

    .sidebar .brand img:hover {
      transform: scale(1.05);
    }

    .sidebar a {
      color: #fff;
      text-decoration: none;
      display: block;
      padding: 10px 15px;
      margin: 6px 0;
      border-radius: 8px;
      transition: 0.25s;
      font-weight: 500;
    }

    .sidebar a:hover,
    .sidebar a.active {
      background-color: rgba(255, 255, 255, 0.25);
      color: #212529;
      transform: translateX(-3px);
    }

    .sidebar .logout {
      background-color: rgba(255, 255, 255, 0.2);
      border: none;
      color: #fff;
      font-weight: 600;
      transition: 0.25s;
    }

    .sidebar .logout:hover {
      background-color: rgba(255, 255, 255, 0.35);
      color: #212529;
    }

    /* 🔸 منطقة المحتوى */
    .content {
      margin-right: 270px;
      padding: 25px;
      transition: margin 0.3s;
    }

    .topbar {
      background-color: #fff;
      padding: 15px 20px;
      border-radius: 12px;
      box-shadow: 0 1px 5px rgba(0, 0, 0, 0.08);
      margin-bottom: 25px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .topbar h5 {
      color: var(--blue);
      font-weight: 700;
      margin: 0;
    }
  </style>
</head>
<body>

  {{-- ✅ الشريط الجانبي --}}
  <div class="sidebar">
    <div>
      <div class="brand">
        <img src="{{ asset('imag/Logo.png') }}" alt="شعار الرسالة">
        <div>نظام إدارة الحافلات</div>
      </div>

      <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('bus.dashboard') ? 'active' : '' }}">
  <i class="bi bi-speedometer2 me-2"></i> لوحة المعلومات
</a>


      <a href="{{ route('bus') }}" class="{{ request()->routeIs('bus') ? 'active' : '' }}">
        <i class="bi bi-bus-front"></i> إدارة الحافلات
      </a>

      <a href="{{ route('bus_expenses') }}" class="{{ request()->routeIs('bus.expenses') ? 'active' : '' }}">
        <i class="bi bi-cash-coin"></i> مصروفات الحافلات
      </a>

      <li>
  <a href="{{ route('bus.operations') }}">
    <i class="bi bi-gear-wide-connected"></i> الصيانة والوقود
  </a>
</li>

    </div>

    <form action="{{ route('logout') }}" method="POST">
      @csrf
      <button class="btn logout w-100 mt-3">
        <i class="bi bi-box-arrow-right me-1"></i> تسجيل الخروج
      </button>
    </form>
  </div>

  {{-- ✅ المحتوى --}}
  <div class="content">
    <div class="topbar">
      <h5>@yield('title')</h5>
      <div><i class="bi bi-person-circle me-1"></i> المستخدم</div>
    </div>

    @yield('content')
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
