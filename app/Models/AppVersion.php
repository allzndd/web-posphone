<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppVersion extends Model
{
    use HasFactory;

    protected $table = 'app_versions';

    protected $fillable = [
        'platform',
        'latest_version',
        'minimum_version',
        'maintenance_mode',
        'maintenance_message',
        'store_url',
    ];

    protected $casts = [
        'maintenance_mode' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
