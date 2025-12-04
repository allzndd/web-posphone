<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'join_date',
        'transaction_id',
        'tradein_id'
    ];

    protected $casts = [
        'join_date' => 'date'
    ];

    // Relationships
    public function tradeIn()
    {
        return $this->belongsTo(TradeIn::class, 'tradein_id');
    }

    /**
     * A customer may have many transactions.
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'customer_id', 'id');
    }
}
