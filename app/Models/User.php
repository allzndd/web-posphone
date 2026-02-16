<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pengguna';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nama',
        'email',
        'password',
        'slug',
        'role_id',
        'email_is_verified',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Automatically hash passwords when they are set.
     *
     * @param string $value
     * @return void
     */
    public function setPasswordAttribute($value)
    {
        // Avoid double-hashing when a bcrypt/argon hash is already provided
        if (is_string($value) && (
            preg_match('/^\$2y\$/', $value) || // bcrypt
            preg_match('/^\$argon2(id|i)\$/', $value) // argon2
        )) {
            $this->attributes['password'] = $value;
        } else {
            $this->attributes['password'] = bcrypt($value);
        }
    }

    /**
     * Get the user's name attribute.
     *
     * @return string
     */
    public function getNameAttribute()
    {
        return $this->attributes['nama'] ?? null;
    }

    /**
     * Check if user is owner
     *
     * @return bool
     */
    public function isOwner()
    {
        return $this->role_id === 2; // Owner role_id
    }

    /**
     * Check if user is admin
     *
     * @return bool
     */
    public function isAdmin()
    {
        return $this->role_id === 3; // Admin role_id
    }

    /**
     * Check if user is superadmin
     *
     * @return bool
     */
    public function isSuperadmin()
    {
        return $this->role_id === 1; // Superadmin role_id
    }

    /**
     * Check if user has specific role
     *
     * @param int $roleId
     * @return bool
     */
    public function hasRole($roleId)
    {
        return $this->role_id === $roleId;
    }

    /**
     * Check if user has specific permission
     *
     * @param string $permission
     * @return bool
     */
    public function hasPermission($permission)
    {
        // Superadmin and Admin have all permissions
        if ($this->isSuperadmin() || $this->isAdmin()) {
            return true;
        }

        // For other users, check based on their paket layanan (subscription package)
        if ($this->isOwner() && $this->owner) {
            // Get user's subscription package
            $paketLayanan = $this->owner->paketLayanan;
            
            if ($paketLayanan) {
                // Check if user's package has this permission
                $hasPermission = $paketLayanan->packagePermissions()
                    ->whereHas('permission', function ($query) use ($permission) {
                        $query->where('nama', $permission);
                    })
                    ->exists();
                
                return $hasPermission;
            }
        }

        return false;
    }

    /**
     * Get the role that the user belongs to.
     */
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    /**
     * Get the owner record if user is owner.
     */
    public function owner()
    {
        return $this->hasOne(Owner::class, 'pengguna_id');
    }

    /**
     * Check if user has verified email.
     *
     * @return bool
     */
    public function hasVerifiedEmail()
    {
        return $this->email_is_verified == 1;
    }

    /**
     * Mark the given user's email as verified.
     *
     * @return bool
     */
    public function markEmailAsVerified()
    {
        return $this->forceFill([
            'email_is_verified' => 1,
        ])->save();
    }
}
