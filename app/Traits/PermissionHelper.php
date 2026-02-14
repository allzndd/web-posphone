<?php

namespace App\Traits;

use App\Models\PackagePermission;
use Illuminate\Support\Facades\Auth;

trait PermissionHelper
{
    /**
     * Check if user has specific permission
     * 
     * @param string $permission Format: "customer.create", "customer.read", etc
     * @return bool
     */
    public function userHasPermission($permission)
    {
        $user = Auth::user();
        
        // Superadmin dan Admin memiliki semua permission
        if ($user->isSuperadmin() || $user->isAdmin()) {
            return true;
        }

        // Untuk owner, check berdasarkan paket layanan
        if ($user->isOwner() && $user->owner) {
            $tipeLayanan = $user->owner->paketLayanan;
            
            if ($tipeLayanan) {
                return $tipeLayanan->packagePermissions()
                    ->whereHas('permission', function ($query) use ($permission) {
                        $query->where('nama', $permission);
                    })
                    ->exists();
            }
        }

        return false;
    }

    /**
     * Get permission record dengan max_records
     * 
     * @param string $permission Format: "customer.create", "customer.read", etc
     * @return \App\Models\PackagePermission|null
     */
    public function getPermissionRecord($permission)
    {
        $user = Auth::user();

        // Superadmin dan Admin: return dengan max_records = 0 (unlimited)
        if ($user->isSuperadmin() || $user->isAdmin()) {
            return null; // unlimited
        }

        // Untuk owner
        if ($user->isOwner() && $user->owner) {
            $tipeLayanan = $user->owner->paketLayanan;
            
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
    public function getMaxRecords($permission)
    {
        $permissionRecord = $this->getPermissionRecord($permission);
        
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
    public function isReachedLimit($permission, $currentCount)
    {
        $maxRecords = $this->getMaxRecords($permission);
        
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
    public function getRemainingQuota($permission, $currentCount)
    {
        $maxRecords = $this->getMaxRecords($permission);
        
        if ($maxRecords === 0) {
            return -1; // unlimited
        }

        return max(0, $maxRecords - $currentCount);
    }
}
