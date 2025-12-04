<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\PasswordReset;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    // public function __invoke(EmailVerificationRequest $request): RedirectResponse
    // {
    //     if ($request->user()->hasVerifiedEmail()) {
    //         return redirect()->intended(
    //             config('app.frontend_url').'/dashboard?verified=1'
    //         );
    //     }

    //     if ($request->user()->markEmailAsVerified()) {
    //         event(new Verified($request->user()));
    //     }

    //     return redirect()->intended(
    //         config('app.frontend_url').'/dashboard?verified=1'
    //     );
    // }

    public function __invoke(Request $request): JsonResponse
    {
        // Validasi signature terlebih dahulu
        if (!$request->hasValidSignature()) {
            return response()->json([
                'message' => 'Invalid or expired verification link'
            ], 403);
        }

        // Cari user berdasarkan ID
        $user = User::find($request->route('id'));

        if (!$user) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }

        // Validasi hash email
        if (!hash_equals((string) $request->route('hash'), sha1($user->email))) {
            return response()->json([
                'message' => 'Invalid verification link'
            ], 403);
        }

        if ($user->verification_link_used_at) {
            return response()->json([
                'message' => 'This verification link has already been used'
            ], 403);
        }

        // Validasi input password
        $request->validate([
            'password' => ['required', 'confirmed', Rules\password::defaults()],
        ]);

        \Log::info('BEFORE UPDATE - verification_link_used_at:', ['value' => $user->verification_link_used_at]);

        // SEMUA UPDATE DALAM SATU QUERY
        $user->update([
            'password' => Hash::make($request->password),
            'remember_token' => Str::random(60),
            'verification_link_used_at' => now(),
            'email_verified_at' => $user->email_verified_at ?? now()
        ]);

        // Trigger events setelah update berhasil
        if (!$user->hasVerifiedEmail()) {
            event(new Verified($user));
        }

        event(new PasswordReset($user));

        \Log::info('UPDATE RESULT:');

        // Refresh dari database
        $user->refresh();
        \Log::info('AFTER REFRESH - verification_link_used_at:', ['value' => $user->verification_link_used_at]);

        return response()->json([
            'message' => 'Email verified and password set successfully',
            'verified' => true,
            'password_set' => true
        ]);
    }
}
