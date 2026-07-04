<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\BusController;
use App\Http\Controllers\BusExpenseController;
use App\Http\Controllers\BusDashboardController;
use App\Http\Controllers\FinancialController;
use App\Http\Controllers\RevenueController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\StudentDashboardController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\GradeController;

/*
|--------------------------------------------------------------------------
| Web Routes - نظام إدارة مدرسة الرسالة
|--------------------------------------------------------------------------
*/

// ====================================================================
// 🏠 الصفحة العامة (واجهة الزوار)
// ====================================================================
Route::get('/', function () {
    return view('welcome');
})->name('home');

// ====================================================================
// ✉️ نموذج التواصل (مفتوح للجميع)
// ====================================================================
Route::post('/contact/store', [ContactController::class, 'store'])->name('contact.store');

// ====================================================================
// 🔐 تسجيل الدخول والخروج (مفتوح للجميع)
// ====================================================================
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ====================================================================
// 🔒 المسارات المحمية - لا يمكن الدخول إلا بعد تسجيل الدخول
// ====================================================================
Route::middleware(['checkLogin'])->group(function () {

    // لوحة تحكم الأدمن
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.dashboard');

    // النظام المالي
    Route::prefix('financial')->group(function () {
        Route::get('/', [FinancialController::class, 'index'])->name('financial.dashboard');

        Route::get('/revenues', [RevenueController::class, 'index'])->name('financial.revenues');
        Route::post('/revenues/store', [RevenueController::class, 'store'])->name('financial.revenues.store');
        Route::delete('/revenues/delete/{id}', [RevenueController::class, 'destroy'])->name('financial.revenues.delete');
        Route::post('/revenues/update/{id}', [RevenueController::class, 'update'])->name('financial.revenues.update');
        Route::get('/revenues/export-pdf', [RevenueController::class, 'exportPDF'])->name('financial.revenues.exportPDF');
        Route::get('/revenues/export-excel', [RevenueController::class, 'exportExcel'])->name('financial.revenues.exportExcel');

        Route::get('/expenses', [ExpenseController::class, 'index'])->name('financial.expenses');
        Route::post('/expenses/store', [ExpenseController::class, 'store'])->name('financial.expenses.store');
        Route::delete('/expenses/delete/{id}', [ExpenseController::class, 'destroy'])->name('financial.expenses.delete');
        Route::post('/expenses/update/{id}', [ExpenseController::class, 'update'])->name('financial.expenses.update');
        Route::get('/expenses/export-pdf', [ExpenseController::class, 'exportPDF'])->name('financial.expenses.exportPDF');
        Route::get('/expenses/export-excel', [ExpenseController::class, 'exportExcel'])->name('financial.expenses.exportExcel');

        Route::get('/reports', [FinancialController::class, 'reports'])->name('financial.reports');
        Route::get('/reports/filter', [FinancialController::class, 'filterReports'])->name('financial.reports.filter');
        Route::get('/reports/export-pdf', [FinancialController::class, 'exportPDF'])->name('financial.reports.exportPDF');
        Route::get('/reports/export-excel', [FinancialController::class, 'exportExcel'])->name('financial.reports.exportExcel');

        Route::get('/bus-expenses/approve/{id}', [FinancialController::class, 'approveBusExpense'])->name('financial.bus_expenses.approve');
        Route::get('/bus-expenses/reject/{id}', [FinancialController::class, 'rejectBusExpense'])->name('financial.bus_expenses.reject');
    });

    

    // نظام إدارة الطلاب
    Route::prefix('student')->group(function () {
        Route::get('/dashboard', [StudentDashboardController::class, 'index'])->name('student.dashboard');
        Route::get('/honors', [StudentDashboardController::class, 'honors'])->name('student.honors');
        Route::get('/points', [StudentDashboardController::class, 'points'])->name('student.points');
        Route::post('/points/store', [StudentDashboardController::class, 'storePoints'])->name('student.points.store');

        Route::get('/', [StudentController::class, 'index'])->name('student.index');
        Route::post('/store', [StudentController::class, 'store'])->name('student.store');
        Route::post('/update/{id}', [StudentController::class, 'update'])->name('student.update');
        Route::delete('/delete/{id}', [StudentController::class, 'destroy'])->name('student.delete');
        Route::get('/export-pdf', [StudentController::class, 'exportPDF'])->name('student.exportPDF');
        Route::get('/export-excel', [StudentController::class, 'exportExcel'])->name('student.exportExcel');

        Route::get('/attendance', [AttendanceController::class, 'index'])->name('student.attendance');
        Route::get('/attendance/report', [AttendanceController::class, 'report'])->name('student.attendance.report');
        Route::post('/attendance/store', [AttendanceController::class, 'store'])->name('student.attendance.store');
        Route::get('/attendance/export-pdf', [AttendanceController::class, 'exportPDF'])->name('student.attendance.exportPDF');
        Route::get('/attendance/export-excel', [AttendanceController::class, 'exportExcel'])->name('student.attendance.exportExcel');

        Route::get('/grades', [GradeController::class, 'index'])->name('student.grades');
        Route::post('/grades/store', [GradeController::class, 'store'])->name('student.grades.store');
        Route::post('/grades/update/{id}', [GradeController::class, 'update'])->name('student.grades.update');
        Route::delete('/grades/delete/{id}', [GradeController::class, 'destroy'])->name('student.grades.delete');
        Route::get('/grades/export-pdf', [GradeController::class, 'exportPDF'])->name('student.grades.exportPDF');
        Route::get('/grades/export-excel', [GradeController::class, 'exportExcel'])->name('student.grades.exportExcel');
    });

    // نظام إدارة الحافلات
    Route::prefix('bus')->group(function () {
        Route::get('/dashboard', [BusDashboardController::class, 'index'])->name('dashboard');
        Route::get('/', [BusController::class, 'index'])->name('bus');
        Route::post('/store', [BusController::class, 'store'])->name('bus.store');
        Route::post('/update/{id}', [BusController::class, 'update'])->name('bus.update');
        Route::delete('/delete/{id}', [BusController::class, 'destroy'])->name('bus.delete');
        Route::get('/view/{id}/{type}', [BusController::class, 'viewFile'])->name('bus.view');
        Route::get('/expenses', [BusExpenseController::class, 'index'])->name('bus_expenses');
        Route::post('/expenses/store', [BusExpenseController::class, 'store'])->name('bus_expenses.store');
        Route::get('/expenses/view/{id}', [BusExpenseController::class, 'viewReceipt'])->name('bus_expenses.view');
        Route::post('/expenses/update/{id}', [BusExpenseController::class, 'update'])->name('bus_expenses.update');
        Route::delete('/expenses/delete/{id}', [BusExpenseController::class, 'destroy'])->name('bus_expenses.delete');
        Route::get('/operations', [App\Http\Controllers\BusOperationsController::class, 'index'])->name('bus.operations');
    });

});
