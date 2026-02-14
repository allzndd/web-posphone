<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackagePermission extends Model
{
    use HasFactory;

    protected $table = 'package_permissions';

    protected $fillable = [
        'tipe_layanan_id',
        'permissions_id',
        'max_records',
    ];

    protected $casts = [
        'tipe_layanan_id' => 'integer',
        'permissions_id' => 'integer',
        'max_records' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relasi ke TipeLayanan (Paket Layanan)
     */
    public function tipeLayanan()
    {
        return $this->belongsTo(TipeLayanan::class, 'tipe_layanan_id');
    }

    /**
     * Relasi ke Permission
     */
    public function permission()
    {
        return $this->belongsTo(Permission::class, 'permissions_id');
    }
}
