<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Expense;
use App\Models\BusExpense;
use Illuminate\Support\Facades\Storage;
use App\Exports\ExpenseExport;
use Maatwebsite\Excel\Facades\Excel;
use Mpdf\Mpdf;

class ExpenseController extends Controller
{
    // ==========================================
    // 🔹 عرض صفحة إدارة المصروفات
    // ==========================================
    public function index(Request $request)
{
    $query = Expense::with('bus');

    // ✅ فلتر نوع المصروف (مثل: رواتب، كهرباء، صيانة، وقود...)
    if ($request->filled('category')) {
        $search = trim($request->category);
        $query->whereRaw("LOWER(category) LIKE ?", ['%' . strtolower($search) . '%']);
    }

    // ✅ فلتر رقم الحافلة (اختياري فقط)
    if ($request->filled('related_bus_id')) {
        $query->where('related_bus_id', $request->related_bus_id);
    }

    // ✅ فلتر حسب التاريخ
    if ($request->filled('date_from') && $request->filled('date_to')) {
        $query->whereBetween('date', [$request->date_from, $request->date_to]);
    } elseif ($request->filled('date_from')) {
        $query->whereDate('date', '>=', $request->date_from);
    } elseif ($request->filled('date_to')) {
        $query->whereDate('date', '<=', $request->date_to);
    }

    // ✅ يمكن لاحقاً إضافة فلاتر إضافية مثل الحالة (approved / pending)

    // ✅ جلب النتائج بعد الفلترة
    $expenses = $query->orderBy('date', 'desc')->get();

    // ✅ جلب مصروفات الحافلات فقط "قيد المراجعة" لعرضها في القسم العلوي
    $pendingBusExpenses = BusExpense::where('status', 'pending')->latest()->get();

    return view('financial.expenses', compact('expenses', 'pendingBusExpenses'));
}

    // ==========================================
    // 🔹 إضافة مصروف جديد (يدوي)
    // ==========================================
    public function store(Request $request)
    {
        $request->validate([
            'category' => 'required|string|max:255',
            'payment_method' => 'required|string|max:100',
            'amount' => 'required|numeric',
            'date' => 'required|date',
        ]);

        $data = $request->only(['category', 'payment_method', 'amount', 'date', 'related_bus_id', 'notes']);

        if ($request->hasFile('attachment')) {
            $data['attachment'] = $request->file('attachment')->store('expenses', 'public');
        }

        // ✅ إضافة الحالة مباشرة كمصروف معتمد
$data['status'] = 'approved';
Expense::create($data);


        return redirect()->route('financial.expenses')->with('success', 'تمت إضافة المصروف بنجاح ✅');
    }

    // ==========================================
    // 🔹 تحديث المصروف
    // ==========================================
    public function update(Request $request, $id)
    {
        $expense = Expense::findOrFail($id);

        $expense->update([
            'category' => $request->category,
            'payment_method' => $request->payment_method,
            'amount' => $request->amount,
            'date' => $request->date,
            'related_bus_id' => $request->related_bus_id,
            'notes' => $request->notes,
        ]);

        if ($request->hasFile('attachment')) {
            if ($expense->attachment && Storage::disk('public')->exists($expense->attachment)) {
                Storage::disk('public')->delete($expense->attachment);
            }
            $expense->attachment = $request->file('attachment')->store('expenses', 'public');
            $expense->save();
        }

        return response()->json(['success' => true, 'message' => 'تم تعديل المصروف بنجاح ✅']);
    }

