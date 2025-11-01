

<?php $__env->startSection('title', 'إدارة المصروفات'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">

  
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold text-secondary m-0">
      <i class="bi bi-wallet2 text-danger me-2"></i> إدارة المصروفات
    </h5>
    <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#addExpenseModal">
      <i class="bi bi-plus-circle me-1"></i> إضافة مصروف جديد
    </button>
  </div>

 <form method="GET" action="<?php echo e(route('financial.expenses')); ?>" class="row g-3 mb-4 p-3 bg-light rounded shadow-sm">
  <div class="col-md-3">
    <label class="form-label">نوع المصروف</label>
    <input type="text" name="category" value="<?php echo e(request('category')); ?>" class="form-control" placeholder="مثل: وقود أو رواتب أو صيانة">
  </div>

  <div class="col-md-3">
    <label class="form-label">من تاريخ</label>
    <input type="date" name="date_from" value="<?php echo e(request('date_from')); ?>" class="form-control">
  </div>

  <div class="col-md-3">
    <label class="form-label">إلى تاريخ</label>
    <input type="date" name="date_to" value="<?php echo e(request('date_to')); ?>" class="form-control">
  </div>

  <div class="col-md-3">
    <label class="form-label">رقم الحافلة (اختياري)</label>
    <input type="number" name="related_bus_id" value="<?php echo e(request('related_bus_id')); ?>" class="form-control" placeholder="رقم الحافلة">
  </div>

  <div class="col-12 text-center">
    <button type="submit" class="btn btn-primary btn-sm">
      <i class="bi bi-search me-1"></i> بحث
    </button>
    <a href="<?php echo e(route('financial.expenses')); ?>" class="btn btn-secondary btn-sm">
      <i class="bi bi-arrow-repeat me-1"></i> إعادة تعيين
    </a>
  </div>
</form>


  
  <?php if(session('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <?php echo e(session('success')); ?>

      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>

  
  
  
  <?php if(isset($pendingBusExpenses) && $pendingBusExpenses->count() > 0): ?>
  <div class="card mb-4 shadow-sm border-warning">
    <div class="card-header bg-warning text-dark fw-semibold">
      <i class="bi bi-bus-front me-2"></i> مصروفات الحافلات قيد المراجعة
    </div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover align-middle text-center mb-0">
          <thead class="table-warning">
            <tr>
              <th>#</th>
              <th>رقم الحافلة</th>
              <th>نوع المصروف</th>
              <th>المبلغ (ر.ع)</th>
              <th>التاريخ</th>
              <th>الوصف</th>
              <th>الإجراء</th>
            </tr>
          </thead>
          <tbody>
            <?php $__currentLoopData = $pendingBusExpenses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $exp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <tr>
                <td><?php echo e($loop->iteration); ?></td>
                <td><?php echo e($exp->bus->bus_number ?? 'غير محدد'); ?></td>
                <td><?php echo e($exp->expense_type); ?></td>
                <td class="fw-bold text-danger"><?php echo e(number_format($exp->amount, 3)); ?></td>
                <td><?php echo e($exp->expense_date); ?></td>
                <td><?php echo e($exp->description ?? '-'); ?></td>
                <td>
                  <a href="<?php echo e(route('financial.bus_expenses.approve', $exp->id)); ?>" class="btn btn-success btn-sm">
                    <i class="bi bi-check-circle"></i> موافقة
                  </a>
                  <a href="<?php echo e(route('financial.bus_expenses.reject', $exp->id)); ?>" class="btn btn-outline-danger btn-sm">
                    <i class="bi bi-x-circle"></i> رفض
                  </a>
                </td>
              </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <?php endif; ?>

  
  <div class="card shadow-sm border-0">
    <div class="card-body p-0">
      <table class="table table-hover align-middle text-center m-0">
        <thead class="table-light">
          <tr>
            <th>#</th>
            <th>الفئة</th>
            <th>طريقة الدفع</th>
            <th>المبلغ (ر.ع)</th>
            <th>التاريخ</th>
            <th>الحافلة</th>
            <th>ملاحظات</th>
            <th>المرفق</th>
            <th>إجراءات</th>
          </tr>
        </thead>
        <tbody>
          <?php $__empty_1 = true; $__currentLoopData = $expenses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $expense): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
              <td><?php echo e($loop->iteration); ?></td>
              <td><?php echo e($expense->category); ?></td>
              <td><?php echo e($expense->payment_method); ?></td>
              <td class="text-danger fw-bold"><?php echo e(number_format($expense->amount, 2)); ?></td>
              <td><?php echo e($expense->date->format('Y-m-d')); ?></td>
              <td>
                <?php if($expense->related_bus_id): ?>
                  <span class="badge bg-info">حافلة رقم <?php echo e($expense->related_bus_id); ?></span>
                <?php else: ?>
                  <span class="text-muted">-</span>
                <?php endif; ?>
              </td>
              <td><?php echo e($expense->notes ?? '-'); ?></td>
              <td>
                <?php if($expense->attachment): ?>
                  <a href="<?php echo e(asset('storage/' . $expense->attachment)); ?>" target="_blank" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-paperclip"></i>
                  </a>
                <?php else: ?>
                  -
                <?php endif; ?>
              </td>
              <td>
                
                <?php if($expense->status != 'approved'): ?>
                  <button class="btn btn-outline-info btn-sm editBtn" data-exp='<?php echo json_encode($expense, 15, 512) ?>'>
                    <i class="bi bi-pencil-square"></i>
                  </button>
                <?php endif; ?>

                
                <form action="<?php echo e(route('financial.expenses.delete', $expense->id)); ?>" method="POST" class="d-inline" onsubmit="return confirm('⚠️ هل أنت متأكد من الحذف؟ سيتم حذف المصروف من جميع الجداول وتحديث النظام.');">
                  <?php echo csrf_field(); ?>
                  <?php echo method_field('DELETE'); ?>
                  <button class="btn btn-outline-danger btn-sm">
                    <i class="bi bi-trash"></i>
                  </button>
                </form>
              </td>
            </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr>
              <td colspan="9" class="text-muted py-3">لا توجد بيانات حالياً</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>






<div class="modal fade" id="addExpenseModal" tabindex="-1" aria-labelledby="addExpenseModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-header bg-danger text-white">
        <h6 class="modal-title" id="addExpenseModalLabel"><i class="bi bi-plus-circle me-2"></i>إضافة مصروف جديد</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form action="<?php echo e(route('financial.expenses.store')); ?>" method="POST" enctype="multipart/form-data">
        <?php echo csrf_field(); ?>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">نوع المصروف</label>
            <input type="text" name="category" class="form-control" placeholder="مثل: وقود - صيانة - رواتب" required>
          </div>

          <div class="mb-3">
            <label class="form-label">طريقة الدفع</label>
            <select name="payment_method" class="form-select" required>
              <option value="">اختر طريقة الدفع</option>
              <option value="نقدًا">نقدًا</option>
              <option value="تحويل بنكي">تحويل بنكي</option>
              <option value="شيك">شيك</option>
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label">المبلغ (ر.ع)</label>
            <input type="number" name="amount" class="form-control" step="0.01" required>
          </div>

          <div class="mb-3">
            <label class="form-label">تاريخ المصروف</label>
            <input type="date" name="date" class="form-control" required>
          </div>

          <div class="mb-3">
            <label class="form-label">رقم الحافلة (اختياري)</label>
            <input type="number" name="related_bus_id" class="form-control" placeholder="مثلاً: 3 أو اتركه فارغاً">
          </div>

          <div class="mb-3">
            <label class="form-label">ملاحظات</label>
            <textarea name="notes" class="form-control" rows="2"></textarea>
          </div>

          <div class="mb-3">
            <label class="form-label">المرفق (اختياري)</label>
            <input type="file" name="attachment" class="form-control" accept=".jpg,.png,.pdf">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">إلغاء</button>
          <button type="submit" class="btn btn-danger btn-sm">حفظ</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- ✅ نافذة تعديل المصروف -->
<div class="modal fade" id="editExpenseModal" tabindex="-1" aria-labelledby="editExpenseLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-warning text-dark">
        <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i> تعديل المصروف</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="editExpenseForm" enctype="multipart/form-data">
          <?php echo csrf_field(); ?>
          <input type="hidden" id="edit_id">

          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">نوع المصروف</label>
              <input type="text" id="edit_category" name="category" class="form-control">
            </div>

            <div class="col-md-6">
              <label class="form-label">طريقة الدفع</label>
              <input type="text" id="edit_payment_method" name="payment_method" class="form-control">
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
              <label class="form-label">رقم الحافلة</label>
              <input type="number" id="edit_related_bus_id" name="related_bus_id" class="form-control">
            </div>

            <div class="col-md-12">
              <label class="form-label">ملاحظات</label>
              <input type="text" id="edit_notes" name="notes" class="form-control">
            </div>

            <div class="col-md-12">
              <label class="form-label">المرفق (PDF/JPG)</label>
              <input type="file" id="edit_attachment" name="attachment" class="form-control" accept="application/pdf,image/*">
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

<!-- ✅ سكربت AJAX -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function(){

  // فتح نافذة التعديل
  $('.editBtn').on('click', function(){
    const exp = $(this).data('exp');
    $('#edit_id').val(exp.id);
    $('#edit_category').val(exp.category);
    $('#edit_payment_method').val(exp.payment_method);
    $('#edit_amount').val(exp.amount);
    $('#edit_date').val(exp.date);
    $('#edit_related_bus_id').val(exp.related_bus_id);
    $('#edit_notes').val(exp.notes);
    $('#editExpenseModal').modal('show');
  });

  // عند الحفظ
  $('#editExpenseForm').on('submit', function(e){
    e.preventDefault();

    const id = $('#edit_id').val();
    const formData = new FormData(this);
    formData.append('_method', 'POST');

    $.ajax({
      url: `/financial/expenses/update/${id}`,
      method: 'POST',
      data: formData,
      processData: false,
      contentType: false,
      success: function(res){
        console.log('✅ Response:', res);
        if(res.success){
          $('#editExpenseModal').modal('hide');
          alert(res.message);
          location.reload();
        } else {
          alert('⚠️ لم يتم التحديث: ' + (res.message || ''));
        }
      },
      error: function(xhr){
        console.error('❌ Error:', xhr.responseText);
        alert('حدث خطأ أثناء التحديث');
      }
    });
  });

});
</script>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('financial.layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\Alresalah-Managment\resources\views/financial/expenses.blade.php ENDPATH**/ ?>