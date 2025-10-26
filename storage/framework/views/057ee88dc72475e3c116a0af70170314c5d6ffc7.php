<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  
  <meta charset="UTF-8">
  <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>الإدارة المالية - مركز الرسالة</title>

  
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body {
      background-color: #f5f6fa;
      font-family: 'Cairo', sans-serif;
    }

    /* Sidebar */
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
    }

    .sidebar .brand {
      font-size: 1.2rem;
      font-weight: bold;
      text-align: center;
      margin-bottom: 30px;
    }

    .sidebar .brand img {
      width: 60px;
      margin-bottom: 10px;
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

    /* Content area */
    .content {
      margin-right: 260px;
      padding: 20px;
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
  </style>
</head>
<body>

  
  <div class="sidebar">
    <div>
      <div class="brand">
        <img src="<?php echo e(asset('imag/Logo.png')); ?>" alt="شعار الرسالة">
        <div>الإدارة المالية</div>
      </div>

      <a href="<?php echo e(route('financial.index')); ?>" class="<?php echo e(request()->routeIs('financial.index') ? 'active' : ''); ?>">
        <i class="bi bi-speedometer2 me-2"></i> لوحة المعلومات
      </a>
      <a href="<?php echo e(route('financial.revenues')); ?>" class="<?php echo e(request()->routeIs('financial.revenues') ? 'active' : ''); ?>">
        <i class="bi bi-cash-stack me-2"></i> الإيرادات
      </a>
      <a href="<?php echo e(route('financial.expenses')); ?>" class="<?php echo e(request()->routeIs('financial.expenses') ? 'active' : ''); ?>">
        <i class="bi bi-wallet2 me-2"></i> المصروفات
      </a>
      <a href="<?php echo e(route('financial.reports')); ?>" class="<?php echo e(request()->routeIs('financial.reports') ? 'active' : ''); ?>">
        <i class="bi bi-graph-up-arrow me-2"></i> التقارير
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
      <h5 class="m-0 fw-bold"><?php echo $__env->yieldContent('title'); ?></h5>
      <div>
        <i class="bi bi-person-circle me-1"></i> المستخدم
      </div>
    </div>

    
    <?php echo $__env->yieldContent('content'); ?>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\Alresalah-Managment\resources\views/financial/layout.blade.php ENDPATH**/ ?>