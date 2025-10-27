<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Expense;
use App\Models\BusExpense; // 🔹 جدول مصروفات الحافلات
use Illuminate\Support\Facades\Storage;

class ExpenseController extends Controller
{
    // ==========================================
    // 🔹 عرض صفحة إدارة المصروفات
    // ==========================================
    public function index(Request $request)
    {
        // ✅ جلب المصروفات الموجودة فعليًا في النظام المالي
        $query = Expense::query();

        if ($request->filled('category')) {
            $query->where('category', 'like', '%' . $request->category . '%');
        }
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }
        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->whereBetween('date', [$request->date_from, $request->date_to]);
        }
        if ($request->filled('related_bus_id')) {
            $query->where('related_bus_id', $request->related_bus_id);
        }

        // ✅ عرض فقط المصروفات المعتمدة (approved)
        $expenses = $query->where('status', 'approved')->latest()->get();


        // ✅ جلب مصروفات الحافلات "قيد المراجعة" من جدول bus_expenses
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
        $expense = Expense::findOrFail($id);

        if ($expense->attachment && Storage::disk('public')->exists($expense->attachment)) {
            Storage::disk('public')->delete($expense->attachment);
        }

        $expense->delete();

        return redirect()->route('financial.expenses')->with('success', 'تم حذف المصروف بنجاح 🗑️');
    }

    // ==========================================
    // 🔹 تأكيد مصروف الحافلة (تحويله لقسم المالية)
    // ==========================================
    public function approveBusExpense($id)
    {
        $busExpense = BusExpense::findOrFail($id);

        // نقل البيانات إلى جدول المصروفات الرئيسي
        Expense::create([
            'category' => $busExpense->expense_type,
            'payment_method' => 'نقدًا',
            'amount' => $busExpense->amount,
            'date' => $busExpense->expense_date,
            'related_bus_id' => $busExpense->bus_id,
            'notes' => $busExpense->description,
        ]);

        // تحديث الحالة
        $busExpense->status = 'approved';
        $busExpense->save();

        return redirect()->route('financial.expenses')->with('success', 'تمت الموافقة على مصروف الحافلة ✅');
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
}
