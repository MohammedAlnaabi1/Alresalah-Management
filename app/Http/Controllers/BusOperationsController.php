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

        // 🔸 فلترة الصيانة
        $maintenanceQuery = BusExpense::with('bus')
            ->where('expense_type', 'صيانة');

        // 🔸 فلترة الوقود
        $fuelQuery = BusExpense::with('bus')
            ->where('expense_type', 'وقود');

        // 🔹 تطبيق الفلاتر
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

        // 🔹 الحصول على النتائج
        $maintenanceExpenses = $maintenanceQuery->orderBy('expense_date', 'desc')->get();
        $fuelExpenses        = $fuelQuery->orderBy('expense_date', 'desc')->get();

        // 🔹 إجماليات
        $yearlyMaintenanceExpense = BusExpense::where('expense_type', 'صيانة')
            ->whereYear('expense_date', Carbon::now()->year)
            ->sum('amount') ?? 0;

        $monthlyFuelExpense = BusExpense::where('expense_type', 'وقود')
            ->whereMonth('expense_date', Carbon::now()->month)
            ->whereYear('expense_date', Carbon::now()->year)
            ->sum('amount') ?? 0;

        return view('bus_operations', compact(
            'buses',
            'maintenanceExpenses',
            'fuelExpenses',
            'yearlyMaintenanceExpense',
            'monthlyFuelExpense'
        ));
    }
}
