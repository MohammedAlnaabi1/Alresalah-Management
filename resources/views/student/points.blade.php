@extends('student.layout')

@section('title', 'إدخال النقاط')

@section('content')

{{-- Filter Bar --}}
<div class="card p-3 mb-4 border-0 shadow-sm">
  <h6 class="fw-bold mb-3"><i class="bi bi-funnel me-2"></i> اختر البيانات</h6>
  <form method="GET" action="{{ route('student.points') }}" class="row g-2 align-items-end">
    <div class="col-md-3">
      <label class="form-label fw-bold">العام الدراسي</label>
      <select name="academic_year" class="form-select" onchange="this.form.submit()">
        @foreach($academicYears as $y)
          <option value="{{ $y }}" {{ $academicYear == $y ? 'selected' : '' }}>{{ $y }}</option>
        @endforeach
      </select>
    </div>
    <div class="col-md-3">
      <label class="form-label fw-bold">المجموعة</label>
      <select name="group" class="form-select" onchange="this.form.submit()">
        <option value="">-- اختر المجموعة --</option>
        @foreach($groups as $g)
          <option value="{{ $g }}" {{ $group == $g ? 'selected' : '' }}>{{ $g }}</option>
        @endforeach
      </select>
    </div>
    <div class="col-md-3">
      <label class="form-label fw-bold">المادة</label>
      <select name="subject" class="form-select" onchange="this.form.submit()">
        <option value="">كل المواد</option>
        @foreach($allSubjects as $s)
          <option value="{{ $s }}" {{ $subjectFilter == $s ? 'selected' : '' }}>{{ $s }}</option>
        @endforeach
      </select>
    </div>
    <div class="col-md-3">
      <a href="{{ route('student.honors', ['academic_year' => $academicYear, 'grade' => $group]) }}" class="btn btn-outline-warning w-100">
        <i class="bi bi-trophy me-1"></i> عرض الترتيب
      </a>
    </div>
  </form>
</div>

@if($group && $allStudents->count() > 0)

<div class="card border-0 shadow-sm">
  <div class="card-header bg-white d-flex justify-content-between align-items-center">
    <h6 class="fw-bold mb-0">
      <i class="bi bi-pencil-square me-2"></i> نقاط {{ $group }}
      @if($subjectFilter)
        — {{ $subjectFilter }}
      @else
        — كل المواد
      @endif
    </h6>
    <div>
      <span class="badge bg-primary me-2">{{ $allStudents->count() }} طالب</span>
      <span id="saveStatus" class="text-muted" style="font-size: 0.85rem;"></span>
    </div>
  </div>
  <div class="card-body">
    <form id="pointsForm">
      <input type="hidden" name="semester" value="الأول">
      <input type="hidden" name="academic_year" value="{{ $academicYear }}">

      <div class="table-responsive">
        <table class="table table-hover align-middle text-center mb-0">
          <thead>
            <tr>
              <th>#</th>
              <th>اسم الطالب</th>
              <th>الصف</th>
              @foreach($subjects as $sub)
                <th style="width: 140px;">{{ $sub }}</th>
              @endforeach
              <th style="width: 80px;">المجموع</th>
            </tr>
          </thead>
          <tbody>
            @foreach($allStudents as $index => $student)
              @php
                $studentPts = $existingPoints[$student->id] ?? [];
                $totalPts = 0;
                foreach ($subjects as $sub) {
                    $totalPts += isset($studentPts[$sub]) ? (int)$studentPts[$sub]->points : 0;
                }
              @endphp
              <tr>
                <td>{{ $index + 1 }}</td>
                <td class="fw-bold">{{ $student->name }}</td>
                <td>{{ $student->class_name }}</td>
                @foreach($subjects as $sub)
                  @php $sp = $studentPts[$sub] ?? null; @endphp
                  <td>
                    <div class="input-group input-group-sm">
                      <button type="button" class="btn btn-outline-danger btn-sm btn-minus" data-target="pts-{{ $student->id }}-{{ $loop->index }}">−</button>
                      <input type="number" id="pts-{{ $student->id }}-{{ $loop->index }}"
                        name="points[{{ $student->id }}][{{ $sub }}]"
                        class="form-control form-control-sm text-center points-input"
                        data-student="{{ $student->id }}"
                        value="{{ $sp ? (int)$sp->points : 0 }}" step="1" min="0" max="100"
                        style="max-width: 55px;">
                      <button type="button" class="btn btn-outline-success btn-sm btn-plus" data-target="pts-{{ $student->id }}-{{ $loop->index }}">+</button>
                    </div>
                  </td>
                @endforeach
                <td>
                  <strong class="text-primary" id="total-{{ $student->id }}">{{ $totalPts }}</strong>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      <div class="text-center mt-3">
        <button type="submit" class="btn btn-success btn-lg" id="saveBtn">
          <i class="bi bi-check-circle me-1"></i> حفظ النقاط
        </button>
      </div>
    </form>
  </div>
</div>

@elseif(!$group)

  <div class="text-center py-5">
    <i class="bi bi-pencil-square" style="font-size: 4rem; color: #ddd;"></i>
    <h5 class="text-muted mt-3">اختر المجموعة لعرض الطلاب وإدخال النقاط</h5>
  </div>

@else
  <div class="alert alert-info text-center">لا يوجد طلاب نشطون في هذه المجموعة</div>
@endif

@endsection

@section('scripts')
<script>
function updateStudentTotal(studentId) {
  const inputs = document.querySelectorAll(`.points-input[data-student="${studentId}"]`);
  let total = 0;
  inputs.forEach(inp => { total += parseInt(inp.value) || 0; });
  document.getElementById('total-' + studentId).textContent = total;
}

document.querySelectorAll('.points-input').forEach(input => {
  input.addEventListener('input', function() { updateStudentTotal(this.dataset.student); });
});

document.querySelectorAll('.btn-plus').forEach(btn => {
  btn.addEventListener('click', function() {
    const input = document.getElementById(this.dataset.target);
    const max = parseInt(input.max) || 100;
    let val = parseInt(input.value) || 0;
    if (val < max) { input.value = val + 1; updateStudentTotal(input.dataset.student); }
  });
});

document.querySelectorAll('.btn-minus').forEach(btn => {
  btn.addEventListener('click', function() {
    const input = document.getElementById(this.dataset.target);
    let val = parseInt(input.value) || 0;
    if (val > 0) { input.value = val - 1; updateStudentTotal(input.dataset.student); }
  });
});

document.getElementById('pointsForm')?.addEventListener('submit', function(e) {
  e.preventDefault();

  const btn = document.getElementById('saveBtn');
  const status = document.getElementById('saveStatus');
  btn.disabled = true;
  btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> جاري الحفظ...';

  fetch('{{ route("student.points.store") }}', {
    method: 'POST',
    headers: {
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
      'Accept': 'application/json',
    },
    body: new FormData(this)
  })
  .then(res => res.ok ? res.json().catch(() => ({ success: true })) : Promise.reject())
  .then(data => {
    status.innerHTML = '<span class="text-success"><i class="bi bi-check-circle me-1"></i> تم الحفظ ✅</span>';
    btn.disabled = false;
    btn.innerHTML = '<i class="bi bi-check-circle me-1"></i> حفظ النقاط';
    setTimeout(() => status.textContent = '', 3000);
  })
  .catch(() => {
    status.innerHTML = '<span class="text-danger"><i class="bi bi-x-circle me-1"></i> حدث خطأ</span>';
    btn.disabled = false;
    btn.innerHTML = '<i class="bi bi-check-circle me-1"></i> حفظ النقاط';
  });
});
</script>
@endsection
