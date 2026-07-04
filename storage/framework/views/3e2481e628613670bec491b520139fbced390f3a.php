<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>تسجيل الدخول - نظام إدارة المؤسسة</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600&display=swap" rel="stylesheet">

  <style>
    body {
      background: linear-gradient(135deg, #eaf3fa 0%, #ffffff 100%);
      font-family: 'Cairo', sans-serif;
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .login-box {
      max-width: 420px;
      width: 100%;
      background: #fff;
      padding: 45px 35px;
      border-radius: 20px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
      position: relative;
      overflow: hidden;
      animation: fadeInUp 0.8s ease;
    }

    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translateY(40px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .login-box::before {
  content: "";
  position: absolute;
  top: -40%;
  left: -40%;
  width: 200%;
  height: 200%;
  background: radial-gradient(circle at 30% 20%, rgba(241,139,34,0.1), transparent 60%);
  z-index: 0;
  pointer-events: none; /* 🔹 السماح بالنقر والكتابة على العناصر تحتها */
}

    .login-logo {
      width: 110px;
      height: 110px;
      display: block;
      margin: 0 auto 25px;
      border-radius: 50%;
      object-fit: cover;
      border: 4px solid #f18b22;
      transition: transform 0.4s ease;
    }

    .login-logo:hover {
      transform: rotate(5deg) scale(1.05);
    }

    h3 {
      position: relative;
      z-index: 1;
      font-weight: 600;
    }

    .form-control {
      border-radius: 30px;
      padding: 12px 15px;
      border: 1.8px solid #ddd;
      transition: all 0.3s ease;
    }

    .form-control:focus {
      border-color: #f18b22;
      box-shadow: 0 0 8px rgba(241, 139, 34, 0.3);
    }

    .btn-login {
      width: 100%;
      border-radius: 30px;
      padding: 12px;
      background-color: #f18b22;
      color: #fff;
      font-weight: 600;
      border: none;
      transition: all 0.3s ease;
      position: relative;
      overflow: hidden;
    }

    .btn-login::before {
      content: "";
      position: absolute;
      left: -100%;
      top: 0;
      width: 100%;
      height: 100%;
      background: rgba(255,255,255,0.2);
      transition: left 0.4s ease;
    }

    .btn-login:hover::before {
      left: 100%;
    }

    .btn-login:hover {
      background-color: #e07810;
      box-shadow: 0 5px 15px rgba(241, 139, 34, 0.4);
      transform: scale(1.02);
    }

    .alert {
      animation: fadeIn 0.6s ease;
    }

    @keyframes fadeIn {
      from { opacity: 0; }
      to { opacity: 1; }
    }

    .toggle-password {
      position: absolute;
      top: 50%;
      left: 15px;
      transform: translateY(-50%);
      cursor: pointer;
      color: #999;
      transition: color 0.3s;
    }

    .toggle-password:hover {
      color: #f18b22;
    }

    .password-wrapper {
      position: relative;
    }

  </style>
</head>
<body>

  <div class="login-box">
    <img src="<?php echo e(asset('images/Logo.png')); ?>" alt="شعار المؤسسة" class="login-logo">
    <h3 class="text-center text-warning mb-4">تسجيل الدخول</h3>

    <?php if(session('error')): ?>
      <div class="alert alert-danger"><?php echo e(session('error')); ?></div>
    <?php endif; ?>

    <form action="<?php echo e(route('login.submit')); ?>" method="POST">
      <?php echo csrf_field(); ?>
      <div class="mb-3">
        <input type="text" name="username" class="form-control" placeholder="اسم المستخدم" required>
      </div>
      <div class="mb-3 password-wrapper">
        <input type="password" name="password" id="password" class="form-control" placeholder="كلمة المرور" required>
        <i class="bi bi-eye toggle-password" id="togglePassword"></i>
      </div>
      <button type="submit" class="btn btn-login">دخول</button>
    </form>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.js"></script>
  <script>
    // 🔹 إظهار / إخفاء كلمة المرور
    const togglePassword = document.getElementById('togglePassword');
    const password = document.getElementById('password');

    togglePassword.addEventListener('click', () => {
      const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
      password.setAttribute('type', type);
      togglePassword.classList.toggle('bi-eye-slash');
    });

    // 🔹 تأثير بسيط عند إرسال النموذج
    document.querySelector('form').addEventListener('submit', function() {
      document.querySelector('.btn-login').innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> جاري التحقق...';
    });
  </script>

</body>
</html>
<?php /**PATH C:\xampp\htdocs\Alresalah-Managment\resources\views/Login.blade.php ENDPATH**/ ?>