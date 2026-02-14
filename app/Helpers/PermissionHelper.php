<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;

class PermissionHelper
{
    /**
     * Check if authenticated user has specific permission
     */
    public static function hasPermission($permission)
    {
        if (!Auth::check()) {
            return false;
        }

        return Auth::user()->hasPermission($permission);
    }

    /**
     * Check if user can perform action on resource
     * Format: "resource.action" (e.g., "service.create", "product.delete")
     */
    public static function can($permission)
    {
        return self::hasPermission($permission);
    }
}
