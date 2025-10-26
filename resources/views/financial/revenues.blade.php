@extends('financial.layout')

@section('title', 'إدارة الإيرادات')

@section('content')
<div class="card p-4 shadow-sm">
  <h4 class="text-center text-primary mb-4">
    <i class="bi bi-cash-coin me-2"></i> إدارة الإيرادات
  </h4>

  {{-- ✅ رسالة النجاح --}}
  @if(session('success'))
    <div class="alert alert-success text-center">{{ session('success') }}</div>
  @endif

  {{-- ✅ نموذج الفلترة --}}
  <form method="GET" action="{{ route('financial.revenues') }}" class="row g-3 mb-4 p-3 bg-light rounded">
    <div class="col-md-3">
      <label class="form-label">المصدر</label>
      <input type="text" name="source" value="{{ request('source') }}" class="form-control" placeholder="ابحث باسم المصدر">
    </div>

    <div class="col-md-3">
      <label class="form-label">النوع</label>
      {{-- ✅ إضافة قائمة اختيار مع إمكانية الكتابة --}}
      <input type="text" name="type" value="{{ request('type') }}" class="form-control" list="revenueTypes" placeholder="اختر أو اكتب نوعًا">
    </div>

    <div class="col-md-3">
      <label class="form-label">من تاريخ</label>
      <input type="date" name="from_date" value="{{ request('from_date') }}" class="form-control">
    </div>

    <div class="col-md-3">
      <label class="form-label">إلى تاريخ</label>
      <input type="date" name="to_date" value="{{ request('to_date') }}" class="form-control">
    </div>

    <div class="col-12 text-center mt-3">
      <button type="submit" class="btn btn-primary px-4"><i class="bi bi-search me-2"></i>بحث</button>
      <a href="{{ route('financial.revenues') }}" class="btn btn-secondary px-4 ms-2">
        <i class="bi bi-arrow-repeat me-2"></i>إعادة تعيين
      </a>
      <button type="button" class="btn btn-warning px-4 ms-2" data-bs-toggle="modal" data-bs-target="#addRevenueModal">
        <i class="bi bi-plus-circle me-2"></i>إضافة إيراد
      </button>
    </div>
  </form>

  <hr>

  {{-- ✅ جدول الإيرادات --}}
  <div class="table-responsive">
    <table class="table table-striped text-center align-middle">
      <thead class="text-white" style="background-color:#006b8f;">
        <tr>
          <th>#</th>
          <th>المصدر</th>
          <th>النوع</th>
          <th>المبلغ (ر.ع)</th>
          <th>التاريخ</th>
          <th>ملاحظات</th>
          <th>المرفق</th>
          <th>تعديل</th>
          <th>حذف</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($revenues as $rev)
          <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $rev->source }}</td>
            <td>{{ $rev->type }}</td>
            <td class="text-success fw-bold">{{ number_format($rev->amount, 3) }}</td>
            <td>{{ $rev->date }}</td>
            <td>{{ $rev->notes ?? '-' }}</td>
            <td>
              @if($rev->attachment)
                <a href="{{ asset('storage/'.$rev->attachment) }}" target="_blank" class="btn btn-sm btn-outline-primary">عرض</a>
              @else
                <span class="text-muted">لا يوجد</span>
              @endif
            </td>
            <td>
              <button class="btn btn-sm btn-info text-white editBtn" data-rev='@json($rev)'>
                <i class="bi bi-pencil-square"></i>
              </button>
            </td>
            <td>
              <form method="POST" action="{{ route('financial.revenues.delete', $rev->id) }}" onsubmit="return confirm('هل أنت متأكد من الحذف؟');">
                @csrf
                @method('DELETE')
                <button class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
              </form>
            </td>
          </tr>
        @empty
          <tr><td colspan="9" class="text-muted">لا توجد بيانات بعد</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

