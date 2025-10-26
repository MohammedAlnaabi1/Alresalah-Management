

<?php $__env->startSection('title', 'لوحة التحكم - الحافلات'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid p-4">

  
  <div class="row g-4">

    <!-- 🔹 إجمالي الحافلات -->
    <div class="col-md-4">
      <div class="card p-4 text-center shadow-sm">
        <div class="card-icon mx-auto mb-3">
          <i class="bi bi-bus-front fs-3 text-primary"></i>
        </div>
        <h6>إجمالي الحافلات</h6>
        <h3 class="text-primary"><?php echo e($totalBuses); ?></h3>
        <small>في النظام</small>
      </div>
    </div>

    <!-- 🔹 الحافلات النشطة -->
    <div class="col-md-4">
      <div class="card p-4 text-center shadow-sm">
        <div class="card-icon mx-auto mb-3">
          <i class="bi bi-check-circle fs-3 text-success"></i>
        </div>
        <h6>الحافلات النشطة</h6>
        <h3 class="text-success"><?php echo e($activeBuses); ?></h3>
        <small>تعمل حاليًا</small>
      </div>
    </div>

    <!-- 🔹 الحافلات قيد الصيانة -->
    <div class="col-md-4">
      <div class="card p-4 text-center shadow-sm">
        <div class="card-icon mx-auto mb-3">
          <i class="bi bi-tools fs-3 text-warning"></i>
        </div>
        <h6>قيد الصيانة</h6>
        <h3 class="text-warning"><?php echo e($maintenanceBuses); ?></h3>
        <small>تحتاج متابعة</small>
      </div>
    </div>

    <!-- 🔹 إجمالي المصروفات -->
    <div class="col-md-4">
      <div class="card p-4 text-center shadow-sm">
        <div class="card-icon mx-auto mb-3">
          <i class="bi bi-wallet2 fs-3 text-danger"></i>
        </div>
        <h6>إجمالي المصروفات</h6>
        <h3 class="text-danger"><?php echo e(number_format($totalExpenses, 3)); ?> ر.ع</h3>
        <small>حتى اليوم</small>
      </div>
    </div>

    <!-- 🔹 صرفية الوقود الشهرية -->
    <div class="col-md-4">
      <div class="card p-4 text-center shadow-sm">
        <div class="card-icon mx-auto mb-3">
          <i class="bi bi-fuel-pump fs-3 text-info"></i>
        </div>
        <h6>صرفية الوقود هذا الشهر</h6>
        <h3 class="text-info"><?php echo e(number_format($monthlyFuelExpense, 3)); ?> ر.ع</h3>
        <small><?php echo e(\Carbon\Carbon::now()->translatedFormat('F Y')); ?></small>
      </div>
    </div>

    <!-- 🔹 مبالغ الصيانة السنوية -->
    <div class="col-md-4">
      <div class="card p-4 text-center shadow-sm">
        <div class="card-icon mx-auto mb-3">
          <i class="bi bi-gear-wide-connected fs-3 text-secondary"></i>
        </div>
        <h6>مبالغ الصيانة السنوية</h6>
        <h3 class="text-secondary"><?php echo e(number_format($yearlyMaintenanceExpense, 3)); ?> ر.ع</h3>
        <small><?php echo e(date('Y')); ?></small>
      </div>
    </div>

  </div>

  
  <div class="row g-4 mt-4">
    <!-- 🔹 الأعمدة السنوية -->
    <div class="col-lg-6">
      <div class="card p-4 shadow-sm">
        <h6 class="mb-3">
          <i class="bi bi-bar-chart-line text-primary me-2"></i>
          إجمالي المصروفات السنوية
        </h6>
        <canvas id="yearlyExpensesBar" height="150"></canvas>
      </div>
    </div>

    <!-- 🔹 التغير السنوي (خطي) -->
    <div class="col-lg-6">
      <div class="card p-4 shadow-sm">
        <h6 class="mb-3">
          <i class="bi bi-graph-up text-success me-2"></i>
          تطور المصروفات على مدار السنوات
        </h6>
        <canvas id="yearlyExpensesLine" height="150"></canvas>
      </div>
    </div>
  </div>

  
  <div class="row g-4 mt-4">
    <!-- 🔸 جدول الحافلات -->
    <div class="col-lg-6">
      <div class="card p-4 shadow-sm">
        <h6 class="mb-3">
          <i class="bi bi-bus-front me-2 text-primary"></i> أحدث الحافلات المسجلة
        </h6>
        <div class="table-responsive">
          <table class="table table-striped text-center align-middle">
            <thead class="table-primary">
              <tr>
                <th>اللوحة</th>
                <th>النوع</th>
                <th>الحالة</th>
                <th>المدرسة</th>
              </tr>
            </thead>
            <tbody>
              <?php $__empty_1 = true; $__currentLoopData = $latestBuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bus): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                  <td><?php echo e($bus->bus_number); ?></td>
                  <td><?php echo e($bus->bus_type); ?></td>
                  <td>
                    <?php if($bus->status == 'نشطة'): ?>
                      <span class="badge bg-success">نشطة</span>
                    <?php elseif($bus->status == 'قيد الصيانة'): ?>
                      <span class="badge bg-warning text-dark">قيد الصيانة</span>
                    <?php else: ?>
                      <span class="badge bg-danger">متوقفة</span>
                    <?php endif; ?>
                  </td>
                  <td><?php echo e($bus->school); ?></td>
                </tr>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="4" class="text-muted">لا توجد حافلات بعد</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- 🔸 جدول المصروفات -->
    <div class="col-lg-6">
      <div class="card p-4 shadow-sm">
        <h6 class="mb-3">
          <i class="bi bi-cash-coin me-2 text-warning"></i> أحدث المصروفات
        </h6>
        <div class="table-responsive">
          <table class="table table-striped text-center align-middle">
            <thead class="table-warning">
              <tr>
                <th>الحافلة</th>
                <th>النوع</th>
                <th>المبلغ</th>
                <th>التاريخ</th>
              </tr>
            </thead>
            <tbody>
              <?php $__empty_1 = true; $__currentLoopData = $latestExpenses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $exp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                  <td><?php echo e($exp->bus->bus_number); ?></td>
                  <td><?php echo e($exp->expense_type); ?></td>
                  <td><?php echo e(number_format($exp->amount, 3)); ?> ر.ع</td>
                  <td><?php echo e($exp->expense_date); ?></td>
                </tr>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="4" class="text-muted">لا توجد مصروفات بعد</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<footer class="text-center mt-4 text-muted">
  © 2025 مدرسة الرسالة - نظام إدارة الحافلات
