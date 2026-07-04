<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Exports\StudentExport;
use Maatwebsite\Excel\Facades\Excel;
use Mpdf\Mpdf;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $query = Student::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('parent_name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('grade')) {
            $query->where('grade', $request->grade);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $students = $query->orderBy('name')->get();
        $grades = Student::select('grade')->distinct()->pluck('grade');

        return view('student.students', compact('students', 'grades'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'            => 'required|string|max:255',
            'grade'           => 'required|string|max:100',
            'class_name'      => 'required|string|max:50',
            'date_of_birth'   => 'required|date',
            'gender'          => 'required|in:ذكر,أنثى',
            'enrollment_date' => 'required|date',
            'parent_name'     => 'required|string|max:255',
            'phone'           => 'required|string|max:20',
            'address'         => 'nullable|string',
            'status'          => 'nullable|string|max:50',
            'notes'           => 'nullable|string',
        ]);

        Student::create($data);

        return redirect()->back()->with('success', 'تمت إضافة الطالب بنجاح ✅');
    }

    public function update(Request $request, $id)
    {
        $student = Student::findOrFail($id);

        $data = $request->validate([
            'name'            => 'required|string|max:255',
            'grade'           => 'required|string|max:100',
            'class_name'      => 'required|string|max:50',
            'date_of_birth'   => 'required|date',
            'gender'          => 'required|in:ذكر,أنثى',
            'enrollment_date' => 'required|date',
            'parent_name'     => 'required|string|max:255',
            'phone'           => 'required|string|max:20',
            'address'         => 'nullable|string',
            'status'          => 'nullable|string|max:50',
            'notes'           => 'nullable|string',
        ]);

        $student->update($data);

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث بيانات الطالب بنجاح ✅'
        ]);
    }

    public function destroy($id)
    {
        $student = Student::findOrFail($id);
        $student->delete();

        return redirect()->back()->with('success', 'تم حذف الطالب بنجاح 🗑️');
    }

    public function exportPDF(Request $request)
    {
        $students = $this->getFilteredStudents($request);

        $html = view('student.export_pdf', compact('students'))->render();

        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4-L',
            'autoArabic' => true,
            'autoLangToFont' => true,
            'default_font' => 'XBRiyaz',
            'tempDir' => storage_path('app/mpdf-temp'),
        ]);

        $mpdf->SetDirectionality('rtl');
        $mpdf->WriteHTML($html);

        return response($mpdf->Output('', 'S'), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="students-report.pdf"',
        ]);
    }

    public function exportExcel(Request $request)
    {
        $students = $this->getFilteredStudents($request);

        $fileName = 'students-report-' . now()->format('Y-m-d') . '.xlsx';
        $filePath = 'exports/' . $fileName;

        Excel::store(new StudentExport($students), $filePath, 'local');

        $fullPath = storage_path('app/' . $filePath);

        return response()->download($fullPath, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    private function getFilteredStudents(Request $request)
    {
        $query = Student::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('parent_name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('grade')) {
            $query->where('grade', $request->grade);
        }

        return $query->orderBy('name')->get();
    }
}
