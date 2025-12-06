<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardSuperadminController extends Controller
{
    public function index()
    {
        // Total owner yang terdaftar dengan role OWNER
        $totalOwners = User::where('roles', 'OWNER')->count();
        
        // Owner dengan status aktif (bisa dikustomisasi sesuai kebutuhan)
        // Misalnya owner yang login dalam 30 hari terakhir atau yang memiliki subscription aktif
        $activeOwners = User::where('roles', 'OWNER')
            ->where('updated_at', '>=', now()->subDays(30))
            ->count();
        
        // Total revenue dari pembayaran (jika tabel pembayaran sudah ada)
        // Untuk sementara menggunakan dummy karena tabel mungkin belum ada data
        $totalRevenue = 45500000; // Nanti bisa diganti dengan: DB::table('pembayarans')->sum('total')
        
        // Pembayaran pending (status pending)
        // $pendingPayments = DB::table('pembayarans')->where('status', 'pending')->count();
        $pendingPayments = 3; // Dummy untuk sementara
        
        // Recent payments - ambil data dari tabel pembayaran jika sudah ada
        // Untuk sementara masih dummy, nanti bisa diganti dengan query real
        $recentPayments = [
            ['id' => 1, 'tanggal' => '2025-12-01', 'owner' => 'PT Maju Jaya', 'email' => 'majujaya@gmail.com', 'paket' => 'Paket Premium', 'periode' => '1 Bulan', 'total' => 500000, 'status' => 'Lunas'],
            ['id' => 2, 'tanggal' => '2025-12-02', 'owner' => 'CV Berkah Store', 'email' => 'berkahstore@gmail.com', 'paket' => 'Paket Basic', 'periode' => '3 Bulan', 'total' => 1200000, 'status' => 'Pending'],
            ['id' => 3, 'tanggal' => '2025-12-03', 'owner' => 'Toko Elektronik Jaya', 'email' => 'elektronikjaya@gmail.com', 'paket' => 'Paket Enterprise', 'periode' => '1 Tahun', 'total' => 5000000, 'status' => 'Lunas'],
        ];
        
        // Statistik owner berdasarkan bulan registrasi (untuk chart)
        $ownersByMonth = User::where('roles', 'OWNER')
            ->select(DB::raw('MONTH(created_at) as month'), DB::raw('COUNT(*) as total'))
            ->whereYear('created_at', date('Y'))
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->pluck('total', 'month')
            ->toArray();
        
        // Format data untuk chart (12 bulan)
        $monthlyOwners = [];
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        for ($i = 1; $i <= 12; $i++) {
            $monthlyOwners[] = [
                'month' => $months[$i - 1],
                'total' => $ownersByMonth[$i] ?? 0
            ];
        }
        
        // Popular packages (dummy untuk sementara)
        $popularPackages = [
            ['nama' => 'Paket Premium', 'total_owner' => 5, 'revenue' => 15000000],
            ['nama' => 'Paket Basic', 'total_owner' => 4, 'revenue' => 8000000],
            ['nama' => 'Paket Enterprise', 'total_owner' => 3, 'revenue' => 22500000],
        ];
        
        // Daftar owner terbaru (5 terbaru)
        $recentOwners = User::where('roles', 'OWNER')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        return view('dashboard-superadmin.index', compact(
            'totalOwners',
            'activeOwners',
            'totalRevenue',
            'pendingPayments',
            'recentPayments',
            'monthlyOwners',
            'popularPackages',
            'recentOwners'
        ));
    }
}
