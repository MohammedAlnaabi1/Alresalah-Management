<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Revenue;
use App\Models\Expense;
use Carbon\Carbon;
use App\Exports\FinancialReportExport;
use Maatwebsite\Excel\Facades\Excel;

class FinancialController extends Controller
{
    // ==========================================
    // 🔹 لوحة المعلومات الرئيسية
    // ==========================================
   public function index(Request $request)
{
    // 🟢 استقبال الشهر والسنة من الفلتر (اختياري)
    $selectedMonth = $request->input('month');
    $selectedYear  = $request->input('year');

    // 🟢 القيم العامة
    $totalRevenues = Revenue::sum('amount');
    $totalExpenses = Expense::sum('amount');
    $netBalance    = $totalRevenues - $totalExpenses;

    // 🟢 مصروفات الحافلات
    $busExpenses = Expense::where('category', 'like', '%حافلة%')->sum('amount');

    // 🟢 مجموع المصروفات حسب الفئة
    $expenseCategories = Expense::select('category')
        ->selectRaw('SUM(amount) as total')
        ->groupBy('category')
        ->pluck('total', 'category');

    // 🟢 أحدث العمليات المالية
    $recentRevenues = Revenue::latest('date')->take(5)->get(['source as name', 'amount', 'date']);
    $recentExpenses = Expense::latest('date')->take(5)->get(['category as name', 'amount', 'date']);

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

    // 🟢 الرسم البياني الشهري (لكل أشهر السنة)
    $monthlyRevenues = [];
    $monthlyExpenses = [];
    foreach (range(1, 12) as $month) {
        $monthlyRevenues[] = Revenue::whereMonth('date', $month)->sum('amount');
        $monthlyExpenses[] = Expense::whereMonth('date', $month)->sum('amount');
    }

    // 🟢 منطق اختيار الشهر والسنة (اختياري)
    $queryRevenue = Revenue::query();
    $queryExpense = Expense::query();

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

    // 🟢 الإيرادات حسب النوع (للرسم الدائري)
    $revenueCategories = Revenue::select('type', \DB::raw('SUM(amount) as total'))
        ->groupBy('type')
        ->pluck('total', 'type');

    // 🟢 المصروفات السنوية (للرسوم)
    $yearlyExpenses = Expense::selectRaw('YEAR(date) as year, SUM(amount) as total')
        ->groupBy('year')
        ->orderBy('year', 'asc')
        ->get();

    $chartYears  = $yearlyExpenses->pluck('year');
    $chartTotals = $yearlyExpenses->pluck('total');

    // ✅ تمرير جميع البيانات إلى الواجهة
    return view('financial.index', compact(
        'selectedMonth', 'selectedYear',
        'totalRevenues','totalExpenses','netBalance',
        'recentTransactions','months','monthlyRevenues','monthlyExpenses',
        'busExpenses','expenseCategories','chartYears','chartTotals',
        'donationTotal','supportTotal','transportTotal',
        'monthlyRevenue','monthlyExpense','revenueCategories'
    ));
}


    // ==========================================
    // 🔹 صفحة التقارير المالية
    // ==========================================
    public function reports()
    {
        $from = Carbon::now()->subDays(30)->toDateString();
        $to   = Carbon::now()->toDateString();
        $type = 'all';

        return $this->generateReport($from, $to, $type);
    }

    public function filterReports(Request $request)
    {
        $from = $request->input('from');
        $to   = $request->input('to');
        $type = $request->input('type', 'all');

        return $this->generateReport($from, $to, $type);
    }

