<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    // 🔹 صفحة تسجيل الدخول
    public function showLogin()
    {
        return view('Login'); // أو layouts.Login إذا كان داخل مجلد layouts
    }

    // 🔹 التحقق من بيانات الدخول
    public function login(Request $request)
    {
        $username = $request->input('username');
        $password = $request->input('password');

        // 🔹 مجموعة المستخدمين التجريبية مع المسارات الصحيحة
        $users = [
    'bus' => [
        'password' => 'bus123',
        'redirect' => 'bus_expenses'
    ],
    'finance' => [
        'password' => 'finance123',
        'redirect' => 'financial.index'
    ],
    'admin' => [
        'password' => 'admin123',
        'redirect' => 'dashboard'
    ],
];

        // 🔸 التحقق من بيانات المستخدم
        foreach ($users as $key => $user) {
            if ($username === $key && $password === $user['password']) {
                return redirect()->route($user['redirect']);
            }
        }

        // 🔸 في حال الخطأ
        return back()->with('error', 'اسم المستخدم أو كلمة المرور غير صحيحة');
    }
}
