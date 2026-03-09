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

class IncomingTransactionExport implements FromCollection, WithHeadings, WithStyles, WithTitle, WithColumnWidths
{
    protected $transactions;
    protected $startDate;
    protected $endDate;

    public function __construct($transactions, $startDate, $endDate)
    {
        $this->transactions = $transactions;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function collection()
    {
        $data = collect();

        // Summary section
        $completedTransactions = $this->transactions->filter(fn($t) => strtolower($t->status) === 'completed');
        $totalAmount = $completedTransactions->sum('total_harga');
        $totalItems = $completedTransactions->sum(fn($t) => $t->items->sum('quantity'));

        $data->push(['LAPORAN TRANSAKSI MASUK (PENJUALAN)', '', '', '', '', '', '', '', '']);
        $data->push(['Periode', $this->startDate . ' s/d ' . $this->endDate, '', '', '', '', '', '', '']);
        $data->push(['Diekspor', now()->format('d/m/Y H:i:s'), '', '', '', '', '', '', '']);
        $data->push(['Total Transaksi', $this->transactions->count(), '', '', '', '', '', '', '']);
        $data->push(['Transaksi Selesai', $completedTransactions->count(), '', '', '', '', '', '', '']);
        $data->push(['Total Penjualan', 'Rp ' . number_format($totalAmount, 0, ',', '.'), '', '', '', '', '', '', '']);
        $data->push(['Total Item Terjual', number_format($totalItems, 0, ',', '.'), '', '', '', '', '', '', '']);
        $data->push(['', '', '', '', '', '', '', '', '']);

        // Detail rows
        foreach ($this->transactions as $index => $transaction) {
            $itemNames = $transaction->items->map(function ($item) {
                if ($item->produk) {
                    return $item->produk->nama ?? ($item->produk->merk->nama ?? 'Produk');
                } elseif ($item->service) {
                    return $item->service->nama ?? 'Service';
                }
                return '-';
            })->implode(', ');

            $itemCount = $transaction->items->sum('quantity');

            $data->push([
                $index + 1,
                $transaction->created_at->format('d/m/Y H:i'),
                $transaction->invoice ?? '-',
                $transaction->toko->nama ?? '-',
                $transaction->pelanggan->nama ?? '-',
                $itemNames,
                number_format($itemCount, 0, ',', '.'),
                'Rp ' . number_format($transaction->total_harga, 0, ',', '.'),
                ucfirst($transaction->metode_pembayaran ?? '-'),
                ucfirst($transaction->status ?? '-'),
            ]);
        }

        return $data;
    }

    public function headings(): array
    {
        return [
            ['No', 'Tanggal', 'Invoice', 'Toko', 'Pelanggan', 'Produk/Service', 'Qty', 'Total Harga', 'Pembayaran', 'Status']
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Title styling
        $sheet->mergeCells('A1:J1');
        $sheet->getStyle('A1:J1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '16A34A']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);

        // Summary section styling
        $sheet->getStyle('A2:A7')->applyFromArray([
            'font' => ['bold' => true],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F0FDF4']],
        ]);
        $sheet->getStyle('B2:B7')->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F0FDF4']],
        ]);

        // Header row styling (row 9)
        $sheet->getStyle('A9:J9')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '16A34A']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);

        // Border for all data
        $lastRow = $sheet->getHighestRow();
        $sheet->getStyle('A9:J' . $lastRow)->applyFromArray([
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D1D5DB']],
            ],
        ]);

        // Alternating row colors
        for ($i = 10; $i <= $lastRow; $i++) {
            if ($i % 2 == 0) {
                $sheet->getStyle("A{$i}:J{$i}")->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F9FAFB']],
                ]);
            }
        }

        return [];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 6,
            'B' => 18,
            'C' => 22,
            'D' => 18,
            'E' => 22,
            'F' => 35,
            'G' => 8,
            'H' => 22,
            'I' => 16,
            'J' => 14,
        ];
    }

    public function title(): string
    {
        return 'Transaksi Masuk';
    }
}
