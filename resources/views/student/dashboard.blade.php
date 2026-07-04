@extends('student.layout')

@section('title', 'لوحة معلومات الطلاب')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

{{-- KPI Cards --}}
<div class="row text-center mb-4">
  <div class="col-md-3 mb-3">
    <div class="card p-3 border-0 shadow-sm">
      <h6 class="text-muted">إجمالي الطلاب</h6>
      <h3 class="text-primary">{{ $totalStudents }}</h3>
    </div>
  </div>
  <div class="col-md-3 mb-3">
    <div class="card p-3 border-0 shadow-sm">
      <h6 class="text-muted">الطلاب النشطون</h6>
      <h3 class="text-success">{{ $activeStudents }}</h3>
    </div>
  </div>
  <div class="col-md-3 mb-3">
    <div class="card p-3 border-0 shadow-sm">
      <h6 class="text-muted">نسبة الحضور اليوم</h6>
      <h3 class="text-warning">{{ $attendanceRate }}%</h3>
    </div>
  </div>
  <div class="col-md-3 mb-3">
    <div class="card p-3 border-0 shadow-sm">
      <h6 class="text-muted">ذكور / إناث</h6>
      <h3><span class="text-info">{{ $maleCount }}</span> / <span class="text-danger">{{ $femaleCount }}</span></h3>
    </div>
  </div>
</div>

{{-- Today Attendance Summary --}}
<div class="row text-center mb-4">
  <div class="col-md-4 mb-3">
    <div class="card p-3 border-0 shadow-sm bg-success bg-opacity-10">
      <h6>حاضر اليوم</h6>
      <h3 class="text-success">{{ $todayPresent }}</h3>
    </div>
  </div>
  <div class="col-md-4 mb-3">
    <div class="card p-3 border-0 shadow-sm bg-danger bg-opacity-10">
      <h6>غائب اليوم</h6>
      <h3 class="text-danger">{{ $todayAbsent }}</h3>
    </div>
  </div>
  <div class="col-md-4 mb-3">
    <div class="card p-3 border-0 shadow-sm bg-warning bg-opacity-10">
      <h6>متأخر اليوم</h6>
      <h3 class="text-warning">{{ $todayLate }}</h3>
    </div>
  </div>
</div>

{{-- Charts Row --}}
<div class="row mb-4">
  <div class="col-md-6 mb-3">
    <div class="card p-4 border-0 shadow-sm">
      <h6 class="fw-bold mb-3"><i class="bi bi-bar-chart me-2"></i> الطلاب حسب الصف</h6>
      <canvas id="gradeChart" height="250"></canvas>
    </div>
  </div>
  <div class="col-md-6 mb-3">
    <div class="card p-4 border-0 shadow-sm">
      <h6 class="fw-bold mb-3"><i class="bi bi-pie-chart me-2"></i> التوزيع حسب الجنس</h6>
      <canvas id="genderChart" height="250"></canvas>
    </div>
  </div>
</div>

{{-- Attendance Trend --}}
<div class="card p-4 border-0 shadow-sm mb-4">
  <h6 class="fw-bold mb-3"><i class="bi bi-graph-up me-2"></i> نسبة الحضور - آخر 30 يوم</h6>
  <canvas id="attendanceChart" height="120"></canvas>
</div>

{{-- Recent Students --}}
<div class="card p-4 border-0 shadow-sm">
  <h6 class="fw-bold mb-3"><i class="bi bi-person-plus me-2"></i> آخر الطلاب المسجلين</h6>
  <div class="table-responsive">
    <table class="table table-hover text-center">
      <thead><tr><th>الاسم</th><th>الصف</th><th>الفصل</th><th>تاريخ التسجيل</th></tr></thead>
      <tbody>
        @forelse($recentStudents as $s)
          <tr>
            <td>{{ $s->name }}</td>
            <td>{{ $s->grade }}</td>
            <td>{{ $s->class_name }}</td>
            <td>{{ $s->enrollment_date->format('Y-m-d') }}</td>
          </tr>
        @empty
          <tr><td colspan="4" class="text-muted">لا يوجد طلاب مسجلين بعد</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection

@section('scripts')
<script>
  // Students per grade bar chart
  new Chart(document.getElementById('gradeChart'), {
    type: 'bar',
    data: {
      labels: {!! json_encode($studentsPerGrade->keys()) !!},
      datasets: [{
        label: 'عدد الطلاب',
        data: {!! json_encode($studentsPerGrade->values()) !!},
        backgroundColor: 'rgba(245, 124, 0, 0.6)',
        borderColor: '#f57c00',
        borderWidth: 1
      }]
    },
    options: { responsive: true, scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } } }
  });

  // Gender doughnut chart
  new Chart(document.getElementById('genderChart'), {
    type: 'doughnut',
    data: {
      labels: ['ذكور', 'إناث'],
      datasets: [{
        data: [{{ $maleCount }}, {{ $femaleCount }}],
        backgroundColor: ['rgba(13,202,240,0.7)', 'rgba(220,53,69,0.7)']
      }]
    },
    options: { responsive: true }
  });

  // Attendance trend line chart
  new Chart(document.getElementById('attendanceChart'), {
    type: 'line',
    data: {
      labels: {!! json_encode($trendLabels) !!},
      datasets: [{
        label: 'نسبة الحضور %',
        data: {!! json_encode($attendanceTrend) !!},
        borderColor: '#198754',
        backgroundColor: 'rgba(25,135,84,0.1)',
        fill: true,
        tension: 0.3
      }]
    },
    options: { responsive: true, scales: { y: { beginAtZero: true, max: 100 } } }
  });
</script>
@endsection
