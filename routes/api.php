<?php

use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\SignedRegisterController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\DataAlumniController;
use App\Http\Controllers\IjazahController;
use App\Http\Controllers\OperatorController;
use App\Http\Controllers\PermohonanController;
use App\Http\Controllers\RiwayatStatusController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

// Public routes (tanpa authentication)
// Route::middleware(['guest'])->group(function () {
    Route::post('/login', [AuthenticatedSessionController::class, 'storeMobile'])->name('login');
    Route::post('/register', [SignedRegisterController::class, 'askForRegister'])->name('register');

    // Verify email - biasanya public karena diakses via link email
    Route::post('/verify-email/{id}/{hash}', [VerifyEmailController::class, '__invoke'])
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');
// });

Route::post('/refresh', [AuthenticatedSessionController::class, 'refresh']);

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
        Route::post('/cancel/{id}', [ApprovalController::class, 'cancel']);
        Route::get('/riwayat/{permohonanId}', [PermohonanController::class, 'getRiwayat']);
        Route::get('/show', [PermohonanController::class, 'getPermohonanByStatus']);
    });

    // API Resources
    Route::apiResource('/permohonan', PermohonanController::class);
    Route::apiResource('/riwayat-status', RiwayatStatusController::class);
    Route::apiResource('/ijazah', IjazahController::class);
    Route::apiResource('/operator', OperatorController::class);

    // Data Alumni routes
    Route::apiResource('/data-alumni', DataAlumniController::class);
    Route::post('/data-alumni/search', [DataAlumniController::class, 'search']);
    Route::get('/data-alumni/debug-search', [DataAlumniController::class, 'debugSearch']);
    Route::get('/test-log', [DataAlumniController::class, 'testLog']);
});