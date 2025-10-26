

<?php $__env->startSection('title', 'إدارة مصروفات الحافلات'); ?>

<?php $__env->startSection('content'); ?>
<div class="card p-4 shadow-sm">
  <h4 class="text-center text-primary mb-4">
    <i class="bi bi-cash-coin me-2"></i> إدارة مصروفات الحافلات
  </h4>

  
  <?php if(session('success')): ?>
    <div class="alert alert-success text-center"><?php echo e(session('success')); ?></div>
  <?php endif; ?>

  
  <form method="POST" action="<?php echo e(route('bus_expenses.store')); ?>" enctype="multipart/form-data" class="row g-3 mb-4">
    <?php echo csrf_field(); ?>

    <div class="col-md-4">
      <label class="form-label">اختر الحافلة</label>
      <select name="bus_id" class="form-select" required>
        <option value="" disabled selected>-- اختر الحافلة --</option>
        <?php $__currentLoopData = $buses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bus): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($bus->id); ?>"><?php echo e($bus->bus_number); ?> (<?php echo e($bus->bus_code); ?>)</option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </select>
    </div>

    <div class="col-md-4">
      <label class="form-label">نوع المصروف</label>
      <select name="expense_type" class="form-select" required>
        <option>وقود</option>
        <option>صيانة</option>
        <option>غسيل</option>
        <option>إطارات</option>
        <option>تأمين</option>
        <option>أخرى</option>
      </select>
    </div>

    <div class="col-md-4">
      <label class="form-label">المبلغ (ر.ع)</label>
      <input type="number" step="0.001" class="form-control" name="amount" required>
    </div>

    <div class="col-md-6">
      <label class="form-label">تاريخ المصروف</label>
      <input type="date" class="form-control" name="expense_date" required>
    </div>

    <div class="col-md-6">
      <label class="form-label">ملاحظات</label>
      <input type="text" class="form-control" name="description" placeholder="مثال: تغيير زيت، غسيل كامل...">
    </div>

    <div class="col-md-6">
      <label class="form-label">إرفاق الفاتورة (PDF)</label>
      <input type="file" class="form-control" name="receipt_pdf" accept="application/pdf">
    </div>

    <div class="text-center mt-3">
      <button type="submit" class="btn btn-warning px-4">
        <i class="bi bi-plus-circle me-2"></i>إضافة مصروف
      </button>
    </div>
  </form>

  <hr>

  
  <form method="GET" action="<?php echo e(route('bus_expenses')); ?>" class="row g-3 mb-4 p-3 bg-light rounded">
    <div class="col-md-3">
      <label class="form-label">الحافلة</label>
      <select name="bus_id" class="form-select">
        <option value="">جميع الحافلات</option>
        <?php $__currentLoopData = $buses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bus): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($bus->id); ?>" <?php echo e(request('bus_id') == $bus->id ? 'selected' : ''); ?>>
            <?php echo e($bus->bus_number); ?> (<?php echo e($bus->bus_code); ?>)
          </option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </select>
    </div>

    <div class="col-md-3">
      <label class="form-label">نوع المصروف</label>
      <select name="expense_type" class="form-select">
        <option value="">الكل</option>
        <option value="وقود" <?php echo e(request('expense_type') == 'وقود' ? 'selected' : ''); ?>>وقود</option>
        <option value="صيانة" <?php echo e(request('expense_type') == 'صيانة' ? 'selected' : ''); ?>>صيانة</option>
        <option value="غسيل" <?php echo e(request('expense_type') == 'غسيل' ? 'selected' : ''); ?>>غسيل</option>
        <option value="إطارات" <?php echo e(request('expense_type') == 'إطارات' ? 'selected' : ''); ?>>إطارات</option>
        <option value="تأمين" <?php echo e(request('expense_type') == 'تأمين' ? 'selected' : ''); ?>>تأمين</option>
        <option value="أخرى" <?php echo e(request('expense_type') == 'أخرى' ? 'selected' : ''); ?>>أخرى</option>
      </select>
    </div>

    <div class="col-md-3">
      <label class="form-label">من تاريخ</label>
      <input type="date" name="from_date" value="<?php echo e(request('from_date')); ?>" class="form-control">
    </div>

    <div class="col-md-3">
      <label class="form-label">إلى تاريخ</label>
      <input type="date" name="to_date" value="<?php echo e(request('to_date')); ?>" class="form-control">
    </div>

    <div class="col-12 text-center mt-3">
      <button type="submit" class="btn btn-primary px-4">
        <i class="bi bi-search me-2"></i>بحث
      </button>
      <a href="<?php echo e(route('bus_expenses')); ?>" class="btn btn-secondary px-4 ms-2">
        <i class="bi bi-arrow-repeat me-2"></i>إعادة تعيين
      </a>
    </div>
  </form>

  <hr>

  
  <h5 class="text-secondary mb-3"><i class="bi bi-list-ul me-2"></i>سجل مصروفات الحافلات</h5>

  <div class="table-responsive">
    <table class="table table-striped text-center align-middle">
      <thead class="text-white" style="background-color:#006b8f;">
        <tr>
          <th>رقم / لوحة الحافلة</th>
          <th>النوع</th>
          <th>المبلغ</th>
          <th>التاريخ</th>
          <th>الملاحظات</th>
          <th>الفاتورة</th>
          <th>تعديل</th>
          <th>حذف</th>
        </tr>
      </thead>
      <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $expenses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $exp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
          <tr>
            <td><?php echo e($exp->bus->bus_number); ?> (<?php echo e($exp->bus->bus_code); ?>)</td>
            <td><?php echo e($exp->expense_type); ?></td>
            <td><?php echo e(number_format($exp->amount, 3)); ?> ر.ع</td>
            <td><?php echo e($exp->expense_date); ?></td>
            <td><?php echo e($exp->description ?? '-'); ?></td>
            <td>
              <?php if($exp->receipt_pdf): ?>
                <a href="<?php echo e(route('bus_expenses.view', $exp->id)); ?>" target="_blank" class="btn btn-sm btn-outline-primary">عرض</a>
              <?php else: ?>
                <span class="text-muted">لا يوجد</span>
              <?php endif; ?>
            </td>
            <td>
              <button class="btn btn-sm btn-info text-white editBtn" data-exp='<?php echo json_encode($exp, 15, 512) ?>'>
                <i class="bi bi-pencil-square"></i>
              </button>
            </td>
            <td>
              <form method="POST" action="<?php echo e(route('bus_expenses.delete', $exp->id)); ?>" onsubmit="return confirm('هل أنت متأكد من حذف هذا المصروف؟');">
                <?php echo csrf_field(); ?>
                <?php echo method_field('DELETE'); ?>
                <button class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
              </form>
            </td>
          </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
          <tr><td colspan="8" class="text-muted">لا توجد مصروفات بعد</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  
  <?php if(isset($total)): ?>
    <div class="alert alert-info mt-3 text-center">
      <strong>إجمالي المصروفات:</strong> <?php echo e(number_format($total, 3)); ?> ر.ع
    </div>
  <?php endif; ?>
