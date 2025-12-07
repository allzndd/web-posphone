<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Pembayaran;
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
        
        // Total revenue dari pembayaran yang sudah paid
        $totalRevenue = Pembayaran::where('status', 'Paid')->sum('total');
        
        // Pembayaran pending (status pending)
        $pendingPayments = Pembayaran::where('status', 'Pending')->count();
        
        // Recent payments - ambil 5 pembayaran terbaru dari database
        $recentPayments = Pembayaran::orderBy('tanggal', 'desc')
            ->limit(5)
            ->get()
            ->map(function($payment) {
                return [
                    'id' => $payment->id,
                    'tanggal' => $payment->tanggal->format('Y-m-d'),
                    'owner' => $payment->owner_name,
                    'email' => $payment->email,
                    'paket' => $payment->paket,
                    'periode' => $payment->periode,
                    'total' => $payment->total,
                    'status' => $payment->status,
                ];
            })
            ->toArray();
        
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
        
        // Popular packages - ambil dari data pembayaran yang paid
        $popularPackages = Pembayaran::where('status', 'Paid')
            ->select('paket', DB::raw('COUNT(DISTINCT owner_id) as total_owner'), DB::raw('SUM(total) as revenue'))
            ->groupBy('paket')
            ->orderBy('revenue', 'desc')
            ->limit(5)
            ->get()
            ->map(function($package) {
                return [
                    'nama' => $package->paket,
                    'total_owner' => $package->total_owner,
                    'revenue' => $package->revenue,
                ];
            })
            ->toArray();
        
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
