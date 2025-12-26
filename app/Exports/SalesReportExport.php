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

class SalesReportExport implements FromCollection, WithHeadings, WithStyles, WithTitle, WithColumnWidths
{
    protected $transactions;
    protected $summary;
    protected $period;

    public function __construct($transactions, $summary, $period)
    {
        $this->transactions = $transactions;
        $this->summary = $summary;
        $this->period = $period;
    }

    public function collection()
    {
        $data = collect();
        
        // Add summary section
        $data->push(['RINGKASAN PENJUALAN', '', '', '', '']);
        $data->push(['Periode', $this->getPeriodLabel(), '', '', '']);
        $data->push(['Total Transaksi', number_format($this->summary['totalTransactions'], 0, ',', '.'), '', '', '']);
        $data->push(['Total Penjualan', 'Rp ' . number_format($this->summary['totalSales'], 0, ',', '.'), '', '', '']);
        $data->push(['Total Item', number_format($this->summary['totalItems'], 0, ',', '.'), '', '', '']);
        $data->push(['Rata-rata Transaksi', 'Rp ' . number_format($this->summary['averageTransaction'], 0, ',', '.'), '', '', '']);
        $data->push(['', '', '', '', '']); // Empty row
        
        // Add detail data
        foreach ($this->transactions as $index => $transaction) {
            $itemCount = $transaction->items->sum('quantity');
            
            $data->push([
                $index + 1,
                $transaction->created_at->format('d/m/Y H:i'),
                $transaction->pelanggan ? $transaction->pelanggan->nama : '-',
                $transaction->toko ? $transaction->toko->nama : '-',
                number_format($itemCount, 0, ',', '.'),
                'Rp ' . number_format($transaction->total_harga, 0, ',', '.')
            ]);
        }
        
        return $data;
    }

    protected function getPeriodLabel()
    {
        $labels = [
            'today' => 'Hari Ini',
            'week' => 'Minggu Ini',
            'month' => 'Bulan Ini',
            'year' => 'Tahun Ini',
            'all' => 'Semua Periode'
        ];
        
        return $labels[$this->period] ?? 'Custom';
    }

    public function headings(): array
    {
        return [
            ['No', 'Tanggal', 'Pelanggan', 'Toko', 'Jumlah Item', 'Total Harga']
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Summary section styling
        $sheet->mergeCells('A1:E1');
        $sheet->getStyle('A1:E1')->applyFromArray([
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
        
        $sheet->getStyle('A2:A6')->applyFromArray([
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'F3F4F6']
            ]
        ]);
        
        // Header row styling (row 8)
        $sheet->getStyle('A8:F8')->applyFromArray([
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
        $sheet->getStyle('A8:F' . $lastRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'D1D5DB']
                ]
            ]
        ]);
        
        // Alternating row colors
        for ($i = 9; $i <= $lastRow; $i++) {
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
            'B' => 20,
            'C' => 25,
            'D' => 20,
            'E' => 15,
            'F' => 25
        ];
    }

    public function title(): string
    {
        return 'Laporan Penjualan';
    }
}
