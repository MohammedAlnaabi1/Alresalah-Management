<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
  <title>تقرير المصروفات - مدرسة الرسالة</title>
  <style>
    body {
      font-family: DejaVu Sans, sans-serif;
      direction: rtl;
      text-align: right;
      color: #333;
      margin: 30px;
    }
    header {
      text-align: center;
      border-bottom: 2px solid #dc3545;
      padding-bottom: 10px;
      margin-bottom: 20px;
    }
    header h2 {
      font-size: 22px;
      color: #dc3545;
      margin: 0;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 15px;
    }
    th, td {
      border: 1px solid #ddd;
      padding: 8px;
      font-size: 13px;
      text-align: center;
    }
    th {
      background-color: #f8d7da;
      color: #000;
    }
    tr:nth-child(even) {
      background-color: #fafafa;
    }
    h3 {
      color: #dc3545;
      margin-top: 25px;
      text-align: center;
    }
    .summary {
      margin-top: 25px;
      text-align: right;
    }
    .summary p {
      margin: 5px 0;
    }
    footer {
      position: fixed;
      bottom: 10px;
      left: 0;
      right: 0;
      text-align: center;
      font-size: 12px;
      color: #888;
    }
  </style>
</head>
<body>

  <header>
    <h2>مدرسة الرسالة</h2>
  </header>

  <h3>تقرير المصروفات</h3>

  <table>
    <thead>
      <tr>
        <th>#</th>
        <th>الفئة</th>
        <th>طريقة الدفع</th>
        <th>المبلغ (ر.ع)</th>
        <th>التاريخ</th>
        <th>الحافلة</th>
        <th>ملاحظات</th>
      </tr>
    </thead>
    <tbody>
      <?php $__empty_1 = true; $__currentLoopData = $expenses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $exp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <tr>
          <td><?php echo e($index + 1); ?></td>
          <td><?php echo e($exp->category); ?></td>
          <td><?php echo e($exp->payment_method); ?></td>
          <td><?php echo e(number_format($exp->amount, 2)); ?></td>
          <td><?php echo e($exp->date instanceof \Carbon\Carbon ? $exp->date->format('Y-m-d') : $exp->date); ?></td>
          <td><?php echo e($exp->related_bus_id ? 'حافلة رقم ' . $exp->related_bus_id : '-'); ?></td>
          <td><?php echo e($exp->notes ?? '-'); ?></td>
        </tr>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <tr>
          <td colspan="7">لا توجد بيانات</td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>

  <div class="summary">
    <p><strong>إجمالي المصروفات:</strong> <?php echo e(number_format($expenses->sum('amount'), 2)); ?> ر.ع</p>
    <p><strong>عدد السجلات:</strong> <?php echo e($expenses->count()); ?></p>
  </div>

  <footer>
    <p>نظام إدارة مدرسة الرسالة | <?php echo e(now()->format('Y-m-d')); ?></p>
  </footer>

</body>
</html>
<?php /**PATH C:\xampp\htdocs\Alresalah-Managment\resources\views/financial/export_expenses_pdf.blade.php ENDPATH**/ ?>