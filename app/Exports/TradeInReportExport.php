<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

// Columns: A–L (12 columns)
// A: No | B: Tanggal | C: Invoice Beli | D: Invoice Jual | E: Toko | F: Pelanggan
// G: HP Masuk (Nama) | H: Merk/Tipe | I: IMEI | J: Spesifikasi | K: Battery | L: HP Keluar
// M: Harga Beli | N: Harga Jual | O: Profit

class TradeInReportExport extends DefaultValueBinder implements FromCollection, WithHeadings, WithStyles, WithTitle, WithColumnWidths, WithCustomValueBinder
{
    protected $tradeIns;
    protected $summary;
    protected $period;

    // Row layout: 1=title, 2-6=summary, 7=spacer, 8=col headers, 9+=data
    const HEADER_ROW = 8;
    const DATA_START = 9;
    const LAST_COL   = 'O';

    public function __construct($tradeIns, $summary, $period)
    {
        $this->tradeIns = $tradeIns;
        $this->summary  = $summary;
        $this->period   = $period;
    }

    public function collection()
    {
        $data = collect();

        // ── Summary block (rows 1–8) ──────────────────────────────────────────
        $empty = array_fill(0, 15, '');

        $data->push(array_merge(['LAPORAN TUKAR TAMBAH'], array_fill(0, 14, '')));
        $data->push(array_merge(['Periode', $this->getPeriodLabel() . '  (' . ($this->summary['start'] ?? '') . ' - ' . ($this->summary['end'] ?? '') . ')'], array_fill(0, 13, '')));
        $data->push(array_merge(['Total Transaksi', number_format($this->summary['totalTradeIns'], 0, ',', '.')], array_fill(0, 13, '')));
        $data->push(array_merge(['Total Harga Beli HP Masuk', format_currency($this->summary['totalTradeInValue'])], array_fill(0, 13, '')));
        $data->push(array_merge(['Total Harga Jual HP Keluar', format_currency($this->summary['totalAdditionalPayment'])], array_fill(0, 13, '')));
        $data->push(array_merge(['Net Profit', format_currency($this->summary['totalProfit'])], array_fill(0, 13, '')));
        $data->push($empty); // spacer row 7

        // Column header row (row 8)
        $data->push([
            'No', 'Tanggal', 'Invoice Beli', 'Invoice Jual', 'Toko', 'Pelanggan',
            'HP Masuk', 'Merk / Tipe', 'IMEI', 'Spesifikasi (RAM/Storage/Warna)',
            'Battery', 'HP Keluar', 'Harga Beli', 'Harga Jual', 'Profit',
        ]);

        // ── Detail rows ───────────────────────────────────────────────────────
        foreach ($this->tradeIns as $index => $tradeIn) {
            $pm          = $tradeIn->produkMasuk;
            $beliAmount  = $tradeIn->transaksiPembelian->total_harga ?? 0;
            $jualAmount  = $tradeIn->transaksiPenjualan->total_harga ?? 0;
            $rowProfit   = $jualAmount - $beliAmount;

            // Specs string
            $specs = collect([
                $pm->ram->kapasitas ?? null,
                ($pm->penyimpanan->kapasitas ?? null) ? $pm->penyimpanan->kapasitas . 'GB' : null,
                $pm->warna->warna ?? null,
            ])->filter()->implode(' / ');

            // Brand + type
            $merkLabel = '';
            if ($pm && $pm->merk) {
                $merkLabel = trim(($pm->merk->merk ?? '') . ' ' . ($pm->merk->nama ?? ''));
            }

            $data->push([
                $index + 1,
                $tradeIn->created_at->format('d/m/Y H:i'),
                $tradeIn->transaksiPembelian->invoice ?? '-',
                $tradeIn->transaksiPenjualan->invoice  ?? '-',
                $tradeIn->toko->nama      ?? '-',
                $tradeIn->pelanggan->nama ?? 'Walk-in',
                $pm ? ($pm->nama ?? '-') : '-',
                $merkLabel ?: '-',
                $pm ? ($pm->imei ?? '-') : '-',
                $specs ?: '-',
                ($pm && $pm->battery_health) ? $pm->battery_health . '%' : '-',
                $tradeIn->produkKeluar->nama ?? '-',
                format_currency($beliAmount),
                format_currency($jualAmount),
                format_currency($rowProfit),
            ]);
        }

        return $data;
    }

