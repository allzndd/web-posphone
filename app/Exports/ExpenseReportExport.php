<?php

namespace App\Exports;

use App\Models\PosTransaksi;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ExpenseReportExport implements FromCollection, WithHeadings, WithStyles, WithTitle, WithColumnWidths
{
    protected $request;
    protected $ownerId;
    protected $expenses;

    public function __construct($request, $ownerId)
    {
        $this->request = $request;
        $this->ownerId = $ownerId;
        $this->loadExpenses();
    }

    private function loadExpenses()
    {
        // Get query parameters
        $period = $this->request->get('period', 'month');
        $startDate = $this->request->get('start_date', '');
        $endDate = $this->request->get('end_date', '');
        $storeId = $this->request->get('store_id', '');
        $categoryId = $this->request->get('category_id', '');

        // Determine date range
        if ($period === 'custom' && $startDate && $endDate) {
            $start = Carbon::parse($startDate)->startOfDay();
            $end = Carbon::parse($endDate)->endOfDay();
        } elseif ($period === 'today') {
            $start = Carbon::now()->startOfDay();
            $end = Carbon::now()->endOfDay();
        } elseif ($period === 'week') {
            $start = Carbon::now()->startOfWeek();
            $end = Carbon::now()->endOfWeek();
        } elseif ($period === 'year') {
            $start = Carbon::now()->startOfYear();
            $end = Carbon::now()->endOfYear();
        } elseif ($period === 'all') {
            $start = Carbon::createFromYear(2000);
            $end = Carbon::now()->endOfDay();
        } else { // default month
            $start = Carbon::now()->startOfMonth();
            $end = Carbon::now()->endOfMonth();
        }

        // Build query
        $query = PosTransaksi::where('owner_id', $this->ownerId)
            ->whereBetween('created_at', [$start, $end])
            ->where('is_transaksi_masuk', 0)
            ->whereNotNull('pos_kategori_expense_id')
            ->with(['kategoriExpense', 'toko']);

        if ($storeId) {
            $query->where('pos_toko_id', $storeId);
        }

        if ($categoryId) {
            $query->where('pos_kategori_expense_id', $categoryId);
        }

        $this->expenses = $query->orderBy('created_at', 'desc')->get();
    }

    public function collection()
    {
        $data = collect();

        // Add summary section
        $totalExpenses = $this->expenses->sum('total_harga');
        $totalCount = $this->expenses->count();
        $averageExpense = $totalCount > 0 ? $totalExpenses / $totalCount : 0;

        $data->push(['LAPORAN OPERASIONAL EXPENSE']);
        $data->push(['Tanggal Export', date('d-m-Y H:i:s')]);
        $data->push(['']);
        $data->push(['RINGKASAN EXPENSE']);
        $data->push(['Total Expense', 'Rp ' . number_format($totalExpenses, 0, ',', '.')]);
        $data->push(['Jumlah Transaksi', number_format($totalCount, 0, ',', '.')]);
        $data->push(['Rata-rata Expense', 'Rp ' . number_format($averageExpense, 0, ',', '.')]);
        $data->push(['']);

        // Breakdown by category
        $data->push(['BREAKDOWN BERDASARKAN KATEGORI']);
        foreach ($this->expenses->groupBy('kategoriExpense.nama') as $categoryName => $categoryExpenses) {
            if ($categoryName && $categoryName !== 'null') {
                $data->push([$categoryName, 'Rp ' . number_format($categoryExpenses->sum('total_harga'), 0, ',', '.')]);
            }
        }
        $data->push(['']);

        // Add detail data
        $data->push(['NO', 'TANGGAL', 'KATEGORI', 'TOKO', 'INVOICE', 'KETERANGAN', 'NOMINAL']);

        foreach ($this->expenses as $index => $expense) {
            $data->push([
                $index + 1,
                $expense->created_at->format('d/m/Y H:i'),
                $expense->kategoriExpense ? $expense->kategoriExpense->nama : '-',
                $expense->toko ? $expense->toko->nama : '-',
                $expense->invoice ?? '-',
                $expense->keterangan ?? '-',
                'Rp ' . number_format($expense->total_harga, 0, ',', '.'),
            ]);
        }

        return $data;
    }

    public function headings(): array
    {
        return [];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 14],
                'alignment' => ['horizontal' => 'left'],
            ],
            14 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
                'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,
            'B' => 15,
            'C' => 20,
            'D' => 15,
            'E' => 12,
            'F' => 25,
            'G' => 18,
        ];
    }

    public function title(): string
    {
        return 'Expense Report';
    }
}
