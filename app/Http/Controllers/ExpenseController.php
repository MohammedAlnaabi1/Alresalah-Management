<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Expense;

class ExpenseController extends Controller
{
    // 🔹 عرض المصروفات
    public function index()
    {
        $expenses = Expense::orderBy('date', 'desc')->get();
        return view('financial.expenses', compact('expenses'));
    }

    // 🔹 إضافة مصروف جديد
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|string|max:100',
            'date' => 'required|date',
            'notes' => 'nullable|string',
            'related_bus_id' => 'nullable|numeric',
            'attachment' => 'nullable|file|mimes:jpg,png,pdf|max:2048'
        ]);

        // حفظ المرفق
        if ($request->hasFile('attachment')) {
            $validated['attachment'] = $request->file('attachment')->store('attachments/expenses', 'public');
        }

        Expense::create($validated);

        return redirect()->back()->with('success', 'تمت إضافة المصروف بنجاح ✅');
    }

    // 🔹 حذف مصروف
    public function destroy($id)
    {
        $expense = Expense::findOrFail($id);

        if ($expense->attachment && file_exists(storage_path('app/public/' . $expense->attachment))) {
            unlink(storage_path('app/public/' . $expense->attachment));
        }

        $expense->delete();

        return redirect()->back()->with('success', 'تم حذف المصروف بنجاح ❌');
    }
    // 🔹 تعديل مصروف
public function update(Request $request, $id)
{
    try {
        $expense = Expense::findOrFail($id);

        $validated = $request->validate([
            'category'        => 'required|string|max:255',
            'amount'          => 'required|numeric|min:0',
            'payment_method'  => 'required|string|max:100',
            'date'            => 'required|date',
            'notes'           => 'nullable|string',
            'related_bus_id'  => 'nullable|numeric',
            'attachment'      => 'nullable|file|mimes:jpg,png,pdf|max:2048'
        ]);

        // ✅ المرفق
        if ($request->hasFile('attachment')) {
            if ($expense->attachment && file_exists(storage_path('app/public/' . $expense->attachment))) {
                unlink(storage_path('app/public/' . $expense->attachment));
            }
            $validated['attachment'] = $request->file('attachment')->store('attachments/expenses', 'public');
        }

        $expense->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث المصروف بنجاح ✅'
        ]);

    } catch (\Throwable $e) {
        \Log::error('Expense update error: ' . $e->getMessage());

        return response()->json([
            'success' => false,
            'message' => 'حدث خطأ أثناء التحديث ❌',
            'error'   => $e->getMessage()
        ], 500);
    }
}

}
