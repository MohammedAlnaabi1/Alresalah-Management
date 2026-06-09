<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;

class ExpenseExport implements FromCollection, WithHeadings, WithEvents, WithTitle, ShouldAutoSize
{
    protected $expenses;
    protected $totalAmount;

    public function __construct($expenses)
    {
        $this->expenses = $expenses;
        $this->totalAmount = $expenses->sum('amount');
    }

    public function title(): string
    {
        return 'المصروفات';
    }

    public function headings(): array
    {
        return ['#', 'الفئة', 'طريقة الدفع', 'المبلغ (ر.ع)', 'التاريخ', 'الحافلة', 'ملاحظات'];
    }

    public function collection()
    {
        if ($this->expenses->isEmpty()) {
            return collect([['لا توجد بيانات', '', '', '', '', '', '']]);
        }

        return $this->expenses->values()->map(function ($exp, $index) {
            return [
                '#' => $index + 1,
                'الفئة' => $exp->category,
                'طريقة الدفع' => $exp->payment_method,
                'المبلغ (ر.ع)' => $exp->amount,
                'التاريخ' => $exp->date instanceof \Carbon\Carbon ? $exp->date->format('Y-m-d') : $exp->date,
                'الحافلة' => $exp->related_bus_id ? 'حافلة رقم ' . $exp->related_bus_id : '-',
                'ملاحظات' => $exp->notes ?? '-',
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
                $sheet->getStyle('A1:G1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 12],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    ],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FFF8D7DA'],
                    ],
                ]);

                // حدود الجدول
                $lastRow = $sheet->getHighestRow();
                $sheet->getStyle("A1:G{$lastRow}")
                    ->getBorders()->getAllBorders()
                    ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

                // صف الإجمالي
                $summaryRow = $lastRow + 2;
                $sheet->setCellValue("C{$summaryRow}", 'إجمالي المصروفات');
                $sheet->setCellValue("D{$summaryRow}", $this->totalAmount);
                $sheet->getStyle("C{$summaryRow}:D{$summaryRow}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 12, 'color' => ['argb' => 'FFDC3545']],
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
