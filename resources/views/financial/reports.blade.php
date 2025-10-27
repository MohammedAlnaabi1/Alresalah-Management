@extends('financial.layout')

@section('title', 'التقارير المالية')

@section('content')

<div class="container-fluid">
  {{-- ===== العنوان ===== --}}
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold text-secondary mb-0">
      <i class="bi bi-graph-up-arrow me-2 text-primary"></i> التقارير والتحليل المالي
    </h5>
    <div>
      <a href="{{ route('financial.reports.exportPDF', request()->all()) }}" class="btn btn-outline-primary btn-sm">
        <i class="bi bi-file-earmark-pdf me-1"></i> تصدير PDF
      </a>
      <a href="{{ route('financial.reports.exportExcel', request()->all()) }}" class="btn btn-outline-success btn-sm">
        <i class="bi bi-file-earmark-excel me-1"></i> تصدير Excel
      </a>
    </div>
  </div>

  {{-- ===== نموذج الفلترة ===== --}}
  <form action="{{ route('financial.reports.filter') }}" method="GET" class="row g-3 bg-light p-3 rounded mb-4 shadow-sm">
    <div class="col-md-3">
      <label class="form-label">من تاريخ</label>
      <input type="date" name="from" value="{{ $from ?? '' }}" class="form-control" required>
    </div>
    <div class="col-md-3">
      <label class="form-label">إلى تاريخ</label>
      <input type="date" name="to" value="{{ $to ?? '' }}" class="form-control" required>
    </div>
    <div class="col-md-3">
      <label class="form-label">النوع</label>
      <select name="type" class="form-select">
        <option value="all" {{ ($type ?? '') == 'all' ? 'selected' : '' }}>الكل</option>
        <option value="revenues" {{ ($type ?? '') == 'revenues' ? 'selected' : '' }}>إيرادات</option>
        <option value="expenses" {{ ($type ?? '') == 'expenses' ? 'selected' : '' }}>مصروفات</option>
        <option value="bus" {{ ($type ?? '') == 'bus' ? 'selected' : '' }}>مصروفات الحافلات فقط</option>
      </select>
    </div>
    <div class="col-md-3 d-flex align-items-end">
      <button type="submit" class="btn btn-primary w-100">
        <i class="bi bi-funnel me-1"></i> تطبيق الفلتر
      </button>
    </div>
  </form>

  {{-- ===== بطاقات الإحصاءات ===== --}}
  <div class="row g-3 mb-4">
    <div class="col-md-3">
      <div class="card text-center shadow-sm border-0">
        <div class="card-body">
          <h6 class="text-muted">إجمالي الإيرادات</h6>
          <h4 class="fw-bold text-success">{{ number_format($totalRevenues ?? 0, 2) }} ر.ع</h4>
        </div>
      </div>
    </div>

    <div class="col-md-3">
      <div class="card text-center shadow-sm border-0">
        <div class="card-body">
          <h6 class="text-muted">إجمالي المصروفات</h6>
          <h4 class="fw-bold text-danger">{{ number_format($totalExpenses ?? 0, 2) }} ر.ع</h4>
        </div>
      </div>
    </div>

    <div class="col-md-3">
      <div class="card text-center shadow-sm border-0">
        <div class="card-body">
          <h6 class="text-muted">صافي الربح / العجز</h6>
          <h4 class="fw-bold {{ ($netBalance ?? 0) >= 0 ? 'text-success' : 'text-danger' }}">
            {{ number_format($netBalance ?? 0, 2) }} ر.ع
          </h4>
        </div>
      </div>
    </div>

    <div class="col-md-3">
      <div class="card text-center shadow-sm border-0">
        <div class="card-body">
          <h6 class="text-muted">نسبة مصروفات الحافلات</h6>
          <h4 class="fw-bold text-warning">
            {{ ($totalExpenses ?? 0) > 0 ? round((($busExpenses ?? 0) / $totalExpenses) * 100, 1) : 0 }}%
          </h4>
        </div>
      </div>
    </div>
  </div>

  {{-- ===== الرسوم البيانية ===== --}}
  <div class="row g-4">
    <div class="col-lg-6">
      <div class="card shadow-sm border-0">
        <div class="card-header bg-light fw-semibold">
          <i class="bi bi-bar-chart-line me-1 text-primary"></i> الإيرادات والمصروفات الشهرية
        </div>
        <div class="card-body">
          <canvas id="monthlyChart" height="160"></canvas>
        </div>
      </div>
    </div>

    <div class="col-lg-6">
      <div class="card shadow-sm border-0">
        <div class="card-header bg-light fw-semibold">
          <i class="bi bi-pie-chart me-1 text-warning"></i> توزيع المصروفات حسب الفئة
        </div>
        <div class="card-body">
          <canvas id="categoryChart" height="160"></canvas>
        </div>
      </div>
    </div>
  </div>

  {{-- ===== الجدول التفصيلي ===== --}}
  <div class="card shadow-sm border-0 mt-4">
    <div class="card-header bg-light fw-semibold">
      <i class="bi bi-list-ul me-1 text-secondary"></i> تفاصيل العمليات المالية
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
          @forelse($transactions ?? [] as $index => $t)
            <tr>
              <td>{{ $index + 1 }}</td>
              <td>
                @if($t['type'] == 'إيراد')
                  <span class="badge bg-success">إيراد</span>
                @else
                  <span class="badge bg-danger">مصروف</span>
                @endif
              </td>
              <td>{{ $t['name'] }}</td>
              <td class="{{ $t['type'] == 'إيراد' ? 'text-success' : 'text-danger' }}">
                {{ number_format($t['amount'], 2) }}
              </td>
              <td>{{ \Carbon\Carbon::parse($t['date'])->format('Y-m-d') }}</td>
            </tr>
          @empty
            <tr><td colspan="5" class="text-muted py-3">لا توجد بيانات للفترة المحددة</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>

{{-- ===== Chart.js ===== --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  // 🔹 الرسم الشهري
  const monthlyCtx = document.getElementById('monthlyChart');
  new Chart(monthlyCtx, {
    type: 'bar',
    data: {
      labels: @json($months ?? []),
      datasets: [
        {
          label: 'الإيرادات',
          data: @json($monthlyRevenues ?? []),
          backgroundColor: '#43a047',
        },
        {
          label: 'المصروفات',
          data: @json($monthlyExpenses ?? []),
          backgroundColor: '#e53935',
        }
      ]
    },
    options: {
      responsive: true,
      plugins: { legend: { position: 'bottom' } }
    }
  });

  // 🔹 الرسم الدائري حسب الفئات
  const categoryCtx = document.getElementById('categoryChart');
  new Chart(categoryCtx, {
    type: 'doughnut',
    data: {
      labels: @json($expenseCategories->keys() ?? []),
      datasets: [{
        data: @json($expenseCategories->values() ?? []),
        backgroundColor: ['#ffb300', '#f57c00', '#ef5350', '#8e24aa', '#29b6f6']
      }]
    },
    options: {
      responsive: true,
      plugins: { legend: { position: 'bottom' } }
    }
  });
</script>

@endsection
