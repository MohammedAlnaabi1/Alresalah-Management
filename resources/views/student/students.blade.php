@extends('student.layout')

@section('title', 'بيانات الطلاب')

@section('content')

{{-- Search & Filter --}}
<div class="card p-3 mb-4 border-0 shadow-sm">
  <form method="GET" action="{{ route('student.index') }}" class="row g-2 align-items-end">
    <div class="col-md-4">
      <label class="form-label">بحث</label>
      <input type="text" name="search" class="form-control" placeholder="اسم الطالب أو ولي الأمر أو الهاتف..." value="{{ request('search') }}">
    </div>
    <div class="col-md-3">
      <label class="form-label">المجموعة</label>
      <select name="grade" class="form-select">
        <option value="">الكل</option>
        <option value="السنة الأولى" {{ request('grade') == 'السنة الأولى' ? 'selected' : '' }}>السنة الأولى</option>
        <option value="السنة الثانية" {{ request('grade') == 'السنة الثانية' ? 'selected' : '' }}>السنة الثانية</option>
      </select>
    </div>
    <div class="col-md-3">
      <label class="form-label">الحالة</label>
      <select name="status" class="form-select">
        <option value="">الكل</option>
        <option value="نشط" {{ request('status') == 'نشط' ? 'selected' : '' }}>نشط</option>
        <option value="منسحب" {{ request('status') == 'منسحب' ? 'selected' : '' }}>منسحب</option>
        <option value="منقول" {{ request('status') == 'منقول' ? 'selected' : '' }}>منقول</option>
      </select>
    </div>
    <div class="col-md-2">
      <button class="btn btn-warning w-100"><i class="bi bi-search me-1"></i> بحث</button>
    </div>
  </form>
</div>

{{-- Add Student Form --}}
<div class="card p-4 mb-4 border-0 shadow-sm">
  <h6 class="fw-bold mb-3"><i class="bi bi-person-plus me-2"></i> إضافة طالب جديد</h6>
  <form method="POST" action="{{ route('student.store') }}">
    @csrf
    <div class="row g-3">
      <div class="col-md-3">
        <label class="form-label">اسم الطالب</label>
        <input type="text" name="name" class="form-control" required>
      </div>
      <div class="col-md-2">
        <label class="form-label">المجموعة</label>
        <select name="grade" class="form-select" required>
          <option value="السنة الأولى">السنة الأولى</option>
          <option value="السنة الثانية">السنة الثانية</option>
        </select>
      </div>
      <div class="col-md-2">
        <label class="form-label">الصف</label>
        <select name="class_name" class="form-select" required>
          @for($i = 5; $i <= 12; $i++)
            <option value="{{ $i }}">{{ $i }}</option>
          @endfor
        </select>
      </div>
      <div class="col-md-2">
        <label class="form-label">تاريخ الميلاد</label>
        <input type="date" name="date_of_birth" class="form-control" required>
      </div>
      <div class="col-md-2">
        <label class="form-label">الجنس</label>
        <select name="gender" class="form-select" required>
          <option value="ذكر">ذكر</option>
          <option value="أنثى">أنثى</option>
        </select>
      </div>
      <div class="col-md-2">
        <label class="form-label">تاريخ التسجيل</label>
        <input type="date" name="enrollment_date" class="form-control" value="{{ date('Y-m-d') }}" required>
      </div>
      <div class="col-md-3">
        <label class="form-label">ولي الأمر</label>
        <input type="text" name="parent_name" class="form-control" required>
      </div>
      <div class="col-md-2">
        <label class="form-label">الهاتف</label>
        <input type="text" name="phone" class="form-control" required>
      </div>
      <div class="col-md-3">
        <label class="form-label">العنوان</label>
        <input type="text" name="address" class="form-control">
      </div>
      <div class="col-md-2">
        <label class="form-label">الحالة</label>
        <select name="status" class="form-select">
          <option value="نشط">نشط</option>
          <option value="منسحب">منسحب</option>
          <option value="منقول">منقول</option>
        </select>
      </div>
      <div class="col-md-12">
        <label class="form-label">ملاحظات</label>
        <input type="text" name="notes" class="form-control">
      </div>
      <div class="col-md-12">
        <button type="submit" class="btn btn-success"><i class="bi bi-plus-circle me-1"></i> إضافة الطالب</button>
      </div>
    </div>
  </form>
</div>

{{-- Export Buttons --}}
<div class="mb-3">
  <a href="{{ route('student.exportPDF', request()->query()) }}" class="btn btn-outline-danger btn-sm"><i class="bi bi-file-earmark-pdf me-1"></i> تصدير PDF</a>
  <a href="{{ route('student.exportExcel', request()->query()) }}" class="btn btn-outline-success btn-sm"><i class="bi bi-file-earmark-spreadsheet me-1"></i> تصدير Excel</a>
  <span class="text-muted ms-2">إجمالي: {{ $students->count() }} طالب</span>
</div>

