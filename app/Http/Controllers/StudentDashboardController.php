<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Attendance;
use App\Models\Grade;
use App\Models\StudentPoint;
use Carbon\Carbon;

class StudentDashboardController extends Controller
{
    public function index()
    {
        $totalStudents = Student::count();
        $activeStudents = Student::where('status', 'نشط')->count();
        $maleCount = Student::where('gender', 'ذكر')->count();
        $femaleCount = Student::where('gender', 'أنثى')->count();

        $today = Carbon::today();
        $todayAttendance = Attendance::where('date', $today)->count();
        $todayPresent = Attendance::where('date', $today)->where('status', 'حاضر')->count();
        $todayAbsent = Attendance::where('date', $today)->where('status', 'غائب')->count();
        $todayLate = Attendance::where('date', $today)->where('status', 'متأخر')->count();
        $attendanceRate = $todayAttendance > 0 ? round(($todayPresent / $todayAttendance) * 100, 1) : 0;

        $studentsPerGrade = Student::where('status', 'نشط')
            ->selectRaw('grade, count(*) as count')
            ->groupBy('grade')
            ->pluck('count', 'grade');

        $attendanceTrend = [];
        $trendLabels = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $trendLabels[] = $date->format('m/d');
            $total = Attendance::where('date', $date)->count();
            $present = Attendance::where('date', $date)->where('status', 'حاضر')->count();
            $attendanceTrend[] = $total > 0 ? round(($present / $total) * 100, 1) : 0;
        }

        $recentStudents = Student::latest()->take(5)->get();

