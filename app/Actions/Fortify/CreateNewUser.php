<?php

namespace App\Actions\Fortify;

use App\Models\User;
use App\Models\Owner;
use App\Models\Langganan;
use App\Models\TipeLayanan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Illuminate\Support\Str;
use Carbon\Carbon;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique(User::class),
            ],
            'password' => $this->passwordRules(),
        ])->validate();

        // Create user dengan semua field yang diperlukan
        $user = User::create([
            'nama' => $input['name'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
            'slug' => Str::slug($input['name'] . '-' . time()),
            'role_id' => 2, // Role Owner
            'email_is_verified' => 0, // Email not verified initially
        ]);

        // Create owner record untuk user tersebut
        $owner = Owner::create([
            'pengguna_id' => $user->id,
        ]);

        try {
            // Auto-create or get trial package
            // Check if durasi_satuan column exists by checking Model's fillable
            $trialData = [
                'nama' => 'Trial',
                'harga' => 0,
                'durasi' => 15,
            ];
            
            // Only add durasi_satuan if it's in the fillable array (column exists)
            if (in_array('durasi_satuan', (new TipeLayanan())->getFillable())) {
                $trialData['durasi_satuan'] = 'hari';
            }
            
            $trialPackage = TipeLayanan::firstOrCreate(
                ['slug' => 'trial'],
                $trialData
            );
            
            // Auto-create trial subscription for 15 days
            $startDate = Carbon::now();
            $endDate = Carbon::now()->addDays(15);

            $langganan = Langganan::create([
                'owner_id' => $owner->id,
                'tipe_layanan_id' => $trialPackage->id,
                'is_trial' => 1,
                'is_active' => 1, // ✅ Auto-activate trial subscription
                'started_date' => $startDate,
                'end_date' => $endDate,
            ]);

            \Log::info('Trial subscription created', [
                'owner_id' => $owner->id,
                'langganan_id' => $langganan->id,
                'trial_package_id' => $trialPackage->id,
            ]);

        } catch (\Exception $e) {
            \Log::error('Failed to create trial subscription: ' . $e->getMessage(), [
                'owner_id' => $owner->id,
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }

        return $user;
    }
}
