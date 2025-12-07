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
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'tanggal',
        'owner_id',
        'owner_name',
        'email',
        'paket',
        'periode',
        'total',
        'status',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tanggal' => 'date',
        'total' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the owner that owns the payment.
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
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
