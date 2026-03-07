<?php

namespace App\Http\Controllers;

use App\Models\TipeLayanan;
use App\Models\Langganan;
use App\Models\Pembayaran;
use App\Models\Owner;
use App\Services\MidtransService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SubscriptionController extends Controller
{
    /**
     * Show available packages for upgrade/renewal
     */
    public function packages()
    {
        $user = Auth::user();
        $owner = $user->owner;

        if (!$owner) {
            return redirect()->route('dashboard')->with('error', 'Owner account not found.');
        }

        // Get current subscription
        $currentSubscription = Langganan::where('owner_id', $owner->id)
            ->orderBy('created_at', 'desc')
            ->first();

        // Get available paid packages (exclude trial and free)
        $packages = TipeLayanan::where('harga', '>', 0)
            ->orderBy('harga', 'asc')
            ->get();

        // Get pending payments (if any)
        $pendingPayment = Pembayaran::where('owner_id', $owner->id)
            ->where('status', 'Pending')
            ->whereNotNull('snap_token')
            ->where('expired_at', '>', Carbon::now())
            ->orderBy('created_at', 'desc')
            ->first();

        return view('subscription.packages', compact(
            'currentSubscription',
            'packages',
            'pendingPayment',
            'owner'
        ));
    }

    /**
     * Initiate payment for a package
     */
    public function checkout(Request $request)
    {
        $request->validate([
            'package_id' => 'required|exists:tipe_layanan,id',
        ]);

        $user = Auth::user();
        $owner = $user->owner;

        if (!$owner) {
            return response()->json(['error' => 'Owner account not found.'], 404);
        }

        $package = TipeLayanan::findOrFail($request->package_id);

        // Validate it's a paid package
        if ($package->harga <= 0) {
            return response()->json(['error' => 'Invalid package selection.'], 422);
        }

        DB::beginTransaction();
        try {
            // Reuse existing valid pending payment for the same owner + package
            $existingPembayaran = Pembayaran::where('owner_id', $owner->id)
                ->where('target_tipe_layanan_id', $package->id)
                ->where('status', 'Pending')
                ->where(function ($q) {
                    $q->whereNull('expired_at')
                      ->orWhere('expired_at', '>', Carbon::now());
                })
                ->whereNotNull('snap_token')
                ->orderBy('created_at', 'desc')
                ->first();

            if ($existingPembayaran) {
                DB::rollBack();
                return response()->json([
                    'snap_token'   => $existingPembayaran->snap_token,
                    'order_id'     => $existingPembayaran->midtrans_order_id,
                    'package_name' => $package->nama,
                    'amount'       => $package->harga,
                ]);
            }

            // Create or get current subscription
            $langganan = Langganan::where('owner_id', $owner->id)
                ->orderBy('created_at', 'desc')
                ->first();

            if (!$langganan) {
                $langganan = Langganan::create([
                    'owner_id' => $owner->id,
                    'tipe_layanan_id' => $package->id,
                    'is_active' => 0,
                    'is_trial' => 0,
                    'started_date' => Carbon::now(),
                    'end_date' => Carbon::now(),
                ]);
            }
            // NOTE: Do NOT update langganan.tipe_layanan_id here.
            // It will be updated only after payment is confirmed via webhook.

            // Generate order ID
            $orderId = MidtransService::generateOrderId($owner->id, $package->id);

            // Create pembayaran record with target package
            $pembayaran = Pembayaran::create([
                'owner_id' => $owner->id,
                'langganan_id' => $langganan->id,
                'target_tipe_layanan_id' => $package->id,
                'nominal' => $package->harga,
                'metode_pembayaran' => 'midtrans',
                'status' => 'Pending',
                'midtrans_order_id' => $orderId,
                'created_at' => Carbon::now(),
            ]);

            // Create Snap token
            $midtransService = new MidtransService();
            $snapToken = $midtransService->createSnapToken($pembayaran, $package, $owner);

            DB::commit();

            return response()->json([
                'snap_token' => $snapToken,
                'order_id' => $orderId,
                'package_name' => $package->nama,
                'amount' => $package->harga,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Subscription checkout failed: ' . $e->getMessage(), [
                'owner_id' => $owner->id,
                'package_id' => $package->id,
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'error' => 'Failed to process payment. Please try again.',
            ], 500);
        }
    }

    /**
     * Continue payment with existing snap token  
     */
    public function continuePayment(Request $request)
    {
        $request->validate([
            'payment_id' => 'required|exists:pembayaran,id',
        ]);

        $user = Auth::user();
        $owner = $user->owner;
        $pembayaran = Pembayaran::where('id', $request->payment_id)
            ->where('owner_id', $owner->id)
            ->where('status', 'Pending')
            ->first();

        if (!$pembayaran || !$pembayaran->snap_token) {
            return response()->json(['error' => 'Payment not found or already processed.'], 404);
        }

        if ($pembayaran->expired_at && Carbon::parse($pembayaran->expired_at)->lt(Carbon::now())) {
            return response()->json(['error' => 'Payment has expired. Please create a new order.'], 422);
        }

        return response()->json([
            'snap_token' => $pembayaran->snap_token,
            'order_id' => $pembayaran->midtrans_order_id,
        ]);
    }

    /**
     * Handle Midtrans webhook notification (server-to-server)
     */
    public function handleWebhook(Request $request)
    {
        $midtransService = new MidtransService();
        $result = $midtransService->handleNotification();

        return response()->json($result);
    }

    /**
     * Payment finish redirect page
     */
    public function paymentFinish(Request $request)
    {
        $orderId = $request->get('order_id');
        $transactionStatus = $request->get('transaction_status');

        if ($orderId) {
            $pembayaran = Pembayaran::where('midtrans_order_id', $orderId)->first();

            if ($pembayaran && $pembayaran->status === 'Paid') {
                return redirect()->route('settings.index', ['tab' => 'subscription'])
                    ->with('success', 'Payment successful! Your subscription has been activated.');
            }
        }

        // For pending/other statuses
        return redirect()->route('settings.index', ['tab' => 'subscription'])
            ->with('info', 'Payment is being processed. Your subscription will be activated once payment is confirmed.');
    }

    /**
     * Get subscription status data (for AJAX)
     */
    public function status()
    {
        $user = Auth::user();
        $owner = $user->owner;

        if (!$owner) {
            return response()->json(['error' => 'Owner not found.'], 404);
        }

        $subscription = Langganan::where('owner_id', $owner->id)
            ->with('tipeLayanan')
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$subscription) {
            return response()->json([
                'has_subscription' => false,
            ]);
        }

        $daysLeft = $subscription->end_date
            ? Carbon::now()->diffInDays(Carbon::parse($subscription->end_date), false)
            : null;

        return response()->json([
            'has_subscription' => true,
            'package_name' => $subscription->tipeLayanan->nama ?? 'Unknown',
            'is_active' => $subscription->is_active,
            'is_trial' => $subscription->is_trial,
            'start_date' => $subscription->started_date?->format('d M Y'),
            'end_date' => $subscription->end_date?->format('d M Y'),
            'days_left' => $daysLeft,
            'is_expired' => $daysLeft !== null && $daysLeft <= 0,
        ]);
    }

    /**
     * Payment history for current owner
     */
    public function history()
    {
        $user = Auth::user();
        $owner = $user->owner;

        $payments = Pembayaran::where('owner_id', $owner->id)
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return response()->json($payments);
    }
}
