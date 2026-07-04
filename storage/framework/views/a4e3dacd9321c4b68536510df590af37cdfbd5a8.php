<?php $__env->startSection('title', 'إدخال النقاط'); ?>

<?php $__env->startSection('content'); ?>


<div class="card p-3 mb-4 border-0 shadow-sm">
  <h6 class="fw-bold mb-3"><i class="bi bi-funnel me-2"></i> اختر البيانات</h6>
  <form method="GET" action="<?php echo e(route('student.points')); ?>" class="row g-2 align-items-end">
    <div class="col-md-3">
      <label class="form-label fw-bold">العام الدراسي</label>
      <select name="academic_year" class="form-select" onchange="this.form.submit()">
        <?php $__currentLoopData = $academicYears; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $y): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($y); ?>" <?php echo e($academicYear == $y ? 'selected' : ''); ?>><?php echo e($y); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </select>
    </div>
    <div class="col-md-3">
      <label class="form-label fw-bold">المجموعة</label>
      <select name="group" class="form-select" onchange="this.form.submit()">
        <option value="">-- اختر المجموعة --</option>
        <?php $__currentLoopData = $groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $g): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($g); ?>" <?php echo e($group == $g ? 'selected' : ''); ?>><?php echo e($g); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </select>
    </div>
    <div class="col-md-3">
      <label class="form-label fw-bold">المادة</label>
      <select name="subject" class="form-select" onchange="this.form.submit()">
        <option value="">كل المواد</option>
        <?php $__currentLoopData = $allSubjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($s); ?>" <?php echo e($subjectFilter == $s ? 'selected' : ''); ?>><?php echo e($s); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </select>
    </div>
    <div class="col-md-3">
      <a href="<?php echo e(route('student.honors', ['academic_year' => $academicYear, 'grade' => $group])); ?>" class="btn btn-outline-warning w-100">
        <i class="bi bi-trophy me-1"></i> عرض الترتيب
      </a>
    </div>
  </form>
</div>

<?php if($group && $allStudents->count() > 0): ?>

<div class="card border-0 shadow-sm">
  <div class="card-header bg-white d-flex justify-content-between align-items-center">
    <h6 class="fw-bold mb-0">
      <i class="bi bi-pencil-square me-2"></i> نقاط <?php echo e($group); ?>

      <?php if($subjectFilter): ?>
        — <?php echo e($subjectFilter); ?>

      <?php else: ?>
        — كل المواد
      <?php endif; ?>
    </h6>
    <div>
      <span class="badge bg-primary me-2"><?php echo e($allStudents->count()); ?> طالب</span>
      <span id="saveStatus" class="text-muted" style="font-size: 0.85rem;"></span>
    </div>
  </div>
  <div class="card-body">
    <form id="pointsForm">
      <input type="hidden" name="semester" value="الأول">
      <input type="hidden" name="academic_year" value="<?php echo e($academicYear); ?>">

      <div class="table-responsive">
        <table class="table table-hover align-middle text-center mb-0">
          <thead>
            <tr>
              <th>#</th>
              <th>اسم الطالب</th>
              <th>الصف</th>
              <?php $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sub): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <th style="width: 140px;"><?php echo e($sub); ?></th>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              <th style="width: 80px;">المجموع</th>
            </tr>
          </thead>
          <tbody>
            <?php $__currentLoopData = $allStudents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <?php
                $studentPts = $existingPoints[$student->id] ?? [];
                $totalPts = 0;
                foreach ($subjects as $sub) {
                    $totalPts += isset($studentPts[$sub]) ? (int)$studentPts[$sub]->points : 0;
                }
              ?>
              <tr>
                <td><?php echo e($index + 1); ?></td>
                <td class="fw-bold"><?php echo e($student->name); ?></td>
                <td><?php echo e($student->class_name); ?></td>
                <?php $__currentLoopData = $subjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sub): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <?php $sp = $studentPts[$sub] ?? null; ?>
                  <td>
                    <div class="input-group input-group-sm">
                      <button type="button" class="btn btn-outline-danger btn-sm btn-minus" data-target="pts-<?php echo e($student->id); ?>-<?php echo e($loop->index); ?>">−</button>
                      <input type="number" id="pts-<?php echo e($student->id); ?>-<?php echo e($loop->index); ?>"
                        name="points[<?php echo e($student->id); ?>][<?php echo e($sub); ?>]"
                        class="form-control form-control-sm text-center points-input"
                        data-student="<?php echo e($student->id); ?>"
                        value="<?php echo e($sp ? (int)$sp->points : 0); ?>" step="1" min="0" max="100"
                        style="max-width: 55px;">
                      <button type="button" class="btn btn-outline-success btn-sm btn-plus" data-target="pts-<?php echo e($student->id); ?>-<?php echo e($loop->index); ?>">+</button>
                    </div>
                  </td>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <td>
                  <strong class="text-primary" id="total-<?php echo e($student->id); ?>"><?php echo e($totalPts); ?></strong>
                </td>
              </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </tbody>
        </table>
      </div>

      <div class="text-center mt-3">
        <button type="submit" class="btn btn-success btn-lg" id="saveBtn">
          <i class="bi bi-check-circle me-1"></i> حفظ النقاط
        </button>
      </div>
    </form>
  </div>
