<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PermissionService
{
    /**
     * Check if user has specific permission
     * 
     * @param string $permission Format: "customer.create", "customer.read", etc
     * @return bool
     */
    public static function check($permission)
    {
        $user = Auth::user();
        
        if (!$user) {
            return false;
        }

        // Superadmin dan Admin memiliki semua permission
        if ($user->isSuperadmin() || $user->isAdmin()) {
            return true;
        }

        // Untuk owner, check berdasarkan paket layanan dan status langganan
        if ($user->isOwner() && $user->owner) {
            // Check apakah punya langganan yang aktif
            $langganan = $user->owner->langganan()
                ->where('is_active', 1)
                ->where('end_date', '>=', now()->toDateString())
                ->first();
            
            if (!$langganan) {
                return false; // Langganan tidak aktif atau sudah expired
            }
            
            $tipeLayanan = $langganan->tipeLayanan;
            
            if ($tipeLayanan) {
                $hasPermission = $tipeLayanan->packagePermissions()
                    ->whereHas('permission', function ($query) use ($permission) {
                        $query->where('nama', $permission);
                    })
                    ->exists();
                
                // Jika permission tidak ditemukan di package permissions, DENY akses
                // (sebelumnya logic ini salah - return true untuk permission yang belum ada di DB)
                if (!$hasPermission) {
                    return false; // DENY - permission tidak dikonfigurasi di paket ini
                }
                
                return true; // ALLOW - permission ditemukan di package
            }
        }

        return false;
    }

    /**
     * Check multiple permissions (OR logic)
     * User has access jika memiliki salah satu dari permissions
     * 
     * @param array $permissions
     * @return bool
     */
    public static function checkAny(array $permissions)
    {
        foreach ($permissions as $permission) {
            if (self::check($permission)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check multiple permissions (AND logic)
     * User has access jika memiliki semua permissions
     * 
     * @param array $permissions
     * @return bool
     */
    public static function checkAll(array $permissions)
    {
        foreach ($permissions as $permission) {
            if (!self::check($permission)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get permission record dengan max_records
     * 
     * @param string $permission Format: "customer.create", "customer.read", etc
     * @return \App\Models\PackagePermission|null
     */
    public static function getRecord($permission)
    {
        $user = Auth::user();

        if (!$user) {
            return null;
        }

        // Superadmin dan Admin: return null (unlimited)
        if ($user->isSuperadmin() || $user->isAdmin()) {
            return null;
        }

        // Untuk owner, check langganan aktif
        if ($user->isOwner() && $user->owner) {
            // Check apakah punya langganan yang aktif
            $langganan = $user->owner->langganan()
                ->where('is_active', 1)
                ->where('end_date', '>=', now()->toDateString())
                ->first();
            
            if (!$langganan) {
                return null; // Langganan tidak aktif atau sudah expired
            }
            
            $tipeLayanan = $langganan->tipeLayanan;
            
            if ($tipeLayanan) {
                return $tipeLayanan->packagePermissions()
                    ->whereHas('permission', function ($query) use ($permission) {
                        $query->where('nama', $permission);
                    })
                    ->with('permission')
                    ->first();
            }
        }

        return null;
    }

    /**
     * Get max records untuk specific permission
     * Return 0 jika unlimited
     * 
     * @param string $permission
     * @return int
     */
    public static function getMaxRecords($permission)
    {
        $permissionRecord = self::getRecord($permission);
        
        if (!$permissionRecord) {
            return 0; // unlimited untuk admin/superadmin
        }

        return (int) $permissionRecord->max_records;
    }

    /**
     * Check apakah user sudah mencapai limit untuk specific action
     * 
     * @param string $permission Format: "customer.create"
     * @param int $currentCount
     * @return bool
     */
    public static function isReachedLimit($permission, $currentCount)
    {
        $maxRecords = self::getMaxRecords($permission);
        
        // 0 = unlimited, jadi tidak pernah reach limit
        if ($maxRecords === 0) {
            return false;
        }

        return $currentCount >= $maxRecords;
    }

    /**
     * Get remaining quota untuk specific permission
     * Return -1 jika unlimited
     * 
     * @param string $permission
     * @param int $currentCount
     * @return int
     */
    public static function getRemainingQuota($permission, $currentCount)
    {
        $maxRecords = self::getMaxRecords($permission);
        
        if ($maxRecords === 0) {
            return -1; // unlimited
        }

        return max(0, $maxRecords - $currentCount);
    }

    /**
     * Get all permissions for current user's package
     * 
     * @return array
     */
    public static function getUserPermissions()
    {
        $user = Auth::user();

        if (!$user) {
            return [];
        }

        // Superadmin dan Admin - return all permissions
        if ($user->isSuperadmin() || $user->isAdmin()) {
            return ['*']; // wildcard for all
        }

        // Untuk owner, check langganan aktif
        if ($user->isOwner() && $user->owner) {
            // Check apakah punya langganan yang aktif
            $langganan = $user->owner->langganan()
                ->where('is_active', 1)
                ->where('end_date', '>=', now()->toDateString())
                ->first();
            
            if (!$langganan) {
                return []; // Langganan tidak aktif atau sudah expired
            }
            
            $tipeLayanan = $langganan->tipeLayanan;
            
            if ($tipeLayanan) {
                return $tipeLayanan->packagePermissions()
                    ->with('permission')
                    ->get()
                    ->pluck('permission.nama')
                    ->toArray();
            }
        }

        return [];
    }

    /**
     * Check if user is superadmin
     * 
     * @return bool
     */
    public static function isSuperadmin()
    {
        $user = Auth::user();
        return $user && $user->isSuperadmin();
    }

    /**
     * Check if user is admin
     * 
     * @return bool
     */
    public static function isAdmin()
    {
        $user = Auth::user();
        return $user && $user->isAdmin();
    }

    /**
     * Check if user is owner
     * 
     * @return bool
     */
    public static function isOwner()
    {
        $user = Auth::user();
        return $user && $user->isOwner();
    }
}
