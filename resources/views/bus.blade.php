@extends('layouts.bus_sidebar')

@section('title', 'إدارة بيانات الحافلات')

@section('content')
<div class="card p-4 shadow-sm">
  <h4 class="text-center text-primary mb-4">
    <i class="bi bi-bus-front me-2"></i> إدارة بيانات الحافلات
  </h4>

  {{-- ✅ رسائل النجاح --}}
  @if(session('success'))
    <div class="alert alert-success text-center">{{ session('success') }}</div>
  @endif

  {{-- ✅ رسائل الخطأ --}}
  @if ($errors->any())
    <div class="alert alert-danger">
      <ul class="mb-0">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  {{-- ✅ نموذج إضافة حافلة --}}
  <form class="row g-3 mb-4" method="POST" action="{{ route('bus.store') }}" enctype="multipart/form-data">
    @csrf

    <div class="col-md-4">
      <label class="form-label">لوحة الحافلة</label>
      <input type="text" class="form-control" name="bus_number" value="{{ old('bus_number') }}" required>
    </div>

    <div class="col-md-4">
      <label class="form-label">نوع الحافلة</label>
      <select class="form-select" name="bus_type" required>
        <option selected disabled>اختر نوع الحافلة</option>
        <option value="هايس">هايس (Hiace)</option>
        <option value="كوستر">كوستر (Coaster)</option>
      </select>
    </div>

    <div class="col-md-4">
      <label class="form-label">اسم السائق</label>
      <input type="text" class="form-control" name="driver_name" value="{{ old('driver_name') }}" required>
    </div>

    <div class="col-md-4">
      <label class="form-label">رقم الحافلة</label>
      <input type="number" class="form-control" name="bus_code" value="{{ old('bus_code') }}" required>
    </div>

    <div class="col-md-6">
      <label class="form-label">المدرسة</label>
      <input type="text" class="form-control" name="school" value="{{ old('school') }}" required>
    </div>

    <div class="col-md-6">
      <label class="form-label">الحالة</label>
      <select class="form-select" name="status" required>
        <option selected disabled>اختر الحالة</option>
        <option value="نشطة">نشطة</option>
        <option value="قيد الصيانة">قيد الصيانة</option>
        <option value="متوقفة">متوقفة</option>
      </select>
    </div>

    <div class="col-md-6">
      <label class="form-label">ملف ملكية الحافلة (PDF)</label>
      <input type="file" class="form-control" name="ownership_pdf" accept="application/pdf">
    </div>

    <div class="col-md-6">
      <label class="form-label">ملف التأمين (PDF)</label>
      <input type="file" class="form-control" name="insurance_pdf" accept="application/pdf">
    </div>

    <div class="text-center mt-3">
      <button type="submit" class="btn btn-warning px-4">
        <i class="bi bi-plus-circle me-2"></i>إضافة حافلة
      </button>
    </div>
  </form>

  <hr>

  {{-- ✅ جدول عرض الحافلات --}}
  <h5 class="text-secondary mb-3"><i class="bi bi-list-ul me-2"></i>قائمة الحافلات الحالية</h5>

  <div class="table-responsive">
    <table class="table table-striped align-middle text-center">
      <thead class="text-white" style="background-color:#006b8f;">
        <tr>
          <th>لوحة الحافلة</th>
          <th>نوع الحافلة</th>
          <th>اسم السائق</th>
          <th>رقم الحافلة</th>
          <th>المدرسة</th>
          <th>الحالة</th>
          <th>ملف الملكية</th>
          <th>ملف التأمين</th>
          <th>تعديل</th>
          <th>حذف</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($buses as $bus)
          <tr id="row-{{ $bus->id }}">
            <td>{{ $bus->bus_number }}</td>
            <td>{{ $bus->bus_type }}</td>
            <td>{{ $bus->driver_name }}</td>
            <td>{{ $bus->bus_code }}</td>
            <td>{{ $bus->school }}</td>
            <td>
              @if($bus->status === 'نشطة')
                <span class="badge bg-success">نشطة</span>
              @elseif($bus->status === 'قيد الصيانة')
                <span class="badge bg-warning text-dark">قيد الصيانة</span>
              @else
                <span class="badge bg-danger">متوقفة</span>
              @endif
            </td>
            <td>
              @if($bus->ownership_pdf)
  <a href="{{ route('bus.view', ['id' => $bus->id, 'type' => 'ownership']) }}" 
     class="btn btn-sm btn-outline-primary" target="_blank">
     عرض
  </a>
@else 
  <span class="text-muted">لا يوجد</span>
