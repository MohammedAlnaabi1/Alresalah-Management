<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;

class StudentExport implements FromCollection, WithHeadings, WithEvents, WithTitle, ShouldAutoSize
{
    protected $students;

    public function __construct($students)
    {
        $this->students = $students;
    }

    public function title(): string
    {
        return 'الطلاب';
    }

    public function headings(): array
    {
        return ['#', 'الاسم', 'الصف', 'الفصل', 'الجنس', 'تاريخ الميلاد', 'ولي الأمر', 'الهاتف', 'الحالة'];
    }

    public function collection()
    {
        if ($this->students->isEmpty()) {
            return collect([['لا توجد بيانات', '', '', '', '', '', '', '', '']]);
        }

        return $this->students->values()->map(function ($s, $index) {
            return [
                '#' => $index + 1,
                'الاسم' => $s->name,
                'الصف' => $s->grade,
                'الفصل' => $s->class_name,
                'الجنس' => $s->gender,
                'تاريخ الميلاد' => $s->date_of_birth->format('Y-m-d'),
                'ولي الأمر' => $s->parent_name,
                'الهاتف' => $s->phone,
                'الحالة' => $s->status,
            ];
        });
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $sheet->setRightToLeft(true);

                $sheet->getStyle('A1:I1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 12],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    ],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FFD4EDDA'],
                    ],
                ]);

                $lastRow = $sheet->getHighestRow();
                $sheet->getStyle("A1:I{$lastRow}")
                    ->getBorders()->getAllBorders()
                    ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            },
        ];
    }
}
