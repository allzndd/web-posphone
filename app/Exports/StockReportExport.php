<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class StockReportExport implements FromCollection, WithHeadings, WithStyles, WithTitle, WithColumnWidths
{
    protected $stocks;
    protected $summary;

    public function __construct($stocks, $summary)
    {
        $this->stocks = $stocks;
        $this->summary = $summary;
    }

    public function collection()
    {
        $data = collect();
        
        // Add summary section
        $data->push(['RINGKASAN STOK', '', '', '']);
        $data->push(['Total Item', number_format($this->summary['totalItems'], 0, ',', '.'), '', '']);
        $data->push(['Total Stok', number_format($this->summary['totalStock'], 0, ',', '.'), '', '']);
        $data->push(['Stok Menipis', number_format($this->summary['lowStockItems'], 0, ',', '.'), '', '']);
        $data->push(['Stok Habis', number_format($this->summary['outOfStock'], 0, ',', '.'), '', '']);
        $data->push(['', '', '', '']); // Empty row
        
        // Add detail data
        foreach ($this->stocks as $index => $stock) {
            $status = 'Aman';
            if ($stock->stok == 0) {
                $status = 'Habis';
            } elseif ($stock->stok <= 5) {
                $status = 'Menipis';
            }
            
            $data->push([
                $index + 1,
                $stock->produk ? $stock->produk->nama : '-',
                $stock->toko ? $stock->toko->nama : '-',
                number_format($stock->stok, 0, ',', '.'),
                $status
            ]);
        }
        
        return $data;
    }

    public function headings(): array
    {
        return [
            ['No', 'Produk', 'Toko', 'Jumlah Stok', 'Status']
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Summary section styling
        $sheet->mergeCells('A1:D1');
        $sheet->getStyle('A1:D1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 14,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'F97316']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ]
        ]);
        
        $sheet->getStyle('A2:A5')->applyFromArray([
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'F3F4F6']
            ]
        ]);
        
        // Header row styling (row 7)
        $sheet->getStyle('A7:E7')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'F97316']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ]
        ]);
        
        // Border for all data
        $lastRow = $sheet->getHighestRow();
        $sheet->getStyle('A7:E' . $lastRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'D1D5DB']
                ]
            ]
        ]);
        
        // Alternating row colors and conditional formatting for status
        for ($i = 8; $i <= $lastRow; $i++) {
            if ($i % 2 == 0) {
                $sheet->getStyle("A{$i}:E{$i}")->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'F9FAFB']
                    ]
                ]);
            }
            
            // Color code status column
            $status = $sheet->getCell("E{$i}")->getValue();
            if ($status == 'Habis') {
                $sheet->getStyle("E{$i}")->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => 'DC2626']
                    ]
                ]);
            } elseif ($status == 'Menipis') {
                $sheet->getStyle("E{$i}")->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => 'F97316']
                    ]
                ]);
            } else {
                $sheet->getStyle("E{$i}")->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => '10B981']
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
            'B' => 35,
            'C' => 20,
            'D' => 15,
            'E' => 15
        ];
    }

    public function title(): string
    {
        return 'Laporan Stok';
    }
}
