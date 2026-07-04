<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Attendance;
use App\Exports\AttendanceExport;
use Maatwebsite\Excel\Facades\Excel;
use Mpdf\Mpdf;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public const SUBJECTS = ['الفقه', 'العقيدة', 'التجويد', 'النحو'];
    public const GROUPS = ['السنة الأولى', 'السنة الثانية'];

    public function index(Request $request)
    {
        $date = $request->get('date', Carbon::today()->format('Y-m-d'));
        $subject = $request->get('subject');
        $group = $request->get('group');

        $subjects = self::SUBJECTS;
        $groups = self::GROUPS;

        $students = collect();
        $attendances = [];
        $attendanceNotes = [];
        $presentCount = 0;
        $absentCount = 0;
        $lateCount = 0;

        if ($group && $subject) {
            $students = Student::where('status', 'نشط')
                ->where('grade', $group)
                ->orderBy('class_name')->orderBy('name')->get();

            $attendances = Attendance::where('date', $date)
                ->where('subject', $subject)
                ->pluck('status', 'student_id')
                ->toArray();

            $attendanceNotes = Attendance::where('date', $date)
                ->where('subject', $subject)
                ->pluck('notes', 'student_id')
                ->toArray();

            $baseQuery = Attendance::where('date', $date)->where('subject', $subject)
                ->whereIn('student_id', $students->pluck('id'));
            $presentCount = (clone $baseQuery)->where('status', 'حاضر')->count();
            $absentCount = (clone $baseQuery)->where('status', 'غائب')->count();
            $lateCount = (clone $baseQuery)->where('status', 'متأخر')->count();
        }

        return view('student.attendance', compact(
            'students', 'attendances', 'attendanceNotes', 'date',
            'subject', 'group', 'subjects', 'groups',
            'presentCount', 'absentCount', 'lateCount'
        ));
    }

    public function report(Request $request)
    {
        $group = $request->get('group');
        $subject = $request->get('subject');
        $fromDate = $request->get('from_date', Carbon::today()->subDays(30)->format('Y-m-d'));
        $toDate = $request->get('to_date', Carbon::today()->format('Y-m-d'));

        $query = Student::where('status', 'نشط');
        if ($group) {
            $query->where('grade', $group);
        }
        $students = $query->orderBy('grade')->orderBy('class_name')->orderBy('name')->get();

        $studentIds = $students->pluck('id');

        $groups = self::GROUPS;
        $subjects = self::SUBJECTS;

        $attQuery = Attendance::whereBetween('date', [$fromDate, $toDate])
            ->whereIn('student_id', $studentIds);
        if ($subject) {
            $attQuery->where('subject', $subject);
        }

        $allDates = collect();
        $current = Carbon::parse($toDate);
        $start = Carbon::parse($fromDate);
        while ($current->gte($start)) {
            $allDates->push($current->format('Y-m-d'));
            $current->subDay();
        }
        $dates = $allDates;

        $attendanceData = $attQuery->get()->groupBy('student_id');

        $report = [];
        foreach ($students as $student) {
            $records = $attendanceData->get($student->id, collect());
            $present = $records->where('status', 'حاضر')->count();
            $absent = $records->where('status', 'غائب')->count();
            $late = $records->where('status', 'متأخر')->count();
            $total = $present + $absent + $late;
            $rate = $total > 0 ? round(($present / $total) * 100, 1) : 0;

            $dailyStatus = [];
            foreach ($dates as $d) {
                $dayRecords = $records->filter(function ($rec) use ($d) {
                    return $rec->date->format('Y-m-d') === $d;
                });

                if ($dayRecords->isEmpty()) {
                    $dailyStatus[$d] = null;
                } else {
                    $dayAbsent = $dayRecords->where('status', 'غائب')->count();
                    $dayPresent = $dayRecords->where('status', 'حاضر')->count();
                    $dayTotal = $dayRecords->count();

                    if ($dayAbsent === $dayTotal) {
                        $dailyStatus[$d] = 'غائب';
                    } elseif ($dayPresent === $dayTotal) {
                        $dailyStatus[$d] = 'حاضر';
                    } else {
                        $dailyStatus[$d] = 'متأخر';
                    }
                }
            }

            $report[] = [
                'student' => $student,
                'present' => $present,
                'absent' => $absent,
                'late' => $late,
                'total' => $total,
                'rate' => $rate,
                'daily' => $dailyStatus,
            ];
        }

        return view('student.attendance_report', compact(
            'report', 'dates', 'group', 'subject', 'groups', 'subjects', 'fromDate', 'toDate'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'subject' => 'required|string',
            'attendance' => 'required|array',
            'attendance.*.status' => 'required|in:حاضر,غائب,متأخر',
        ]);

        $date = $request->date;
        $subject = $request->subject;

        foreach ($request->attendance as $studentId => $data) {
            Attendance::updateOrCreate(
                [
                    'student_id' => $studentId,
                    'date' => $date,
                    'subject' => $subject,
                    'period' => 1,
                ],
                [
                    'status' => $data['status'],
                    'notes' => $data['notes'] ?? null,
                ]
            );
        }

        $group = $request->get('group');
        return redirect()->route('student.attendance', [
            'date' => $date,
            'subject' => $subject,
            'group' => $group,
        ])->with('success', "تم حفظ حضور {$subject} بنجاح ✅");
    }

    public function exportPDF(Request $request)
    {
        $date = $request->get('date', Carbon::today()->format('Y-m-d'));
        $subject = $request->get('subject');

        $query = Attendance::with('student')->where('date', $date);
        if ($subject) {
            $query->where('subject', $subject);
        }
        $attendances = $query->get();

        $html = view('student.export_attendance_pdf', compact('attendances', 'date', 'subject'))->render();

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
            'Content-Disposition' => 'attachment; filename="attendance-' . $date . '.pdf"',
        ]);
    }

    public function exportExcel(Request $request)
    {
        $date = $request->get('date', Carbon::today()->format('Y-m-d'));
        $subject = $request->get('subject');

        $query = Attendance::with('student')->where('date', $date);
        if ($subject) {
            $query->where('subject', $subject);
        }
        $attendances = $query->get();

        $fileName = 'attendance-' . $date . '.xlsx';
        $filePath = 'exports/' . $fileName;

        Excel::store(new AttendanceExport($attendances, $date), $filePath, 'local');

        $fullPath = storage_path('app/' . $filePath);

        return response()->download($fullPath, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }
}
