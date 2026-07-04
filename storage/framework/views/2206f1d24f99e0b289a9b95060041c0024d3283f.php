<?php $__env->startSection('title', 'بيانات الطلاب'); ?>

<?php $__env->startSection('content'); ?>


<div class="card p-3 mb-4 border-0 shadow-sm">
  <form method="GET" action="<?php echo e(route('student.index')); ?>" class="row g-2 align-items-end">
    <div class="col-md-4">
      <label class="form-label">بحث</label>
      <input type="text" name="search" class="form-control" placeholder="اسم الطالب أو ولي الأمر أو الهاتف..." value="<?php echo e(request('search')); ?>">
    </div>
    <div class="col-md-3">
      <label class="form-label">المجموعة</label>
      <select name="grade" class="form-select">
        <option value="">الكل</option>
        <option value="السنة الأولى" <?php echo e(request('grade') == 'السنة الأولى' ? 'selected' : ''); ?>>السنة الأولى</option>
        <option value="السنة الثانية" <?php echo e(request('grade') == 'السنة الثانية' ? 'selected' : ''); ?>>السنة الثانية</option>
      </select>
    </div>
    <div class="col-md-3">
      <label class="form-label">الحالة</label>
      <select name="status" class="form-select">
        <option value="">الكل</option>
        <option value="نشط" <?php echo e(request('status') == 'نشط' ? 'selected' : ''); ?>>نشط</option>
        <option value="منسحب" <?php echo e(request('status') == 'منسحب' ? 'selected' : ''); ?>>منسحب</option>
        <option value="منقول" <?php echo e(request('status') == 'منقول' ? 'selected' : ''); ?>>منقول</option>
      </select>
    </div>
    <div class="col-md-2">
      <button class="btn btn-warning w-100"><i class="bi bi-search me-1"></i> بحث</button>
    </div>
  </form>
</div>


<div class="card p-4 mb-4 border-0 shadow-sm">
  <h6 class="fw-bold mb-3"><i class="bi bi-person-plus me-2"></i> إضافة طالب جديد</h6>
  <form method="POST" action="<?php echo e(route('student.store')); ?>">
    <?php echo csrf_field(); ?>
    <div class="row g-3">
      <div class="col-md-3">
        <label class="form-label">اسم الطالب</label>
        <input type="text" name="name" class="form-control" required>
      </div>
      <div class="col-md-2">
        <label class="form-label">المجموعة</label>
        <select name="grade" class="form-select" required>
          <option value="السنة الأولى">السنة الأولى</option>
          <option value="السنة الثانية">السنة الثانية</option>
        </select>
      </div>
      <div class="col-md-2">
        <label class="form-label">الصف</label>
        <select name="class_name" class="form-select" required>
          <?php for($i = 5; $i <= 12; $i++): ?>
            <option value="<?php echo e($i); ?>"><?php echo e($i); ?></option>
          <?php endfor; ?>
        </select>
      </div>
      <div class="col-md-2">
        <label class="form-label">تاريخ الميلاد</label>
        <input type="date" name="date_of_birth" class="form-control" required>
      </div>
      <div class="col-md-2">
        <label class="form-label">الجنس</label>
        <select name="gender" class="form-select" required>
          <option value="ذكر">ذكر</option>
          <option value="أنثى">أنثى</option>
        </select>
      </div>
      <div class="col-md-2">
        <label class="form-label">تاريخ التسجيل</label>
        <input type="date" name="enrollment_date" class="form-control" value="<?php echo e(date('Y-m-d')); ?>" required>
      </div>
      <div class="col-md-3">
        <label class="form-label">ولي الأمر</label>
        <input type="text" name="parent_name" class="form-control" required>
      </div>
      <div class="col-md-2">
        <label class="form-label">الهاتف</label>
        <input type="text" name="phone" class="form-control" required>
      </div>
      <div class="col-md-3">
        <label class="form-label">العنوان</label>
        <input type="text" name="address" class="form-control">
      </div>
      <div class="col-md-2">
        <label class="form-label">الحالة</label>
        <select name="status" class="form-select">
          <option value="نشط">نشط</option>
          <option value="منسحب">منسحب</option>
          <option value="منقول">منقول</option>
        </select>
      </div>
      <div class="col-md-12">
        <label class="form-label">ملاحظات</label>
        <input type="text" name="notes" class="form-control">
      </div>
      <div class="col-md-12">
        <button type="submit" class="btn btn-success"><i class="bi bi-plus-circle me-1"></i> إضافة الطالب</button>
      </div>
    </div>
  </form>
</div>


<div class="mb-3">
  <a href="<?php echo e(route('student.exportPDF', request()->query())); ?>" class="btn btn-outline-danger btn-sm"><i class="bi bi-file-earmark-pdf me-1"></i> تصدير PDF</a>
  <a href="<?php echo e(route('student.exportExcel', request()->query())); ?>" class="btn btn-outline-success btn-sm"><i class="bi bi-file-earmark-spreadsheet me-1"></i> تصدير Excel</a>
  <span class="text-muted ms-2">إجمالي: <?php echo e($students->count()); ?> طالب</span>
</div>


