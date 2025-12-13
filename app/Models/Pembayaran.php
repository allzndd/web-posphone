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
        'nominal',
        'metode_pembayaran',
        'status',
        'paid_at',
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
}
