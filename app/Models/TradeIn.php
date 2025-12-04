<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TradeIn extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'old_phone',
        'old_imei',
        'old_value',
        'new_product_id',
        'date',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function newProduct()
    {
        return $this->belongsTo(Product::class, 'new_product_id');
    }
}
