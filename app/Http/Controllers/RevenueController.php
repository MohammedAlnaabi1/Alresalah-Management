<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Revenue;

class RevenueController extends Controller
{
    // 🔹 عرض كل الإيرادات
    public function index()
    {
        $revenues = Revenue::orderBy('date', 'desc')->get();
        return view('financial.revenues', compact('revenues'));
    }

    // 🔹 إضافة إيراد جديد
    public function store(Request $request)
    {
        $validated = $request->validate([
            'source' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'type' => 'required|string|max:100',
            'date' => 'required|date',
            'notes' => 'nullable|string',
            'attachment' => 'nullable|file|mimes:jpg,png,pdf|max:2048'
        ]);

        // حفظ الملف إن وجد
        if ($request->hasFile('attachment')) {
            $validated['attachment'] = $request->file('attachment')->store('attachments/revenues', 'public');
        }

        Revenue::create($validated);

        return redirect()->back()->with('success', 'تمت إضافة الإيراد بنجاح ✅');
    }

    // 🔹 حذف إيراد
    public function destroy($id)
    {
        $revenue = Revenue::findOrFail($id);

        // حذف المرفق إن وجد
        if ($revenue->attachment && file_exists(storage_path('app/public/' . $revenue->attachment))) {
            unlink(storage_path('app/public/' . $revenue->attachment));
        }

        $revenue->delete();

        return redirect()->back()->with('success', 'تم حذف الإيراد بنجاح ❌');
    }

   public function update(Request $request, $id)
{
    try {
        $revenue = Revenue::findOrFail($id);

        $validated = $request->validate([
            'source' => 'required|string|max:255',
            'type'   => 'required|string|max:100',
            'amount' => 'required|numeric|min:0',
            'date'   => 'required|date',
            'notes'  => 'nullable|string',
            'attachment' => 'nullable|file|mimes:jpg,png,pdf|max:2048',
        ]);

        // ✅ تحديث المرفق
        if ($request->hasFile('attachment')) {
            if ($revenue->attachment && file_exists(storage_path('app/public/' . $revenue->attachment))) {
                unlink(storage_path('app/public/' . $revenue->attachment));
            }
            $validated['attachment'] = $request->file('attachment')->store('attachments/revenues', 'public');
        }

        $revenue->update($validated);

        // ✅ استجابة JSON واضحة
        return response()->json([
            'success' => true,
            'message' => 'تم تحديث الإيراد بنجاح ✅'
        ]);

    } catch (\Throwable $e) {
        \Log::error('Revenue update error: ' . $e->getMessage());

        return response()->json([
            'success' => false,
            'message' => 'حدث خطأ أثناء التحديث ❌',
            'error'   => $e->getMessage(),
        ], 500);
    }
}

}
