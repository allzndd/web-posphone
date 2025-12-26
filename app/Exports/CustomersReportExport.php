<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class CustomersReportExport implements FromCollection, WithHeadings, WithStyles, WithTitle, WithColumnWidths
{
    protected $customers;
    protected $sortBy;
    protected $summary;

    public function __construct($customers, $sortBy, $summary)
    {
        $this->customers = $customers;
        $this->sortBy = $sortBy;
        $this->summary = $summary;
    }

    public function collection()
    {
        $data = collect();
        
        // Add summary section
        $data->push(['RINGKASAN PELANGGAN', '', '', '']);
        $data->push(['Total Pelanggan', number_format($this->summary['totalCustomers'], 0, ',', '.'), '', '']);
        $data->push(['Total Pembelian', number_format($this->summary['totalPurchases'], 0, ',', '.'), '', '']);
        $data->push(['Total Nilai', 'Rp ' . number_format($this->summary['totalValue'], 0, ',', '.'), '', '']);
        $data->push(['Rata-rata Nilai', 'Rp ' . number_format($this->summary['averageValue'], 0, ',', '.'), '', '']);
        $data->push(['', '', '', '']); // Empty row
        
        // Add detail data
        foreach ($this->customers as $index => $customer) {
            $purchaseCount = $customer->transaksi->count();
            $purchaseValue = $customer->transaksi->sum('total_harga');
            
            $data->push([
                $index + 1,
                $customer->nama,
                $customer->no_telepon ?? '-',
                number_format($purchaseCount, 0, ',', '.'),
                'Rp ' . number_format($purchaseValue, 0, ',', '.')
            ]);
        }
        
        return $data;
    }

    public function headings(): array
    {
        return [
            ['No', 'Nama Pelanggan', 'No. Telepon', 'Jumlah Pembelian', 'Total Nilai']
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
                'startColor' => ['rgb' => '4F46E5']
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
                'startColor' => ['rgb' => '4F46E5']
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
        
        // Alternating row colors
        for ($i = 8; $i <= $lastRow; $i++) {
            if ($i % 2 == 0) {
                $sheet->getStyle("A{$i}:E{$i}")->applyFromArray([
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
            'C' => 20,
            'D' => 20,
            'E' => 25
        ];
    }

    public function title(): string
    {
        return 'Laporan Pelanggan';
    }
}
