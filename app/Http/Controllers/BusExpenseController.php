<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bus;
use App\Models\BusExpense;

class BusExpenseController extends Controller
{
    // 🔹 عرض الصفحة الرئيسية لمصروفات الحافلات
    public function index(Request $request)
    {
        $buses = Bus::all();
        $query = BusExpense::with('bus');

        // ✅ فلترة حسب رقم الحافلة
        if ($request->filled('bus_id')) {
            $query->where('bus_id', $request->bus_id);
        }

        // ✅ فلترة حسب نوع المصروف
        if ($request->filled('expense_type')) {
            $query->where('expense_type', $request->expense_type);
        }

        // ✅ فلترة حسب التاريخ
        if ($request->filled('from_date')) {
            $query->whereDate('expense_date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('expense_date', '<=', $request->to_date);
        }

        // ✅ عرض جميع السجلات (بغض النظر عن الحالة)
        $expenses = $query->latest()->get();

        // ✅ المجموع العام فقط للمصروفات "المعتمدة"
        $total = BusExpense::where('status', 'approved')->sum('amount');

        return view('bus_expenses', compact('buses', 'expenses', 'total'));
    }

    // 🔹 إضافة مصروف جديد (افتراضيًا بالحالة pending)
    public function store(Request $request)
    {
        $request->validate([
            'bus_id' => 'required|exists:buses,id',
            'expense_type' => 'required|string|max:100',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'receipt_pdf' => 'nullable|mimes:pdf|max:2048',
        ]);

        $path = $request->hasFile('receipt_pdf')
            ? $request->file('receipt_pdf')->store('bus_expenses', 'public')
            : null;

        BusExpense::create([
            'bus_id' => $request->bus_id,
            'expense_type' => $request->expense_type,
            'description' => $request->description,
            'amount' => $request->amount,
            'expense_date' => $request->expense_date,
            'receipt_pdf' => $path,
            'status' => 'pending', // 🟡 بانتظار موافقة المالية
        ]);

        return redirect()->back()->with('success', 'تمت إضافة المصروف وإرساله إلى المالية للمراجعة ✅');
    }

    // 🔹 عرض ملف الفاتورة
    public function viewReceipt($id)
    {
        $expense = BusExpense::findOrFail($id);

        if (!$expense->receipt_pdf) {
            abort(404, 'لا يوجد ملف فاتورة');
        }

        $path = storage_path('app/public/' . $expense->receipt_pdf);

        if (!file_exists($path)) {
            abort(404, 'الملف غير موجود');
        }

        return response()->file($path);
    }

    // 🔹 تعديل مصروف
    public function update(Request $request, $id)
    {
        $expense = BusExpense::findOrFail($id);

        $request->validate([
            'expense_type' => 'required|string|max:100',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'description' => 'nullable|string|max:255',
            'receipt_pdf' => 'nullable|mimes:pdf|max:2048',
        ]);

        if ($request->hasFile('receipt_pdf')) {
            if ($expense->receipt_pdf && file_exists(storage_path('app/public/' . $expense->receipt_pdf))) {
                unlink(storage_path('app/public/' . $expense->receipt_pdf));
            }
            $expense->receipt_pdf = $request->file('receipt_pdf')->store('bus_expenses', 'public');
        }

        $expense->update([
            'expense_type' => $request->expense_type,
            'amount' => $request->amount,
            'expense_date' => $request->expense_date,
            'description' => $request->description,
            'receipt_pdf' => $expense->receipt_pdf,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث بيانات المصروف بنجاح ✅',
        ]);
    }

    // 🔹 حذف مصروف
    public function destroy($id)
    {
        $expense = BusExpense::findOrFail($id);

        if ($expense->receipt_pdf && file_exists(storage_path('app/public/' . $expense->receipt_pdf))) {
            unlink(storage_path('app/public/' . $expense->receipt_pdf));
        }

        $expense->delete();

        return redirect()->back()->with('success', 'تم حذف المصروف بنجاح 🗑️');
    }
}
