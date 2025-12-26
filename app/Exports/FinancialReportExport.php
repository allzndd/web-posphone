<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class FinancialReportExport implements WithMultipleSheets
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function sheets(): array
    {
        return [
            new FinancialSummarySheet($this->data),
            new FinancialDetailSheet($this->data),
            new ExpensesDetailSheet($this->data)
        ];
    }
}

// Summary Sheet
class FinancialSummarySheet implements FromCollection, WithHeadings, WithStyles, WithTitle, WithColumnWidths
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        $data = collect();
        
        // Core Metrics
        $data->push(['CORE METRICS', '']);
        $data->push(['Revenue', 'Rp ' . number_format($this->data['revenue'], 0, ',', '.')]);
        $data->push(['HPP (COGS)', 'Rp ' . number_format($this->data['hpp'], 0, ',', '.')]);
        $data->push(['Gross Profit', 'Rp ' . number_format($this->data['grossProfit'], 0, ',', '.')]);
        $data->push(['Gross Margin', number_format($this->data['grossMargin'], 2) . '%']);
        $data->push(['Operating Expenses', 'Rp ' . number_format($this->data['operatingExpenses'], 0, ',', '.')]);
        $data->push(['Net Profit', 'Rp ' . number_format($this->data['netProfit'], 0, ',', '.')]);
        $data->push(['Net Margin', number_format($this->data['netMargin'], 2) . '%']);
        $data->push(['', '']); // Empty row
        
        // Cash Flow
        $data->push(['CASH FLOW', '']);
        $data->push(['Cash In', 'Rp ' . number_format($this->data['cashIn'], 0, ',', '.')]);
        $data->push(['Cash Out', 'Rp ' . number_format($this->data['cashOut'], 0, ',', '.')]);
        $data->push(['Free Cash Flow', 'Rp ' . number_format($this->data['freeCashFlow'], 0, ',', '.')]);
        $data->push(['Receivable', 'Rp ' . number_format($this->data['receivable'], 0, ',', '.')]);
        $data->push(['', '']); // Empty row
        
        // Payment Method Breakdown
        $data->push(['PAYMENT METHOD BREAKDOWN', '']);
        foreach ($this->data['paymentMethods'] as $method => $amount) {
            $data->push([ucfirst($method), 'Rp ' . number_format($amount, 0, ',', '.')]);
        }
        
        return $data;
    }

    public function headings(): array
    {
        return [
            ['Metric', 'Value']
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Header styling
        $sheet->getStyle('A1:B1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4F46E5']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER
            ]
        ]);
        
        // Section headers
        $sectionRows = [2, 11, 17];
        foreach ($sectionRows as $row) {
            $sheet->mergeCells("A{$row}:B{$row}");
            $sheet->getStyle("A{$row}:B{$row}")->applyFromArray([
                'font' => [
                    'bold' => true,
                    'size' => 12,
                    'color' => ['rgb' => 'FFFFFF']
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '6366F1']
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER
                ]
            ]);
        }
        
        // Borders
        $lastRow = $sheet->getHighestRow();
        $sheet->getStyle('A1:B' . $lastRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'D1D5DB']
                ]
            ]
        ]);
        
        return [];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 30,
            'B' => 25
        ];
    }

    public function title(): string
    {
        return 'Ringkasan';
    }
}

// Detail Sheet
class FinancialDetailSheet implements FromCollection, WithHeadings, WithStyles, WithTitle, WithColumnWidths
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        $data = collect();
        
        foreach ($this->data['detailPerItem'] as $index => $item) {
            $data->push([
                $index + 1,
                $item['nama_produk'],
                $item['type_badge'],
                number_format($item['quantity'], 0, ',', '.'),
                'Rp ' . number_format($item['harga_jual'], 0, ',', '.'),
                'Rp ' . number_format($item['harga_beli'], 0, ',', '.'),
                'Rp ' . number_format($item['revenue'], 0, ',', '.'),
                'Rp ' . number_format($item['hpp'], 0, ',', '.'),
                'Rp ' . number_format($item['gross_profit'], 0, ',', '.')
            ]);
        }
        
        return $data;
    }

    public function headings(): array
    {
        return [
            ['No', 'Produk', 'Tipe', 'Qty', 'Harga Jual', 'Harga Beli', 'Revenue', 'HPP', 'Gross Profit']
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Header styling
        $sheet->getStyle('A1:I1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4F46E5']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER
            ]
        ]);
        
        // Borders
        $lastRow = $sheet->getHighestRow();
        $sheet->getStyle('A1:I' . $lastRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'D1D5DB']
                ]
            ]
        ]);
        
        // Alternating row colors
        for ($i = 2; $i <= $lastRow; $i++) {
            if ($i % 2 == 0) {
                $sheet->getStyle("A{$i}:I{$i}")->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'F9FAFB']
                    ]
                ]);
            }
        }
        
        return [];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 8,
            'B' => 30,
            'C' => 15,
            'D' => 10,
            'E' => 18,
            'F' => 18,
            'G' => 20,
            'H' => 20,
            'I' => 20
        ];
    }

    public function title(): string
    {
        return 'Detail Per Item';
    }
}

// Expenses Detail Sheet
class ExpensesDetailSheet implements FromCollection, WithHeadings, WithStyles, WithTitle, WithColumnWidths
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        $data = collect();
        
        foreach ($this->data['recentExpenses'] as $index => $expense) {
            $data->push([
                $index + 1,
                $expense->expense_date->format('d/m/Y'),
                $expense->getExpenseTypeLabel(),
                $expense->description ?? '-',
                $expense->toko ? $expense->toko->nama : '-',
                'Rp ' . number_format($expense->amount, 0, ',', '.')
            ]);
        }
        
        return $data;
    }

    public function headings(): array
    {
        return [
            ['No', 'Tanggal', 'Tipe', 'Deskripsi', 'Toko', 'Jumlah']
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Header styling
        $sheet->getStyle('A1:F1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4F46E5']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER
            ]
        ]);
        
        // Borders
        $lastRow = $sheet->getHighestRow();
        $sheet->getStyle('A1:F' . $lastRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'D1D5DB']
                ]
            ]
        ]);
        
        // Alternating row colors
        for ($i = 2; $i <= $lastRow; $i++) {
            if ($i % 2 == 0) {
                $sheet->getStyle("A{$i}:F{$i}")->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'F9FAFB']
                    ]
                ]);
            }
        }
        
        return [];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 8,
            'B' => 15,
            'C' => 20,
            'D' => 35,
            'E' => 20,
            'F' => 20
        ];
    }

    public function title(): string
    {
        return 'Operating Expenses';
    }
}