<!-- ✅ نافذة إضافة الإيراد -->
<div class="modal fade" id="addRevenueModal" tabindex="-1" aria-labelledby="addRevenueLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i> إضافة إيراد جديد</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form action="{{ route('financial.revenues.store') }}" method="POST" enctype="multipart/form-data">
          @csrf
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">المصدر</label>
              <input type="text" name="source" class="form-control" required>
            </div>

            <div class="col-md-6">
              <label class="form-label">النوع</label>
              {{-- ✅ إضافة قائمة اختيار مع إمكانية الكتابة --}}
              <input type="text" name="type" class="form-control" list="revenueTypes" placeholder="اختر أو اكتب نوعًا" required>
            </div>

            <div class="col-md-4">
              <label class="form-label">المبلغ</label>
              <input type="number" step="0.001" name="amount" class="form-control" required>
            </div>

            <div class="col-md-4">
              <label class="form-label">التاريخ</label>
              <input type="date" name="date" class="form-control" required>
            </div>

            <div class="col-md-4">
              <label class="form-label">المرفق (PDF/JPG)</label>
              <input type="file" name="attachment" class="form-control" accept="application/pdf,image/*">
            </div>

            <div class="col-md-12">
              <label class="form-label">ملاحظات</label>
              <textarea name="notes" class="form-control" rows="2"></textarea>
            </div>
          </div>

          <div class="text-center mt-4">
            <button type="submit" class="btn btn-success px-4"><i class="bi bi-check2-circle"></i> حفظ</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- ✅ نافذة تعديل الإيراد -->
<div class="modal fade" id="editRevenueModal" tabindex="-1" aria-labelledby="editRevenueLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i> تعديل الإيراد</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="editRevenueForm" method="POST" enctype="multipart/form-data">
          @csrf
          <input type="hidden" id="edit_id" name="id">

          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">المصدر</label>
              <input type="text" id="edit_source" name="source" class="form-control">
            </div>

            <div class="col-md-6">
              <label class="form-label">النوع</label>
              <input type="text" id="edit_type" name="type" class="form-control">
            </div>

            <div class="col-md-4">
              <label class="form-label">المبلغ</label>
              <input type="number" step="0.001" id="edit_amount" name="amount" class="form-control">
            </div>

            <div class="col-md-4">
              <label class="form-label">التاريخ</label>
              <input type="date" id="edit_date" name="date" class="form-control">
            </div>

            <div class="col-md-4">
              <label class="form-label">المرفق (PDF/JPG)</label>
              <input type="file" id="edit_attachment" name="attachment" class="form-control" accept="application/pdf,image/*">
            </div>

            <div class="col-md-12">
              <label class="form-label">ملاحظات</label>
              <input type="text" id="edit_notes" name="notes" class="form-control">
            </div>
          </div>

          <div class="text-center mt-4">
            <button type="submit" class="btn btn-warning px-4"><i class="bi bi-save"></i> حفظ التعديلات</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

{{-- ✅ قائمة الأنواع الجاهزة --}}
<datalist id="revenueTypes">
  <option value="دعم"></option>
  <option value="تبرع"></option>
  <option value="رسوم نقل"></option>
  <option value="رسوم طلاب"></option>
  <option value="مشاريع"></option>
  <option value="إيجارات"></option>
  <option value="مبيعات"></option>
  <option value="منح"></option>
  <option value="أخرى"></option>
</datalist>

<!-- ✅ سكربت الـ AJAX -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(function() {

  // ✅ تفعيل CSRF
  $.ajaxSetup({
    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
  });

  // ✅ تعبئة بيانات التعديل
  $('.editBtn').on('click', function(){
    const rev = $(this).data('rev');
    $('#edit_id').val(rev.id);
    $('#edit_source').val(rev.source);
    $('#edit_type').val(rev.type);
    $('#edit_amount').val(rev.amount);
    $('#edit_date').val(rev.date);
    $('#edit_notes').val(rev.notes || '');
    $('#editRevenueModal').modal('show');
  });

  // ✅ عند حفظ التعديلات
  $('#editRevenueForm').on('submit', function(e){
    e.preventDefault();

    const id = $('#edit_id').val();
    const formData = new FormData(this);

    $.ajax({
      url: `/financial/revenues/update/${id}`,
      type: 'POST',
      data: formData,
      processData: false,
      contentType: false,
      success: function(res){
        console.log("✅ Response:", res);
        if (res.success) {
          $('#editRevenueModal').modal('hide');
          alert(res.message);
          location.reload();
        } else {
          alert('⚠️ لم يتم التحديث: ' + (res.message || ''));
        }
      },
      error: function(xhr){
        console.error("❌ Error:", xhr.responseText);
        alert('❌ حدث خطأ أثناء التحديث');
      }
    });
  });

});
</script>
@endsection
