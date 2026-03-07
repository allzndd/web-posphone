<?php

namespace App\Services;

use GuzzleHttp\Client;
use App\Models\Pembayaran;
use App\Models\Langganan;
use App\Models\TipeLayanan;
use App\Models\Owner;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class MidtransService
{
    private string $serverKey;
    private string $clientKey;
    private bool $isProduction;
    private Client $client;

    public function __construct()
    {
        $this->serverKey    = config('services.midtrans.server_key');
        $this->clientKey    = config('services.midtrans.client_key');
        $this->isProduction = config('services.midtrans.is_production', false);

        $baseUri = $this->isProduction
            ? 'https://app.midtrans.com/'
            : 'https://app.sandbox.midtrans.com/';

        $this->client = new Client([
            'base_uri' => $baseUri,
            'headers'  => [
                'Content-Type'  => 'application/json',
                'Accept'        => 'application/json',
                'Authorization' => 'Basic ' . base64_encode($this->serverKey . ':'),
            ],
            'timeout'  => 30,
        ]);
    }

    /**
     * Get Midtrans client key for frontend
     */
    public static function getClientKey(): string
    {
        return config('services.midtrans.client_key');
    }

    /**
     * Get Snap JS URL based on environment
     */
    public static function getSnapUrl(): string
    {
        return config('services.midtrans.is_production', false)
            ? 'https://app.midtrans.com/snap/snap.js'
            : 'https://app.sandbox.midtrans.com/snap/snap.js';
    }

    /**
     * Create Snap token by calling Midtrans Snap API directly
     */
    public function createSnapToken(Pembayaran $pembayaran, TipeLayanan $package, Owner $owner): string
    {
        $user    = $owner->pengguna;
        $orderId = $pembayaran->midtrans_order_id;

        $payload = [
            'transaction_details' => [
                'order_id'     => $orderId,
                'gross_amount' => (int) $pembayaran->nominal,
            ],
            'customer_details' => [
                'first_name' => $user->nama ?? 'Customer',
                'email'      => $user->email ?? '',
            ],
            'item_details' => [
                [
                    'id'       => $package->id,
                    'price'    => (int) $package->harga,
                    'quantity' => 1,
                    'name'     => 'Paket ' . $package->nama . ' (' . $package->duration_text . ')',
                ],
            ],
            'callbacks' => [
                'finish' => route('subscription.payment.finish'),
            ],
            'expiry' => [
                'start_time' => Carbon::now()->format('Y-m-d H:i:s O'),
                'unit'       => 'hours',
                'duration'   => 24,
            ],
        ];

        $response = $this->client->post('snap/v1/transactions', [
            'json' => $payload,
        ]);

        $body = json_decode($response->getBody()->getContents(), true);

        if (empty($body['token'])) {
            throw new \RuntimeException('Midtrans did not return a snap token. Response: ' . json_encode($body));
        }

        $snapToken = $body['token'];

        $pembayaran->update([
            'snap_token' => $snapToken,
            'expired_at' => Carbon::now()->addHours(24),
        ]);

        Log::info('Midtrans Snap Token created', [
            'order_id' => $orderId,
            'amount'   => $pembayaran->nominal,
            'package'  => $package->nama,
        ]);

        return $snapToken;
    }

    /**
     * Generate a unique order ID
     */
    public static function generateOrderId(int $ownerId, int $packageId): string
    {
        return 'SUB-' . $ownerId . '-' . $packageId . '-' . time();
    }

    /**
     * Handle Midtrans webhook notification (server-to-server callback)
     */
    public function handleNotification(): array
    {
        try {
            $body = file_get_contents('php://input');
            $data = json_decode($body, true);

            if (empty($data)) {
                return ['status' => 'error', 'message' => 'Empty notification body'];
            }

            $orderId           = $data['order_id']           ?? null;
            $statusCode        = $data['status_code']        ?? null;
            $grossAmount       = $data['gross_amount']       ?? null;
            $transactionStatus = $data['transaction_status'] ?? null;
            $paymentType       = $data['payment_type']       ?? null;
            $fraudStatus       = $data['fraud_status']       ?? null;
            $transactionId     = $data['transaction_id']     ?? null;
            $signatureKey      = $data['signature_key']      ?? null;

            // Verify signature: SHA512(order_id + status_code + gross_amount + server_key)
            $expectedSignature = hash('sha512', $orderId . $statusCode . $grossAmount . $this->serverKey);
            if ($signatureKey !== $expectedSignature) {
                Log::warning('Midtrans: invalid signature', ['order_id' => $orderId]);
                return ['status' => 'error', 'message' => 'Invalid signature'];
            }

            Log::info('Midtrans Notification Received', [
                'order_id'   => $orderId,
                'status'     => $transactionStatus,
                'type'       => $paymentType,
                'fraud'      => $fraudStatus,
            ]);

            $pembayaran = Pembayaran::where('midtrans_order_id', $orderId)->first();

            if (!$pembayaran) {
                Log::error('Pembayaran not found for order_id: ' . $orderId);
                return ['status' => 'error', 'message' => 'Payment not found'];
            }

            $pembayaran->update([
                'midtrans_transaction_id' => $transactionId,
                'payment_type'            => $paymentType,
                'midtrans_response'       => json_encode($data),
            ]);

            if ($transactionStatus === 'capture') {
                if ($fraudStatus === 'accept') {
                    $this->activateSubscription($pembayaran);
                } else {
                    $pembayaran->update(['status' => 'Challenged']);
                }
            } elseif ($transactionStatus === 'settlement') {
                $this->activateSubscription($pembayaran);
            } elseif (in_array($transactionStatus, ['cancel', 'deny', 'expire'])) {
                $pembayaran->update(['status' => 'Failed']);
            } elseif ($transactionStatus === 'pending') {
                $pembayaran->update(['status' => 'Pending']);
            }

            return ['status' => 'success', 'order_id' => $orderId, 'transaction_status' => $transactionStatus];

        } catch (\Exception $e) {
            Log::error('Midtrans Notification Handler Error: ' . $e->getMessage());
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    /**
     * Activate the subscription after a successful payment
     */
    private function activateSubscription(Pembayaran $pembayaran): void
    {
        $pembayaran->update([
            'status'  => 'Paid',
            'paid_at' => Carbon::now(),
            'metode_pembayaran' => $pembayaran->payment_type ?? 'midtrans',
        ]);

        $langganan = $pembayaran->langganan;
        if (!$langganan) {
            Log::error('Langganan not found for pembayaran ID: ' . $pembayaran->id);
            return;
        }

        // Use the target package from pembayaran (what the user actually paid for)
        $targetPackageId = $pembayaran->target_tipe_layanan_id ?? $langganan->tipe_layanan_id;
        $tipeLayanan = TipeLayanan::find($targetPackageId);
        if (!$tipeLayanan) {
            Log::error('TipeLayanan not found for target package ID: ' . $targetPackageId);
            return;
        }

        $startDate = Carbon::now();
        $endDate   = $this->calculateEndDate($startDate, $tipeLayanan);

        // NOW update the langganan with the new package + activate
        $langganan->update([
            'tipe_layanan_id' => $tipeLayanan->id,
            'is_active'    => 1,
            'is_trial'     => 0,
            'started_date' => $startDate,
            'end_date'     => $endDate,
        ]);

        Log::info('Subscription activated', [
            'owner_id'    => $langganan->owner_id,
            'langganan_id'=> $langganan->id,
            'package'     => $tipeLayanan->nama,
            'start_date'  => $startDate->format('Y-m-d'),
            'end_date'    => $endDate->format('Y-m-d'),
        ]);
    }

    /**
     * Calculate subscription end date based on package duration
     */
    private function calculateEndDate(Carbon $startDate, TipeLayanan $package): Carbon
    {
        $satuan = $package->durasi_satuan ?? 'bulan';
        $durasi = $package->durasi;

        return match ($satuan) {
            'hari'   => $startDate->copy()->addDays($durasi),
            'tahun'  => $startDate->copy()->addYears($durasi),
            default  => $startDate->copy()->addMonths($durasi),
        };
    }
}
