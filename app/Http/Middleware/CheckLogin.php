<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckLogin
{
    public function handle(Request $request, Closure $next)
    {
        // إذا لم يكن المستخدم مسجل دخول
        if (!$request->session()->has('logged_in')) {
            return redirect()->route('login')->with('error', 'يجب تسجيل الدخول أولاً.');
        }

        return $next($request);
    }
}
