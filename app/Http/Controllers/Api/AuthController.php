<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\PosPengguna;
use App\Models\Owner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Register new user
     * Membuat akun di tabel pengguna dan sekaligus membuat owner
     */
    public function register(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:pengguna,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        try {
            // Create user di tabel pengguna
            $user = User::create([
                'nama' => $request->nama,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'slug' => \Illuminate\Support\Str::slug($request->nama . '-' . time()),
                'role_id' => 2, // Owner role
                'email_is_verified' => false,
            ]);

            // Create owner untuk user tersebut
            $owner = Owner::create([
                'pengguna_id' => $user->id,
            ]);

            // Generate token untuk mobile app
            $token = $user->createToken('mobile-app')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Registrasi berhasil',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'nama' => $user->nama,
                        'email' => $user->email,
                        'role_id' => $user->role_id,
                        'is_owner' => true,
                        'owner_id' => $owner->id,
                    ],
                    'token' => $token,
                    'token_type' => 'Bearer',
                ],
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Registrasi gagal: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Login user
     * Bisa login dengan data dari pengguna atau pos_pengguna
     * Tapi harus terdaftar sebagai owner
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $email = $request->email;
        $password = $request->password;

        // Coba cari di tabel pengguna (users) dulu
        $user = User::where('email', $email)->first();
        $isPosUser = false;
        $userId = null;

        if ($user && Hash::check($password, $user->password)) {
            // User ditemukan di tabel pengguna
            $userId = $user->id;
            
            // Validasi apakah user ini terdaftar sebagai owner
            $owner = Owner::where('pengguna_id', $userId)->first();
            
            if (!$owner) {
                throw ValidationException::withMessages([
                    'email' => ['Akun Anda tidak memiliki akses sebagai owner.'],
                ]);
            }

            // Generate token
            $token = $user->createToken('mobile-app')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Login berhasil',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'nama' => $user->nama,
                        'email' => $user->email,
                        'role_id' => $user->role_id,
                        'is_owner' => true,
                        'owner_id' => $owner->id,
                    ],
                    'token' => $token,
                    'token_type' => 'Bearer',
                ],
            ], 200);
        }

        // Jika tidak ditemukan di pengguna, cari di pos_pengguna
        $posUser = PosPengguna::where('email', $email)->first();

        if ($posUser && Hash::check($password, $posUser->password)) {
            // POS User ditemukan
            $isPosUser = true;
            
            // Validasi apakah owner_id dari pos_pengguna ada
            if (!$posUser->owner_id) {
                throw ValidationException::withMessages([
                    'email' => ['Akun POS Anda tidak terhubung dengan owner.'],
                ]);
            }

            // Validasi owner_id valid
            $owner = Owner::find($posUser->owner_id);
            if (!$owner) {
                throw ValidationException::withMessages([
                    'email' => ['Owner tidak ditemukan.'],
                ]);
            }

            // Untuk POS User, kita perlu membuat token menggunakan User model
            // Cari user berdasarkan owner->pengguna_id
            $mainUser = User::find($owner->pengguna_id);
            
            if (!$mainUser) {
                throw ValidationException::withMessages([
                    'email' => ['Data user owner tidak ditemukan.'],
                ]);
            }

            // Generate token menggunakan main user
            $token = $mainUser->createToken('mobile-app-pos-user')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Login berhasil',
                'data' => [
                    'user' => [
                        'id' => $posUser->id,
                        'nama' => $posUser->nama,
                        'email' => $posUser->email,
                        'role_id' => $posUser->pos_role_id,
                        'is_pos_user' => true,
                        'owner_id' => $posUser->owner_id,
                        'toko_id' => $posUser->pos_toko_id,
                    ],
                    'token' => $token,
                    'token_type' => 'Bearer',
                ],
            ], 200);
        }

        // Jika tidak ditemukan atau password salah
        throw ValidationException::withMessages([
            'email' => ['Email atau password salah.'],
        ]);
    }

    /**
     * Logout user
     */
    public function logout(Request $request)
    {
        // Revoke all tokens
        $request->user()->tokens()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil',
        ], 200);
    }

    /**
     * Get authenticated user info
     */
    public function me(Request $request)
    {
        $user = $request->user();
        
        // Check if this is owner
        $owner = Owner::where('pengguna_id', $user->id)->first();

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'nama' => $user->nama,
                'email' => $user->email,
                'role_id' => $user->role_id,
                'is_owner' => $owner ? true : false,
                'owner_id' => $owner ? $owner->id : null,
            ],
        ], 200);
    }
}
