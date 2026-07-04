<?php $__env->startSection('title', 'الحضور والغياب'); ?>

<?php $__env->startSection('content'); ?>


<div class="card p-4 mb-4 border-0 shadow-sm">
  <h6 class="fw-bold mb-3"><i class="bi bi-funnel me-2"></i> اختر البيانات</h6>
  <form method="GET" action="<?php echo e(route('student.attendance')); ?>" class="row g-3 align-items-end">
    <div class="col-md-3">
      <label class="form-label fw-bold">التاريخ</label>
      <input type="date" name="date" class="form-control" value="<?php echo e($date); ?>">
    </div>
    <div class="col-md-3">
      <label class="form-label fw-bold">المجموعة</label>
      <select name="group" class="form-select" required>
        <option value="">-- اختر المجموعة --</option>
        <?php $__currentLoopData = $groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $g): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($g); ?>" <?php echo e($group == $g ? 'selected' : ''); ?>><?php echo e($g); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </select>
    </div>
    <div class="col-md-3">
      <label class="form-label fw-bold">المادة</label>
      <select name="subject" class="form-select" required>
        <option value="">-- اختر المادة --</option>
        <?php $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($s); ?>" <?php echo e($subject == $s ? 'selected' : ''); ?>><?php echo e($s); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </select>
    </div>
    <div class="col-md-3">
      <button class="btn btn-warning w-100 btn-lg">
        <i class="bi bi-search me-1"></i> عرض الطلاب
      </button>
    </div>
  </form>
</div>

<?php if($group && $subject): ?>

  
  <div class="alert alert-light border shadow-sm mb-4 d-flex justify-content-between align-items-center">
    <div>
      <i class="bi bi-bookmark-fill text-warning me-1"></i>
      <strong><?php echo e($subject); ?></strong> —
      <span class="badge bg-secondary"><?php echo e($group); ?></span> —
      <?php echo e(\Carbon\Carbon::parse($date)->format('Y/m/d')); ?>

    </div>
    <div>
      <span class="badge bg-success me-1">حاضر: <?php echo e($presentCount); ?></span>
      <span class="badge bg-danger me-1">غائب: <?php echo e($absentCount); ?></span>
      <span class="badge bg-warning me-1">متأخر: <?php echo e($lateCount); ?></span>
      <a href="<?php echo e(route('student.attendance.exportPDF', ['date' => $date, 'subject' => $subject, 'group' => $group])); ?>" class="btn btn-sm btn-outline-danger ms-2">
        <i class="bi bi-file-earmark-pdf"></i> PDF
      </a>
      <a href="<?php echo e(route('student.attendance.exportExcel', ['date' => $date, 'subject' => $subject, 'group' => $group])); ?>" class="btn btn-sm btn-outline-success ms-1">
        <i class="bi bi-file-earmark-spreadsheet"></i> Excel
      </a>
    </div>
  </div>

  
  <?php if($students->count() > 0): ?>
  <form method="POST" action="<?php echo e(route('student.attendance.store')); ?>">
    <?php echo csrf_field(); ?>
    <input type="hidden" name="date" value="<?php echo e($date); ?>">
    <input type="hidden" name="subject" value="<?php echo e($subject); ?>">
    <input type="hidden" name="group" value="<?php echo e($group); ?>">

    <div class="card border-0 shadow-sm">
      <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h6 class="fw-bold mb-0">
          <i class="bi bi-people me-2"></i> طلاب <?php echo e($group); ?> — <?php echo e($subject); ?>

        </h6>
        <span class="badge bg-primary"><?php echo e($students->count()); ?> طالب</span>
      </div>
      <div class="table-responsive">
        <table class="table table-hover align-middle text-center mb-0">
          <thead>
            <tr>
              <th>#</th>
              <th>اسم الطالب</th>
              <th>الفصل</th>
              <th>حاضر</th>
              <th>غائب</th>
              <th>متأخر</th>
              <th style="width: 200px;">ملاحظات</th>
            </tr>
          </thead>
          <tbody>
            <?php $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <?php $currentStatus = $attendances[$student->id] ?? ''; ?>
              <tr>
                <td><?php echo e($index + 1); ?></td>
                <td class="fw-bold"><?php echo e($student->name); ?></td>
                <td><?php echo e($student->class_name); ?></td>
                <td>
                  <input type="radio" name="attendance[<?php echo e($student->id); ?>][status]" value="حاضر"
                    class="form-check-input" <?php echo e($currentStatus == 'حاضر' ? 'checked' : ''); ?> required>
                </td>
                <td>
                  <input type="radio" name="attendance[<?php echo e($student->id); ?>][status]" value="غائب"
                    class="form-check-input" <?php echo e($currentStatus == 'غائب' ? 'checked' : ''); ?>>
                </td>
                <td>
                  <input type="radio" name="attendance[<?php echo e($student->id); ?>][status]" value="متأخر"
                    class="form-check-input" <?php echo e($currentStatus == 'متأخر' ? 'checked' : ''); ?>>
                </td>
                <td>
                  <input type="text" name="attendance[<?php echo e($student->id); ?>][notes]" class="form-control form-control-sm"
                    value="<?php echo e($attendanceNotes[$student->id] ?? ''); ?>" placeholder="ملاحظة...">
                </td>
              </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </tbody>
        </table>
      </div>
    </div>

    <div class="mt-3 text-center">
      <button type="submit" class="btn btn-success btn-lg">
        <i class="bi bi-check-circle me-1"></i> حفظ الحضور
      </button>
      <button type="button" class="btn btn-outline-success ms-2" onclick="markAll('حاضر')">
        <i class="bi bi-check-all me-1"></i> الكل حاضر
      </button>
      <button type="button" class="btn btn-outline-danger ms-2" onclick="markAll('غائب')">
        <i class="bi bi-x-circle me-1"></i> الكل غائب
      </button>
      <button type="button" class="btn btn-outline-warning ms-2" onclick="markAll('متأخر')">
        <i class="bi bi-clock me-1"></i> الكل متأخر
      </button>
    </div>
  </form>
  <?php else: ?>
    <div class="alert alert-info text-center">لا يوجد طلاب في هذه المجموعة</div>
  <?php endif; ?>

  
  <div class="card p-3 mt-4 border-0 shadow-sm">
    <div class="d-flex justify-content-between align-items-center">
      <span class="text-muted"><i class="bi bi-arrow-left-right me-1"></i> انتقال سريع:</span>
      <div class="d-flex gap-2 flex-wrap">
        <?php $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <a href="<?php echo e(route('student.attendance', ['date' => $date, 'group' => $group, 'subject' => $s])); ?>"
            class="btn btn-sm <?php echo e($s == $subject ? 'btn-warning' : 'btn-outline-secondary'); ?>">
            <?php echo e($s); ?>

          </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </div>
      <a href="<?php echo e(route('student.attendance.report', ['group' => $group, 'subject' => $subject, 'to_date' => $date])); ?>" class="btn btn-sm btn-info text-white">
        <i class="bi bi-calendar-range me-1"></i> كل الأيام
      </a>
    </div>
  </div>

<?php else: ?>

  
  <div class="text-center py-5">
    <i class="bi bi-clipboard-check" style="font-size: 4rem; color: #ddd;"></i>
    <h5 class="text-muted mt-3">اختر المجموعة والمادة لعرض قائمة الطلاب</h5>
  </div>

<?php endif; ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script>
function markAll(status) {
  document.querySelectorAll(`input[type="radio"][value="${status}"]`).forEach(r => r.checked = true);
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('student.layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\Alresalah-Managment\resources\views/student/attendance.blade.php ENDPATH**/ ?>