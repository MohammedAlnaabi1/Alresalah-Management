<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Grade;
use App\Exports\GradeExport;
use Maatwebsite\Excel\Facades\Excel;
use Mpdf\Mpdf;

class GradeController extends Controller
{
    public const SUBJECTS = ['الفقه', 'العقيدة', 'التجويد', 'النحو'];
    public const GROUPS = ['السنة الأولى', 'السنة الثانية'];
    public const MAX_GRADE = 25;

    public function index(Request $request)
    {
        $group = $request->get('group');
        $academicYear = $request->get('academic_year');

        $subjects = self::SUBJECTS;
        $groups = self::GROUPS;
        $maxGrade = self::MAX_GRADE;

        $academicYears = Grade::select('academic_year')->distinct()->pluck('academic_year');
        if ($academicYears->isEmpty()) {
            $academicYears = collect([date('Y')]);
        }
        if (!$academicYear) {
            $academicYear = $academicYears->last();
        }

        $students = collect();
        $existingGrades = [];

        if ($group) {
            $students = Student::where('status', 'نشط')
                ->where('grade', $group)
                ->orderBy('class_name')->orderBy('name')->get();

            $gradeRecords = Grade::whereIn('student_id', $students->pluck('id'))
                ->where('academic_year', $academicYear)
                ->get();

            foreach ($gradeRecords as $g) {
                $existingGrades[$g->student_id][$g->subject] = $g;
            }
        }

        return view('student.grades', compact(
            'students', 'group', 'groups', 'subjects', 'maxGrade',
            'academicYear', 'academicYears', 'existingGrades'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'academic_year' => 'required|string',
            'grades' => 'required|array',
        ]);

        $academicYear = $request->academic_year;
        $maxGrade = self::MAX_GRADE;

        foreach ($request->grades as $studentId => $subjects) {
            foreach ($subjects as $subject => $value) {
                $gradeValue = $value ?? null;
                if ($gradeValue === null || $gradeValue === '') {
                    continue;
                }

                Grade::updateOrCreate(
                    [
                        'student_id' => $studentId,
                        'subject' => $subject,
                        'academic_year' => $academicYear,
                        'semester' => 'الأول',
                    ],
                    [
                        'grade_value' => $gradeValue,
                        'max_grade' => $maxGrade,
                    ]
                );
            }
        }

        $group = $request->get('group');
        return redirect()->route('student.grades', [
            'group' => $group,
            'academic_year' => $academicYear,
        ])->with('success', 'تم حفظ الدرجات بنجاح ✅');
    }

    public function update(Request $request, $id)
    {
        $grade = Grade::findOrFail($id);

        $data = $request->validate([
            'grade_value' => 'required|numeric|min:0|max:25',
            'notes' => 'nullable|string',
        ]);

        $grade->update($data);

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث الدرجة بنجاح ✅'
        ]);
    }

    public function destroy($id)
    {
        $grade = Grade::findOrFail($id);
        $grade->delete();

        return redirect()->back()->with('success', 'تم حذف الدرجة بنجاح');
    }

    public function exportPDF(Request $request)
    {
        $grades = $this->getFilteredGrades($request);

        $html = view('student.export_grades_pdf', compact('grades'))->render();

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
            'Content-Disposition' => 'attachment; filename="grades-report.pdf"',
        ]);
    }

    public function exportExcel(Request $request)
    {
        $grades = $this->getFilteredGrades($request);

        $fileName = 'grades-report-' . now()->format('Y-m-d') . '.xlsx';
        $filePath = 'exports/' . $fileName;

        Excel::store(new GradeExport($grades), $filePath, 'local');

        $fullPath = storage_path('app/' . $filePath);

        return response()->download($fullPath, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    private function getFilteredGrades(Request $request)
    {
        $query = Grade::with('student');

        if ($request->filled('group')) {
            $studentIds = Student::where('grade', $request->group)->pluck('id');
            $query->whereIn('student_id', $studentIds);
        }
        if ($request->filled('academic_year')) {
            $query->where('academic_year', $request->academic_year);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }
}
