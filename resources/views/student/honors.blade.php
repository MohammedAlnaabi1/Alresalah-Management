@extends('student.layout')

@section('title', 'تكريم المتفوقين')

@section('content')

{{-- Points System Explanation --}}
<div class="alert alert-info border-0 shadow-sm mb-4">
  <h6 class="fw-bold mb-2"><i class="bi bi-info-circle me-2"></i> نظام احتساب النقاط</h6>
  <div class="row">
    <div class="col-md-4">
      <strong>معدل الدرجات (80%)</strong> — متوسط النسبة المئوية لجميع المواد
    </div>
    <div class="col-md-4">
      <strong>نسبة الحضور (20%)</strong> — نسبة أيام الحضور من إجمالي الأيام
    </div>
    <div class="col-md-4">
      <strong>نقاط المسؤول</strong> — نقاط إضافية يدخلها المسؤول يدوياً
    </div>
  </div>
  <hr class="my-2">
  <small><strong>إجمالي النقاط</strong> = (معدل الدرجات × 0.8) + (نسبة الحضور × 0.2) + نقاط المسؤول</small>
</div>

{{-- Filter Bar --}}
<div class="card p-3 mb-4 border-0 shadow-sm">
  <form method="GET" action="{{ route('student.honors') }}" class="row g-2 align-items-end">
    <div class="col-md-3">
      <label class="form-label">الفصل الدراسي</label>
      <select name="semester" class="form-select">
        <option value="الأول" {{ $semester == 'الأول' ? 'selected' : '' }}>الأول</option>
        <option value="الثاني" {{ $semester == 'الثاني' ? 'selected' : '' }}>الثاني</option>
      </select>
    </div>
    <div class="col-md-3">
      <label class="form-label">العام الدراسي</label>
      <select name="academic_year" class="form-select">
        <option value="">الكل</option>
        @foreach($academicYears as $y)
          <option value="{{ $y }}" {{ $academicYear == $y ? 'selected' : '' }}>{{ $y }}</option>
        @endforeach
      </select>
    </div>
    <div class="col-md-2">
      <label class="form-label">الصف</label>
      <select name="grade" class="form-select">
        <option value="">كل الصفوف</option>
        @foreach($gradesList as $g)
          <option value="{{ $g }}" {{ $gradeFilter == $g ? 'selected' : '' }}>{{ $g }}</option>
        @endforeach
      </select>
    </div>
    <div class="col-md-2">
      <button class="btn btn-warning w-100"><i class="bi bi-funnel me-1"></i> عرض النتائج</button>
    </div>
    <div class="col-md-2">
      <a href="{{ route('student.points', ['semester' => $semester, 'academic_year' => $academicYear, 'grade' => $gradeFilter]) }}" class="btn btn-outline-primary w-100">
        <i class="bi bi-pencil-square me-1"></i> إدخال النقاط
      </a>
    </div>
  </form>
</div>

{{-- Top 3 Podium --}}
@if($rankings->count() >= 3)
<div class="row text-center mb-4">
  <div class="col-md-4 mb-3">
    <div class="card border-0 shadow-sm p-4" style="border-top: 4px solid #adb5bd !important;">
      <div class="mb-2"><span style="font-size: 2.5rem;">🥈</span></div>
      <h5 class="fw-bold">{{ $rankings[1]['student']->name }}</h5>
      <p class="text-muted mb-1">{{ $rankings[1]['student']->grade }} - {{ $rankings[1]['student']->class_name }}</p>
      <h3 class="text-secondary">{{ $rankings[1]['total_points'] }}</h3>
      <small class="text-muted">نقطة</small>
    </div>
  </div>
  <div class="col-md-4 mb-3">
    <div class="card border-0 shadow p-4" style="border-top: 4px solid #ffc107 !important; transform: scale(1.05);">
      <div class="mb-2"><span style="font-size: 2.5rem;">🥇</span></div>
      <h5 class="fw-bold">{{ $rankings[0]['student']->name }}</h5>
      <p class="text-muted mb-1">{{ $rankings[0]['student']->grade }} - {{ $rankings[0]['student']->class_name }}</p>
      <h3 class="text-warning">{{ $rankings[0]['total_points'] }}</h3>
      <small class="text-muted">نقطة</small>
    </div>
  </div>
  <div class="col-md-4 mb-3">
    <div class="card border-0 shadow-sm p-4" style="border-top: 4px solid #cd7f32 !important;">
      <div class="mb-2"><span style="font-size: 2.5rem;">🥉</span></div>
      <h5 class="fw-bold">{{ $rankings[2]['student']->name }}</h5>
      <p class="text-muted mb-1">{{ $rankings[2]['student']->grade }} - {{ $rankings[2]['student']->class_name }}</p>
      <h3 style="color: #cd7f32;">{{ $rankings[2]['total_points'] }}</h3>
      <small class="text-muted">نقطة</small>
    </div>
  </div>
