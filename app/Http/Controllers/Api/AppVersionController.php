<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AppVersion;
use Illuminate\Http\Request;

class AppVersionController extends Controller
{
    /**
     * Get app version by platform (for mobile app version checking)
     * No auth required - public endpoint
     */
    public function getByPlatform(Request $request)
    {
        try {
            $platform = $request->query('platform', 'Android');
            
            // Validate platform
            if (!in_array($platform, ['Android', 'iOS'])) {
                return response()->json([
                    'status' => false,
                    'message' => 'Platform tidak valid. Gunakan Android atau iOS',
                    'data' => null,
                ], 400);
            }
            
            $appVersion = AppVersion::where('platform', $platform)->first();
            
            if (!$appVersion) {
                return response()->json([
                    'status' => false,
                    'message' => 'Data versi aplikasi tidak ditemukan untuk platform ' . $platform,
                    'data' => null,
                ], 404);
            }
            
            return response()->json([
                'status' => true,
                'message' => 'Data versi aplikasi berhasil diambil',
                'data' => [
                    'id' => $appVersion->id,
                    'platform' => $appVersion->platform,
                    'latest_version' => $appVersion->latest_version,
                    'minimum_version' => $appVersion->minimum_version,
                    'maintenance_mode' => $appVersion->maintenance_mode,
                    'maintenance_message' => $appVersion->maintenance_message,
                    'store_url' => $appVersion->store_url,
                    'created_at' => $appVersion->created_at->toDateTimeString(),
                    'updated_at' => $appVersion->updated_at->toDateTimeString(),
                ],
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
                'data' => null,
            ], 500);
        }
    }
    
    /**
     * Get all app versions (protected - admin only)
     */
    public function index()
    {
        try {
            $appVersions = AppVersion::all();
            
            return response()->json([
                'status' => true,
                'message' => 'Semua data versi aplikasi berhasil diambil',
                'data' => $appVersions->map(function ($version) {
                    return [
                        'id' => $version->id,
                        'platform' => $version->platform,
                        'latest_version' => $version->latest_version,
                        'minimum_version' => $version->minimum_version,
                        'maintenance_mode' => $version->maintenance_mode,
                        'maintenance_message' => $version->maintenance_message,
                        'store_url' => $version->store_url,
                        'created_at' => $version->created_at->toDateTimeString(),
                        'updated_at' => $version->updated_at->toDateTimeString(),
                    ];
                }),
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
                'data' => [],
            ], 500);
        }
    }
}
