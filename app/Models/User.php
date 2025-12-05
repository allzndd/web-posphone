<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'roles',
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
     * Check if user is owner
     *
     * @return bool
     */
    public function isOwner()
    {
        return $this->roles === 'OWNER';
    }

    /**
     * Check if user is admin
     *
     * @return bool
     */
    public function isAdmin()
    {
        return $this->roles === 'ADMIN';
    }

    /**
     * Check if user has specific role
     *
     * @param string $role
     * @return bool
     */
    public function hasRole($role)
    {
        return $this->roles === $role;
    }
}