    protected function getPeriodLabel(): string
    {
        return [
            'today' => 'Hari Ini',
            'week'  => 'Minggu Ini',
            'month' => 'Bulan Ini',
            'year'  => 'Tahun Ini',
            'all'   => 'Semua Periode',
        ][$this->period] ?? 'Custom';
    }

    /**
     * Force IMEI column (I) to always be stored as text, preventing E+18 scientific notation.
     */
    public function bindValue(Cell $cell, $value)
    {
        if ($cell->getColumn() === 'I' && $cell->getRow() >= self::DATA_START) {
            $cell->setValueExplicit((string) $value, DataType::TYPE_STRING);
            return true;
        }
        return parent::bindValue($cell, $value);
    }

    public function headings(): array
    {
        // headings() injects a header row at the TOP, before collection() rows.
        // We return empty headings and handle the header manually in collection()
        // so that our summary block sits above the header.
        // Trick: return empty array — header is part of collection row 9.
        return [];
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $sheet->getHighestRow();
        $lastCol = self::LAST_COL;
        $hdr     = self::HEADER_ROW;
        $cols    = 'A:' . $lastCol;

        // ── Title row (row 1) ─────────────────────────────────────────────────
        $sheet->mergeCells('A1:' . $lastCol . '1');
        $sheet->getRowDimension(1)->setRowHeight(28);
        $sheet->getStyle('A1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E40AF']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);

        // ── Summary rows (2–7) ────────────────────────────────────────────────
        $sheet->getStyle('A2:A6')->applyFromArray([
            'font' => ['bold' => true],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'EFF6FF']],
        ]);
        $sheet->getStyle('B2:B6')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => '1E40AF']],
        ]);
        // Highlight profit row (row 6)
        $profitVal = $this->summary['totalProfit'] ?? 0;
        $profitColor = $profitVal >= 0 ? '065F46' : '991B1B';
        $sheet->getStyle('B6')->getFont()->getColor()->setRGB($profitColor);

        // ── Header row (row 9) ────────────────────────────────────────────────
        $sheet->getRowDimension($hdr)->setRowHeight(20);
        $sheet->getStyle('A' . $hdr . ':' . $lastCol . $hdr)->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 10],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1D4ED8']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => false],
        ]);

        // ── Data borders ──────────────────────────────────────────────────────
        if ($lastRow >= self::DATA_START) {
            $sheet->getStyle('A' . $hdr . ':' . $lastCol . $lastRow)->applyFromArray([
                'borders' => [
                    'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D1D5DB']],
                ],
            ]);

            // Alternating rows
            for ($i = self::DATA_START; $i <= $lastRow; $i++) {
                if ($i % 2 === 0) {
                    $sheet->getStyle('A' . $i . ':' . $lastCol . $i)->applyFromArray([
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F8FAFC']],
                    ]);
                }
            }

            // Profit column (O) — colour based on positive/negative
            for ($i = self::DATA_START; $i <= $lastRow; $i++) {
                $cell = $sheet->getCell('O' . $i)->getValue();
                // Detect negative by presence of '-' after currency symbol
                $isNegative = str_contains((string) $cell, '-');
                $sheet->getStyle('O' . $i)->getFont()->getColor()->setRGB($isNegative ? '991B1B' : '065F46');
                $sheet->getStyle('O' . $i)->getFont()->setBold(true);
            }

            // IMEI column (I) — monospace-like via font
            $sheet->getStyle('I' . self::DATA_START . ':I' . $lastRow)
                ->getFont()->setName('Courier New')->setSize(9);
        }

        // ── Row 9 is the manual header row we placed in collection() ─────────
        // (already styled above)

        return [];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 6,   // No
            'B' => 18,  // Tanggal
            'C' => 18,  // Invoice Beli
            'D' => 18,  // Invoice Jual
            'E' => 20,  // Toko
            'F' => 22,  // Pelanggan
            'G' => 28,  // HP Masuk
            'H' => 22,  // Merk/Tipe
            'I' => 18,  // IMEI
            'J' => 22,  // Spesifikasi
            'K' => 10,  // Battery
            'L' => 28,  // HP Keluar
            'M' => 18,  // Harga Beli
            'N' => 18,  // Harga Jual
            'O' => 18,  // Profit
        ];
    }

    public function title(): string
    {
        return 'Laporan Tukar Tambah';
    }
}
