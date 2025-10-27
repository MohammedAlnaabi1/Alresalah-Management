<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bus;
use App\Models\BusExpense;
use Carbon\Carbon;

class BusOperationsController extends Controller
{
    public function index(Request $request)
    {
        $buses = Bus::all();

        // 🔸 فلترة الصيانة (فقط الموافق عليها)
        $maintenanceQuery = BusExpense::with('bus')
            ->where('expense_type', 'صيانة')
            ->where('status', 'approved');

        // 🔸 فلترة الوقود (فقط الموافق عليها)
        $fuelQuery = BusExpense::with('bus')
            ->where('expense_type', 'وقود')
            ->where('status', 'approved');

        // 🔹 تطبيق الفلاتر العامة
        foreach ([$maintenanceQuery, $fuelQuery] as $query) {
            if ($request->from_date) {
                $query->whereDate('expense_date', '>=', $request->from_date);
            }
            if ($request->to_date) {
                $query->whereDate('expense_date', '<=', $request->to_date);
            }
            if ($request->bus_id) {
                $query->where('bus_id', $request->bus_id);
            }
        }

        // 🔹 جلب النتائج بعد الفلترة
        $maintenanceExpenses = $maintenanceQuery
            ->orderBy('expense_date', 'desc')
            ->get();

        $fuelExpenses = $fuelQuery
            ->orderBy('expense_date', 'desc')
            ->get();

        // 🔹 إجماليات (فقط الموافق عليها)
        $yearlyMaintenanceExpense = BusExpense::where('expense_type', 'صيانة')
            ->where('status', 'approved')
            ->whereYear('expense_date', Carbon::now()->year)
            ->sum('amount') ?? 0;

        $monthlyFuelExpense = BusExpense::where('expense_type', 'وقود')
            ->where('status', 'approved')
            ->whereMonth('expense_date', Carbon::now()->month)
            ->whereYear('expense_date', Carbon::now()->year)
            ->sum('amount') ?? 0;

        // 🔹 إرسال البيانات إلى الواجهة
        return view('bus_operations', compact(
            'buses',
            'maintenanceExpenses',
            'fuelExpenses',
            'yearlyMaintenanceExpense',
            'monthlyFuelExpense'
        ));
    }
}
