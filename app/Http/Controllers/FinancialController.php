<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Revenue;
use App\Models\Expense;
use App\Models\BusExpense;
use Carbon\Carbon;
use App\Exports\FinancialReportExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Mpdf\Mpdf;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;


class FinancialController extends Controller
{
    // ==========================================================
    // 🔹 لوحة المعلومات الرئيسية (Dashboard)
    // ==========================================================
    public function index(Request $request)
    {
        // 🟢 استقبال الشهر والسنة من الفلتر (اختياري)
        $selectedMonth = $request->input('month');
        $selectedYear  = $request->input('year');

        // 🟢 القيم العامة
        $totalRevenues = Revenue::sum('amount');
        $totalExpenses = Expense::where('status', 'approved')->sum('amount');
        $netBalance    = $totalRevenues - $totalExpenses;

        // 🟢 مصروفات الحافلات (المعتمدة فقط)
        $busExpenses = BusExpense::where('status', 'approved')->sum('amount');

        // 🟢 مصروفات الحافلات قيد المراجعة
        $pendingBusExpenses = BusExpense::where('status', 'pending')
            ->with('bus')
            ->get();

        // 🟢 مجموع المصروفات حسب الفئة (المعتمدة فقط)
        $expenseCategories = Expense::where('status', 'approved')
            ->select('category')
            ->selectRaw('SUM(amount) as total')
            ->groupBy('category')
            ->pluck('total', 'category');

        // 🟢 أحدث العمليات المالية
        $recentRevenues = Revenue::latest('date')->take(5)->get(['source as name', 'amount', 'date']);
        $recentExpenses = Expense::where('status', 'approved')->latest('date')->take(5)->get(['category as name', 'amount', 'date']);

        $recentTransactions = collect($recentRevenues)
            ->map(fn($r) => ['type' => 'إيراد', 'name' => $r->name, 'amount' => $r->amount, 'date' => $r->date])
            ->merge(
                collect($recentExpenses)->map(fn($e) => ['type' => 'مصروف', 'name' => $e->name, 'amount' => $e->amount, 'date' => $e->date])
            )
            ->sortByDesc('date')
            ->take(5)
            ->values();

        // 🟢 أسماء الأشهر بالعربية
        $months = ['يناير','فبراير','مارس','أبريل','مايو','يونيو','يوليو','أغسطس','سبتمبر','أكتوبر','نوفمبر','ديسمبر'];

        // 🟢 الرسم البياني الشهري
        $monthlyRevenues = [];
        $monthlyExpenses = [];
        foreach (range(1, 12) as $month) {
            $monthlyRevenues[] = Revenue::whereMonth('date', $month)->sum('amount');
            $monthlyExpenses[] = Expense::whereMonth('date', $month)
                ->where('status', 'approved')
                ->sum('amount');
        }

        // 🟢 فلترة حسب الشهر والسنة
        $queryRevenue = Revenue::query();
        $queryExpense = Expense::where('status', 'approved');

        if (!empty($selectedMonth)) {
            $queryRevenue->whereMonth('date', $selectedMonth);
            $queryExpense->whereMonth('date', $selectedMonth);
        }

        if (!empty($selectedYear)) {
            $queryRevenue->whereYear('date', $selectedYear);
            $queryExpense->whereYear('date', $selectedYear);
        }

        $monthlyRevenue = $queryRevenue->sum('amount');
        $monthlyExpense = $queryExpense->sum('amount');

        // 🟢 إجمالي التبرعات / الدعم / رسوم النقل
        $donationTotal  = Revenue::where('type', 'تبرع')->sum('amount');
        $supportTotal   = Revenue::where('type', 'دعم')->sum('amount');
        $transportTotal = Revenue::where('type', 'رسوم نقل')->sum('amount');

        // 🟢 الإيرادات حسب النوع
        $revenueCategories = Revenue::select('type', \DB::raw('SUM(amount) as total'))
            ->groupBy('type')
            ->pluck('total', 'type');

        // 🟢 المصروفات السنوية
        $yearlyExpenses = Expense::where('status', 'approved')
            ->selectRaw('YEAR(date) as year, SUM(amount) as total')
            ->groupBy('year')
            ->orderBy('year', 'asc')
            ->get();

        $chartYears  = $yearlyExpenses->pluck('year');
        $chartTotals = $yearlyExpenses->pluck('total');

        // ✅ تمرير جميع البيانات للواجهة
        return view('financial.index', compact(
            'selectedMonth', 'selectedYear',
            'totalRevenues', 'totalExpenses', 'netBalance',
            'recentTransactions', 'months', 'monthlyRevenues', 'monthlyExpenses',
            'busExpenses', 'pendingBusExpenses', 'expenseCategories', 'chartYears', 'chartTotals',
            'donationTotal', 'supportTotal', 'transportTotal',
            'monthlyRevenue', 'monthlyExpense', 'revenueCategories'
        ));
    }