{{-- Students Table --}}
<div class="card border-0 shadow-sm">
  <div class="table-responsive">
    <table class="table table-hover align-middle text-center mb-0">
      <thead>
        <tr>
          <th>#</th>
          <th>الاسم</th>
          <th>المجموعة</th>
          <th>الصف</th>
          <th>الجنس</th>
          <th>ولي الأمر</th>
          <th>الهاتف</th>
          <th>الحالة</th>
          <th>الإجراءات</th>
        </tr>
      </thead>
      <tbody>
        @forelse($students as $index => $student)
          <tr>
            <td>{{ $index + 1 }}</td>
            <td>{{ $student->name }}</td>
            <td><span class="badge bg-secondary">{{ $student->grade }}</span></td>
            <td>{{ $student->class_name }}</td>
            <td>{{ $student->gender }}</td>
            <td>{{ $student->parent_name }}</td>
            <td>{{ $student->phone }}</td>
            <td>
              <span class="badge {{ $student->status == 'نشط' ? 'bg-success' : ($student->status == 'منسحب' ? 'bg-danger' : 'bg-secondary') }}">
                {{ $student->status }}
              </span>
            </td>
            <td>
              <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editModal{{ $student->id }}">
                <i class="bi bi-pencil"></i>
              </button>
              <form action="{{ route('student.delete', $student->id) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا الطالب؟')">
                @csrf
                @method('DELETE')
                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
              </form>
            </td>
          </tr>

          {{-- Edit Modal --}}
          <div class="modal fade" id="editModal{{ $student->id }}" tabindex="-1">
            <div class="modal-dialog modal-lg">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title">تعديل بيانات الطالب - {{ $student->name }}</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                  <form id="editForm{{ $student->id }}">
                    @csrf
                    <div class="row g-3">
                      <div class="col-md-4">
                        <label class="form-label">اسم الطالب</label>
                        <input type="text" name="name" class="form-control" value="{{ $student->name }}" required>
                      </div>
                      <div class="col-md-4">
                        <label class="form-label">المجموعة</label>
                        <select name="grade" class="form-select" required>
                          <option value="السنة الأولى" {{ $student->grade == 'السنة الأولى' ? 'selected' : '' }}>السنة الأولى</option>
                          <option value="السنة الثانية" {{ $student->grade == 'السنة الثانية' ? 'selected' : '' }}>السنة الثانية</option>
                        </select>
                      </div>
                      <div class="col-md-4">
                        <label class="form-label">الصف</label>
                        <select name="class_name" class="form-select" required>
                          @for($i = 5; $i <= 12; $i++)
                            <option value="{{ $i }}" {{ $student->class_name == $i ? 'selected' : '' }}>{{ $i }}</option>
                          @endfor
                        </select>
                      </div>
                      <div class="col-md-4">
                        <label class="form-label">تاريخ الميلاد</label>
                        <input type="date" name="date_of_birth" class="form-control" value="{{ $student->date_of_birth->format('Y-m-d') }}" required>
                      </div>
                      <div class="col-md-4">
                        <label class="form-label">الجنس</label>
                        <select name="gender" class="form-select" required>
                          <option value="ذكر" {{ $student->gender == 'ذكر' ? 'selected' : '' }}>ذكر</option>
                          <option value="أنثى" {{ $student->gender == 'أنثى' ? 'selected' : '' }}>أنثى</option>
                        </select>
                      </div>
                      <div class="col-md-4">
                        <label class="form-label">تاريخ التسجيل</label>
                        <input type="date" name="enrollment_date" class="form-control" value="{{ $student->enrollment_date->format('Y-m-d') }}" required>
                      </div>
                      <div class="col-md-4">
                        <label class="form-label">ولي الأمر</label>
                        <input type="text" name="parent_name" class="form-control" value="{{ $student->parent_name }}" required>
                      </div>
                      <div class="col-md-4">
                        <label class="form-label">الهاتف</label>
                        <input type="text" name="phone" class="form-control" value="{{ $student->phone }}" required>
                      </div>
                      <div class="col-md-4">
                        <label class="form-label">الحالة</label>
                        <select name="status" class="form-select">
                          <option value="نشط" {{ $student->status == 'نشط' ? 'selected' : '' }}>نشط</option>
                          <option value="منسحب" {{ $student->status == 'منسحب' ? 'selected' : '' }}>منسحب</option>
                          <option value="منقول" {{ $student->status == 'منقول' ? 'selected' : '' }}>منقول</option>
                        </select>
                      </div>
                      <div class="col-md-6">
                        <label class="form-label">العنوان</label>
                        <input type="text" name="address" class="form-control" value="{{ $student->address }}">
                      </div>
                      <div class="col-md-6">
                        <label class="form-label">ملاحظات</label>
                        <input type="text" name="notes" class="form-control" value="{{ $student->notes }}">
                      </div>
                    </div>
                  </form>
                </div>
                <div class="modal-footer">
                  <button class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                  <button class="btn btn-primary" onclick="updateStudent({{ $student->id }})">حفظ التعديلات</button>
                </div>
              </div>
            </div>
          </div>
        @empty
          <tr><td colspan="9" class="text-muted py-4">لا يوجد طلاب مسجلين</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

@endsection

@section('scripts')
<script>
function updateStudent(id) {
  const form = document.getElementById('editForm' + id);
  const formData = new FormData(form);

  fetch(`/student/update/${id}`, {
    method: 'POST',
    headers: {
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
      'Accept': 'application/json',
    },
    body: formData
  })
  .then(res => res.json())
  .then(data => {
    if (data.success) {
      location.reload();
    } else {
      alert(data.message || 'حدث خطأ أثناء التحديث');
    }
  })
  .catch(() => alert('حدث خطأ في الاتصال'));
}
</script>
@endsection
