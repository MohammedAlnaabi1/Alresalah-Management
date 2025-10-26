<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bus;
use App\Models\BusExpense;
use Carbon\Carbon;

class BusDashboardController extends Controller
{
    public function index()
    {
        // ==================================================
        // 🔹 الإحصاءات الأساسية
        // ==================================================
        $totalBuses        = Bus::count();
        $activeBuses       = Bus::where('status', 'نشطة')->count();
        $maintenanceBuses  = Bus::where('status', 'قيد الصيانة')->count();
        $totalExpenses     = BusExpense::sum('amount') ?? 0;

        // ==================================================
        // 🔹 صرفية الوقود الشهرية
        // ==================================================
        $monthlyFuelExpense = BusExpense::where('expense_type', 'وقود')
            ->whereMonth('expense_date', Carbon::now()->month)
            ->whereYear('expense_date', Carbon::now()->year)
            ->sum('amount') ?? 0;

        // ==================================================
        // 🔹 الصيانة السنوية
        // ==================================================
            $yearlyMaintenanceExpense = BusExpense::where('expense_type', 'صيانة')
    ->whereYear('expense_date', now()->year)
    ->sum('amount') ?? 0;


        // ==================================================
        // 🔹 آخر الحافلات والمصروفات
        // ==================================================
        $latestBuses = Bus::latest()->take(5)->get();

        $latestExpenses = BusExpense::with('bus')
            ->orderBy('expense_date', 'desc')
            ->take(5)
            ->get();

        // ==================================================
        // 🔹 تجميع المصروفات السنوية (للرسوم البيانية)
        // ==================================================
        $yearlyExpenses = BusExpense::selectRaw('YEAR(expense_date) as year, SUM(amount) as total')
            ->groupBy('year')
            ->orderBy('year', 'asc')
            ->get();

        // 🔹 تجهيز بيانات الرسوم
        $chartYears  = $yearlyExpenses->pluck('year');
        $chartTotals = $yearlyExpenses->pluck('total');

        // في حال لا توجد بيانات
        if ($chartYears->isEmpty()) {
            $chartYears  = collect([date('Y')]);
            $chartTotals = collect([0]);
        }

        // ==================================================
        // 🔹 تمرير البيانات إلى واجهة لوحة التحكم الرئيسية (dashboard)
        // ==================================================
        return view('dashboard', compact(
            'totalBuses',
            'activeBuses',
            'maintenanceBuses',
            'totalExpenses',
            'latestBuses',
            'latestExpenses',
            'chartYears',
            'chartTotals',
            'monthlyFuelExpense',
            'yearlyMaintenanceExpense'

             // ✅ تمت إضافته هنا
        ));
    }
}
