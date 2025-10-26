<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithCharts;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Chart\Title;
use PhpOffice\PhpSpreadsheet\Chart\Legend;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use Maatwebsite\Excel\Events\AfterSheet;

class FinancialReportExport implements FromCollection, WithHeadings, WithCharts, WithEvents, WithTitle
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
            return collect([['لا توجد بيانات متاحة في الفترة المحددة']]);
        }

        return $this->transactions->map(function ($t, $index) {
            return [
                '#' => $index + 1,
                'النوع' => $t['type'] ?? '',
                'الوصف' => $t['name'] ?? '',
                'المبلغ (ر.ع)' => number_format($t['amount'] ?? 0, 3),
                'التاريخ' => $t['date'] ?? '',
            ];
        });
    }

    public function charts()
    {
        // 🔹 الرسم البياني للأعمدة (إيرادات ومصروفات شهرية)
        $labelRevenues = [new DataSeriesValues('String', 'التقرير المالي!$B$1', null, 1)];
        $labelExpenses = [new DataSeriesValues('String', 'التقرير المالي!$C$1', null, 1)];

        $xAxisLabels = [new DataSeriesValues('String', 'التقرير المالي!$E$2:$E$13', null, 12)];
        $valuesRevenues = [new DataSeriesValues('Number', 'التقرير المالي!$F$2:$F$13', null, 12)];
        $valuesExpenses = [new DataSeriesValues('Number', 'التقرير المالي!$G$2:$G$13', null, 12)];

        $series = [
            new DataSeries(
                DataSeries::TYPE_BARCHART,
                DataSeries::GROUPING_CLUSTERED,
                range(0, count($valuesRevenues) - 1),
                [new DataSeriesValues('String', 'التقرير المالي!$F$1')],
                $xAxisLabels,
                [$valuesRevenues[0]]
            ),
            new DataSeries(
                DataSeries::TYPE_BARCHART,
                DataSeries::GROUPING_CLUSTERED,
                range(0, count($valuesExpenses) - 1),
                [new DataSeriesValues('String', 'التقرير المالي!$G$1')],
                $xAxisLabels,
                [$valuesExpenses[0]]
            )
        ];

        $plotArea = new PlotArea(null, $series);
        $chart = new Chart(
            'chart1',
            new Title('الإيرادات والمصروفات الشهرية'),
            new Legend(Legend::POSITION_RIGHT, null, false),
            $plotArea
        );
        $chart->setTopLeftPosition('I3');
        $chart->setBottomRightPosition('Q20');

        return [$chart];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // ✅ الاتجاه من اليمين لليسار
                $sheet->setRightToLeft(true);

                // ✅ عرض الأعمدة
                foreach (range('A', 'E') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }

                // ✅ عنوان التقرير
                $sheet->mergeCells('A1:E1');
                $sheet->setCellValue('A1', '📊 التقرير المالي العام');
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
                $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

                // ✅ حدود الجدول
                $lastRow = $sheet->getHighestRow();
                $sheet->getStyle("A2:E{$lastRow}")
                    ->getBorders()->getAllBorders()
                    ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            },
        ];
    }
}