    private function generateReport($from, $to, $type)
    {
        $revenues = collect();
        $expenses = collect();

        if ($type == 'all' || $type == 'revenues') {
            $revenues = Revenue::whereBetween('date', [$from, $to])
                ->select('source as name', 'amount', 'date')->get();
        }

        if ($type == 'all' || $type == 'expenses') {
            $expenses = Expense::whereBetween('date', [$from, $to])
                ->select('category as name', 'amount', 'date')->get();
        }

        $transactions = collect($revenues)
            ->map(fn($r) => ['type' => 'إيراد', 'name' => $r->name, 'amount' => $r->amount, 'date' => $r->date])
            ->merge(
                collect($expenses)->map(fn($e) => ['type' => 'مصروف', 'name' => $e->name, 'amount' => $e->amount, 'date' => $e->date])
            )
            ->sortByDesc('date')->values();

        $totalRevenues = $revenues->sum('amount');
        $totalExpenses = $expenses->sum('amount');
        $netBalance    = $totalRevenues - $totalExpenses;

        $busExpenses = Expense::where('category', 'like', '%حافلة%')
            ->whereBetween('date', [$from, $to])->sum('amount');

        $expenseCategories = Expense::whereBetween('date', [$from, $to])
            ->select('category')->selectRaw('SUM(amount) as total')
            ->groupBy('category')->pluck('total', 'category');

        $months = ['يناير','فبراير','مارس','أبريل','مايو','يونيو','يوليو','أغسطس','سبتمبر','أكتوبر','نوفمبر','ديسمبر'];

        $monthlyRevenues = [];
        $monthlyExpenses = [];
        foreach (range(1, 12) as $month) {
            $monthlyRevenues[] = Revenue::whereMonth('date', $month)->sum('amount');
            $monthlyExpenses[] = Expense::whereMonth('date', $month)->sum('amount');
        }

        return view('financial.reports', compact(
            'transactions','from','to','type',
            'totalRevenues','totalExpenses','netBalance',
            'months','monthlyRevenues','monthlyExpenses',
            'busExpenses','expenseCategories'
        ));
    }

    // ==========================================
    // 🔹 تصدير PDF
    // ==========================================
    public function exportPDF(Request $request)
    {
        $from = $request->input('from') ?? Carbon::now()->subDays(30)->toDateString();
        $to   = $request->input('to') ?? Carbon::now()->toDateString();
        $type = $request->input('type', 'all');

        $data = $this->generateReportData($from, $to, $type);

        \PDF::setOptions(['defaultFont' => 'Cairo']);
        $pdf = \PDF::loadView('financial.export_pdf', $data)->setPaper('a4', 'portrait');
        return $pdf->download('التقرير-المالي.pdf');
    }

    // ==========================================
    // 🔹 تصدير Excel
    // ==========================================
    public function exportExcel(Request $request)
    {
        $from = $request->input('from') ?? Carbon::now()->subDays(30)->toDateString();
        $to   = $request->input('to') ?? Carbon::now()->toDateString();
        $type = $request->input('type', 'all');

        $data = $this->generateReportData($from, $to, $type);
        return Excel::download(new FinancialReportExport($data), 'التقرير-المالي.xlsx');
    }

    // ==========================================
    // 🔹 تجميع بيانات التقرير
    // ==========================================
    private function generateReportData($from, $to, $type)
    {
        $revenues = collect();
        $expenses = collect();

        if ($type == 'all' || $type == 'revenues') {
            $revenues = Revenue::whereBetween('date', [$from, $to])
                ->select('source as name', 'amount', 'date')->get();
        }

        if ($type == 'all' || $type == 'expenses') {
            $expenses = Expense::whereBetween('date', [$from, $to])
                ->select('category as name', 'amount', 'date')->get();
        }

        $transactions = collect($revenues)
            ->map(fn($r) => ['type' => 'إيراد', 'name' => $r->name, 'amount' => $r->amount, 'date' => $r->date])
            ->merge(
                collect($expenses)->map(fn($e) => ['type' => 'مصروف', 'name' => $e->name, 'amount' => $e->amount, 'date' => $e->date])
            )
            ->sortByDesc('date')->values();

        $totalRevenues = $revenues->sum('amount');
        $totalExpenses = $expenses->sum('amount');
        $netBalance    = $totalRevenues - $totalExpenses;

        $busExpenses = Expense::where('category', 'like', '%حافلة%')
            ->whereBetween('date', [$from, $to])->sum('amount');

        $expenseCategories = Expense::whereBetween('date', [$from, $to])
            ->select('category')->selectRaw('SUM(amount) as total')
            ->groupBy('category')->pluck('total', 'category');

        return compact(
            'transactions','from','to','type',
            'totalRevenues','totalExpenses','netBalance',
            'busExpenses','expenseCategories'
        );
    }
}