    // ==========================================================
    // 🔹 تأكيد أو رفض مصروف الحافلات
    // ==========================================================
    public function approveBusExpense($id)
{
    DB::transaction(function () use ($id) {
        $busExpense = BusExpense::findOrFail($id);

        // ✅ تحديث حالة مصروف الحافلة
        $busExpense->update(['status' => 'approved']);

        // ✅ إنشاء سجل جديد في جدول المصروفات العامة
        Expense::create([
            'category' => 'مصروف حافلة رقم ' . ($busExpense->bus->bus_number ?? 'غير محدد'),
            'payment_method' => 'نقدًا',
            'amount' => $busExpense->amount,
            'date' => $busExpense->expense_date,
            'related_bus_id' => $busExpense->bus_id,
            'notes' => $busExpense->description,
            'status' => 'approved',
        ]);
    });

    return redirect()->route('financial.expenses')->with('success', 'تمت الموافقة على المصروف وإضافته للبيانات المالية ✅');
}

    public function rejectBusExpense($id)
    {
        $busExpense = BusExpense::findOrFail($id);
        $busExpense->update(['status' => 'rejected']);

        return back()->with('success', 'تم رفض المصروف ولن يُحتسب في التقارير ❌');
    }

    // ==========================================================
// 🔹 صفحة التقارير المالية - العرض الأساسي
// ==========================================================
public function reports()
{
    $from = Carbon::now()->subDays(30)->toDateString();
    $to   = Carbon::now()->toDateString();
    $type = 'all';

    return $this->generateReport($from, $to, $type);
}

// ==========================================================
// 🔹 تطبيق الفلتر في التقارير
// ==========================================================
public function filterReports(Request $request)
{
    $from = $request->input('from');
    $to   = $request->input('to');
    $type = $request->input('type', 'all');

    return $this->generateReport($from, $to, $type);
}

// ==========================================================
// 🔹 منطق بناء التقرير (متوافق مع لوحة المعلومات)
// ==========================================================
private function generateReport($from, $to, $type)
{
    $revenues = collect();
    $expenses = collect();

    // ✅ الإيرادات
    if ($type === 'all' || $type === 'revenues') {
        $revenues = Revenue::whereBetween('date', [$from, $to])
            ->select('source as name', 'amount', 'date')
            ->get();
    }

    // ✅ المصروفات المعتمدة فقط
    if ($type === 'all' || $type === 'expenses') {
        $expenses = Expense::where('status', 'approved')
            ->whereBetween('date', [$from, $to])
            ->select('category as name', 'amount', 'date')
            ->get();
    }

    // ✅ مصروفات الحافلات فقط (المعتمدة)
    if ($type === 'bus') {
        $expenses = Expense::where('status', 'approved')
            ->where('category', 'like', '%حافلة%')
            ->whereBetween('date', [$from, $to])
            ->select('category as name', 'amount', 'date')
            ->get();
        // في هذا الاختيار لا نعرض إيرادات
        $revenues = collect();
    }

    // ✅ دمج العمليات للجدول
    $transactions = collect($revenues)
        ->map(fn($r) => ['type' => 'إيراد', 'name' => $r->name, 'amount' => $r->amount, 'date' => $r->date])
        ->merge(
            collect($expenses)->map(fn($e) => ['type' => 'مصروف', 'name' => $e->name, 'amount' => $e->amount, 'date' => $e->date])
        )
        ->sortByDesc('date')
        ->values();

    // ✅ إجماليات
    $totalRevenues = $revenues->sum('amount');
    $totalExpenses = $expenses->sum('amount');
    $netBalance    = $totalRevenues - $totalExpenses;

    // ✅ مصروفات الحافلات (المعتمدة فقط)
    $busExpenses = Expense::where('status', 'approved')
        ->where('category', 'like', '%حافلة%')
        ->whereBetween('date', [$from, $to])
        ->sum('amount');

    // ✅ توزيع المصروفات حسب الفئة (المعتمدة فقط)
    $expenseCategories = Expense::where('status', 'approved')
        ->whereBetween('date', [$from, $to])
        ->select('category')
        ->selectRaw('SUM(amount) as total')
        ->groupBy('category')
        ->pluck('total', 'category');

    // ✅ الأشهر
    $months = ['يناير','فبراير','مارس','أبريل','مايو','يونيو','يوليو','أغسطس','سبتمبر','أكتوبر','نوفمبر','ديسمبر'];

    // ✅ سلاسل الرسم الشهري لسنة الحالية (مصروفات معتمدة فقط)
    $monthlyRevenues = [];
    $monthlyExpenses = [];
    foreach (range(1, 12) as $m) {
        $monthlyRevenues[] = Revenue::whereMonth('date', $m)
            ->whereYear('date', now()->year)
            ->sum('amount');

        $monthlyExpenses[] = Expense::where('status', 'approved')
            ->whereMonth('date', $m)
            ->whereYear('date', now()->year)
            ->sum('amount');
    }

    return view('financial.reports', compact(
        'transactions', 'from', 'to', 'type',
        'totalRevenues', 'totalExpenses', 'netBalance',
        'months', 'monthlyRevenues', 'monthlyExpenses',
        'busExpenses', 'expenseCategories'
    ));
}

// ==========================================================
// 🔹 تصدير PDF
// ==========================================================
public function exportPDF(Request $request)
{
    $from = $request->input('from') ?? Carbon::now()->subDays(30)->toDateString();
    $to   = $request->input('to') ?? Carbon::now()->toDateString();
    $type = $request->input('type', 'all');

    $data = $this->generateReportData($from, $to, $type);

    $html = view('financial.export_pdf', $data)->render();

    $mpdf = new Mpdf([
        'mode' => 'utf-8',
        'format' => 'A4',
        'autoArabic' => true,
        'autoLangToFont' => true,
        'default_font' => 'XBRiyaz',
        'tempDir' => storage_path('app/mpdf-temp'),
    ]);

    $mpdf->SetDirectionality('rtl');
    $mpdf->WriteHTML($html);

    return response($mpdf->Output('', 'S'), 200, [
        'Content-Type' => 'application/pdf',
        'Content-Disposition' => 'attachment; filename="financial-report.pdf"',
    ]);
}

// ==========================================================
// 🔹 تصدير Excel
// ==========================================================
public function exportExcel(Request $request)
{
    $from = $request->input('from') ?? Carbon::now()->subDays(30)->toDateString();
    $to   = $request->input('to') ?? Carbon::now()->toDateString();
    $type = $request->input('type', 'all');

    $data = $this->generateReportData($from, $to, $type);

    $fileName = 'financial-report-' . now()->format('Y-m-d') . '.xlsx';
    $filePath = 'exports/' . $fileName;

    // حفظ الملف أولاً ثم تحميله
    Excel::store(new FinancialReportExport($data), $filePath, 'local');

    $fullPath = storage_path('app/' . $filePath);

    return response()->download($fullPath, $fileName, [
        'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    ])->deleteFileAfterSend(true);
}

// ==========================================================
// 🔹 نفس منطق التقارير لكن كـ Data للتصدير
// ==========================================================
private function generateReportData($from, $to, $type)
{
    $revenues = collect();
    $expenses = collect();

    if ($type === 'all' || $type === 'revenues') {
        $revenues = Revenue::whereBetween('date', [$from, $to])
            ->select('source as name', 'amount', 'date')
            ->get();
    }

    if ($type === 'all' || $type === 'expenses') {
        $expenses = Expense::where('status', 'approved')
            ->whereBetween('date', [$from, $to])
            ->select('category as name', 'amount', 'date')
            ->get();
    }

    if ($type === 'bus') {
        $expenses = Expense::where('status', 'approved')
            ->where('category', 'like', '%حافلة%')
            ->whereBetween('date', [$from, $to])
            ->select('category as name', 'amount', 'date')
            ->get();
        $revenues = collect();
    }

    $transactions = collect($revenues)
        ->map(fn($r) => ['type' => 'إيراد', 'name' => $r->name, 'amount' => $r->amount, 'date' => $r->date])
        ->merge(
            collect($expenses)->map(fn($e) => ['type' => 'مصروف', 'name' => $e->name, 'amount' => $e->amount, 'date' => $e->date])
        )
        ->sortByDesc('date')
        ->values();

    $totalRevenues = $revenues->sum('amount');
    $totalExpenses = $expenses->sum('amount');
    $netBalance    = $totalRevenues - $totalExpenses;

    $busExpenses = Expense::where('status', 'approved')
        ->where('category', 'like', '%حافلة%')
        ->whereBetween('date', [$from, $to])
        ->sum('amount');

    $expenseCategories = Expense::where('status', 'approved')
        ->whereBetween('date', [$from, $to])
        ->select('category')
        ->selectRaw('SUM(amount) as total')
        ->groupBy('category')
        ->pluck('total', 'category');

    return compact(
        'transactions', 'from', 'to', 'type',
        'totalRevenues', 'totalExpenses', 'netBalance',
        'busExpenses', 'expenseCategories'
    );
}

}
