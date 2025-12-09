<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Owner;
use App\Models\Pembayaran;
use Illuminate\Support\Facades\DB;

class DashboardSuperadminController extends Controller
{
    public function index()
    {
        // Total owner yang terdaftar dengan role_id = 2 (Owner)
        $totalOwners = User::where('role_id', 2)->count();
        
        // Owner dengan status aktif (login dalam 30 hari terakhir)
        $activeOwners = User::where('role_id', 2)
            ->where('updated_at', '>=', now()->subDays(30))
            ->count();
        
        // Total revenue - Temporary disabled (tabel pembayaran belum ada data)
        $totalRevenue = 0; // Pembayaran::where('status', 'Paid')->sum('nominal');
        
        // Pembayaran pending - Temporary disabled
        $pendingPayments = 0; // Pembayaran::where('status', 'Pending')->count();
        
        // Recent payments - Temporary empty
        $recentPayments = [];
        
        // Statistik owner berdasarkan bulan registrasi (untuk chart)
        $ownersByMonth = User::where('role_id', 2)
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
        
        // Popular packages - Temporary empty
        $popularPackages = [];
        
        // Daftar owner terbaru (5 terbaru)
        $recentOwners = User::where('role_id', 2)
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