@endif
</td>
<td>
@if($bus->insurance_pdf)
  <a href="{{ route('bus.view', ['id' => $bus->id, 'type' => 'insurance']) }}" 
     class="btn btn-sm btn-outline-success" target="_blank">
     عرض
  </a>
@else 
  <span class="text-muted">لا يوجد</span>
@endif

            <td>
              <button class="btn btn-sm btn-info text-white editBtn" data-bus='@json($bus)'>
                <i class="bi bi-pencil-square"></i>
              </button>
            </td>
            <td>
              <form method="POST" action="{{ route('bus.delete',$bus->id) }}" onsubmit="return confirm('هل أنت متأكد من حذف هذه الحافلة؟');">
                @csrf
                @method('DELETE')
                <button class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
              </form>
            </td>
          </tr>
        @empty
          <tr><td colspan="10" class="text-muted">لا توجد بيانات حاليًا</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

<!-- ✅ نافذة التعديل -->
<div class="modal fade" id="editBusModal" tabindex="-1" aria-labelledby="editBusLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i> تعديل بيانات الحافلة</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="editBusForm" enctype="multipart/form-data">
          @csrf
          @method('PUT')
          <input type="hidden" id="edit_id">
          <div class="row g-3">
            <div class="col-md-4">
              <label class="form-label">لوحة الحافلة</label>
              <input type="text" class="form-control" id="edit_bus_number" name="bus_number" required>
            </div>
            <div class="col-md-4">
              <label class="form-label">نوع الحافلة</label>
              <select class="form-select" id="edit_bus_type" name="bus_type" required>
                <option value="هايس">هايس</option>
                <option value="كوستر">كوستر</option>
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label">اسم السائق</label>
              <input type="text" class="form-control" id="edit_driver_name" name="driver_name" required>
            </div>
            <div class="col-md-4">
              <label class="form-label">رقم الحافلة</label>
              <input type="number" class="form-control" id="edit_bus_code" name="bus_code" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">المدرسة</label>
              <input type="text" class="form-control" id="edit_school" name="school" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">الحالة</label>
              <select class="form-select" id="edit_status" name="status" required>
                <option value="نشطة">نشطة</option>
                <option value="قيد الصيانة">قيد الصيانة</option>
                <option value="متوقفة">متوقفة</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">ملف الملكية (PDF)</label>
              <input type="file" class="form-control" id="edit_ownership_pdf" name="ownership_pdf" accept="application/pdf">
            </div>
            <div class="col-md-6">
              <label class="form-label">ملف التأمين (PDF)</label>
              <input type="file" class="form-control" id="edit_insurance_pdf" name="insurance_pdf" accept="application/pdf">
            </div>
          </div>
          <div class="text-center mt-4">
            <button type="submit" class="btn btn-warning px-4"><i class="bi bi-save me-1"></i> حفظ التعديلات</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- ✅ jQuery + AJAX Script -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(function(){

  // ✅ إعداد الـ CSRF Token لجميع الطلبات
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  // فتح نافذة التعديل
  $('.editBtn').on('click', function(){
    const bus = $(this).data('bus');
    $('#edit_id').val(bus.id);
    $('#edit_bus_number').val(bus.bus_number);
    $('#edit_bus_type').val(bus.bus_type);
    $('#edit_driver_name').val(bus.driver_name);
    $('#edit_bus_code').val(bus.bus_code);
    $('#edit_school').val(bus.school);
    $('#edit_status').val(bus.status);
    $('#editBusModal').modal('show');
  });

  // إرسال التحديث عبر AJAX
  $('#editBusForm').on('submit', function(e){
    e.preventDefault();

    const id = $('#edit_id').val();
    const formData = new FormData(this);
    formData.append('_method', 'POST'); // لتأكيد نوع الطلب

    $.ajax({
      url: `/bus/update/${id}`,
      type: 'POST',
      data: formData,
      processData: false,
      contentType: false,
      success: function(res){
        if(res.success){
          $('#editBusModal').modal('hide');
          // إشعار أنيق بدل alert
          const toast = $('<div>')
            .text(res.message)
            .css({
              position: 'fixed',
              top: '20px',
              right: '20px',
              background: '#198754',
              color: '#fff',
              padding: '10px 20px',
              borderRadius: '6px',
              zIndex: '9999'
            })
            .appendTo('body');
          setTimeout(() => toast.fadeOut(400, () => toast.remove()), 2500);
          setTimeout(() => location.reload(), 1500);
        } else {
          alert('لم يتم التحديث، تحقق من البيانات.');
        }
      },
      error: function(xhr){
        console.error('❌ Error details:', xhr.responseText);
        alert('حدث خطأ أثناء التحديث. تحقق من التوكن أو البيانات.');
      }
    });
  });
});
</script>

@endsection
