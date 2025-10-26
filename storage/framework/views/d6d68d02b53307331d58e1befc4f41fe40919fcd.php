

<?php $__env->startSection('title', 'الصيانة واستهلاك الوقود - الحافلات'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid p-4">

  
  <div class="text-center mb-4">
    <h4 class="fw-bold text-secondary">
      <i class="bi bi-gear-wide-connected text-warning me-2"></i>
      الصيانة واستهلاك الوقود للحافلات
    </h4>
  </div>

  
  <div class="row g-4 mb-4">
    <div class="col-md-6">
      <div class="card p-4 text-center shadow-sm">
        <div class="card-icon mx-auto mb-3"><i class="bi bi-tools fs-3 text-warning"></i></div>
        <h6>إجمالي مصروفات الصيانة السنوية</h6>
        <h3 class="text-warning"><?php echo e(number_format($yearlyMaintenanceExpense, 3)); ?> ر.ع</h3>
        <small><?php echo e(date('Y')); ?></small>
      </div>
    </div>

    <div class="col-md-6">
      <div class="card p-4 text-center shadow-sm">
        <div class="card-icon mx-auto mb-3"><i class="bi bi-fuel-pump fs-3 text-info"></i></div>
        <h6>إجمالي استهلاك الوقود الشهري</h6>
        <h3 class="text-info"><?php echo e(number_format($monthlyFuelExpense, 3)); ?> ر.ع</h3>
        <small><?php echo e(\Carbon\Carbon::now()->translatedFormat('F Y')); ?></small>
      </div>
    </div>
  </div>

  
  <form method="GET" action="<?php echo e(route('bus.operations')); ?>" class="row g-3 mb-4 p-3 bg-light rounded shadow-sm">
    <div class="col-md-3">
      <label class="form-label">من تاريخ</label>
      <input type="date" name="from_date" value="<?php echo e(request('from_date')); ?>" class="form-control">
    </div>
    <div class="col-md-3">
      <label class="form-label">إلى تاريخ</label>
      <input type="date" name="to_date" value="<?php echo e(request('to_date')); ?>" class="form-control">
    </div>
    <div class="col-md-3">
      <label class="form-label">اختر الحافلة</label>
      <select name="bus_id" class="form-select">
        <option value="">جميع الحافلات</option>
        <?php $__currentLoopData = $buses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bus): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($bus->id); ?>" <?php echo e(request('bus_id') == $bus->id ? 'selected' : ''); ?>>
            <?php echo e($bus->bus_number); ?>

          </option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </select>
    </div>
    <div class="col-md-3 d-flex align-items-end">
      <button class="btn btn-primary w-100"><i class="bi bi-search me-2"></i>بحث</button>
    </div>
  </form>

  
  <div class="card p-4 shadow-sm mb-4">
    <h6 class="mb-3"><i class="bi bi-tools me-2 text-warning"></i> سجل الصيانة</h6>
    <div class="table-responsive">
      <table class="table table-striped text-center align-middle">
        <thead class="table-warning">
          <tr>
            <th>رقم الحافلة</th>
            <th>نوع الصيانة</th>
            <th>المبلغ (ر.ع)</th>
            <th>التاريخ</th>
            <th>ملاحظات</th>
          </tr>
        </thead>
        <tbody>
          <?php $__empty_1 = true; $__currentLoopData = $maintenanceExpenses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $exp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
              <td><?php echo e($exp->bus->bus_number); ?></td>
              <td><?php echo e($exp->expense_type); ?></td>
              <td><?php echo e(number_format($exp->amount, 3)); ?></td>
              <td><?php echo e($exp->expense_date); ?></td>
              <td><?php echo e($exp->description ?? '-'); ?></td>
            </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr><td colspan="5" class="text-muted">لا توجد عمليات صيانة مسجلة</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  
  <div class="card p-4 shadow-sm">
    <h6 class="mb-3"><i class="bi bi-fuel-pump me-2 text-info"></i> سجل استهلاك الوقود</h6>
    <div class="table-responsive">
      <table class="table table-striped text-center align-middle">
        <thead class="table-info">
          <tr>
            <th>رقم الحافلة</th>
            <th>نوع المصروف</th>
            <th>المبلغ (ر.ع)</th>
            <th>التاريخ</th>
            <th>ملاحظات</th>
          </tr>
        </thead>
        <tbody>
          <?php $__empty_1 = true; $__currentLoopData = $fuelExpenses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $exp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
              <td><?php echo e($exp->bus->bus_number); ?></td>
              <td><?php echo e($exp->expense_type); ?></td>
              <td><?php echo e(number_format($exp->amount, 3)); ?></td>
              <td><?php echo e($exp->expense_date); ?></td>
              <td><?php echo e($exp->description ?? '-'); ?></td>
            </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr><td colspan="5" class="text-muted">لا توجد عمليات وقود مسجلة</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

</div>

<footer class="text-center mt-4 text-muted">
  © 2025 مدرسة الرسالة - نظام إدارة الحافلات
</footer>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.bus_sidebar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\Alresalah-Managment\resources\views/bus_operations.blade.php ENDPATH**/ ?>