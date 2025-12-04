<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use DB;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): Response
    {
        $request->authenticate();

        $request->session()->regenerate();

        return response()->noContent();
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): Response
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return response()->noContent();
    }

    public function storeMobile(LoginRequest $request): JsonResponse
    {
        $request->authenticate();

        $user = $request->user();
        $remember = $request->boolean('remember');

        // Tentukan expiry berdasarkan remember me
        $accessTokenExpiry = now()->addMinutes(15); // Short-lived
        $refreshTokenExpiry = $remember ? now()->addDays(30) : now()->addDays(7);

        // Create tokens dengan scope yang tepat
        $accessToken = $user->createToken(
            'access-token',
            ['*'], // Full access
            $accessTokenExpiry
        )->plainTextToken;

        $refreshToken = $user->createToken(
            'refresh-token',
            ['refresh'], // Hanya bisa refresh
            $refreshTokenExpiry
        )->plainTextToken;

        return response()->json([
            'user' => $user,
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
            'token_type' => 'Bearer',
            'expires_in' => 900, // 15 menit dalam detik
            'refresh_expires_in' => $remember ? 2592000 : 604800 // 30 atau 7 hari
        ]);
    }

    /**
     * Refresh access token.
     */
    public function refresh(Request $request): JsonResponse
    {
        $request->validate([
            'refresh_token' => 'required|string'
        ]);

        // Cek apakah refresh token valid
        $token = DB::table('personal_access_tokens')
            ->where('token', hash('sha256', $request->refresh_token))
            ->where('abilities', '["refresh"]')
            ->where('expires_at', '>', now())
            ->first();

        if (!$token) {
            return response()->json([
                'message' => 'Invalid or expired refresh token'
            ], 401);
        }

        // ✅ PERBAIKAN: Gunakan find() langsung, bukan loginUsingId()
        $user = \App\Models\User::find($token->tokenable_id);

        if (!$user) {
            return response()->json([
                'message' => 'User not found'
            ], 401);
        }

        // Revoke token lama yang sudah dipakai
        DB::table('personal_access_tokens')
            ->where('id', $token->id)
            ->delete();

        // Buat tokens baru
        $newAccessToken = $user->createToken(
            'access-token',
            ['*'],
            now()->addMinutes(15)
        )->plainTextToken;

        $newRefreshToken = $user->createToken(
            'refresh-token',
            ['refresh'],
            now()->addDays(30) // Reset refresh token expiry
        )->plainTextToken;

        return response()->json([
            'access_token' => $newAccessToken,
            'refresh_token' => $newRefreshToken,
            'token_type' => 'Bearer',
            'expires_in' => 900
        ]);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroyMobile(Request $request): JsonResponse
    {
        // Hapus semua tokens user (logout dari semua devices)
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Successfully logged out from all devices'
        ]);
    }

    /**
     * Logout from current device only.
     */
    public function logoutCurrent(Request $request): JsonResponse
    {
        // Hapus token current saja
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }
}
