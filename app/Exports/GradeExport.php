<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;

class GradeExport implements FromCollection, WithHeadings, WithEvents, WithTitle, ShouldAutoSize
{
    protected $grades;
    protected $subjects = ['الفقه', 'العقيدة', 'التجويد', 'النحو'];
    protected $maxGrade = 25;

    public function __construct($grades)
    {
        $this->grades = $grades;
    }

    public function title(): string
    {
        return 'الدرجات';
    }

    public function headings(): array
    {
        $headers = ['#', 'اسم الطالب', 'المجموعة', 'الفصل'];
        foreach ($this->subjects as $sub) {
            $headers[] = $sub . ' /25';
        }
        $headers[] = 'المجموع';
        $headers[] = 'من';
        $headers[] = 'النسبة %';
        return $headers;
    }

    public function collection()
    {
        if ($this->grades->isEmpty()) {
            return collect([['لا توجد بيانات']]);
        }

        $grouped = [];
        foreach ($this->grades as $g) {
            $sid = $g->student_id;
            if (!isset($grouped[$sid])) {
                $grouped[$sid] = [
                    'student' => $g->student,
                    'subjects' => [],
                ];
            }
            $grouped[$sid]['subjects'][$g->subject] = $g->grade_value;
        }

        $rows = collect();
        $index = 0;
        foreach ($grouped as $data) {
            $index++;
            $student = $data['student'];
            $total = 0;
            $row = [
                $index,
                $student->name ?? '-',
                $student->grade ?? '-',
                $student->class_name ?? '-',
            ];
            foreach ($this->subjects as $sub) {
                $val = $data['subjects'][$sub] ?? '';
                $row[] = $val;
                $total += (float) $val;
            }
            $maxTotal = count($this->subjects) * $this->maxGrade;
            $pct = $maxTotal > 0 ? round(($total / $maxTotal) * 100, 1) : 0;
            $row[] = $total;
            $row[] = $maxTotal;
            $row[] = $pct . '%';

            $rows->push($row);
        }

        return $rows;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $sheet->setRightToLeft(true);

                $lastCol = 'K';
                $sheet->getStyle("A1:{$lastCol}1")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 12],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    ],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FFD1ECF1'],
                    ],
                ]);

                $lastRow = $sheet->getHighestRow();
                $sheet->getStyle("A1:{$lastCol}{$lastRow}")->applyFromArray([
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        ],
                    ],
                ]);

                // Subject columns header color
                $sheet->getStyle("E1:H1")->applyFromArray([
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FFFFF3CD'],
                    ],
                ]);

                // Total columns header color
                $sheet->getStyle("I1:K1")->applyFromArray([
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FFD4EDDA'],
                    ],
                ]);
            },
        ];
    }
}
