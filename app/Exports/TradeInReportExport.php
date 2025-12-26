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

class TradeInReportExport implements FromCollection, WithHeadings, WithStyles, WithTitle, WithColumnWidths
{
    protected $tradeIns;
    protected $summary;
    protected $period;

    public function __construct($tradeIns, $summary, $period)
    {
        $this->tradeIns = $tradeIns;
        $this->summary = $summary;
        $this->period = $period;
    }

    public function collection()
    {
        $data = collect();
        
        // Add summary section
        $data->push(['RINGKASAN TUKAR TAMBAH', '', '', '', '']);
        $data->push(['Periode', $this->getPeriodLabel(), '', '', '']);
        $data->push(['Total Tukar Tambah', number_format($this->summary['totalTradeIns'], 0, ',', '.'), '', '', '']);
        $data->push(['Total Nilai Trade-In', 'Rp ' . number_format($this->summary['totalTradeInValue'], 0, ',', '.'), '', '', '']);
        $data->push(['Total Pembayaran Tambahan', 'Rp ' . number_format($this->summary['totalAdditionalPayment'], 0, ',', '.'), '', '', '']);
        $data->push(['Total Nilai Keseluruhan', 'Rp ' . number_format($this->summary['totalValue'], 0, ',', '.'), '', '', '']);
        $data->push(['', '', '', '', '']); // Empty row
        
        // Add detail data
        foreach ($this->tradeIns as $index => $tradeIn) {
            $tradeInValue = $tradeIn->transaksiPembelian ? $tradeIn->transaksiPembelian->total_harga : 0;
            $additionalPayment = $tradeIn->transaksiPenjualan ? $tradeIn->transaksiPenjualan->total_harga : 0;
            
            $data->push([
                $index + 1,
                $tradeIn->created_at->format('d/m/Y H:i'),
                $tradeIn->pelanggan ? $tradeIn->pelanggan->nama : '-',
                $tradeIn->produkMasuk ? $tradeIn->produkMasuk->nama : '-',
                $tradeIn->produkKeluar ? $tradeIn->produkKeluar->nama : '-',
                $tradeIn->toko ? $tradeIn->toko->nama : '-',
                'Rp ' . number_format($tradeInValue, 0, ',', '.'),
                'Rp ' . number_format($additionalPayment, 0, ',', '.')
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
            ['No', 'Tanggal', 'Pelanggan', 'Produk Masuk', 'Produk Keluar', 'Toko', 'Nilai Trade-In', 'Pembayaran Tambahan']
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
                'startColor' => ['rgb' => '3B82F6']
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
        $sheet->getStyle('A8:H8')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '3B82F6']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ]
        ]);
        
        // Border for all data
        $lastRow = $sheet->getHighestRow();
        $sheet->getStyle('A8:H' . $lastRow)->applyFromArray([
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
                $sheet->getStyle("A{$i}:H{$i}")->applyFromArray([
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
            'D' => 25,
            'E' => 25,
            'F' => 20,
            'G' => 20,
            'H' => 22
        ];
    }

    public function title(): string
    {
        return 'Laporan Tukar Tambah';
    }
}
