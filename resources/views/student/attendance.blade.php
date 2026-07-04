@extends('student.layout')

@section('title', 'الحضور والغياب')

@section('content')

{{-- Filter Bar --}}
<div class="card p-4 mb-4 border-0 shadow-sm">
  <h6 class="fw-bold mb-3"><i class="bi bi-funnel me-2"></i> اختر البيانات</h6>
  <form method="GET" action="{{ route('student.attendance') }}" class="row g-3 align-items-end">
    <div class="col-md-3">
      <label class="form-label fw-bold">التاريخ</label>
      <input type="date" name="date" class="form-control" value="{{ $date }}">
    </div>
    <div class="col-md-3">
      <label class="form-label fw-bold">المجموعة</label>
      <select name="group" class="form-select" required>
        <option value="">-- اختر المجموعة --</option>
        @foreach($groups as $g)
          <option value="{{ $g }}" {{ $group == $g ? 'selected' : '' }}>{{ $g }}</option>
        @endforeach
      </select>
    </div>
    <div class="col-md-3">
      <label class="form-label fw-bold">المادة</label>
      <select name="subject" class="form-select" required>
        <option value="">-- اختر المادة --</option>
        @foreach($subjects as $s)
          <option value="{{ $s }}" {{ $subject == $s ? 'selected' : '' }}>{{ $s }}</option>
        @endforeach
      </select>
    </div>
    <div class="col-md-3">
      <button class="btn btn-warning w-100 btn-lg">
        <i class="bi bi-search me-1"></i> عرض الطلاب
      </button>
    </div>
  </form>
</div>

@if($group && $subject)

  {{-- Current Selection --}}
  <div class="alert alert-light border shadow-sm mb-4 d-flex justify-content-between align-items-center">
    <div>
      <i class="bi bi-bookmark-fill text-warning me-1"></i>
      <strong>{{ $subject }}</strong> —
      <span class="badge bg-secondary">{{ $group }}</span> —
      {{ \Carbon\Carbon::parse($date)->format('Y/m/d') }}
    </div>
    <div>
      <span class="badge bg-success me-1">حاضر: {{ $presentCount }}</span>
      <span class="badge bg-danger me-1">غائب: {{ $absentCount }}</span>
      <span class="badge bg-warning me-1">متأخر: {{ $lateCount }}</span>
      <a href="{{ route('student.attendance.exportPDF', ['date' => $date, 'subject' => $subject, 'group' => $group]) }}" class="btn btn-sm btn-outline-danger ms-2">
        <i class="bi bi-file-earmark-pdf"></i> PDF
      </a>
      <a href="{{ route('student.attendance.exportExcel', ['date' => $date, 'subject' => $subject, 'group' => $group]) }}" class="btn btn-sm btn-outline-success ms-1">
        <i class="bi bi-file-earmark-spreadsheet"></i> Excel
      </a>
    </div>
  </div>

  {{-- Attendance Form --}}
  @if($students->count() > 0)
  <form method="POST" action="{{ route('student.attendance.store') }}">
    @csrf
    <input type="hidden" name="date" value="{{ $date }}">
    <input type="hidden" name="subject" value="{{ $subject }}">
    <input type="hidden" name="group" value="{{ $group }}">

    <div class="card border-0 shadow-sm">
      <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h6 class="fw-bold mb-0">
          <i class="bi bi-people me-2"></i> طلاب {{ $group }} — {{ $subject }}
        </h6>
        <span class="badge bg-primary">{{ $students->count() }} طالب</span>
      </div>
      <div class="table-responsive">
        <table class="table table-hover align-middle text-center mb-0">
          <thead>
            <tr>
              <th>#</th>
              <th>اسم الطالب</th>
              <th>الفصل</th>
              <th>حاضر</th>
              <th>غائب</th>
              <th>متأخر</th>
              <th style="width: 200px;">ملاحظات</th>
            </tr>
          </thead>
          <tbody>
            @foreach($students as $index => $student)
              @php $currentStatus = $attendances[$student->id] ?? ''; @endphp
              <tr>
                <td>{{ $index + 1 }}</td>
                <td class="fw-bold">{{ $student->name }}</td>
                <td>{{ $student->class_name }}</td>
                <td>
                  <input type="radio" name="attendance[{{ $student->id }}][status]" value="حاضر"
                    class="form-check-input" {{ $currentStatus == 'حاضر' ? 'checked' : '' }} required>
                </td>
                <td>
                  <input type="radio" name="attendance[{{ $student->id }}][status]" value="غائب"
                    class="form-check-input" {{ $currentStatus == 'غائب' ? 'checked' : '' }}>
                </td>
                <td>
                  <input type="radio" name="attendance[{{ $student->id }}][status]" value="متأخر"
                    class="form-check-input" {{ $currentStatus == 'متأخر' ? 'checked' : '' }}>
                </td>
                <td>
                  <input type="text" name="attendance[{{ $student->id }}][notes]" class="form-control form-control-sm"
                    value="{{ $attendanceNotes[$student->id] ?? '' }}" placeholder="ملاحظة...">
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>

    <div class="mt-3 text-center">
      <button type="submit" class="btn btn-success btn-lg">
        <i class="bi bi-check-circle me-1"></i> حفظ الحضور
      </button>
      <button type="button" class="btn btn-outline-success ms-2" onclick="markAll('حاضر')">
        <i class="bi bi-check-all me-1"></i> الكل حاضر
      </button>
      <button type="button" class="btn btn-outline-danger ms-2" onclick="markAll('غائب')">
        <i class="bi bi-x-circle me-1"></i> الكل غائب
      </button>
      <button type="button" class="btn btn-outline-warning ms-2" onclick="markAll('متأخر')">
        <i class="bi bi-clock me-1"></i> الكل متأخر
      </button>
    </div>
  </form>
  @else
    <div class="alert alert-info text-center">لا يوجد طلاب في هذه المجموعة</div>
  @endif

  {{-- Quick Navigation --}}
  <div class="card p-3 mt-4 border-0 shadow-sm">
    <div class="d-flex justify-content-between align-items-center">
      <span class="text-muted"><i class="bi bi-arrow-left-right me-1"></i> انتقال سريع:</span>
      <div class="d-flex gap-2 flex-wrap">
        @foreach($subjects as $s)
          <a href="{{ route('student.attendance', ['date' => $date, 'group' => $group, 'subject' => $s]) }}"
            class="btn btn-sm {{ $s == $subject ? 'btn-warning' : 'btn-outline-secondary' }}">
            {{ $s }}
          </a>
        @endforeach
      </div>
      <a href="{{ route('student.attendance.report', ['group' => $group, 'subject' => $subject, 'to_date' => $date]) }}" class="btn btn-sm btn-info text-white">
        <i class="bi bi-calendar-range me-1"></i> كل الأيام
      </a>
    </div>
  </div>

@else

  {{-- Empty State --}}
  <div class="text-center py-5">
    <i class="bi bi-clipboard-check" style="font-size: 4rem; color: #ddd;"></i>
    <h5 class="text-muted mt-3">اختر المجموعة والمادة لعرض قائمة الطلاب</h5>
  </div>

@endif

@endsection

@section('scripts')
<script>
function markAll(status) {
  document.querySelectorAll(`input[type="radio"][value="${status}"]`).forEach(r => r.checked = true);
}
</script>
@endsection
