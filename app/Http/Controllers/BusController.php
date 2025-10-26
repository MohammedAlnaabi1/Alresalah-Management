<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bus;

class BusController extends Controller
{
    // 🟢 عرض جميع الحافلات
    public function index()
    {
        $buses = Bus::all();
        return view('bus', compact('buses'));
    }

    // 🟣 عرض ملف الملكية أو التأمين
    public function viewFile($id, $type)
    { 
        $bus = Bus::findOrFail($id);

        if ($type === 'ownership' && $bus->ownership_pdf) {
            $path = storage_path('app/public/' . $bus->ownership_pdf);
        } elseif ($type === 'insurance' && $bus->insurance_pdf) {
            $path = storage_path('app/public/' . $bus->insurance_pdf);
        } else {
            abort(404, 'الملف غير موجود');
        }

        if (!file_exists($path)) {
            abort(404, 'الملف غير موجود');
        }

        return response()->file($path);
    }

    // 🟠 حفظ حافلة جديدة
    public function store(Request $request)
    {
        $data = $request->validate([
            'bus_number'    => 'required|string|max:100',
            'bus_type'      => 'required|string|max:50',
            'driver_name'   => 'required|string|max:100',
            'bus_code'      => 'required|numeric|min:1',
            'school'        => 'required|string|max:150',
            'status'        => 'required|string',
            'ownership_pdf' => 'nullable|file|mimes:pdf|max:5120',
            'insurance_pdf' => 'nullable|file|mimes:pdf|max:5120',
        ]);

        if ($request->hasFile('ownership_pdf')) {
            $data['ownership_pdf'] = $request->file('ownership_pdf')->store('buses', 'public');
        }

        if ($request->hasFile('insurance_pdf')) {
            $data['insurance_pdf'] = $request->file('insurance_pdf')->store('buses', 'public');
        }

        Bus::create($data);

        return redirect()->route('bus')->with('success', 'تمت إضافة الحافلة بنجاح ✅');
    }

    // 🔵 تعديل بيانات حافلة (AJAX)
    public function update(Request $request, $id)
    {
        $bus = Bus::findOrFail($id);

        $data = $request->validate([
            'bus_number'    => 'required|string|max:100',
            'bus_type'      => 'required|string|max:50',
            'driver_name'   => 'required|string|max:100',
            'bus_code'      => 'required|numeric|min:1',
            'school'        => 'required|string|max:150',
            'status'        => 'required|string',
            'ownership_pdf' => 'nullable|file|mimes:pdf|max:5120',
            'insurance_pdf' => 'nullable|file|mimes:pdf|max:5120',
        ]);

        // ✅ تحديث الملفات فقط إذا تم رفع ملفات جديدة
        if ($request->hasFile('ownership_pdf')) {
            $data['ownership_pdf'] = $request->file('ownership_pdf')->store('buses', 'public');
        }

        if ($request->hasFile('insurance_pdf')) {
            $data['insurance_pdf'] = $request->file('insurance_pdf')->store('buses', 'public');
        }

        $bus->update($data);

        // ✅ الرد بـ JSON إلى AJAX
        return response()->json([
            'success' => true,
            'message' => 'تم تحديث بيانات الحافلة بنجاح ✅'
        ]);
    }

    // 🔴 حذف حافلة
    public function destroy($id)
    {
        $bus = Bus::findOrFail($id);
        $bus->delete();

        return redirect()->back()->with('success', 'تم حذف الحافلة بنجاح 🗑️');
    }
}
