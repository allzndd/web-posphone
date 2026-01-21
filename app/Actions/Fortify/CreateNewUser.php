<?php

namespace App\Actions\Fortify;

use App\Models\User;
use App\Models\Owner;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Illuminate\Support\Str;

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
            'email_is_verified' => 1, // Email verified by default
        ]);

        // Create owner record untuk user tersebut
        Owner::create([
            'pengguna_id' => $user->id,
        ]);

        return $user;
    }
}
