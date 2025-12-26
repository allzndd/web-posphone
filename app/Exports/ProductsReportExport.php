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

class ProductsReportExport implements FromCollection, WithHeadings, WithStyles, WithTitle, WithColumnWidths
{
    protected $products;
    protected $summary;

    public function __construct($products, $summary)
    {
        $this->products = $products;
        $this->summary = $summary;
    }

    public function collection()
    {
        $data = collect();
        
        // Add summary section
        $data->push(['RINGKASAN PRODUK', '', '', '']);
        $data->push(['Total Produk', number_format($this->summary['totalProducts'], 0, ',', '.'), '', '']);
        $data->push(['Total Stok', number_format($this->summary['totalStock'], 0, ',', '.'), '', '']);
        $data->push(['Total Nilai Stok', 'Rp ' . number_format($this->summary['totalValue'], 0, ',', '.'), '', '']);
        $data->push(['', '', '', '']); // Empty row
        
        // Add detail data
        foreach ($this->products as $index => $product) {
            $totalStok = $product->stok->sum('stok');
            $nilaiTotal = $totalStok * $product->harga_jual;
            
            $data->push([
                $index + 1,
                $product->nama,
                $product->merk ? $product->merk->nama_merk : '-',
                number_format($totalStok, 0, ',', '.'),
                'Rp ' . number_format($product->harga_jual, 0, ',', '.'),
                'Rp ' . number_format($nilaiTotal, 0, ',', '.')
            ]);
        }
        
        return $data;
    }

    public function headings(): array
    {
        return [
            ['No', 'Nama Produk', 'Merk', 'Total Stok', 'Harga Jual', 'Nilai Total']
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
                'startColor' => ['rgb' => '10B981']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ]
        ]);
        
        $sheet->getStyle('A2:A4')->applyFromArray([
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'F3F4F6']
            ]
        ]);
        
        // Header row styling (row 6)
        $sheet->getStyle('A6:F6')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '10B981']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ]
        ]);
        
        // Border for all data
        $lastRow = $sheet->getHighestRow();
        $sheet->getStyle('A6:F' . $lastRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'D1D5DB']
                ]
            ]
        ]);
        
        // Alternating row colors
        for ($i = 7; $i <= $lastRow; $i++) {
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
            'B' => 35,
            'C' => 20,
            'D' => 15,
            'E' => 20,
            'F' => 25
        ];
    }

    public function title(): string
    {
        return 'Laporan Produk';
    }
}
