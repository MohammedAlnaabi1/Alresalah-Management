@extends('student.layout')

@section('title', 'الدرجات')

@section('content')

{{-- Filter Bar --}}
<div class="card p-4 mb-4 border-0 shadow-sm">
  <h6 class="fw-bold mb-3"><i class="bi bi-funnel me-2"></i> اختر البيانات</h6>
  <div class="row g-3 align-items-end">
    <div class="col-md-4">
      <label class="form-label fw-bold">المجموعة</label>
      <select id="groupSelect" class="form-select" onchange="applyFilter()">
        <option value="">-- اختر المجموعة --</option>
        @foreach($groups as $g)
          <option value="{{ $g }}" {{ $group == $g ? 'selected' : '' }}>{{ $g }}</option>
        @endforeach
      </select>
    </div>
    <div class="col-md-4">
      <label class="form-label fw-bold">العام الدراسي</label>
      <select id="yearSelect" class="form-select" onchange="applyFilter()">
        @foreach($academicYears as $y)
          <option value="{{ $y }}" {{ $academicYear == $y ? 'selected' : '' }}>{{ $y }}</option>
        @endforeach
      </select>
    </div>
    <div class="col-md-4">
      <div class="d-flex gap-2">
        <a href="{{ route('student.grades.exportPDF', ['group' => $group, 'academic_year' => $academicYear]) }}" class="btn btn-outline-danger flex-fill {{ !$group ? 'disabled' : '' }}">
          <i class="bi bi-file-earmark-pdf me-1"></i> PDF
        </a>
        <a href="{{ route('student.grades.exportExcel', ['group' => $group, 'academic_year' => $academicYear]) }}" class="btn btn-outline-success flex-fill {{ !$group ? 'disabled' : '' }}">
          <i class="bi bi-file-earmark-spreadsheet me-1"></i> Excel
        </a>
      </div>
    </div>
  </div>
</div>

@if($group)

  {{-- Grades Table --}}
  <form method="POST" action="{{ route('student.grades.store') }}">
    @csrf
    <input type="hidden" name="academic_year" value="{{ $academicYear }}">
    <input type="hidden" name="group" value="{{ $group }}">

    <div class="card border-0 shadow-sm">
      <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h6 class="fw-bold mb-0">
          <i class="bi bi-journal-bookmark me-2"></i> درجات {{ $group }} — {{ $academicYear }}
        </h6>
        <div>
          <span class="badge bg-primary me-2">{{ $students->count() }} طالب</span>
          <span class="text-muted" id="saveStatus" style="font-size: 0.85rem;"></span>
        </div>
      </div>
      <div class="table-responsive">
        <table class="table table-hover align-middle text-center mb-0">
          <thead>
            <tr>
              <th>#</th>
              <th>اسم الطالب</th>
              <th>الفصل</th>
              @foreach($subjects as $sub)
                <th style="width: 100px;">{{ $sub }}<br><small class="text-muted">/ {{ $maxGrade }}</small></th>
              @endforeach
              <th>المجموع</th>
              <th>النسبة</th>
            </tr>
          </thead>
          <tbody>
            @forelse($students as $index => $student)
              @php
                $totalGrade = 0;
                $totalMax = 0;
                foreach ($subjects as $sub) {
                    $existing = $existingGrades[$student->id][$sub] ?? null;
                    if ($existing) {
                        $totalGrade += $existing->grade_value;
                        $totalMax += $existing->max_grade;
                    } else {
                        $totalMax += $maxGrade;
                    }
                }
                $pct = $totalMax > 0 ? round(($totalGrade / $totalMax) * 100, 1) : 0;
              @endphp
              <tr>
                <td>{{ $index + 1 }}</td>
                <td class="fw-bold">{{ $student->name }}</td>
                <td>{{ $student->class_name }}</td>
                @foreach($subjects as $sub)
                  @php $existing = $existingGrades[$student->id][$sub] ?? null; @endphp
                  <td>
                    <input type="number"
                      name="grades[{{ $student->id }}][{{ $sub }}]"
                      class="form-control form-control-sm text-center grade-input"
                      data-student="{{ $student->id }}"
                      value="{{ $existing ? $existing->grade_value : '' }}"
                      placeholder="-"
                      step="0.01" min="0" max="{{ $maxGrade }}">
                  </td>
                @endforeach
                <td class="fw-bold total-cell" id="total-{{ $student->id }}">{{ $totalGrade }}</td>
                <td>
                  <span class="badge pct-badge {{ $pct >= 90 ? 'bg-success' : ($pct >= 70 ? 'bg-primary' : ($pct >= 50 ? 'bg-warning' : 'bg-danger')) }}"
                    id="pct-{{ $student->id }}">{{ $pct }}%</span>
                </td>
              </tr>
            @empty
              <tr><td colspan="{{ 5 + count($subjects) }}" class="text-muted py-4">لا يوجد طلاب في هذه المجموعة</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    @if($students->count() > 0)
    <div class="mt-3 text-center">
      <button type="submit" class="btn btn-success btn-lg">
        <i class="bi bi-check-circle me-1"></i> حفظ الدرجات
      </button>
    </div>
    @endif
  </form>

@else

  {{-- Empty State --}}
  <div class="text-center py-5">
    <i class="bi bi-journal-bookmark" style="font-size: 4rem; color: #ddd;"></i>
    <h5 class="text-muted mt-3">اختر المجموعة لعرض الطلاب وإدخال الدرجات</h5>
  </div>

@endif

@endsection

@section('scripts')
<script>
function applyFilter() {
  const group = document.getElementById('groupSelect').value;
  const year = document.getElementById('yearSelect').value;
  if (group) {
    window.location.href = `{{ route('student.grades') }}?group=${encodeURIComponent(group)}&academic_year=${encodeURIComponent(year)}`;
  }
}

document.querySelectorAll('.grade-input').forEach(input => {
  input.addEventListener('input', function() {
    const studentId = this.dataset.student;
    const inputs = document.querySelectorAll(`.grade-input[data-student="${studentId}"]`);
    let total = 0;
    let maxTotal = 0;
    const maxPerSubject = {{ $maxGrade }};

    inputs.forEach(inp => {
      const val = parseFloat(inp.value) || 0;
      total += val;
      maxTotal += maxPerSubject;
    });

    const pct = maxTotal > 0 ? Math.round((total / maxTotal) * 1000) / 10 : 0;

    document.getElementById('total-' + studentId).textContent = total;

    const badge = document.getElementById('pct-' + studentId);
    badge.textContent = pct + '%';
    badge.className = 'badge pct-badge ' +
      (pct >= 90 ? 'bg-success' : (pct >= 70 ? 'bg-primary' : (pct >= 50 ? 'bg-warning' : 'bg-danger')));
  });
});
</script>
@endsection
