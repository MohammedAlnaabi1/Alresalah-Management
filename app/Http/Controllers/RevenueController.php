<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Revenue;
use App\Exports\RevenueExport;
use Maatwebsite\Excel\Facades\Excel;
use Mpdf\Mpdf;

class RevenueController extends Controller
{
    // 🔹 عرض كل الإيرادات
    public function index(Request $request)
    {
        $query = Revenue::query();

        if ($request->filled('source')) {
            $query->where('source', 'like', '%' . $request->source . '%');
        }
        if ($request->filled('type')) {
            $query->where('type', 'like', '%' . $request->type . '%');
        }
        if ($request->filled('from_date')) {
            $query->whereDate('date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('date', '<=', $request->to_date);
        }

        $revenues = $query->orderBy('date', 'desc')->get();
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

    // ==========================================================
    // 🔹 تصدير الإيرادات PDF
    // ==========================================================
    public function exportPDF(Request $request)
    {
        $revenues = $this->getFilteredRevenues($request);

        $html = view('financial.export_revenues_pdf', compact('revenues'))->render();

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
            'Content-Disposition' => 'attachment; filename="revenues-report.pdf"',
        ]);
    }

    // ==========================================================
    // 🔹 تصدير الإيرادات Excel
    // ==========================================================
    public function exportExcel(Request $request)
    {
        $revenues = $this->getFilteredRevenues($request);

        $fileName = 'revenues-report-' . now()->format('Y-m-d') . '.xlsx';
        $filePath = 'exports/' . $fileName;

        Excel::store(new RevenueExport($revenues), $filePath, 'local');

        $fullPath = storage_path('app/' . $filePath);

        return response()->download($fullPath, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    // ==========================================================
    // 🔹 جلب الإيرادات مع الفلتر
    // ==========================================================
    private function getFilteredRevenues(Request $request)
    {
        $query = Revenue::query();

        if ($request->filled('source')) {
            $query->where('source', 'like', '%' . $request->source . '%');
        }
        if ($request->filled('type')) {
            $query->where('type', 'like', '%' . $request->type . '%');
        }
        if ($request->filled('from_date')) {
            $query->whereDate('date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('date', '<=', $request->to_date);
        }

        return $query->orderBy('date', 'desc')->get();
    }

}