<div class="card border-0 shadow-sm">
  <div class="table-responsive">
    <table class="table table-hover align-middle text-center mb-0">
      <thead>
        <tr>
          <th>#</th>
          <th>الاسم</th>
          <th>المجموعة</th>
          <th>الصف</th>
          <th>الجنس</th>
          <th>ولي الأمر</th>
          <th>الهاتف</th>
          <th>الحالة</th>
          <th>الإجراءات</th>
        </tr>
      </thead>
      <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
          <tr>
            <td><?php echo e($index + 1); ?></td>
            <td><?php echo e($student->name); ?></td>
            <td><span class="badge bg-secondary"><?php echo e($student->grade); ?></span></td>
            <td><?php echo e($student->class_name); ?></td>
            <td><?php echo e($student->gender); ?></td>
            <td><?php echo e($student->parent_name); ?></td>
            <td><?php echo e($student->phone); ?></td>
            <td>
              <span class="badge <?php echo e($student->status == 'نشط' ? 'bg-success' : ($student->status == 'منسحب' ? 'bg-danger' : 'bg-secondary')); ?>">
                <?php echo e($student->status); ?>

              </span>
            </td>
            <td>
              <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editModal<?php echo e($student->id); ?>">
                <i class="bi bi-pencil"></i>
              </button>
              <form action="<?php echo e(route('student.delete', $student->id)); ?>" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا الطالب؟')">
                <?php echo csrf_field(); ?>
                <?php echo method_field('DELETE'); ?>
                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
              </form>
            </td>
          </tr>

          
          <div class="modal fade" id="editModal<?php echo e($student->id); ?>" tabindex="-1">
            <div class="modal-dialog modal-lg">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title">تعديل بيانات الطالب - <?php echo e($student->name); ?></h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                  <form id="editForm<?php echo e($student->id); ?>">
                    <?php echo csrf_field(); ?>
                    <div class="row g-3">
                      <div class="col-md-4">
                        <label class="form-label">اسم الطالب</label>
                        <input type="text" name="name" class="form-control" value="<?php echo e($student->name); ?>" required>
                      </div>
                      <div class="col-md-4">
                        <label class="form-label">المجموعة</label>
                        <select name="grade" class="form-select" required>
                          <option value="السنة الأولى" <?php echo e($student->grade == 'السنة الأولى' ? 'selected' : ''); ?>>السنة الأولى</option>
                          <option value="السنة الثانية" <?php echo e($student->grade == 'السنة الثانية' ? 'selected' : ''); ?>>السنة الثانية</option>
                        </select>
                      </div>
                      <div class="col-md-4">
                        <label class="form-label">الصف</label>
                        <select name="class_name" class="form-select" required>
                          <?php for($i = 5; $i <= 12; $i++): ?>
                            <option value="<?php echo e($i); ?>" <?php echo e($student->class_name == $i ? 'selected' : ''); ?>><?php echo e($i); ?></option>
                          <?php endfor; ?>
                        </select>
                      </div>
                      <div class="col-md-4">
                        <label class="form-label">تاريخ الميلاد</label>
                        <input type="date" name="date_of_birth" class="form-control" value="<?php echo e($student->date_of_birth->format('Y-m-d')); ?>" required>
                      </div>
                      <div class="col-md-4">
                        <label class="form-label">الجنس</label>
                        <select name="gender" class="form-select" required>
                          <option value="ذكر" <?php echo e($student->gender == 'ذكر' ? 'selected' : ''); ?>>ذكر</option>
                          <option value="أنثى" <?php echo e($student->gender == 'أنثى' ? 'selected' : ''); ?>>أنثى</option>
                        </select>
                      </div>
                      <div class="col-md-4">
                        <label class="form-label">تاريخ التسجيل</label>
                        <input type="date" name="enrollment_date" class="form-control" value="<?php echo e($student->enrollment_date->format('Y-m-d')); ?>" required>
                      </div>
                      <div class="col-md-4">
                        <label class="form-label">ولي الأمر</label>
                        <input type="text" name="parent_name" class="form-control" value="<?php echo e($student->parent_name); ?>" required>
                      </div>
                      <div class="col-md-4">
                        <label class="form-label">الهاتف</label>
                        <input type="text" name="phone" class="form-control" value="<?php echo e($student->phone); ?>" required>
                      </div>
                      <div class="col-md-4">
                        <label class="form-label">الحالة</label>
                        <select name="status" class="form-select">
                          <option value="نشط" <?php echo e($student->status == 'نشط' ? 'selected' : ''); ?>>نشط</option>
                          <option value="منسحب" <?php echo e($student->status == 'منسحب' ? 'selected' : ''); ?>>منسحب</option>
                          <option value="منقول" <?php echo e($student->status == 'منقول' ? 'selected' : ''); ?>>منقول</option>
                        </select>
                      </div>
                      <div class="col-md-6">
                        <label class="form-label">العنوان</label>
                        <input type="text" name="address" class="form-control" value="<?php echo e($student->address); ?>">
                      </div>
                      <div class="col-md-6">
                        <label class="form-label">ملاحظات</label>
                        <input type="text" name="notes" class="form-control" value="<?php echo e($student->notes); ?>">
                      </div>
                    </div>
                  </form>
                </div>
                <div class="modal-footer">
                  <button class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                  <button class="btn btn-primary" onclick="updateStudent(<?php echo e($student->id); ?>)">حفظ التعديلات</button>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
          <tr><td colspan="9" class="text-muted py-4">لا يوجد طلاب مسجلين</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script>
function updateStudent(id) {
  const form = document.getElementById('editForm' + id);
  const formData = new FormData(form);

  fetch(`/student/update/${id}`, {
    method: 'POST',
    headers: {
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
      'Accept': 'application/json',
    },
    body: formData
  })
  .then(res => res.json())
  .then(data => {
    if (data.success) {
      location.reload();
    } else {
      alert(data.message || 'حدث خطأ أثناء التحديث');
    }
  })
  .catch(() => alert('حدث خطأ في الاتصال'));
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('student.layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\Alresalah-Managment\resources\views/student/students.blade.php ENDPATH**/ ?>