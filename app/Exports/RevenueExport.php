<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;

class RevenueExport implements FromCollection, WithHeadings, WithEvents, WithTitle, ShouldAutoSize
{
    protected $revenues;
    protected $totalAmount;

    public function __construct($revenues)
    {
        $this->revenues = $revenues;
        $this->totalAmount = $revenues->sum('amount');
    }

    public function title(): string
    {
        return 'الإيرادات';
    }

    public function headings(): array
    {
        return ['#', 'المصدر', 'النوع', 'المبلغ (ر.ع)', 'التاريخ', 'ملاحظات'];
    }

    public function collection()
    {
        if ($this->revenues->isEmpty()) {
            return collect([['لا توجد بيانات', '', '', '', '', '']]);
        }

        return $this->revenues->values()->map(function ($rev, $index) {
            return [
                '#' => $index + 1,
                'المصدر' => $rev->source,
                'النوع' => $rev->type,
                'المبلغ (ر.ع)' => $rev->amount,
                'التاريخ' => $rev->date,
                'ملاحظات' => $rev->notes ?? '-',
            ];
        });
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                $sheet->setRightToLeft(true);

                // تنسيق رأس الجدول
                $sheet->getStyle('A1:F1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 12],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    ],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FFD4EDDA'],
                    ],
                ]);

                // حدود الجدول
                $lastRow = $sheet->getHighestRow();
                $sheet->getStyle("A1:F{$lastRow}")
                    ->getBorders()->getAllBorders()
                    ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

                // صف الإجمالي
                $summaryRow = $lastRow + 2;
                $sheet->setCellValue("C{$summaryRow}", 'إجمالي الإيرادات');
                $sheet->setCellValue("D{$summaryRow}", $this->totalAmount);
                $sheet->getStyle("C{$summaryRow}:D{$summaryRow}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 12, 'color' => ['argb' => 'FF198754']],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        ],
                    ],
                ]);
            },
        ];
    }
}
