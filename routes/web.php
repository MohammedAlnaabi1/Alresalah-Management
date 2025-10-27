<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BusController;
use App\Http\Controllers\BusExpenseController;
use App\Http\Controllers\BusDashboardController;
use App\Http\Controllers\RevenueController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\FinancialController;
/*
|--------------------------------------------------------------------------
| Web Routes - نظام إدارة مدرسة الرسالة
|--------------------------------------------------------------------------
*/

// ====================================================================
// 🔹 صفحة تسجيل الدخول
// ====================================================================

Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');

// تسجيل الخروج
Route::post('/logout', function () {
    Session::flush();
    return redirect()->route('login');
})->name('logout');

// ====================================================================
// 🔸 لوحة التحكم
// ====================================================================
Route::get('/dashboard', [BusDashboardController::class, 'index'])->name('dashboard');

// ====================================================================
// 🔸 إدارة الحافلات (Bus Management System)
// ====================================================================
Route::prefix('bus')->group(function () {

    Route::get('/bus/operations', [App\Http\Controllers\BusOperationsController::class, 'index'])->name('bus.operations');


    // ✅ قائمة الحافلات
    Route::get('/', [BusController::class, 'index'])->name('bus');
    Route::post('/store', [BusController::class, 'store'])->name('bus.store');
    Route::post('/update/{id}', [BusController::class, 'update'])->name('bus.update');
    Route::delete('/delete/{id}', [BusController::class, 'destroy'])->name('bus.delete');
    Route::get('/view/{id}/{type}', [BusController::class, 'viewFile'])->name('bus.view');

    // ✅ مصروفات الحافلات
    Route::get('/expenses', [BusExpenseController::class, 'index'])->name('bus_expenses');
    Route::post('/expenses/store', [BusExpenseController::class, 'store'])->name('bus_expenses.store');
    Route::get('/expenses/view/{id}', [BusExpenseController::class, 'viewReceipt'])->name('bus_expenses.view');
    Route::post('/expenses/update/{id}', [BusExpenseController::class, 'update'])->name('bus_expenses.update');
    Route::delete('/expenses/delete/{id}', [BusExpenseController::class, 'destroy'])->name('bus_expenses.delete');
    Route::get('/bus-expenses/approve/{id}', [FinancialController::class, 'approveBusExpense'])
    ->name('bus.expenses.approve');

Route::get('/bus-expenses/reject/{id}', [FinancialController::class, 'rejectBusExpense'])
    ->name('bus.expenses.reject');
});

// ====================================================================
// 🔸 النظام المالي (Financial Management)
// ====================================================================
Route::prefix('financial')->group(function () {

    Route::get('/financial', [FinancialController::class, 'index'])->name('financial.index');


    // 🔹 الإيرادات
    Route::get('/revenues', [RevenueController::class, 'index'])->name('financial.revenues');
    Route::post('/revenues/store', [RevenueController::class, 'store'])->name('financial.revenues.store');
    Route::delete('/revenues/delete/{id}', [RevenueController::class, 'destroy'])->name('financial.revenues.delete');
    Route::post('/revenues/update/{id}', [RevenueController::class, 'update'])->name('financial.revenues.update');

    // 🔹 المصروفات
    Route::get('/expenses', [ExpenseController::class, 'index'])->name('financial.expenses');
    Route::post('/expenses/store', [ExpenseController::class, 'store'])->name('financial.expenses.store');
    Route::delete('/expenses/delete/{id}', [ExpenseController::class, 'destroy'])->name('financial.expenses.delete');
    Route::post('/expenses/update/{id}', [ExpenseController::class, 'update'])->name('financial.expenses.update');
    


    // 🔹 التقارير
    // 🔹 صفحة التقارير (Reports)
Route::get('/reports', [FinancialController::class, 'reports'])->name('financial.reports');
Route::get('/reports/filter', [FinancialController::class, 'filterReports'])->name('financial.reports.filter');
Route::get('/financial/reports/export-pdf', [FinancialController::class, 'exportPDF'])->name('financial.reports.exportPDF');
Route::get('/financial/reports/export-excel', [FinancialController::class, 'exportExcel'])->name('financial.reports.exportExcel');

});

// ✅ موافقة مصروف الحافلات (من صفحة المالية)
Route::get('/financial/bus-expenses/approve/{id}', [FinancialController::class, 'approveBusExpense'])
    ->name('financial.bus_expenses.approve');

// ✅ رفض مصروف الحافلات
Route::get('/financial/bus-expenses/reject/{id}', [FinancialController::class, 'rejectBusExpense'])
    ->name('financial.bus_expenses.reject');
