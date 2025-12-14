<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    use ApiResponseTrait;

    /**
     * Register a new user
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'avatar' => 'nullable|string',
            'banner' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'avatar' => $validated['avatar'] ?? null,
                'banner' => $validated['banner'] ?? null,
            ]);

            $token = $user->createToken('api_token')->plainTextToken;

            DB::commit();

            return $this->created([
                'user' => $user,
                'token' => $token,
            ], 'messages.user_registered');
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }


    /**
     * Login user
     */
    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (! $user || ! Hash::check($validated['password'], $user->password)) {
            return $this->unauthorized(__('messages.invalid_credentials'));
        }

        $user->tokens()->delete();

        $token = $user->createToken('api_token')->plainTextToken;

        return $this->ok([
            'user' => $user,
            'token' => $token,
        ], 'messages.login_success');
    }


    /**
     * Logout user
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return $this->ok(null, 'messages.logout_success');
    }

    /**
     * Show current user profile
     */
    public function profile(Request $request)
    {
        $user = $request->user();

        return $this->ok($user, 'messages.user_profile');
    }

    /**
     * Update profile
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'avatar' => 'sometimes|string',
            'banner' => 'sometimes|string',
            'bio' => 'sometimes|string',
            'birth_date' => 'sometimes|date',
            'password' => 'sometimes|string|min:8|confirmed',
        ]);

        try {
            DB::beginTransaction();

            if (isset($validated['password'])) {
                $validated['password'] = Hash::make($validated['password']);
            }

            if (isset($validated['name'])) {
                $validated['slug'] = $user->generateSlug(
                    $validated['name'],
                    app()->getLocale()
                );
            }

            $user->update($validated);

            DB::commit();

            return $this->ok($user, 'messages.profile_updated');
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
