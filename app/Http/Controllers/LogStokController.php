<?php

namespace App\Http\Controllers;

use App\Models\LogStok;
use App\Models\PosTransaksiItem;
use App\Services\PermissionService;
use Illuminate\Http\Request;

class LogStokController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Check read permission
        $hasAccessRead = PermissionService::check('log-stok.read');

        $user = auth()->user();
        $ownerId = $user->owner ? $user->owner->id : null;
        $perPage = $request->get('per_page', 10);
        
        $query = LogStok::with(['produk', 'toko', 'pengguna'])
            ->where('owner_id', $ownerId)
            ->orderBy('created_at', 'desc');

        // Filter by type
        if ($request->filled('tipe')) {
            $query->where('tipe', $request->tipe);
        }

        // Filter by product
        if ($request->filled('pos_produk_id')) {
            $query->where('pos_produk_id', $request->pos_produk_id);
        }

        // Filter by store
        if ($request->filled('pos_toko_id')) {
            $query->where('pos_toko_id', $request->pos_toko_id);
        }

        // Search by reference
        if ($request->filled('referensi')) {
            $query->where('referensi', 'like', '%' . $request->referensi . '%');
        }

        $logs = $query->paginate($perPage);

        // Ambil semua invoice dari semua log yang punya referensi
        $allInvoices = $logs->filter(fn($l) => $l->referensi)->pluck('referensi')->unique()->values()->toArray();

        $transaksiItemsByInvoice = collect();
        if (!empty($allInvoices)) {
            $rows = PosTransaksiItem::join('pos_transaksi', 'pos_transaksi.id', '=', 'pos_transaksi_item.pos_transaksi_id')
                ->whereIn('pos_transaksi.invoice', $allInvoices)
                ->select(
                    'pos_transaksi.invoice',
                    'pos_transaksi_item.pos_produk_id',
                    'pos_transaksi_item.product_name',
                    'pos_transaksi_item.imei'
                )
                ->get();
            $transaksiItemsByInvoice = $rows->groupBy('invoice');
        }

        $logs->each(function ($log) use ($transaksiItemsByInvoice) {
            $itemsForInvoice = $log->referensi ? $transaksiItemsByInvoice->get($log->referensi) : null;

            // Fallback nama_produk untuk log lama
            if (!$log->nama_produk && $itemsForInvoice) {
                $matched = $itemsForInvoice->firstWhere('pos_produk_id', $log->pos_produk_id)
                    ?? $itemsForInvoice->first();
                if ($matched) {
                    $log->nama_produk = $matched->product_name;
                }
            }

            // Kumpulkan SEMUA IMEI dari invoice ini (bisa lebih dari 1)
            if ($itemsForInvoice) {
                $allImeis = $itemsForInvoice
                    ->pluck('imei')
                    ->filter(fn($v) => !empty($v))
                    ->unique()
                    ->values()
                    ->toArray();
                $log->imei_list = $allImeis;
            } else {
                // Untuk log baru yang sudah punya snapshot imei
                $log->imei_list = $log->imei ? [$log->imei] : [];
            }
        });

        return view('pages.log-stok.index', compact('logs', 'hasAccessRead'));
    }
}