</div>
@endif

{{-- Full Rankings Table --}}
<div class="card border-0 shadow-sm">
  <div class="card-header bg-white d-flex justify-content-between align-items-center">
    <h6 class="fw-bold mb-0"><i class="bi bi-list-ol me-2"></i> ترتيب الطلاب</h6>
    <span class="badge bg-primary">{{ $rankings->count() }} طالب</span>
  </div>
  <div class="table-responsive">
    <table class="table table-hover align-middle text-center mb-0">
      <thead>
        <tr>
          <th>الترتيب</th>
          <th>اسم الطالب</th>
          <th>الصف</th>
          <th>عدد المواد</th>
          <th>معدل الدرجات</th>
          <th>نسبة الحضور</th>
          <th>نقاط المسؤول</th>
          <th>إجمالي النقاط</th>
          <th>التقدير</th>
        </tr>
      </thead>
      <tbody>
        @forelse($rankings as $index => $r)
          @php
            if ($r['total_points'] >= 90) { $ratingBadge = 'bg-success'; $ratingLabel = 'ممتاز'; }
            elseif ($r['total_points'] >= 80) { $ratingBadge = 'bg-primary'; $ratingLabel = 'جيد جداً'; }
            elseif ($r['total_points'] >= 70) { $ratingBadge = 'bg-info'; $ratingLabel = 'جيد'; }
            elseif ($r['total_points'] >= 60) { $ratingBadge = 'bg-warning'; $ratingLabel = 'مقبول'; }
            else { $ratingBadge = 'bg-danger'; $ratingLabel = 'ضعيف'; }
          @endphp
          <tr class="{{ $index < 3 ? 'table-warning' : '' }}">
            <td>
              @if($index == 0) 🥇
              @elseif($index == 1) 🥈
              @elseif($index == 2) 🥉
              @else {{ $index + 1 }}
              @endif
            </td>
            <td class="fw-bold">{{ $r['student']->name }}</td>
            <td>{{ $r['student']->grade }} {{ $r['student']->class_name }}</td>
            <td>{{ $r['subjects_count'] }}</td>
            <td>
              <span class="badge {{ $r['grade_avg'] >= 90 ? 'bg-success' : ($r['grade_avg'] >= 70 ? 'bg-primary' : 'bg-warning') }}">
                {{ $r['grade_avg'] }}%
              </span>
            </td>
            <td>
              <span class="badge {{ $r['attendance_rate'] >= 90 ? 'bg-success' : ($r['attendance_rate'] >= 70 ? 'bg-primary' : 'bg-danger') }}">
                {{ $r['attendance_rate'] }}%
              </span>
            </td>
            <td>
              @if($r['manual_points'] > 0)
                <span class="badge bg-info">+{{ $r['manual_points'] }}</span>
              @else
                <span class="text-muted">0</span>
              @endif
            </td>
            <td><strong>{{ $r['total_points'] }}</strong></td>
            <td><span class="badge {{ $ratingBadge }}">{{ $ratingLabel }}</span></td>
          </tr>
        @empty
          <tr><td colspan="9" class="text-muted py-4">لا توجد بيانات لعرض الترتيب</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

@endsection
