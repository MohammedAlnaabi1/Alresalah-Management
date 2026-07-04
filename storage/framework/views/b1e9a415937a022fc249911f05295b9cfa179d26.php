<?php $__env->startSection('title', 'تكريم المتفوقين'); ?>

<?php $__env->startSection('content'); ?>


<div class="alert alert-info border-0 shadow-sm mb-4">
  <h6 class="fw-bold mb-2"><i class="bi bi-info-circle me-2"></i> نظام احتساب النقاط</h6>
  <div class="row">
    <div class="col-md-4">
      <strong>معدل الدرجات (80%)</strong> — متوسط النسبة المئوية لجميع المواد
    </div>
    <div class="col-md-4">
      <strong>نسبة الحضور (20%)</strong> — نسبة أيام الحضور من إجمالي الأيام
    </div>
    <div class="col-md-4">
      <strong>نقاط المسؤول</strong> — نقاط إضافية يدخلها المسؤول يدوياً
    </div>
  </div>
  <hr class="my-2">
  <small><strong>إجمالي النقاط</strong> = (معدل الدرجات × 0.8) + (نسبة الحضور × 0.2) + نقاط المسؤول</small>
</div>


<div class="card p-3 mb-4 border-0 shadow-sm">
  <form method="GET" action="<?php echo e(route('student.honors')); ?>" class="row g-2 align-items-end">
    <div class="col-md-3">
      <label class="form-label">الفصل الدراسي</label>
      <select name="semester" class="form-select">
        <option value="الأول" <?php echo e($semester == 'الأول' ? 'selected' : ''); ?>>الأول</option>
        <option value="الثاني" <?php echo e($semester == 'الثاني' ? 'selected' : ''); ?>>الثاني</option>
      </select>
    </div>
    <div class="col-md-3">
      <label class="form-label">العام الدراسي</label>
      <select name="academic_year" class="form-select">
        <option value="">الكل</option>
        <?php $__currentLoopData = $academicYears; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $y): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($y); ?>" <?php echo e($academicYear == $y ? 'selected' : ''); ?>><?php echo e($y); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </select>
    </div>
    <div class="col-md-2">
      <label class="form-label">الصف</label>
      <select name="grade" class="form-select">
        <option value="">كل الصفوف</option>
        <?php $__currentLoopData = $gradesList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $g): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($g); ?>" <?php echo e($gradeFilter == $g ? 'selected' : ''); ?>><?php echo e($g); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </select>
    </div>
    <div class="col-md-2">
      <button class="btn btn-warning w-100"><i class="bi bi-funnel me-1"></i> عرض النتائج</button>
    </div>
    <div class="col-md-2">
      <a href="<?php echo e(route('student.points', ['semester' => $semester, 'academic_year' => $academicYear, 'grade' => $gradeFilter])); ?>" class="btn btn-outline-primary w-100">
        <i class="bi bi-pencil-square me-1"></i> إدخال النقاط
      </a>
    </div>
  </form>
</div>


<?php if($rankings->count() >= 3): ?>
<div class="row text-center mb-4">
  <div class="col-md-4 mb-3">
    <div class="card border-0 shadow-sm p-4" style="border-top: 4px solid #adb5bd !important;">
      <div class="mb-2"><span style="font-size: 2.5rem;">🥈</span></div>
      <h5 class="fw-bold"><?php echo e($rankings[1]['student']->name); ?></h5>
      <p class="text-muted mb-1"><?php echo e($rankings[1]['student']->grade); ?> - <?php echo e($rankings[1]['student']->class_name); ?></p>
      <h3 class="text-secondary"><?php echo e($rankings[1]['total_points']); ?></h3>
      <small class="text-muted">نقطة</small>
    </div>
  </div>
  <div class="col-md-4 mb-3">
    <div class="card border-0 shadow p-4" style="border-top: 4px solid #ffc107 !important; transform: scale(1.05);">
      <div class="mb-2"><span style="font-size: 2.5rem;">🥇</span></div>
      <h5 class="fw-bold"><?php echo e($rankings[0]['student']->name); ?></h5>
      <p class="text-muted mb-1"><?php echo e($rankings[0]['student']->grade); ?> - <?php echo e($rankings[0]['student']->class_name); ?></p>
      <h3 class="text-warning"><?php echo e($rankings[0]['total_points']); ?></h3>
      <small class="text-muted">نقطة</small>
    </div>
  </div>
  <div class="col-md-4 mb-3">
    <div class="card border-0 shadow-sm p-4" style="border-top: 4px solid #cd7f32 !important;">
      <div class="mb-2"><span style="font-size: 2.5rem;">🥉</span></div>
      <h5 class="fw-bold"><?php echo e($rankings[2]['student']->name); ?></h5>
      <p class="text-muted mb-1"><?php echo e($rankings[2]['student']->grade); ?> - <?php echo e($rankings[2]['student']->class_name); ?></p>
      <h3 style="color: #cd7f32;"><?php echo e($rankings[2]['total_points']); ?></h3>
      <small class="text-muted">نقطة</small>
    </div>
  </div>
