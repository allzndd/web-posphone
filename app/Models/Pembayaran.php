<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pembayaran';

    /**
     * Indicates if the model should be timestamped.
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'owner_id',
        'langganan_id',
        'target_tipe_layanan_id',
        'nominal',
        'metode_pembayaran',
        'bukti_transfer',
        'status',
        'midtrans_order_id',
        'snap_token',
        'midtrans_transaction_id',
        'payment_type',
        'midtrans_response',
        'paid_at',
        'expired_at',
        'created_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'nominal' => 'decimal:2',
        'paid_at' => 'datetime',
        'expired_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    /**
     * Get the owner that owns the payment.
     */
    public function owner()
    {
        return $this->belongsTo(Owner::class, 'owner_id');
    }

    /**
     * Get the subscription for this payment.
     */
    public function langganan()
    {
        return $this->belongsTo(Langganan::class, 'langganan_id');
    }

    /**
     * Scope a query to only include paid payments.
     */
    public function scopePaid($query)
    {
        return $query->where('status', 'Paid');
    }

    /**
     * Scope a query to only include pending payments.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'Pending');
    }

    /**
     * Check if payment is paid.
     */
    public function isPaid()
    {
        return $this->status === 'Paid';
    }

    /**
     * Check if payment is pending.
     */
    public function isPending()
    {
        return $this->status === 'Pending';
    }

    public function getBuktiTransferUrlAttribute()
    {
        if (!$this->bukti_transfer) {
            return null;
        }

        if (filter_var($this->bukti_transfer, FILTER_VALIDATE_URL)) {
            return $this->bukti_transfer;
        }

        $path = ltrim($this->bukti_transfer, '/');

        if (str_starts_with($path, 'storage/')) {
            if (is_file(public_path($path))) {
                return url($path);
            }

            $basename = basename($path);
            $publicFallbackPath = 'bukti-transfer/' . $basename;
            if (is_file(public_path($publicFallbackPath))) {
                return route('bukti-transfer.file', ['path' => $basename]);
            }

            return url($path);
        }

        if (str_starts_with($path, 'bukti-transfer/')) {
            $relativePath = substr($path, strlen('bukti-transfer/'));
            return route('bukti-transfer.file', ['path' => $relativePath]);

        }

        if (is_file(public_path('bukti-transfer/' . $path))) {
            return route('bukti-transfer.file', ['path' => $path]);
        }

        if (str_contains($path, '/')) {
            $basename = basename($path);
            if (is_file(public_path('bukti-transfer/' . $basename))) {
                return route('bukti-transfer.file', ['path' => $basename]);
            }
        }

        return url('storage/' . $path);
    }
}
