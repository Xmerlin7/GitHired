<?php

namespace App\Services;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function register(array $data): array
    {
        return DB::transaction(function () use ($data) {

            // Create User
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role' => $data['role'],
            ]);

            // Create Profile
            $this->createProfile($user, $data);

            // Generate Token
            $token = $user->createToken('auth_token')->plainTextToken;

            // Load appropriate profile
            $relationName = $user->role === UserRole::EMPLOYER
                ? 'employerProfile'
                : 'candidateProfile';

            $user->load($relationName);

            return [
                'user' => $user,
                'token' => $token,
            ];
        });

    }

    /**
     * Login
     */
    public function login(array $credentials): array
    {
        // fetch user by email
        $user = User::where('email', $credentials['email'])->first();
        // ensure user exists and password matches
        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'message' => 'Login Credentials are not valid',
            ]);
        }
        $token = $user->createToken('auth_token')->plainTextToken;
        $relationName = $user->role === UserRole::EMPLOYER
                        ? 'employerProfile'
                        : 'candidateProfile';

        $user->load($relationName);

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    /**
     * Logout Current Session
     */
    public function logout(User $user): void
    {
        $user->currentAccessToken()->delete();

    }

    /**
     * Logout All active Sessions
     */
    public function logoutAll(User $user): void
    {
        $user->tokens()->delete();

    }

    /**
     * Create profile based on user role
     */
    protected function createProfile(User $user, array $data): void
    {
        if ($user->role === UserRole::EMPLOYER) {
            $user->employerProfile()->create([
                'company_name' => $data['company_name'],
                'company_bio' => $data['company_bio'] ?? null,
                'website' => $data['website'] ?? null,
                'company_logo_url' => $data['company_logo_url'] ?? null,
            ]);
        } elseif ($user->role === UserRole::CANDIDATE) {
            $user->candidateProfile()->create([
                'resume_url' => $data['resume_url'] ?? null,
                'phone_number' => $data['phone_number'] ?? null,
                'portfolio_link' => $data['portfolio_link'] ?? null,
                'years_experience' => $data['years_experience'] ?? 0,
                'skills' => $data['skills'] ?? [],
            ]);
        }
    }
}
