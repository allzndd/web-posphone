<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    protected $table = 'pos_produk';

    protected  $fillable = [
        'name',
        'slug',
        'description',
        'image_url',
        'sell_price',
        'buy_price',
        'costs',
        'barre_health',
        'gross_profit',
        'net_profit',
        'assessoris',
        'imei',
        'stock',
        'color',
        'storage',
        'view_count',
        'category_id'
    ];

    public function getAutoCategoryAttribute(): string
    {
        if (!$this->name) return '-';
        $first = trim(strtok($this->name, ' '));
        return $first !== '' ? $first : $this->name;
    }

    /**
     * Get calculated profit per product
     * Profit = sell_price - (buy_price + total cost)
     */
    public function getProfitAttribute()
    {
        $totalCost = 0;
        if (is_array($this->costs)) {
            foreach ($this->costs as $item) {
                $totalCost += isset($item['amount']) ? (float)$item['amount'] : 0;
            }
        } elseif (is_string($this->costs)) {
            $costs = json_decode($this->costs, true);
            if (is_array($costs)) {
                foreach ($costs as $item) {
                    $totalCost += isset($item['amount']) ? (float)$item['amount'] : 0;
                }
            }
        }
        return $this->sell_price - ($this->buy_price + $totalCost);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

}