</div>

<!-- ✅ نافذة التعديل -->
<div class="modal fade" id="editExpenseModal" tabindex="-1" aria-labelledby="editExpenseLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i> تعديل المصروف</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="editExpenseForm" enctype="multipart/form-data">
          <?php echo csrf_field(); ?>
          <?php echo method_field('PUT'); ?>
          <input type="hidden" id="edit_id">

          <div class="row g-3">
            <div class="col-md-4">
              <label class="form-label">نوع المصروف</label>
              <select id="edit_expense_type" name="expense_type" class="form-select">
                <option>وقود</option>
                <option>صيانة</option>
                <option>غسيل</option>
                <option>إطارات</option>
                <option>تأمين</option>
                <option>أخرى</option>
              </select>
            </div>

            <div class="col-md-4">
              <label class="form-label">المبلغ</label>
              <input type="number" step="0.001" id="edit_amount" name="amount" class="form-control">
            </div>

            <div class="col-md-4">
              <label class="form-label">التاريخ</label>
              <input type="date" id="edit_expense_date" name="expense_date" class="form-control">
            </div>

            <div class="col-md-12">
              <label class="form-label">الملاحظات</label>
              <input type="text" id="edit_description" name="description" class="form-control">
            </div>

            <div class="col-md-6">
              <label class="form-label">فاتورة جديدة (PDF)</label>
              <input type="file" id="edit_receipt_pdf" name="receipt_pdf" class="form-control" accept="application/pdf">
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

<!-- ✅ jQuery + AJAX -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(function(){

  $.ajaxSetup({
    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
  });

  $('.editBtn').on('click', function(){
    const exp = $(this).data('exp');
    $('#edit_id').val(exp.id);
    $('#edit_expense_type').val(exp.expense_type);
    $('#edit_amount').val(exp.amount);
    $('#edit_expense_date').val(exp.expense_date);
    $('#edit_description').val(exp.description);
    $('#editExpenseModal').modal('show');
  });

  $('#editExpenseForm').on('submit', function(e){
    e.preventDefault();
    const id = $('#edit_id').val();
    const formData = new FormData(this);
    formData.append('_method', 'POST');

    $.ajax({
      url: `/bus/expenses/update/${id}`,
      method: 'POST',
      data: formData,
      processData: false,
      contentType: false,
      success: function(res){
        if(res.success){
          $('#editExpenseModal').modal('hide');

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
              zIndex: '9999',
              boxShadow: '0 2px 10px rgba(0,0,0,0.15)'
            })
            .appendTo('body');

          setTimeout(() => toast.fadeOut(400, () => toast.remove()), 2500);
          setTimeout(() => location.reload(), 1500);
        }
      },
      error: function(xhr){
        console.error(xhr.responseText);
        alert('حدث خطأ أثناء التحديث');
      }
    });
  });
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.bus_sidebar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\Alresalah-Managment\resources\views/bus_expenses.blade.php ENDPATH**/ ?>