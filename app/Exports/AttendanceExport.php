<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;

class AttendanceExport implements FromCollection, WithHeadings, WithEvents, WithTitle, ShouldAutoSize
{
    protected $attendances;
    protected $date;

    public function __construct($attendances, $date)
    {
        $this->attendances = $attendances;
        $this->date = $date;
    }

    public function title(): string
    {
        return 'الحضور - ' . $this->date;
    }

    public function headings(): array
    {
        return ['#', 'اسم الطالب', 'الصف', 'الفصل', 'الحالة', 'ملاحظات'];
    }

    public function collection()
    {
        if ($this->attendances->isEmpty()) {
            return collect([['لا توجد بيانات', '', '', '', '', '']]);
        }

        return $this->attendances->values()->map(function ($a, $index) {
            return [
                '#' => $index + 1,
                'اسم الطالب' => $a->student->name ?? '-',
                'الصف' => $a->student->grade ?? '-',
                'الفصل' => $a->student->class_name ?? '-',
                'الحالة' => $a->status,
                'ملاحظات' => $a->notes ?? '-',
            ];
        });
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $sheet->setRightToLeft(true);

                $sheet->getStyle('A1:F1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 12],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    ],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FFFFF3CD'],
                    ],
                ]);

                $lastRow = $sheet->getHighestRow();
                $sheet->getStyle("A1:F{$lastRow}")
                    ->getBorders()->getAllBorders()
                    ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            },
        ];
    }
}
