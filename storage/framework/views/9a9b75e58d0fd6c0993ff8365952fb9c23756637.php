<?php $__env->startSection('title', 'الدرجات'); ?>

<?php $__env->startSection('content'); ?>


<div class="card p-4 mb-4 border-0 shadow-sm">
  <h6 class="fw-bold mb-3"><i class="bi bi-funnel me-2"></i> اختر البيانات</h6>
  <div class="row g-3 align-items-end">
    <div class="col-md-4">
      <label class="form-label fw-bold">المجموعة</label>
      <select id="groupSelect" class="form-select" onchange="applyFilter()">
        <option value="">-- اختر المجموعة --</option>
        <?php $__currentLoopData = $groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $g): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($g); ?>" <?php echo e($group == $g ? 'selected' : ''); ?>><?php echo e($g); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </select>
    </div>
    <div class="col-md-4">
      <label class="form-label fw-bold">العام الدراسي</label>
      <select id="yearSelect" class="form-select" onchange="applyFilter()">
        <?php $__currentLoopData = $academicYears; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $y): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($y); ?>" <?php echo e($academicYear == $y ? 'selected' : ''); ?>><?php echo e($y); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </select>
    </div>
    <div class="col-md-4">
      <div class="d-flex gap-2">
        <a href="<?php echo e(route('student.grades.exportPDF', ['group' => $group, 'academic_year' => $academicYear])); ?>" class="btn btn-outline-danger flex-fill <?php echo e(!$group ? 'disabled' : ''); ?>">
          <i class="bi bi-file-earmark-pdf me-1"></i> PDF
        </a>
        <a href="<?php echo e(route('student.grades.exportExcel', ['group' => $group, 'academic_year' => $academicYear])); ?>" class="btn btn-outline-success flex-fill <?php echo e(!$group ? 'disabled' : ''); ?>">
          <i class="bi bi-file-earmark-spreadsheet me-1"></i> Excel
        </a>
      </div>
    </div>
  </div>
</div>

<?php if($group): ?>

  
  <form method="POST" action="<?php echo e(route('student.grades.store')); ?>">
    <?php echo csrf_field(); ?>
    <input type="hidden" name="academic_year" value="<?php echo e($academicYear); ?>">
    <input type="hidden" name="group" value="<?php echo e($group); ?>">

    <div class="card border-0 shadow-sm">
      <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h6 class="fw-bold mb-0">
          <i class="bi bi-journal-bookmark me-2"></i> درجات <?php echo e($group); ?> — <?php echo e($academicYear); ?>

        </h6>
        <div>
          <span class="badge bg-primary me-2"><?php echo e($students->count()); ?> طالب</span>
          <span class="text-muted" id="saveStatus" style="font-size: 0.85rem;"></span>
        </div>
      </div>
      <div class="table-responsive">
        <table class="table table-hover align-middle text-center mb-0">
          <thead>
            <tr>
              <th>#</th>
              <th>اسم الطالب</th>
              <th>الفصل</th>
              <?php $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sub): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <th style="width: 100px;"><?php echo e($sub); ?><br><small class="text-muted">/ <?php echo e($maxGrade); ?></small></th>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              <th>المجموع</th>
              <th>النسبة</th>
            </tr>
          </thead>
          <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
              <?php
                $totalGrade = 0;
                $totalMax = 0;
                foreach ($subjects as $sub) {
                    $existing = $existingGrades[$student->id][$sub] ?? null;
                    if ($existing) {
                        $totalGrade += $existing->grade_value;
                        $totalMax += $existing->max_grade;
                    } else {
                        $totalMax += $maxGrade;
                    }
                }
                $pct = $totalMax > 0 ? round(($totalGrade / $totalMax) * 100, 1) : 0;
              ?>
              <tr>
                <td><?php echo e($index + 1); ?></td>
                <td class="fw-bold"><?php echo e($student->name); ?></td>
                <td><?php echo e($student->class_name); ?></td>
                <?php $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sub): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <?php $existing = $existingGrades[$student->id][$sub] ?? null; ?>
                  <td>
                    <input type="number"
                      name="grades[<?php echo e($student->id); ?>][<?php echo e($sub); ?>]"
                      class="form-control form-control-sm text-center grade-input"
                      data-student="<?php echo e($student->id); ?>"
                      value="<?php echo e($existing ? $existing->grade_value : ''); ?>"
                      placeholder="-"
                      step="0.01" min="0" max="<?php echo e($maxGrade); ?>">
                  </td>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <td class="fw-bold total-cell" id="total-<?php echo e($student->id); ?>"><?php echo e($totalGrade); ?></td>
                <td>
                  <span class="badge pct-badge <?php echo e($pct >= 90 ? 'bg-success' : ($pct >= 70 ? 'bg-primary' : ($pct >= 50 ? 'bg-warning' : 'bg-danger'))); ?>"
                    id="pct-<?php echo e($student->id); ?>"><?php echo e($pct); ?>%</span>
                </td>
              </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
              <tr><td colspan="<?php echo e(5 + count($subjects)); ?>" class="text-muted py-4">لا يوجد طلاب في هذه المجموعة</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

    <?php if($students->count() > 0): ?>
    <div class="mt-3 text-center">
      <button type="submit" class="btn btn-success btn-lg">
        <i class="bi bi-check-circle me-1"></i> حفظ الدرجات
      </button>
    </div>
    <?php endif; ?>
  </form>

<?php else: ?>

  
  <div class="text-center py-5">
    <i class="bi bi-journal-bookmark" style="font-size: 4rem; color: #ddd;"></i>
    <h5 class="text-muted mt-3">اختر المجموعة لعرض الطلاب وإدخال الدرجات</h5>
  </div>

<?php endif; ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script>
function applyFilter() {
  const group = document.getElementById('groupSelect').value;
  const year = document.getElementById('yearSelect').value;
  if (group) {
    window.location.href = `<?php echo e(route('student.grades')); ?>?group=${encodeURIComponent(group)}&academic_year=${encodeURIComponent(year)}`;
  }
}

document.querySelectorAll('.grade-input').forEach(input => {
  input.addEventListener('input', function() {
    const studentId = this.dataset.student;
    const inputs = document.querySelectorAll(`.grade-input[data-student="${studentId}"]`);
    let total = 0;
    let maxTotal = 0;
    const maxPerSubject = <?php echo e($maxGrade); ?>;

    inputs.forEach(inp => {
      const val = parseFloat(inp.value) || 0;
      total += val;
      maxTotal += maxPerSubject;
    });

    const pct = maxTotal > 0 ? Math.round((total / maxTotal) * 1000) / 10 : 0;

    document.getElementById('total-' + studentId).textContent = total;

    const badge = document.getElementById('pct-' + studentId);
    badge.textContent = pct + '%';
    badge.className = 'badge pct-badge ' +
      (pct >= 90 ? 'bg-success' : (pct >= 70 ? 'bg-primary' : (pct >= 50 ? 'bg-warning' : 'bg-danger')));
  });
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('student.layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\Alresalah-Managment\resources\views/student/grades.blade.php ENDPATH**/ ?>