    // ==========================================
    // 🔹 حذف المصروف
    // ==========================================
    public function destroy($id)
{
    try {
        // ✅ البحث عن المصروف في جدول المالية
        $expense = Expense::findOrFail($id);

        // ✅ إذا كان مرتبطًا بمصروف حافلة (أي له bus_expense_id)
        if ($expense->related_bus_id) {
            // البحث عن مصروف الحافلة المقابل بناءً على bus_id والمبلغ والتاريخ
            $busExpense = \App\Models\BusExpense::where('bus_id', $expense->related_bus_id)
                ->where('amount', $expense->amount)
                ->whereDate('expense_date', $expense->date)
                ->first();

            // 🔹 إذا تم العثور عليه، نحذفه أيضًا
            if ($busExpense) {
                $busExpense->delete();
            }
        }

        // ✅ حذف المصروف من جدول المالية
        $expense->delete();

        // ✅ إرجاع رسالة نجاح
        return redirect()->back()->with('success', 'تم حذف المصروف من النظام بنجاح وتحديث جميع الجداول.');

    } catch (\Exception $e) {
        // ⚠️ في حال حدوث أي خطأ
        return redirect()->back()->with('error', 'حدث خطأ أثناء الحذف: ' . $e->getMessage());
    }
}

    // ==========================================
    // 🔹 تأكيد مصروف الحافلة (تحويله لقسم المالية)
    // ==========================================
    public function approveBusExpense($id)
{
    $busExpense = BusExpense::findOrFail($id);

    // ✅ نقل بيانات مصروف الحافلة إلى جدول المصروفات المالي بشكل موحد
    $expense = Expense::create([
        'category' => 'مصروف حافلة رقم ' . $busExpense->bus->bus_number,
        'payment_method' => 'نقدًا', // أو اجعلها حسب نوع الحافلة لو موجود في الجدول
        'amount' => $busExpense->amount,
        'date' => $busExpense->expense_date,
        'related_bus_id' => $busExpense->bus_id,
        'bus_expense_id' => $busExpense->id,
        'notes' => $busExpense->description,
        'status' => 'approved', // ✅ تأكيد أن الحالة معتمدة رسميًا
    ]);

    // ✅ تحديث حالة مصروف الحافلة في جدول bus_expenses
    $busExpense->status = 'approved';
    $busExpense->save();

    return redirect()->route('financial.expenses')
        ->with('success', 'تمت الموافقة على مصروف الحافلة وإضافته إلى قائمة المصروفات المالية ✅');
}

    // ==========================================
    // 🔹 رفض مصروف الحافلة
    // ==========================================
    public function rejectBusExpense($id)
    {
        $busExpense = BusExpense::findOrFail($id);
        $busExpense->status = 'rejected';
        $busExpense->save();

        return redirect()->route('financial.expenses')->with('success', 'تم رفض مصروف الحافلة ❌');
    }

    // ==========================================================
    // 🔹 تصدير المصروفات PDF
    // ==========================================================
    public function exportPDF(Request $request)
    {
        $expenses = $this->getFilteredExpenses($request);

        $html = view('financial.export_expenses_pdf', compact('expenses'))->render();

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
            'Content-Disposition' => 'attachment; filename="expenses-report.pdf"',
        ]);
    }

    // ==========================================================
    // 🔹 تصدير المصروفات Excel
    // ==========================================================
    public function exportExcel(Request $request)
    {
        $expenses = $this->getFilteredExpenses($request);

        $fileName = 'expenses-report-' . now()->format('Y-m-d') . '.xlsx';
        $filePath = 'exports/' . $fileName;

        Excel::store(new ExpenseExport($expenses), $filePath, 'local');

        $fullPath = storage_path('app/' . $filePath);

        return response()->download($fullPath, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    // ==========================================================
    // 🔹 جلب المصروفات مع الفلتر
    // ==========================================================
    private function getFilteredExpenses(Request $request)
    {
        $query = Expense::with('bus');

        if ($request->filled('category')) {
            $query->whereRaw("LOWER(category) LIKE ?", ['%' . strtolower($request->category) . '%']);
        }
        if ($request->filled('related_bus_id')) {
            $query->where('related_bus_id', $request->related_bus_id);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }

        return $query->orderBy('date', 'desc')->get();
    }
}
