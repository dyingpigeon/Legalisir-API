<?php

use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\SignedRegisterController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\PermohonanController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

// Public routes (tanpa authentication)
Route::middleware(['guest'])->group(function () {
    Route::post('/login', [AuthenticatedSessionController::class, 'storeMobile'])->name('login');
    Route::post('/registerwah', [SignedRegisterController::class, 'askForRegister'])->name('register');

    // Verify email - biasanya public karena diakses via link email
    Route::post('/verify-email/{id}/{hash}', [VerifyEmailController::class, '__invoke'])
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');
});

Route::post('/refresh', [AuthenticatedSessionController::class, 'refresh'])
    ->middleware('guest') // Butuh valid token untuk refresh
    ->name('token.refresh');

// Protected routes dengan sanctum
Route::middleware(['auth:sanctum'])->group(function () {
    // Token refresh - butuh token yang valid

    // Logout
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroyMobile'])->name('logout');

    // Approval routes
    Route::prefix('permohonan')->group(function () {
        Route::post('/create', [PermohonanController::class, 'buatPermohonan']);
        Route::post('/operator/{id}', [ApprovalController::class, 'verifiedByOperator']);
        Route::post('/wadir/{id}', [ApprovalController::class, 'signedByWadir']);
        Route::post('/ready/{id}', [ApprovalController::class, 'markAsReady']);
        Route::post('/done/{id}', [ApprovalController::class, 'markAsDone']);
        Route::post('/reject/{id}', [ApprovalController::class, 'reject']);
        Route::get('/riwayat/{permohonanId}', [ApprovalController::class, 'getRiwayat']);
        Route::get('/show', [ApprovalController::class, 'getPermohonanByStatus']);
    });

    Route::post('/permohonan-debug', function (Request $request) {
        return response()->json([
            'message' => 'Debug route works',
            'user' => $request->user(),
            'auth' => auth()->check()
        ]);
    });

    // API Resources
    Route::apiResource('/permohonan', \App\Http\Controllers\PermohonanController::class);
    Route::apiResource('/riwayat-status', \App\Http\Controllers\RiwayatStatusController::class);
    Route::apiResource('/ijazah', \App\Http\Controllers\IjazahController::class);
    Route::apiResource('/operator', \App\Http\Controllers\OperatorController::class);

    // Data Alumni routes
    Route::apiResource('/data-alumni', \App\Http\Controllers\DataAlumniController::class);
    Route::post('/data-alumni/search', [\App\Http\Controllers\DataAlumniController::class, 'search']);
    Route::get('/data-alumni/debug-search', [\App\Http\Controllers\DataAlumniController::class, 'debugSearch']);
    Route::get('/test-log', [\App\Http\Controllers\DataAlumniController::class, 'testLog']);
});