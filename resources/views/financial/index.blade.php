@extends('financial.layout')

@section('title', 'لوحة المعلومات المالية')

@section('content')

<div class="container-fluid">

  {{-- ✅ فلتر الشهر والسنة --}}
<form method="GET" class="d-flex justify-content-end align-items-center mb-4 bg-light p-3 rounded shadow-sm">
  <label class="me-2 fw-semibold text-secondary">اختر الشهر:</label>
  <select name="month" class="form-select w-auto me-2">
    <option value="">الكل</option>
    @foreach (range(1, 12) as $m)
      <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>
        {{ \Carbon\Carbon::create()->month($m)->locale('ar')->translatedFormat('F') }}
      </option>
    @endforeach
  </select>

  <label class="me-2 fw-semibold text-secondary">السنة:</label>
  <select name="year" class="form-select w-auto me-2">
    <option value="">الكل</option>
    @foreach (range(now()->year - 3, now()->year) as $y)
      <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
    @endforeach
  </select>

  <button type="submit" class="btn btn-primary">
    <i class="bi bi-search me-1"></i> عرض
  </button>
</form>


  {{-- ====== البطاقات الأساسية ====== --}}
  <div class="row g-4 mb-4">
    <!-- إجمالي الإيرادات -->
    <div class="col-md-6 col-xl-3">
      <div class="card p-4 text-center shadow-sm border-0">
        <div class="card-icon mx-auto mb-3 bg-light p-3 rounded-circle">
          <i class="bi bi-cash-coin text-success fs-3"></i>
        </div>
        <h6 class="fw-semibold text-secondary">إجمالي الإيرادات</h6>
        <h4 class="fw-bold text-success">{{ number_format(($totalRevenues ?? 0), 2) }} ر.ع</h4>
        <small class="text-muted">منذ بداية العام</small>
      </div>
    </div>

    <!-- إجمالي المصروفات -->
    <div class="col-md-6 col-xl-3">
      <div class="card p-4 text-center shadow-sm border-0">
        <div class="card-icon mx-auto mb-3 bg-light p-3 rounded-circle">
          <i class="bi bi-wallet2 text-danger fs-3"></i>
        </div>
        <h6 class="fw-semibold text-secondary">إجمالي المصروفات</h6>
        <h4 class="fw-bold text-danger">{{ number_format(($totalExpenses ?? 0), 2) }} ر.ع</h4>
        <small class="text-muted">منذ بداية العام</small>
      </div>
    </div>

    <!-- صافي الرصيد العام -->
    <div class="col-md-6 col-xl-3">
      <div class="card p-4 text-center shadow-sm border-0">
        <div class="card-icon mx-auto mb-3 bg-light p-3 rounded-circle">
          <i class="bi bi-bar-chart text-primary fs-3"></i>
        </div>
        <h6 class="fw-semibold text-secondary">صافي الرصيد العام</h6>
        <h4 class="fw-bold text-primary">{{ number_format($netBalance, 2) }} ر.ع</h4>
        <small class="text-muted">حتى نهاية الشهر</small>
      </div>
    </div>

    <!-- مصروفات الحافلات -->
    <div class="col-md-6 col-xl-3">
      <div class="card p-4 text-center shadow-sm border-0">
        <div class="card-icon mx-auto mb-3 bg-light p-3 rounded-circle">
          <i class="bi bi-bus-front text-warning fs-3"></i>
        </div>
        <h6 class="fw-semibold text-secondary">مصروفات الحافلات</h6>
        <h4 class="fw-bold text-warning">{{ number_format($busExpenses, 2) }} ر.ع</h4>
        <small class="text-muted">إجمالي جميع الحافلات</small>
      </div>
    </div>
  </div>

  {{-- ====== بطاقات إضافية تحليلية ====== --}}
  <div class="row g-4 mb-4">
    <!-- إيرادات الشهر المحدد -->
    <div class="col-md-6 col-xl-3">
      <div class="card p-4 text-center shadow-sm border-0 bg-success-subtle">
        <div class="card-icon mx-auto mb-3 bg-light p-3 rounded-circle">
          <i class="bi bi-graph-up text-success fs-3"></i>
        </div>
        <h6 class="fw-semibold">إيرادات الشهر المحدد</h6>
        <h4 class="fw-bold text-success">{{ number_format($monthlyRevenue, 2) }} ر.ع</h4>
        <small class="text-muted">للشهر المختار من الفلتر</small>
      </div>
    </div>

    <!-- مصروفات الشهر المحدد -->
    <div class="col-md-6 col-xl-3">
      <div class="card p-4 text-center shadow-sm border-0 bg-danger-subtle">
        <div class="card-icon mx-auto mb-3 bg-light p-3 rounded-circle">
          <i class="bi bi-cash-stack text-danger fs-3"></i>
        </div>
        <h6 class="fw-semibold">مصروفات الشهر المحدد</h6>
        <h4 class="fw-bold text-danger">{{ number_format($monthlyExpense, 2) }} ر.ع</h4>
        <small class="text-muted">للشهر المختار</small>
      </div>
    </div>

    <!-- صافي الشهر -->
    <div class="col-md-6 col-xl-3">
      <div class="card p-4 text-center shadow-sm border-0 bg-primary-subtle">
        <div class="card-icon mx-auto mb-3 bg-light p-3 rounded-circle">
          <i class="bi bi-calculator text-primary fs-3"></i>
        </div>
        <h6 class="fw-semibold">صافي الشهر</h6>
        <h4 class="fw-bold text-primary">{{ number_format($monthlyRevenue - $monthlyExpense, 2) }} ر.ع</h4>
        <small class="text-muted">الفرق بين الإيرادات والمصروفات</small>
      </div>
    </div>

    <!-- أكبر فئة مصروف -->
    <div class="col-md-6 col-xl-3">
      @php
        $topCategory = $expenseCategories->sortDesc()->keys()->first();
        $topValue = $expenseCategories->sortDesc()->values()->first();
      @endphp
      <div class="card p-4 text-center shadow-sm border-0 bg-light">
        <div class="card-icon mx-auto mb-3 bg-white p-3 rounded-circle">
          <i class="bi bi-award text-secondary fs-3"></i>
        </div>
        <h6 class="fw-semibold">أكبر فئة مصروف</h6>
        <h4 class="fw-bold text-secondary">{{ $topCategory ?? 'غير محدد' }}</h4>
        <small class="text-muted">{{ number_format($topValue ?? 0, 2) }} ر.ع</small>
      </div>
    </div>
  </div>

  {{-- ====== بطاقات الإيرادات حسب النوع ====== --}}
  <div class="row g-4 mb-4">
    <!-- التبرعات -->
    <div class="col-md-6 col-xl-3">
      <div class="card p-4 text-center shadow-sm border-0 bg-light">
        <div class="card-icon mx-auto mb-3 bg-white p-3 rounded-circle">
          <i class="bi bi-heart text-danger fs-3"></i>
        </div>
        <h6 class="fw-semibold text-danger">إجمالي التبرعات</h6>
        <h4 class="fw-bold text-danger">{{ number_format($donationTotal, 2) }} ر.ع</h4>
      </div>
    </div>

    <!-- الدعم -->
    <div class="col-md-6 col-xl-3">
      <div class="card p-4 text-center shadow-sm border-0 bg-light">
        <div class="card-icon mx-auto mb-3 bg-white p-3 rounded-circle">
          <i class="bi bi-bank text-warning fs-3"></i>
        </div>
        <h6 class="fw-semibold text-warning">إجمالي الدعم</h6>
        <h4 class="fw-bold text-warning">{{ number_format($supportTotal, 2) }} ر.ع</h4>
      </div>
    </div>

    <!-- رسوم النقل -->
    <div class="col-md-6 col-xl-3">
      <div class="card p-4 text-center shadow-sm border-0 bg-light">
        <div class="card-icon mx-auto mb-3 bg-white p-3 rounded-circle">
          <i class="bi bi-truck text-secondary fs-3"></i>
        </div>
        <h6 class="fw-semibold text-secondary"> اجمالي النقل</h6>
        <h4 class="fw-bold text-secondary">{{ number_format($transportTotal, 2) }} ر.ع</h4>
      </div>
    </div>
  </div>

  {{-- ====== التحليلات الشهرية ====== --}}
  <div class="row g-4">
    <div class="col-lg-6">
      <div class="card shadow-sm border-0">
        <div class="card-header bg-light fw-semibold"><i class="bi bi-graph-up-arrow me-2 text-orange"></i> مقارنة الإيرادات والمصروفات الشهرية</div>
        <div class="card-body"><canvas id="financeChart" height="160"></canvas></div>
      </div>
    </div>

    <div class="col-lg-6">
      <div class="card shadow-sm border-0">
        <div class="card-header bg-light fw-semibold"><i class="bi bi-pie-chart me-2 text-orange"></i> توزيع المصروفات حسب الفئة</div>
        <div class="card-body"><canvas id="expenseChart" height="160"></canvas></div>
      </div>
    </div>
  </div>

  {{-- ====== الرسوم السنوية ====== --}}
  <div class="row g-4 mt-4">
    <div class="col-lg-6">
      <div class="card shadow-sm border-0">
        <div class="card-header bg-light fw-semibold"><i class="bi bi-bar-chart-line text-primary me-2"></i> إجمالي المصروفات السنوية</div>
        <div class="card-body"><canvas id="yearlyExpensesBar" height="160"></canvas></div>
      </div>
    </div>

    <div class="col-lg-6">
      <div class="card shadow-sm border-0">
        <div class="card-header bg-light fw-semibold"><i class="bi bi-graph-up text-success me-2"></i> تطور المصروفات على مدار السنوات</div>
        <div class="card-body"><canvas id="yearlyExpensesLine" height="160"></canvas></div>
      </div>
    </div>
  </div>

  {{-- ====== آخر العمليات المالية ====== --}}
  <div class="row g-4 mt-4">
    <div class="col-12">
      <div class="card shadow-sm border-0">
        <div class="card-header bg-light fw-semibold d-flex justify-content-between align-items-center">
          <span><i class="bi bi-calendar3 me-2 text-primary"></i> آخر العمليات المالية</span>
          <small class="text-muted">عرض مختصر لآخر السجلات</small>
        </div>
        <div class="card-body p-0">
          <table class="table table-hover align-middle text-center m-0">
            <thead class="table-light">
              <tr>
                <th>#</th>
                <th>النوع</th>
                <th>الوصف</th>
                <th>المبلغ (ر.ع)</th>
                <th>التاريخ</th>
              </tr>
            </thead>
            <tbody>
              @forelse ($recentTransactions as $index => $t)
                <tr>
                  <td>{{ $index + 1 }}</td>
                  <td>
                    @if ($t['type'] == 'إيراد')
                      <span class="badge bg-success">إيراد</span>
                    @else
                      <span class="badge bg-danger">مصروف</span>
                    @endif
                  </td>
                  <td>{{ $t['name'] }}</td>
                  <td>{{ number_format($t['amount'], 2) }}</td>
                  <td>{{ \Carbon\Carbon::parse($t['date'])->format('Y-m-d') }}</td>
                </tr>
              @empty
                <tr><td colspan="5" class="text-muted">لا توجد بيانات حالياً</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- ====== الرسوم البيانية ====== --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  new Chart(document.getElementById('financeChart'), {
    type: 'bar',
    data: {
      labels: @json($months),
      datasets: [
        { label: 'الإيرادات', data: @json($monthlyRevenues), backgroundColor: 'rgba(0,107,143,0.8)' },
        { label: 'المصروفات', data: @json($monthlyExpenses), backgroundColor: 'rgba(241,139,34,0.8)' }
      ]
    },
    options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
  });

  new Chart(document.getElementById('expenseChart'), {
    type: 'doughnut',
    data: {
      labels: @json($expenseCategories->keys()),
      datasets: [{ data: @json($expenseCategories->values()), backgroundColor: ['#f18b22','#006b8f','#ffc107','#4bc0c0','#9c27b0'] }]
    },
    options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
  });

  new Chart(document.getElementById('yearlyExpensesBar'), {
    type: 'bar',
    data: { labels: @json($chartYears), datasets: [{ label: 'إجمالي المصروفات', data: @json($chartTotals), backgroundColor: 'rgba(0,86,179,0.7)' }] },
    options: { responsive: true, scales: { y: { beginAtZero: true } } }
  });

  new Chart(document.getElementById('yearlyExpensesLine'), {
    type: 'line',
    data: { labels: @json($chartYears), datasets: [{ label: 'إجمالي المصروفات', data: @json($chartTotals), borderColor: '#00b35a', backgroundColor: 'rgba(0,179,90,0.2)', tension: 0.3, fill: true }] },
    options: { responsive: true, scales: { y: { beginAtZero: true } } }
  });
</script>

@endsection
