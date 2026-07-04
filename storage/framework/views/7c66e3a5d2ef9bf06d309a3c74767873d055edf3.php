<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>إدارة الطلاب - مركز الرسالة</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600&display=swap" rel="stylesheet">

  <style>
    body {
      background-color: #f5f6fa;
      font-family: 'Cairo', sans-serif;
      margin: 0;
      overflow-x: hidden;
    }

    .sidebar {
      width: 240px;
      height: 100vh;
      position: fixed;
      right: 0;
      top: 0;
      background-color: #f57c00;
      color: #fff;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      padding: 20px 10px;
      border-radius: 0 0 0 10px;
      z-index: 1000;
      transition: transform 0.3s ease-in-out;
    }

    .sidebar.hide {
      transform: translateX(100%);
    }

    .sidebar .brand {
      text-align: center;
      margin-bottom: 30px;
    }

    .sidebar .brand img {
      width: 70px;
      background: #fff;
      border-radius: 50%;
      padding: 5px;
      margin-bottom: 8px;
      transition: transform 0.3s;
    }

    .sidebar .brand img:hover {
      transform: scale(1.05);
    }

    .sidebar a {
      color: #fff;
      text-decoration: none;
      display: block;
      padding: 10px 15px;
      margin: 5px 0;
      border-radius: 6px;
      transition: 0.2s;
      font-weight: 500;
    }

    .sidebar a:hover,
    .sidebar a.active {
      background-color: rgba(255, 255, 255, 0.25);
      color: #212529;
    }

    .sidebar .logout {
      background-color: rgba(255, 255, 255, 0.15);
      text-align: center;
      margin-top: 20px;
    }

    .content {
      margin-right: 260px;
      padding: 20px;
      transition: margin 0.3s;
    }

    .topbar {
      background-color: #fff;
      padding: 15px 20px;
      border-radius: 10px;
      box-shadow: 0 1px 3px rgba(0,0,0,0.1);
      margin-bottom: 20px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .topbar h5 {
      color: #333;
      margin: 0;
      font-weight: 700;
    }

    .menu-toggle {
      display: none;
      background: #f57c00;
      color: #fff;
      border: none;
      padding: 8px 12px;
      border-radius: 8px;
      font-size: 20px;
    }

    @media (max-width: 991px) {
      .sidebar {
        transform: translateX(100%);
      }
      .sidebar.show {
        transform: translateX(0);
      }
      .content {
        margin-right: 0;
      }
      .menu-toggle {
        display: inline-block;
      }
    }
  </style>
</head>
<body>

  <div class="sidebar" id="sidebar">
    <div>
      <a href="<?php echo e(route('home')); ?>" class="brand text-decoration-none text-center d-flex flex-column align-items-center">
        <div class="logo-bg mb-2">
          <img src="<?php echo e(asset('images/Logo.png')); ?>" alt="شعار الرسالة">
        </div>
        <span class="brand-text fw-bold text-white" style="font-size: 1.1rem;">مدرسة الرسالة</span>
      </a>

      <a href="<?php echo e(route('student.dashboard')); ?>" class="<?php echo e(request()->routeIs('student.dashboard') ? 'active' : ''); ?>">
        <i class="bi bi-speedometer2 me-2"></i> لوحة المعلومات
      </a>
      <a href="<?php echo e(route('student.index')); ?>" class="<?php echo e(request()->routeIs('student.index') ? 'active' : ''); ?>">
        <i class="bi bi-people me-2"></i> بيانات الطلاب
      </a>
      <a href="<?php echo e(route('student.attendance')); ?>" class="<?php echo e(request()->routeIs('student.attendance*') ? 'active' : ''); ?>">
        <i class="bi bi-calendar-check me-2"></i> الحضور والغياب
      </a>
      <a href="<?php echo e(route('student.grades')); ?>" class="<?php echo e(request()->routeIs('student.grades*') ? 'active' : ''); ?>">
        <i class="bi bi-journal-bookmark me-2"></i> الدرجات
      </a>
      <a href="<?php echo e(route('student.honors')); ?>" class="<?php echo e(request()->routeIs('student.honors') ? 'active' : ''); ?>">
        <i class="bi bi-trophy me-2"></i> تكريم المتفوقين
      </a>
      <a href="<?php echo e(route('student.points')); ?>" class="<?php echo e(request()->routeIs('student.points') ? 'active' : ''); ?>">
        <i class="bi bi-pencil-square me-2"></i> إدخال النقاط
      </a>
    </div>

    <form action="<?php echo e(route('logout')); ?>" method="POST">
      <?php echo csrf_field(); ?>
      <button class="btn btn-light w-100 mt-3 logout">
        <i class="bi bi-box-arrow-right me-1"></i> تسجيل الخروج
      </button>
    </form>
  </div>

  <div class="content">
    <div class="topbar">
      <div class="d-flex align-items-center gap-2">
        <button class="menu-toggle" id="menu-toggle"><i class="bi bi-list"></i></button>
        <h5 class="m-0 fw-bold"><?php echo $__env->yieldContent('title'); ?></h5>
      </div>
      <div><i class="bi bi-person-circle me-1"></i> <?php echo e(session('username') ?? 'المستخدم'); ?></div>
    </div>

    <?php if(session('success')): ?>
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?php echo e(session('success')); ?>

        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    <?php endif; ?>

    <?php if($errors->any()): ?>
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <ul class="mb-0">
          <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <li><?php echo e($error); ?></li>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    <?php endif; ?>

    <?php echo $__env->yieldContent('content'); ?>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    const toggleBtn = document.getElementById('menu-toggle');
    const sidebar = document.getElementById('sidebar');
    toggleBtn.addEventListener('click', () => {
      sidebar.classList.toggle('show');
    });
  </script>
  <?php echo $__env->yieldContent('scripts'); ?>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\Alresalah-Managment\resources\views/student/layout.blade.php ENDPATH**/ ?>