        return view('student.dashboard', compact(
            'totalStudents', 'activeStudents', 'maleCount', 'femaleCount',
            'todayPresent', 'todayAbsent', 'todayLate', 'attendanceRate',
            'studentsPerGrade', 'attendanceTrend', 'trendLabels', 'recentStudents'
        ));
    }

    public function honors(Request $request)
    {
        $semester = $request->get('semester', 'الأول');
        $academicYear = $request->get('academic_year');
        $gradeFilter = $request->get('grade');

        $academicYears = Grade::select('academic_year')->distinct()->pluck('academic_year');
        $pointYears = StudentPoint::select('academic_year')->distinct()->pluck('academic_year');
        $academicYears = $academicYears->merge($pointYears)->unique()->sort()->values();
        if ($academicYears->isEmpty()) {
            $academicYears = collect([date('Y')]);
        }

        if (!$academicYear) {
            $academicYear = $academicYears->last();
        }

        $query = Student::where('status', 'نشط');
        if ($gradeFilter) {
            $query->where('grade', $gradeFilter);
        }
        $allStudents = $query->orderBy('grade')->orderBy('class_name')->orderBy('name')->get();

        $gradesList = Student::where('status', 'نشط')->select('grade')->distinct()->pluck('grade');

        $existingPoints = [];
        if ($academicYear) {
            $allPoints = StudentPoint::where('semester', $semester)
                ->where('academic_year', $academicYear)
                ->get();
            foreach ($allPoints as $p) {
                $existingPoints[$p->student_id][$p->subject] = $p;
            }
        }

        $rankings = collect();

        foreach ($allStudents as $student) {
            $studentGrades = Grade::where('student_id', $student->id)
                ->where('semester', $semester);
            if ($academicYear) {
                $studentGrades->where('academic_year', $academicYear);
            }
            $studentGrades = $studentGrades->get();

            $gradeAvg = 0;
            $subjectsCount = 0;
            if ($studentGrades->isNotEmpty()) {
                $totalPercentage = 0;
                foreach ($studentGrades as $g) {
                    $totalPercentage += $g->max_grade > 0 ? ($g->grade_value / $g->max_grade) * 100 : 0;
                }
                $gradeAvg = round($totalPercentage / $studentGrades->count(), 2);
                $subjectsCount = $studentGrades->count();
            }

            $totalAttendance = Attendance::where('student_id', $student->id)->count();
            $presentCount = Attendance::where('student_id', $student->id)
                ->where('status', 'حاضر')->count();
            $attendanceRate = $totalAttendance > 0 ? round(($presentCount / $totalAttendance) * 100, 2) : 0;

            $manualPoints = 0;
            if (isset($existingPoints[$student->id])) {
                foreach ($existingPoints[$student->id] as $sp) {
                    $manualPoints += $sp->points;
                }
            }
            $manualNotes = '';
            $manualCategory = '';

            // 80% درجات + 20% حضور + نقاط المسؤول
            $totalPoints = round(($gradeAvg * 0.8) + ($attendanceRate * 0.2) + $manualPoints, 2);

            $rankings->push([
                'student' => $student,
                'subjects_count' => $subjectsCount,
                'grade_avg' => $gradeAvg,
                'attendance_rate' => $attendanceRate,
                'manual_points' => $manualPoints,
                'manual_notes' => $manualNotes,
                'manual_category' => $manualCategory,
                'total_points' => $totalPoints,
            ]);
        }

        $rankings = $rankings->sortByDesc('total_points')->values();

        return view('student.honors', compact(
            'rankings', 'semester', 'academicYear', 'academicYears',
            'gradeFilter', 'gradesList'
        ));
    }

    public const SUBJECTS = ['الفقه', 'العقيدة', 'التجويد', 'النحو'];

    public function points(Request $request)
    {
        $academicYear = $request->get('academic_year');
        $group = $request->get('group');
        $subjectFilter = $request->get('subject');
        $allSubjects = self::SUBJECTS;
        $groups = Student::where('status', 'نشط')->select('grade')->distinct()->orderBy('grade')->pluck('grade');

        $academicYears = Grade::select('academic_year')->distinct()->pluck('academic_year');
        $pointYears = StudentPoint::select('academic_year')->distinct()->pluck('academic_year');
        $academicYears = $academicYears->merge($pointYears)->unique()->sort()->values();
        if ($academicYears->isEmpty()) {
            $academicYears = collect([date('Y')]);
        }

        if (!$academicYear) {
            $academicYear = $academicYears->last();
        }

        $subjects = $subjectFilter ? [$subjectFilter] : $allSubjects;

        $allStudents = collect();
        if ($group) {
            $allStudents = Student::where('status', 'نشط')
                ->where('grade', $group)
                ->orderBy('class_name')->orderBy('name')->get();
        }

        $existingPoints = [];
        if ($academicYear) {
            $points = StudentPoint::where('semester', 'الأول')
                ->where('academic_year', $academicYear)
                ->get();
            foreach ($points as $p) {
                $existingPoints[$p->student_id][$p->subject] = $p;
            }
        }

        return view('student.points', compact(
            'allStudents', 'academicYear', 'academicYears',
            'group', 'groups', 'subjectFilter', 'subjects', 'allSubjects', 'existingPoints'
        ));
    }

    public function storePoints(Request $request)
    {
        $request->validate([
            'semester' => 'required|string',
            'academic_year' => 'required|string',
            'points' => 'required|array',
        ]);

        $semester = $request->semester;
        $academicYear = $request->academic_year;

        foreach ($request->points as $studentId => $subjects) {
            foreach ($subjects as $subject => $value) {
                $pointsValue = (int)($value ?? 0);

                if ($pointsValue == 0) {
                    StudentPoint::where('student_id', $studentId)
                        ->where('subject', $subject)
                        ->where('semester', $semester)
                        ->where('academic_year', $academicYear)
                        ->delete();
                    continue;
                }

                StudentPoint::updateOrCreate(
                    [
                        'student_id' => $studentId,
                        'subject' => $subject,
                        'semester' => $semester,
                        'academic_year' => $academicYear,
                    ],
                    [
                        'points' => $pointsValue,
                    ]
                );
            }
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'تم حفظ النقاط بنجاح']);
        }

        return redirect()->back()->with('success', 'تم حفظ النقاط بنجاح ✅');
    }
}
