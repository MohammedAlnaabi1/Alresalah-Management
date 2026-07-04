<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LoginActivity;
use Illuminate\Support\Facades\Route;

class AuthController extends Controller
{
    // 🔹 صفحة تسجيل الدخول
    public function showLogin()
    {
        return view('Login');
    }

    public function login(Request $request)
{
    // ✅ التحقق من الحقول
    $request->validate([
        'username' => 'required',
        'password' => 'required',
    ]);

    $username = trim($request->input('username'));
    $password = trim($request->input('password'));

    // ✅ المستخدمين التجريبيين
    $users = [
        'bus' => [
            'password' => 'bus123',
            'redirect' => 'dashboard'
        ],
        'finance' => [
            'password' => 'finance123',
            'redirect' => 'financial.dashboard'
        ],
        'admin' => [
            'password' => 'admin123',
            'redirect' => 'admin.dashboard'
        ],
        'alresalah' => [
            'password' => 'alresalah123',
            'redirect' => 'admin.dashboard'
        ],
        'alrs' => [
            'password' => 'alrs123',
            'redirect' => 'student.dashboard'
        ],
    ];

    foreach ($users as $key => $user) {
        if ($username === $key && $password === $user['password']) {

            // 🟢 حفظ بيانات الجلسة
            $request->session()->put('logged_in', true);
            $request->session()->put('username', $username);
            $request->session()->put('role', $key);

            // 🟢 تسجيل النشاط
            \App\Models\LoginActivity::create([
                'username' => $username,
                'ip_address' => $request->ip(),
                'login_time' => now(),
            ]);

            // 🟢 التوجيه للصفحة الصحيحة
            return redirect()->route($user['redirect']);
        }
    }

    return back()->with('error', '❌ اسم المستخدم أو كلمة المرور غير صحيحة');
}

    // 🔹 تسجيل الخروج
   public function logout(Request $request)
{
    $request->session()->forget(['logged_in', 'username', 'role']);
    $request->session()->flush();
    return redirect()->route('login');
}

}