</div>
<?php endif; ?>


<div class="card border-0 shadow-sm">
  <div class="card-header bg-white d-flex justify-content-between align-items-center">
    <h6 class="fw-bold mb-0"><i class="bi bi-list-ol me-2"></i> ترتيب الطلاب</h6>
    <span class="badge bg-primary"><?php echo e($rankings->count()); ?> طالب</span>
  </div>
  <div class="table-responsive">
    <table class="table table-hover align-middle text-center mb-0">
      <thead>
        <tr>
          <th>الترتيب</th>
          <th>اسم الطالب</th>
          <th>الصف</th>
          <th>عدد المواد</th>
          <th>معدل الدرجات</th>
          <th>نسبة الحضور</th>
          <th>نقاط المسؤول</th>
          <th>إجمالي النقاط</th>
          <th>التقدير</th>
        </tr>
      </thead>
      <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $rankings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
          <?php
            if ($r['total_points'] >= 90) { $ratingBadge = 'bg-success'; $ratingLabel = 'ممتاز'; }
            elseif ($r['total_points'] >= 80) { $ratingBadge = 'bg-primary'; $ratingLabel = 'جيد جداً'; }
            elseif ($r['total_points'] >= 70) { $ratingBadge = 'bg-info'; $ratingLabel = 'جيد'; }
            elseif ($r['total_points'] >= 60) { $ratingBadge = 'bg-warning'; $ratingLabel = 'مقبول'; }
            else { $ratingBadge = 'bg-danger'; $ratingLabel = 'ضعيف'; }
          ?>
          <tr class="<?php echo e($index < 3 ? 'table-warning' : ''); ?>">
            <td>
              <?php if($index == 0): ?> 🥇
              <?php elseif($index == 1): ?> 🥈
              <?php elseif($index == 2): ?> 🥉
              <?php else: ?> <?php echo e($index + 1); ?>

              <?php endif; ?>
            </td>
            <td class="fw-bold"><?php echo e($r['student']->name); ?></td>
            <td><?php echo e($r['student']->grade); ?> <?php echo e($r['student']->class_name); ?></td>
            <td><?php echo e($r['subjects_count']); ?></td>
            <td>
              <span class="badge <?php echo e($r['grade_avg'] >= 90 ? 'bg-success' : ($r['grade_avg'] >= 70 ? 'bg-primary' : 'bg-warning')); ?>">
                <?php echo e($r['grade_avg']); ?>%
              </span>
            </td>
            <td>
              <span class="badge <?php echo e($r['attendance_rate'] >= 90 ? 'bg-success' : ($r['attendance_rate'] >= 70 ? 'bg-primary' : 'bg-danger')); ?>">
                <?php echo e($r['attendance_rate']); ?>%
              </span>
            </td>
            <td>
              <?php if($r['manual_points'] > 0): ?>
                <span class="badge bg-info">+<?php echo e($r['manual_points']); ?></span>
              <?php else: ?>
                <span class="text-muted">0</span>
              <?php endif; ?>
            </td>
            <td><strong><?php echo e($r['total_points']); ?></strong></td>
            <td><span class="badge <?php echo e($ratingBadge); ?>"><?php echo e($ratingLabel); ?></span></td>
          </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
          <tr><td colspan="9" class="text-muted py-4">لا توجد بيانات لعرض الترتيب</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('student.layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\Alresalah-Managment\resources\views/student/honors.blade.php ENDPATH**/ ?>