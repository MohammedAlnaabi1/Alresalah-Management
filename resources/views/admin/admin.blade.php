<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>لوحة المدير العام - مركز الرسالة</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600&display=swap" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <style>
    body {
      background-color: #f5f6fa;
      font-family: 'Cairo', sans-serif;
      transition: background-color 0.3s, color 0.3s;
    }
    .dark-mode {
      background-color: #1e1e1e !important;
      color: #f5f5f5 !important;
    }
    .topbar {
      background-color: #fff;
      padding: 15px 25px;
      border-radius: 12px;
      margin-bottom: 25px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    .dark-mode .topbar { background-color: #2c2c2c; }
    .card {
      border: none;
      border-radius: 12px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
      transition: 0.3s;
    }
    .card:hover { transform: translateY(-3px); }
    .chart-container { height: 300px; }
    th { background-color: #f57c00; color: #fff; text-align: center; }
    .progress {
      height: 10px;
      border-radius: 5px;
      background-color: #eee;
    }
    .dark-mode th { background-color: #e07b00; }

    .bg-orange {
  background-color: #F28C28 !important;
}

  </style>
</head>

<body>
  <div class="container-fluid p-4">

    {{-- 🔹 الشريط العلوي --}}
    <div class="topbar">
      <h4><i class="bi bi-speedometer2 me-2"></i> لوحة المدير العام</h4>
      <div>
        <span id="liveTime" class="text-muted me-2"></span>
        <i class="bi bi-person-circle me-1"></i> {{ session('username') ?? 'admin' }}
        <button id="toggleDark" class="btn btn-sm btn-outline-secondary ms-3">
          <i class="bi bi-moon"></i> تبديل الوضع
        </button>
      </div>
    </div>

    {{-- 🔹 التنبيهات --}}
    <div class="alert alert-warning d-flex align-items-center mb-4" role="alert">
      <i class="bi bi-bell-fill me-2"></i>
      <div>مرحبا {{ session('username') ?? 'مدير النظام' }} 👋 — آخر تحديث للبيانات: {{ now()->format('Y-m-d H:i') }}</div>
    </div>

    {{-- 🔹 مؤشرات الأداء --}}
    <div class="card p-3 mb-4">
      <h6 class="fw-bold text-secondary mb-2"><i class="bi bi-graph-up me-1"></i> مؤشر الربحية</h6>
      @php
        $profitRate = $totalRevenues > 0 ? round(($netProfit / $totalRevenues) * 100, 2) : 0;
      @endphp
      <div class="progress mb-2">
        <div class="progress-bar bg-success" role="progressbar" style="width: {{ $profitRate }}%;" aria-valuenow="{{ $profitRate }}" aria-valuemin="0" aria-valuemax="100"></div>
      </div>
      <small>نسبة الربح إلى الإيرادات: <strong>{{ $profitRate }}%</strong></small>
    </div>

    {{-- 🔹 البطاقات --}}
    <div class="row text-center">
      <div class="col-md-2 mb-3">
        <div class="card p-3"><h6>تسجيلات الدخول</h6><h3>{{ $loginCount }}</h3></div>
      </div>
      <div class="col-md-2 mb-3">
        <div class="card p-3"><h6>عدد المستخدمين</h6><h3>{{ $userCount }}</h3></div>
      </div>
      <div class="col-md-2 mb-3">
        <div class="card p-3"><h6>الإيرادات</h6><h3 class="text-success">{{ number_format($totalRevenues,2) }} ر.ع</h3></div>
      </div>
      <div class="col-md-2 mb-3">
        <div class="card p-3"><h6>المصروفات</h6><h3 class="text-danger">{{ number_format($totalExpenses,2) }} ر.ع</h3></div>
      </div>
      <div class="col-md-2 mb-3">
        <div class="card p-3"><h6>عدد الحافلات</h6><h3>{{ $busCount }}</h3></div>
      </div>
      <div class="col-md-2 mb-3">
        <div class="card p-3"><h6>عدد الطلاب</h6><h3 class="text-info">{{ $studentCount }}</h3></div>
      </div>
      <div class="col-md-2 mb-3">
        <div class="card p-3"><h6>صافي الربح</h6><h3 class="{{ $netProfit >= 0 ? 'text-success' : 'text-danger' }}">{{ number_format($netProfit,2) }} ر.ع</h3></div>
      </div>
    </div>

    {{-- 🔹 المخطط البياني --}}
    <div class="card mt-4 p-4">
      <h5 class="mb-3"><i class="bi bi-graph-up-arrow me-2"></i> الإيرادات والمصروفات الشهرية</h5>
      <div class="chart-container"><canvas id="financeChart"></canvas></div>
    </div>

    {{-- 🔹 جدول تسجيلات الدخول --}}
    <div class="card mt-4 p-3">
      <h5 class="mb-3"><i class="bi bi-clock-history me-2"></i> آخر تسجيلات الدخول</h5>
      <div class="table-responsive">
        <table class="table table-striped text-center">
          <thead><tr><th>المستخدم</th><th>عنوان IP</th><th>وقت الدخول</th></tr></thead>
          <tbody>
            @foreach($logins as $log)
              <tr>
                <td>{{ $log->username }}</td>
                <td>{{ $log->ip_address }}</td>
                <td>{{ $log->login_time }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>

    {{-- 🔹 آخر النشاطات --}}
    <div class="card mt-4 p-3">
      <h5 class="mb-3"><i class="bi bi-activity me-2"></i> آخر النشاطات في النظام</h5>
      <div class="row">
        <div class="col-md-6">
          <h6 class="text-success">آخر الإيرادات</h6>
          <ul class="list-group">
            @foreach($recentRevenues as $rev)
              <li class="list-group-item d-flex justify-content-between align-items-center">
                {{ $rev->source ?? 'غير محدد' }}
                <span class="badge bg-success">{{ number_format($rev->amount, 2) }} ر.ع</span>
              </li>
            @endforeach
          </ul>
        </div>
        <div class="col-md-6">
          <h6 class="text-danger">آخر المصروفات</h6>
          <ul class="list-group">
            @foreach($recentExpenses as $exp)
              <li class="list-group-item d-flex justify-content-between align-items-center">
                {{ $exp->category ?? 'غير محدد' }}
                <span class="badge bg-danger">{{ number_format($exp->amount, 2) }} ر.ع</span>
              </li>
            @endforeach
          </ul>
        </div>
      </div>
    </div>

    {{-- 🔹 أزرار التصدير والوصول السريع --}}
    <div class="card mt-4 p-4 text-center">
      <h5 class="mb-3"><i class="bi bi-link-45deg me-2"></i> أدوات سريعة</h5>
      <div class="mb-3">
        <a href="{{ route('financial.reports.exportPDF') }}" class="btn btn-outline-danger m-1"><i class="bi bi-file-earmark-pdf"></i> تصدير PDF</a>
        <a href="{{ route('financial.reports.exportExcel') }}" class="btn btn-outline-success m-1"><i class="bi bi-file-earmark-spreadsheet"></i> تصدير Excel</a>
      </div>
      <a href="{{ route('financial.dashboard') }}" class="btn btn-warning m-2"><i class="bi bi-wallet2 me-1"></i> الإدارة المالية</a>
      <a href="{{ route('dashboard') }}" class="btn btn-primary m-2"><i class="bi bi-bus-front me-1"></i> إدارة الحافلات</a>
      <a href="{{ route('bus.operations') }}" class="btn btn-success m-2"><i class="bi bi-gear me-1"></i> الصيانة والوقود</a>
      <a href="{{ route('student.dashboard') }}" class="btn btn-info m-2"><i class="bi bi-people me-1"></i> إدارة الطلاب</a>
    </div>

    {{-- 🔹 قسم رسائل الزوار --}}
<div class="card mt-5 shadow-sm">
  <div class="card-header bg-orange text-white fw-bold">
    <i class="bi bi-envelope-paper me-2"></i> رسائل الزوار الأخيرة
  </div>
  <div class="card-body">
    @if($contacts->isEmpty())
      <div class="alert alert-info text-center">لا توجد رسائل حتى الآن.</div>
    @else
      <div class="table-responsive">
        <table class="table table-striped table-hover align-middle">
          <thead class="table-light">
            <tr>
              <th>#</th>
              <th>الاسم</th>
              <th>البريد الإلكتروني</th>
              <th>الرسالة</th>
              <th>تاريخ الإرسال</th>
            </tr>
          </thead>
          <tbody>
            @foreach($contacts as $index => $contact)
              <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $contact->name }}</td>
                <td>{{ $contact->email }}</td>
                <td style="white-space: pre-wrap;">{{ $contact->message }}</td>
                <td>{{ $contact->created_at->format('Y-m-d | H:i') }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @endif
  </div>
</div>


    {{-- 🔹 قسم الإشعارات --}}
<div class="card mt-4 p-4">
  <h5 class="mb-3"><i class="bi bi-bell-fill me-2 text-warning"></i> أحدث الإشعارات</h5>
  <div class="table-responsive">
    <table class="table table-hover align-middle text-center">
      <thead>
        <tr>
          <th style="width: 10%">النوع</th>
          <th>الحدث</th>
          <th style="width: 25%">الوقت</th>
        </tr>
      </thead>
      <tbody>
        @forelse($notifications as $note)
          <tr>
            <td>
              @if($note['type'] === 'login')
                <i class="bi bi-person-check text-primary"></i>
              @elseif($note['type'] === 'revenue')
                <i class="bi bi-cash-coin text-success"></i>
              @elseif($note['type'] === 'expense')
                <i class="bi bi-wallet2 text-danger"></i>
              @endif
            </td>
            <td>{{ $note['message'] }}</td>
            <td class="text-muted">{{ \Carbon\Carbon::parse($note['time'])->diffForHumans() }}</td>
          </tr>
        @empty
          <tr>
            <td colspan="3" class="text-muted">لا توجد إشعارات حالياً</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>


  </div>

  <script>
    // 🔹 الرسم البياني
    const ctx = document.getElementById('financeChart').getContext('2d');
    new Chart(ctx, {
      type: 'bar',
      data: {
        labels: {!! json_encode($months) !!},
        datasets: [
          { label: 'الإيرادات', data: {!! json_encode($revenuesData) !!}, backgroundColor: 'rgba(40,167,69,0.6)' },
          { label: 'المصروفات', data: {!! json_encode($expensesData) !!}, backgroundColor: 'rgba(220,53,69,0.6)' }
        ]
      },
      options: { responsive: true, scales: { y: { beginAtZero: true } } }
    });

    // 🔹 تبديل الوضع الليلي
    document.getElementById('toggleDark').addEventListener('click', () => {
      document.body.classList.toggle('dark-mode');
    });

    // 🔹 تحديث الوقت كل ثانية
    setInterval(() => {
      const now = new Date();
      document.getElementById('liveTime').innerText = now.toLocaleString('ar-EG');
    }, 1000);
  </script>
</body>
</html>
