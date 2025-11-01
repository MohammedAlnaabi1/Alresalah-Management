<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>لوحة المدير العام - مركز الرسالة</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600&display=swap" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <style>
    body {
      background-color: #f5f6fa;
      font-family: 'Cairo', sans-serif;
      transition: background-color 0.3s, color 0.3s;
    }
    .dark-mode {
      background-color: #1e1e1e !important;
      color: #f5f5f5 !important;
    }
    .topbar {
      background-color: #fff;
      padding: 15px 25px;
      border-radius: 12px;
      margin-bottom: 25px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    .dark-mode .topbar { background-color: #2c2c2c; }
    .card {
      border: none;
      border-radius: 12px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
      transition: 0.3s;
    }
    .card:hover { transform: translateY(-3px); }
    .chart-container { height: 300px; }
    th { background-color: #f57c00; color: #fff; text-align: center; }
    .progress {
      height: 10px;
      border-radius: 5px;
      background-color: #eee;
    }
    .dark-mode th { background-color: #e07b00; }

    .bg-orange {
  background-color: #F28C28 !important;
}

  </style>
</head>

<body>
  <div class="container-fluid p-4">

    
    <div class="topbar">
      <h4><i class="bi bi-speedometer2 me-2"></i> لوحة المدير العام</h4>
      <div>
        <span id="liveTime" class="text-muted me-2"></span>
        <i class="bi bi-person-circle me-1"></i> <?php echo e(session('username') ?? 'admin'); ?>

        <button id="toggleDark" class="btn btn-sm btn-outline-secondary ms-3">
          <i class="bi bi-moon"></i> تبديل الوضع
        </button>
      </div>
    </div>

    
    <div class="alert alert-warning d-flex align-items-center mb-4" role="alert">
      <i class="bi bi-bell-fill me-2"></i>
      <div>مرحبا <?php echo e(session('username') ?? 'مدير النظام'); ?> 👋 — آخر تحديث للبيانات: <?php echo e(now()->format('Y-m-d H:i')); ?></div>
    </div>

    
    <div class="card p-3 mb-4">
      <h6 class="fw-bold text-secondary mb-2"><i class="bi bi-graph-up me-1"></i> مؤشر الربحية</h6>
      <?php
        $profitRate = $totalRevenues > 0 ? round(($netProfit / $totalRevenues) * 100, 2) : 0;
      ?>
      <div class="progress mb-2">
        <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo e($profitRate); ?>%;" aria-valuenow="<?php echo e($profitRate); ?>" aria-valuemin="0" aria-valuemax="100"></div>
      </div>
      <small>نسبة الربح إلى الإيرادات: <strong><?php echo e($profitRate); ?>%</strong></small>
    </div>

    
    <div class="row text-center">
      <div class="col-md-2 mb-3">
        <div class="card p-3"><h6>تسجيلات الدخول</h6><h3><?php echo e($loginCount); ?></h3></div>
      </div>
      <div class="col-md-2 mb-3">
        <div class="card p-3"><h6>عدد المستخدمين</h6><h3><?php echo e($userCount); ?></h3></div>
      </div>
      <div class="col-md-2 mb-3">
        <div class="card p-3"><h6>الإيرادات</h6><h3 class="text-success"><?php echo e(number_format($totalRevenues,2)); ?> ر.ع</h3></div>
      </div>
      <div class="col-md-2 mb-3">
        <div class="card p-3"><h6>المصروفات</h6><h3 class="text-danger"><?php echo e(number_format($totalExpenses,2)); ?> ر.ع</h3></div>
      </div>
      <div class="col-md-2 mb-3">
        <div class="card p-3"><h6>عدد الحافلات</h6><h3><?php echo e($busCount); ?></h3></div>
      </div>
      <div class="col-md-2 mb-3">
        <div class="card p-3"><h6>صافي الربح</h6><h3 class="<?php echo e($netProfit >= 0 ? 'text-success' : 'text-danger'); ?>"><?php echo e(number_format($netProfit,2)); ?> ر.ع</h3></div>
      </div>
    </div>

    
    <div class="card mt-4 p-4">
      <h5 class="mb-3"><i class="bi bi-graph-up-arrow me-2"></i> الإيرادات والمصروفات الشهرية</h5>
      <div class="chart-container"><canvas id="financeChart"></canvas></div>
    </div>

    
    <div class="card mt-4 p-3">
      <h5 class="mb-3"><i class="bi bi-clock-history me-2"></i> آخر تسجيلات الدخول</h5>
      <div class="table-responsive">
        <table class="table table-striped text-center">
          <thead><tr><th>المستخدم</th><th>عنوان IP</th><th>وقت الدخول</th></tr></thead>
          <tbody>
            <?php $__currentLoopData = $logins; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <tr>
                <td><?php echo e($log->username); ?></td>
                <td><?php echo e($log->ip_address); ?></td>
                <td><?php echo e($log->login_time); ?></td>
              </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </tbody>
        </table>
      </div>
    </div>

    
    <div class="card mt-4 p-3">
      <h5 class="mb-3"><i class="bi bi-activity me-2"></i> آخر النشاطات في النظام</h5>
      <div class="row">
        <div class="col-md-6">
          <h6 class="text-success">آخر الإيرادات</h6>
          <ul class="list-group">
            <?php $__currentLoopData = $recentRevenues; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rev): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <li class="list-group-item d-flex justify-content-between align-items-center">
                <?php echo e($rev->source ?? 'غير محدد'); ?>

                <span class="badge bg-success"><?php echo e(number_format($rev->amount, 2)); ?> ر.ع</span>
              </li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </ul>
        </div>
        <div class="col-md-6">
          <h6 class="text-danger">آخر المصروفات</h6>
          <ul class="list-group">
            <?php $__currentLoopData = $recentExpenses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $exp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <li class="list-group-item d-flex justify-content-between align-items-center">
                <?php echo e($exp->category ?? 'غير محدد'); ?>

                <span class="badge bg-danger"><?php echo e(number_format($exp->amount, 2)); ?> ر.ع</span>
              </li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </ul>
        </div>
      </div>
    </div>

    
    <div class="card mt-4 p-4 text-center">
      <h5 class="mb-3"><i class="bi bi-link-45deg me-2"></i> أدوات سريعة</h5>
      <div class="mb-3">
        <a href="<?php echo e(route('financial.reports.exportPDF')); ?>" class="btn btn-outline-danger m-1"><i class="bi bi-file-earmark-pdf"></i> تصدير PDF</a>
        <a href="<?php echo e(route('financial.reports.exportExcel')); ?>" class="btn btn-outline-success m-1"><i class="bi bi-file-earmark-spreadsheet"></i> تصدير Excel</a>
      </div>
      <a href="<?php echo e(route('financial.dashboard')); ?>" class="btn btn-warning m-2"><i class="bi bi-wallet2 me-1"></i> الإدارة المالية</a>
      <a href="<?php echo e(route('dashboard')); ?>" class="btn btn-primary m-2"><i class="bi bi-bus-front me-1"></i> إدارة الحافلات</a>
      <a href="<?php echo e(route('bus.operations')); ?>" class="btn btn-success m-2"><i class="bi bi-gear me-1"></i> الصيانة والوقود</a>
    </div>

    
<div class="card mt-5 shadow-sm">
  <div class="card-header bg-orange text-white fw-bold">
    <i class="bi bi-envelope-paper me-2"></i> رسائل الزوار الأخيرة
  </div>
  <div class="card-body">
    <?php if($contacts->isEmpty()): ?>
      <div class="alert alert-info text-center">لا توجد رسائل حتى الآن.</div>
    <?php else: ?>
      <div class="table-responsive">
        <table class="table table-striped table-hover align-middle">
          <thead class="table-light">
            <tr>
              <th>#</th>
              <th>الاسم</th>
              <th>البريد الإلكتروني</th>
              <th>الرسالة</th>
              <th>تاريخ الإرسال</th>
            </tr>
          </thead>
          <tbody>
            <?php $__currentLoopData = $contacts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $contact): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <tr>
                <td><?php echo e($index + 1); ?></td>
                <td><?php echo e($contact->name); ?></td>
                <td><?php echo e($contact->email); ?></td>
                <td style="white-space: pre-wrap;"><?php echo e($contact->message); ?></td>
                <td><?php echo e($contact->created_at->format('Y-m-d | H:i')); ?></td>
              </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>
</div>


    
<div class="card mt-4 p-4">
  <h5 class="mb-3"><i class="bi bi-bell-fill me-2 text-warning"></i> أحدث الإشعارات</h5>
  <div class="table-responsive">
    <table class="table table-hover align-middle text-center">
      <thead>
        <tr>
          <th style="width: 10%">النوع</th>
          <th>الحدث</th>
          <th style="width: 25%">الوقت</th>
        </tr>
      </thead>
      <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $notifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $note): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
          <tr>
            <td>
              <?php if($note['type'] === 'login'): ?>
                <i class="bi bi-person-check text-primary"></i>
              <?php elseif($note['type'] === 'revenue'): ?>
                <i class="bi bi-cash-coin text-success"></i>
              <?php elseif($note['type'] === 'expense'): ?>
                <i class="bi bi-wallet2 text-danger"></i>
              <?php endif; ?>
            </td>
            <td><?php echo e($note['message']); ?></td>
            <td class="text-muted"><?php echo e(\Carbon\Carbon::parse($note['time'])->diffForHumans()); ?></td>
          </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
          <tr>
            <td colspan="3" class="text-muted">لا توجد إشعارات حالياً</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>


  </div>

  <script>
    // 🔹 الرسم البياني
    const ctx = document.getElementById('financeChart').getContext('2d');
    new Chart(ctx, {
      type: 'bar',
      data: {
        labels: <?php echo json_encode($months); ?>,
        datasets: [
          { label: 'الإيرادات', data: <?php echo json_encode($revenuesData); ?>, backgroundColor: 'rgba(40,167,69,0.6)' },
          { label: 'المصروفات', data: <?php echo json_encode($expensesData); ?>, backgroundColor: 'rgba(220,53,69,0.6)' }
        ]
      },
      options: { responsive: true, scales: { y: { beginAtZero: true } } }
    });

    // 🔹 تبديل الوضع الليلي
    document.getElementById('toggleDark').addEventListener('click', () => {
      document.body.classList.toggle('dark-mode');
    });

    // 🔹 تحديث الوقت كل ثانية
    setInterval(() => {
      const now = new Date();
      document.getElementById('liveTime').innerText = now.toLocaleString('ar-EG');
    }, 1000);
  </script>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\Alresalah-Managment\resources\views/admin/admin.blade.php ENDPATH**/ ?>