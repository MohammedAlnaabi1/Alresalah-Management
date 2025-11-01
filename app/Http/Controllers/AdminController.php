<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Bus;
use App\Models\Revenue;
use App\Models\Expense;
use App\Models\LoginActivity;
use Carbon\Carbon;
use App\Models\Contact;


class AdminController extends Controller
{
    public function index()
    {
        $loginCount = LoginActivity::count();
        $userCount = User::count();
        $busCount = Bus::count();
        $totalRevenues = Revenue::sum('amount');
        $totalExpenses = Expense::sum('amount');
        $netProfit = $totalRevenues - $totalExpenses;

        $logins = LoginActivity::latest()->take(10)->get();
        $recentRevenues = Revenue::latest()->take(5)->get();
        $recentExpenses = Expense::latest()->take(5)->get();

        // بيانات المخطط البياني
        $months = collect(range(1, 12))->map(fn($m) => Carbon::create()->month($m)->format('M'));
        $revenuesData = [];
        $expensesData = [];

        foreach (range(1, 12) as $m) {
            $revenuesData[] = Revenue::whereMonth('created_at', $m)->sum('amount');
            $expensesData[] = Expense::whereMonth('created_at', $m)->sum('amount');
        }

        $contacts = Contact::latest()->take(10)->get(); // عرض آخر 10 رسائل فقط
    

        // 🔹 الإشعارات المدمجة (آخر الأحداث من 3 مصادر)
$notifications = collect();

// تسجيلات الدخول
foreach ($logins as $log) {
    $notifications->push([
        'type' => 'login',
        'message' => "تسجيل دخول جديد من المستخدم {$log->username}",
        'time' => $log->login_time,
    ]);
}

// الإيرادات
foreach ($recentRevenues as $rev) {
    $notifications->push([
        'type' => 'revenue',
        'message' => "تمت إضافة إيراد جديد بقيمة {$rev->amount} ر.ع من {$rev->source}",
        'time' => $rev->created_at,
    ]);
}

// المصروفات
foreach ($recentExpenses as $exp) {
    $notifications->push([
        'type' => 'expense',
        'message' => "تمت إضافة مصروف جديد بقيمة {$exp->amount} ر.ع ضمن فئة {$exp->category}",
        'time' => $exp->created_at,
    ]);
}

// ترتيب الأحداث من الأحدث إلى الأقدم
$notifications = $notifications->sortByDesc('time')->take(10);


        return view('admin.admin', compact(
    'loginCount', 'userCount', 'busCount', 'totalRevenues', 'totalExpenses',
    'netProfit', 'logins', 'recentRevenues', 'recentExpenses',
    'months', 'revenuesData', 'expensesData', 'notifications', 'contacts'
));

    }
}
