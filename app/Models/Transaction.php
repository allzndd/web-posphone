<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'type',
        'invoice_number',
        'delivery_cost',
        'tax_cost',
        'total_price',
        'date',
        'payment_id',
        'notes',
        'warranty_period',
        'warranty_expires_at',
        'cashier_id',
    ];

    protected $casts = [
        'date' => 'datetime',
        'warranty_expires_at' => 'date',
        'delivery_cost' => 'float',
        'tax_cost' => 'float',
        'total_price' => 'float'
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(TransactionItem::class, 'transaction_id', 'id');
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class, 'payment_id', 'id');
    }

    public function cashier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cashier_id', 'id');
    }
}