</footer>


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


<script>
  // 📊 الأعمدة السنوية
  const ctxBar = document.getElementById('yearlyExpensesBar')?.getContext('2d');
  if (ctxBar) {
    new Chart(ctxBar, {
      type: 'bar',
      data: {
        labels: <?php echo json_encode($chartYears ?? [], 15, 512) ?>,
        datasets: [{
          label: 'إجمالي المصروفات (ر.ع)',
          data: <?php echo json_encode($chartTotals ?? [], 15, 512) ?>,
          backgroundColor: 'rgba(0, 86, 179, 0.7)',
          borderColor: 'rgba(0, 86, 179, 1)',
          borderWidth: 1
        }]
      },
      options: { responsive: true, scales: { y: { beginAtZero: true } } }
    });
  }

  // 📈 الخط البياني السنوي
  const ctxLine = document.getElementById('yearlyExpensesLine')?.getContext('2d');
  if (ctxLine) {
    new Chart(ctxLine, {
      type: 'line',
      data: {
        labels: <?php echo json_encode($chartYears ?? [], 15, 512) ?>,
        datasets: [{
          label: 'إجمالي المصروفات (ر.ع)',
          data: <?php echo json_encode($chartTotals ?? [], 15, 512) ?>,
          fill: true,
          borderColor: 'rgba(0,179,90,1)',
          backgroundColor: 'rgba(0,179,90,0.2)',
          tension: 0.3,
          pointRadius: 4,
          pointBackgroundColor: 'rgba(0,179,90,1)'
        }]
      },
      options: { responsive: true, scales: { y: { beginAtZero: true } } }
    });
  }
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.bus_sidebar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\Alresalah-Managment\resources\views/dashboard.blade.php ENDPATH**/ ?>