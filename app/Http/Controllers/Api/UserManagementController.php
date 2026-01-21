<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class UserManagementController extends Controller
{
    /**
     * Get list of admin users for the owner
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $user = $request->user();

            // Only owners can access this
            if (!$user->isOwner()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Only owners can manage users.',
                ], 403);
            }

            // Get owner_id from the owner relationship
            $ownerId = $user->owner ? $user->owner->id : null;

            if (!$ownerId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Owner record not found.',
                ], 404);
            }

            // Get all admin users (role_id = 3)
            // Since we don't have a parent_owner_id field, we'll get all admins
            // In production, you should add a 'created_by' or 'parent_owner_id' field
            $admins = User::where('role_id', 3)
                ->with('owner.toko') // Load store relationship
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($admin) {
                    $toko = $admin->owner && $admin->owner->toko ? $admin->owner->toko->first() : null;
                    return [
                        'id' => $admin->id,
                        'nama' => $admin->nama,
                        'email' => $admin->email,
                        'role_id' => $admin->role_id,
                        'store_id' => $toko ? $toko->id : null,
                        'store_name' => $toko ? $toko->nama : null,
                        'created_at' => $admin->created_at->format('Y-m-d H:i:s'),
                        'updated_at' => $admin->updated_at->format('Y-m-d H:i:s'),
                    ];
                });

            return response()->json([
                'success' => true,
                'message' => 'Admin users retrieved successfully',
                'data' => $admins,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve admin users: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Create a new admin user
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $user = $request->user();

            // Only owners can create admin users
            if (!$user->isOwner()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Only owners can create admin users.',
                ], 403);
            }

            // Validation
            $validator = Validator::make($request->all(), [
                'nama' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:pengguna,email',
                'password' => 'required|string|min:8|confirmed',
                'store_id' => 'nullable|exists:pos_toko,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422);
            }

            // Get owner_id from the owner relationship
            $ownerId = $user->owner ? $user->owner->id : null;

            if (!$ownerId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Owner record not found.',
                ], 404);
            }

            // Create new admin user
            $admin = User::create([
                'nama' => $request->nama,
                'email' => $request->email,
                'password' => $request->password, // Will be hashed automatically
                'slug' => Str::slug($request->nama) . '-' . time(),
                'role_id' => 3, // Admin role
                'email_is_verified' => 1,
            ]);

            // Create owner relationship for the admin
            $ownerRecord = $admin->owner()->create([
                'pengguna_id' => $admin->id,
            ]);

            // If store_id is provided, associate the admin with the store
            if ($request->has('store_id') && $request->store_id) {
                \DB::table('pos_toko')
                    ->where('id', $request->store_id)
                    ->update(['owner_id' => $ownerRecord->id]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Admin user created successfully',
                'data' => [
                    'id' => $admin->id,
                    'nama' => $admin->nama,
                    'email' => $admin->email,
                    'role_id' => $admin->role_id,
                    'store_id' => $request->store_id,
                    'created_at' => $admin->created_at->format('Y-m-d H:i:s'),
                ],
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create admin user: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get a specific admin user
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $id)
    {
        try {
            $user = $request->user();

            // Only owners can view admin users
            if (!$user->isOwner()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Only owners can view admin users.',
                ], 403);
            }

            $admin = User::where('id', $id)
                ->where('role_id', 3)
                ->with('owner.toko')
                ->first();

            if (!$admin) {
                return response()->json([
                    'success' => false,
                    'message' => 'Admin user not found',
                ], 404);
            }

            $toko = $admin->owner && $admin->owner->toko ? $admin->owner->toko->first() : null;

            return response()->json([
                'success' => true,
                'message' => 'Admin user retrieved successfully',
                'data' => [
                    'id' => $admin->id,
                    'nama' => $admin->nama,
                    'email' => $admin->email,
                    'role_id' => $admin->role_id,
                    'store_id' => $toko ? $toko->id : null,
                    'store_name' => $toko ? $toko->nama : null,
                    'created_at' => $admin->created_at->format('Y-m-d H:i:s'),
                    'updated_at' => $admin->updated_at->format('Y-m-d H:i:s'),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve admin user: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update an admin user
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        try {
            $user = $request->user();

            // Only owners can update admin users
            if (!$user->isOwner()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Only owners can update admin users.',
                ], 403);
            }

            $admin = User::where('id', $id)
                ->where('role_id', 3)
                ->first();

            if (!$admin) {
                return response()->json([
                    'success' => false,
                    'message' => 'Admin user not found',
                ], 404);
            }

            // Validation
            $validator = Validator::make($request->all(), [
                'nama' => 'sometimes|required|string|max:255',
                'email' => 'sometimes|required|string|email|max:255|unique:pengguna,email,' . $id,
                'password' => 'sometimes|nullable|string|min:8|confirmed',
                'store_id' => 'nullable|exists:pos_toko,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422);
            }

            // Update admin user
            if ($request->has('nama')) {
                $admin->nama = $request->nama;
                $admin->slug = Str::slug($request->nama) . '-' . time();
            }

            if ($request->has('email')) {
                $admin->email = $request->email;
            }

            if ($request->has('password') && !empty($request->password)) {
                $admin->password = $request->password; // Will be hashed automatically
            }

            $admin->save();

            // Update store association if provided
            if ($request->has('store_id')) {
                $ownerRecord = $admin->owner;
                if ($ownerRecord) {
                    // Remove old store association
                    \DB::table('pos_toko')
                        ->where('owner_id', $ownerRecord->id)
                        ->update(['owner_id' => null]);

                    // Add new store association
                    if ($request->store_id) {
                        \DB::table('pos_toko')
                            ->where('id', $request->store_id)
                            ->update(['owner_id' => $ownerRecord->id]);
                    }
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Admin user updated successfully',
                'data' => [
                    'id' => $admin->id,
                    'nama' => $admin->nama,
                    'email' => $admin->email,
                    'role_id' => $admin->role_id,
                    'store_id' => $request->store_id,
                    'updated_at' => $admin->updated_at->format('Y-m-d H:i:s'),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update admin user: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete an admin user
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $id)
    {
        try {
            $user = $request->user();

            // Only owners can delete admin users
            if (!$user->isOwner()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Only owners can delete admin users.',
                ], 403);
            }

            $admin = User::where('id', $id)
                ->where('role_id', 3)
                ->first();

            if (!$admin) {
                return response()->json([
                    'success' => false,
                    'message' => 'Admin user not found',
                ], 404);
            }

            // Delete admin user
            $admin->delete();

            return response()->json([
                'success' => true,
                'message' => 'Admin user deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete admin user: ' . $e->getMessage(),
            ], 500);
        }
    }
}
