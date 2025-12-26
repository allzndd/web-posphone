<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PosExpense extends Model
{
    use HasFactory;

    protected $table = 'pos_expenses';

    protected $fillable = [
        'owner_id',
        'pos_toko_id',
        'expense_type',
        'amount',
        'description',
        'expense_date',
        'receipt_number',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'expense_date' => 'date',
    ];

    /**
     * Get the store that owns the expense.
     */
    public function toko()
    {
        return $this->belongsTo(PosToko::class, 'pos_toko_id');
    }

    /**
     * Get expense type label
     */
    public function getExpenseTypeLabel()
    {
        $labels = [
            'salary' => 'Gaji',
            'rent' => 'Sewa',
            'utilities' => 'Utilitas',
            'maintenance' => 'Perawatan',
            'marketing' => 'Marketing',
            'transportation' => 'Transportasi',
            'other' => 'Lainnya',
        ];

        return $labels[$this->expense_type] ?? $this->expense_type;
    }

    /**
     * Scope for filtering by owner
     */
    public function scopeByOwner($query, $ownerId)
    {
        return $query->where('owner_id', $ownerId);
    }

    /**
     * Scope for filtering by store
     */
    public function scopeByStore($query, $storeId)
    {
        return $query->where('pos_toko_id', $storeId);
    }

    /**
     * Scope for filtering by date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('expense_date', [$startDate, $endDate]);
    }

    /**
     * Scope for filtering by expense type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('expense_type', $type);
    }
}
