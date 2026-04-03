<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use App\Models\TipeLayanan;
use App\Models\Langganan;
use App\Models\Pembayaran;
use App\Models\Owner;
use App\Services\MidtransService;
use App\Mail\PaymentProofSubmittedMail;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SubscriptionController extends Controller
{
    /**
     * Show available packages for upgrade/renewal
     */
    public function packages()
    {
        $user = Auth::user();
        $owner = $user->owner;
        $useMidtrans = (bool) config('services.midtrans.enabled', false);
        $paymentMode = $useMidtrans ? 'midtrans' : 'manual';

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
            ->when($useMidtrans, function ($query) {
                $query->whereNotNull('snap_token')
                    ->where('expired_at', '>', Carbon::now());
            })
            ->orderBy('created_at', 'desc')
            ->first();

        $banks = Bank::orderBy('nama_bank', 'asc')->get();

        return view('subscription.packages', compact(
            'currentSubscription',
            'packages',
            'pendingPayment',
            'owner',
            'banks',
            'paymentMode'
        ));
    }

    /**
     * Initiate payment for a package
     */
    public function checkout(Request $request)
    {
        $useMidtrans = (bool) config('services.midtrans.enabled', false);

        if (!$useMidtrans) {
            return $this->checkoutManualTransfer($request);
        }

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

    private function checkoutManualTransfer(Request $request)
    {
        $request->validate([
            'package_id' => 'required|exists:tipe_layanan,id',
            'bank_id' => 'required|exists:bank,id',
            'bukti_transfer' => 'required|file|mimes:jpg,jpeg,png,pdf|max:10240',
        ]);

        $user = Auth::user();
        $owner = $user->owner;

        if (!$owner) {
            return redirect()->route('dashboard')->with('error', 'Owner account not found.');
        }

        $package = TipeLayanan::findOrFail($request->package_id);
        if ($package->harga <= 0) {
            return redirect()->back()->with('error', 'Invalid package selection.');
        }

        $bank = Bank::findOrFail($request->bank_id);

        DB::beginTransaction();
        try {
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

            $proofPath = $this->storeTransferProof($request->file('bukti_transfer'));

            $pembayaran = Pembayaran::create([
                'owner_id' => $owner->id,
                'langganan_id' => $langganan->id,
                'target_tipe_layanan_id' => $package->id,
                'nominal' => $package->harga,
                'metode_pembayaran' => 'transfer_bank',
                'bukti_transfer' => $proofPath,
                'midtrans_response' => json_encode([
                    'manual_transfer' => true,
                    'bank_id' => $bank->id,
                    'nama_bank' => $bank->nama_bank,
                    'nama_rekening' => $bank->nama_rekening,
                    'nomor_rekening' => $bank->nomor_rekening,
                ]),
                'status' => 'Pending',
                'created_at' => Carbon::now(),
            ]);

            DB::commit();

            $adminEmail = env('ADMIN_EMAIL', config('mail.from.address'));
            if (!empty($adminEmail)) {
                Mail::to($adminEmail)->send(new PaymentProofSubmittedMail(
                    $pembayaran,
                    $user,
                    $package,
                    $bank,
                    $pembayaran->bukti_transfer_url
                ));
            } else {
                Log::warning('Admin email is not configured. Payment proof email was not sent.', [
                    'owner_id' => $owner->id,
                    'pembayaran_id' => $pembayaran->id,
                ]);
            }

            return redirect()->route('settings.index', ['tab' => 'subscription'])
                ->with('success', 'Bukti transfer berhasil dikirim. Tim admin akan memverifikasi pembayaran Anda.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Manual transfer checkout failed: ' . $e->getMessage(), [
                'owner_id' => $owner->id,
                'package_id' => $request->package_id,
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()->withInput()->with('error', 'Gagal mengirim bukti transfer. Coba lagi.');
        }
    }

    private function storeTransferProof(UploadedFile $file): string
    {
        $storageSymlinkPath = public_path('storage');

        if (is_link($storageSymlinkPath) || is_dir($storageSymlinkPath)) {
            $storedPath = $file->store('bukti-transfer', 'public');
            return 'storage/' . ltrim($storedPath, '/');
        }

        $targetDir = public_path('bukti-transfer');
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        $extension = strtolower($file->getClientOriginalExtension() ?: 'jpg');
        $filename = 'proof_' . uniqid('', true) . '.' . $extension;
        $file->move($targetDir, $filename);

        return 'bukti-transfer/' . $filename;
    }

    /**
     * Continue payment with existing snap token  
     */
    public function continuePayment(Request $request)
    {
        $useMidtrans = (bool) config('services.midtrans.enabled', false);
        if (!$useMidtrans) {
            return response()->json(['error' => 'Continue payment is unavailable in manual transfer mode.'], 422);
        }

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
        $useMidtrans = (bool) config('services.midtrans.enabled', false);
        if (!$useMidtrans) {
            return response()->json(['status' => 'error', 'message' => 'Midtrans is disabled.'], 422);
        }

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
            'start_date' => $subscription->started_date ? $subscription->started_date->format('d M Y') : null,
            'end_date' => $subscription->end_date ? $subscription->end_date->format('d M Y') : null,
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
