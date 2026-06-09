<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;

class FinancialReportExport implements FromCollection, WithHeadings, WithEvents, WithTitle, ShouldAutoSize
{
    protected $data;
    protected $transactions;

    public function __construct($data)
    {
        $this->data = $data;
        $this->transactions = collect(data_get($data, 'transactions', []));
    }

    public function title(): string
    {
        return 'التقرير المالي';
    }

    public function headings(): array
    {
        return ['#', 'النوع', 'الوصف', 'المبلغ (ر.ع)', 'التاريخ'];
    }

    public function collection()
    {
        if ($this->transactions->isEmpty()) {
            return collect([['لا توجد بيانات متاحة في الفترة المحددة', '', '', '', '']]);
        }

        return $this->transactions->map(function ($t, $index) {
            return [
                '#' => $index + 1,
                'النوع' => $t['type'] ?? '',
                'الوصف' => $t['name'] ?? '',
                'المبلغ (ر.ع)' => $t['amount'] ?? 0,
                'التاريخ' => $t['date'] ?? '',
            ];
        });
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // الاتجاه من اليمين لليسار
                $sheet->setRightToLeft(true);

                // تنسيق رأس الجدول
                $sheet->getStyle('A1:E1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 12,
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    ],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FFE8E8E8'],
                    ],
                ]);

                // حدود الجدول
                $lastRow = $sheet->getHighestRow();
                $sheet->getStyle("A1:E{$lastRow}")
                    ->getBorders()->getAllBorders()
                    ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

                // صف الإجماليات
                $summaryRow = $lastRow + 2;
                $sheet->setCellValue("C{$summaryRow}", 'إجمالي الإيرادات');
                $sheet->setCellValue("D{$summaryRow}", $this->data['totalRevenues'] ?? 0);

                $summaryRow2 = $summaryRow + 1;
                $sheet->setCellValue("C{$summaryRow2}", 'إجمالي المصروفات');
                $sheet->setCellValue("D{$summaryRow2}", $this->data['totalExpenses'] ?? 0);

                $summaryRow3 = $summaryRow2 + 1;
                $sheet->setCellValue("C{$summaryRow3}", 'صافي الربح / العجز');
                $sheet->setCellValue("D{$summaryRow3}", $this->data['netBalance'] ?? 0);

                // تنسيق الإجماليات
                $sheet->getStyle("C{$summaryRow}:D{$summaryRow3}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 11],
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
