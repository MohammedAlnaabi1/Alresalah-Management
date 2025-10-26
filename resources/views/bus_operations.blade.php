@extends('layouts.bus_sidebar')

@section('title', 'الصيانة واستهلاك الوقود - الحافلات')

@section('content')
<div class="container-fluid p-4">

  {{-- ====== العنوان ====== --}}
  <div class="text-center mb-4">
    <h4 class="fw-bold text-secondary">
      <i class="bi bi-gear-wide-connected text-warning me-2"></i>
      الصيانة واستهلاك الوقود للحافلات
    </h4>
  </div>

  {{-- ====== البطاقات العلوية ====== --}}
  <div class="row g-4 mb-4">
    <div class="col-md-6">
      <div class="card p-4 text-center shadow-sm">
        <div class="card-icon mx-auto mb-3"><i class="bi bi-tools fs-3 text-warning"></i></div>
        <h6>إجمالي مصروفات الصيانة السنوية</h6>
        <h3 class="text-warning">{{ number_format($yearlyMaintenanceExpense, 3) }} ر.ع</h3>
        <small>{{ date('Y') }}</small>
      </div>
    </div>

    <div class="col-md-6">
      <div class="card p-4 text-center shadow-sm">
        <div class="card-icon mx-auto mb-3"><i class="bi bi-fuel-pump fs-3 text-info"></i></div>
        <h6>إجمالي استهلاك الوقود الشهري</h6>
        <h3 class="text-info">{{ number_format($monthlyFuelExpense, 3) }} ر.ع</h3>
        <small>{{ \Carbon\Carbon::now()->translatedFormat('F Y') }}</small>
      </div>
    </div>
  </div>

  {{-- ====== الفلاتر ====== --}}
  <form method="GET" action="{{ route('bus.operations') }}" class="row g-3 mb-4 p-3 bg-light rounded shadow-sm">
    <div class="col-md-3">
      <label class="form-label">من تاريخ</label>
      <input type="date" name="from_date" value="{{ request('from_date') }}" class="form-control">
    </div>
    <div class="col-md-3">
      <label class="form-label">إلى تاريخ</label>
      <input type="date" name="to_date" value="{{ request('to_date') }}" class="form-control">
    </div>
    <div class="col-md-3">
      <label class="form-label">اختر الحافلة</label>
      <select name="bus_id" class="form-select">
        <option value="">جميع الحافلات</option>
        @foreach($buses as $bus)
          <option value="{{ $bus->id }}" {{ request('bus_id') == $bus->id ? 'selected' : '' }}>
            {{ $bus->bus_number }}
          </option>
        @endforeach
      </select>
    </div>
    <div class="col-md-3 d-flex align-items-end">
      <button class="btn btn-primary w-100"><i class="bi bi-search me-2"></i>بحث</button>
    </div>
  </form>

  {{-- ====== قسم الصيانة ====== --}}
  <div class="card p-4 shadow-sm mb-4">
    <h6 class="mb-3"><i class="bi bi-tools me-2 text-warning"></i> سجل الصيانة</h6>
    <div class="table-responsive">
      <table class="table table-striped text-center align-middle">
        <thead class="table-warning">
          <tr>
            <th>رقم الحافلة</th>
            <th>نوع الصيانة</th>
            <th>المبلغ (ر.ع)</th>
            <th>التاريخ</th>
            <th>ملاحظات</th>
          </tr>
        </thead>
        <tbody>
          @forelse($maintenanceExpenses as $exp)
            <tr>
              <td>{{ $exp->bus->bus_number }}</td>
              <td>{{ $exp->expense_type }}</td>
              <td>{{ number_format($exp->amount, 3) }}</td>
              <td>{{ $exp->expense_date }}</td>
              <td>{{ $exp->description ?? '-' }}</td>
            </tr>
          @empty
            <tr><td colspan="5" class="text-muted">لا توجد عمليات صيانة مسجلة</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  {{-- ====== قسم الوقود ====== --}}
  <div class="card p-4 shadow-sm">
    <h6 class="mb-3"><i class="bi bi-fuel-pump me-2 text-info"></i> سجل استهلاك الوقود</h6>
    <div class="table-responsive">
      <table class="table table-striped text-center align-middle">
        <thead class="table-info">
          <tr>
            <th>رقم الحافلة</th>
            <th>نوع المصروف</th>
            <th>المبلغ (ر.ع)</th>
            <th>التاريخ</th>
            <th>ملاحظات</th>
          </tr>
        </thead>
        <tbody>
          @forelse($fuelExpenses as $exp)
            <tr>
              <td>{{ $exp->bus->bus_number }}</td>
              <td>{{ $exp->expense_type }}</td>
              <td>{{ number_format($exp->amount, 3) }}</td>
              <td>{{ $exp->expense_date }}</td>
              <td>{{ $exp->description ?? '-' }}</td>
            </tr>
          @empty
            <tr><td colspan="5" class="text-muted">لا توجد عمليات وقود مسجلة</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

</div>

<footer class="text-center mt-4 text-muted">
  © 2025 مدرسة الرسالة - نظام إدارة الحافلات
</footer>
@endsection
