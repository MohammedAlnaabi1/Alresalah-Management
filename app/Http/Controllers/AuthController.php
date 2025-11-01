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

    // 🔹 التحقق من بيانات الدخول
    public function login(Request $request)
    {
        // التحقق من أن الحقول غير فارغة
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $username = trim($request->input('username'));
        $password = trim($request->input('password'));

        // المستخدمين التجريبيين
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
        ];


        

        foreach ($users as $key => $user) {
            if ($username === $key && $password === $user['password']) {
                

                // تسجيل الدخول في جدول LoginActivity
                LoginActivity::create([
                    'username' => $username,
                    'ip_address' => $request->ip(),
                    'login_time' => now(),
                ]);

                // التوجيه
                if (Route::has($user['redirect'])) {
                    return redirect()->route($user['redirect']);
                } else {
                    return back()->with('error', '⚠️ لم يتم العثور على الصفحة المطلوبة. تحقق من المسار.');
                }
            }
        }

        return back()->with('error', '❌ اسم المستخدم أو كلمة المرور غير صحيحة');
    }

    // 🔹 تسجيل الخروج
    public function logout(Request $request)
    {
        $request->session()->flush();
        return redirect()->route('login');
    }
}