</div>

<?php elseif(!$group): ?>

  <div class="text-center py-5">
    <i class="bi bi-pencil-square" style="font-size: 4rem; color: #ddd;"></i>
    <h5 class="text-muted mt-3">اختر المجموعة لعرض الطلاب وإدخال النقاط</h5>
  </div>

<?php else: ?>
  <div class="alert alert-info text-center">لا يوجد طلاب نشطون في هذه المجموعة</div>
<?php endif; ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script>
function updateStudentTotal(studentId) {
  const inputs = document.querySelectorAll(`.points-input[data-student="${studentId}"]`);
  let total = 0;
  inputs.forEach(inp => { total += parseInt(inp.value) || 0; });
  document.getElementById('total-' + studentId).textContent = total;
}

document.querySelectorAll('.points-input').forEach(input => {
  input.addEventListener('input', function() { updateStudentTotal(this.dataset.student); });
});

document.querySelectorAll('.btn-plus').forEach(btn => {
  btn.addEventListener('click', function() {
    const input = document.getElementById(this.dataset.target);
    const max = parseInt(input.max) || 100;
    let val = parseInt(input.value) || 0;
    if (val < max) { input.value = val + 1; updateStudentTotal(input.dataset.student); }
  });
});

document.querySelectorAll('.btn-minus').forEach(btn => {
  btn.addEventListener('click', function() {
    const input = document.getElementById(this.dataset.target);
    let val = parseInt(input.value) || 0;
    if (val > 0) { input.value = val - 1; updateStudentTotal(input.dataset.student); }
  });
});

document.getElementById('pointsForm')?.addEventListener('submit', function(e) {
  e.preventDefault();

  const btn = document.getElementById('saveBtn');
  const status = document.getElementById('saveStatus');
  btn.disabled = true;
  btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> جاري الحفظ...';

  fetch('<?php echo e(route("student.points.store")); ?>', {
    method: 'POST',
    headers: {
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
      'Accept': 'application/json',
    },
    body: new FormData(this)
  })
  .then(res => res.ok ? res.json().catch(() => ({ success: true })) : Promise.reject())
  .then(data => {
    status.innerHTML = '<span class="text-success"><i class="bi bi-check-circle me-1"></i> تم الحفظ ✅</span>';
    btn.disabled = false;
    btn.innerHTML = '<i class="bi bi-check-circle me-1"></i> حفظ النقاط';
    setTimeout(() => status.textContent = '', 3000);
  })
  .catch(() => {
    status.innerHTML = '<span class="text-danger"><i class="bi bi-x-circle me-1"></i> حدث خطأ</span>';
    btn.disabled = false;
    btn.innerHTML = '<i class="bi bi-check-circle me-1"></i> حفظ النقاط';
  });
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('student.layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\Alresalah-Managment\resources\views/student/points.blade.php ENDPATH**/ ?>