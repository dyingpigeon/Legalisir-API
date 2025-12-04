<?php

use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\SignedRegisterController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('approval')->group(function () {
    Route::post('/operator/{id}', [ApprovalController::class, 'approveByOperator']);
    Route::post('/wadir/{id}', [ApprovalController::class, 'approveByWadir']);
    Route::post('/sign/{id}', [ApprovalController::class, 'markAsSigned']);
    Route::post('/ready/{id}', [ApprovalController::class, 'markAsReady']);
    Route::post('/reject/{id}', [ApprovalController::class, 'reject']);
    Route::get('/riwayat/{permohonanId}', [ApprovalController::class, 'getRiwayat']);
    Route::get('/permohonan', [ApprovalController::class, 'getPermohonanByStatus']);
});

Route::apiResource('/permohonan', \App\Http\Controllers\PermohonanController::class);
Route::apiResource('/riwayat-status', \App\Http\Controllers\RiwayatStatusController::class);
Route::apiResource('/ijazah', \App\Http\Controllers\IjazahController::class);
Route::apiResource('/operator', \App\Http\Controllers\OperatorController::class);
Route::apiResource('/data-alumni', \App\Http\Controllers\DataAlumniController::class);
Route::post('/data-alumni/search', [\App\Http\Controllers\DataAlumniController::class, 'search']);
Route::get('/data-alumni/debug-search', [\App\Http\Controllers\DataAlumniController::class, 'debugSearch']); // untuk debugging
Route::get('/test-log', [\App\Http\Controllers\DataAlumniController::class, 'testLog']);


Route::post('/registerwah', [SignedRegisterController::class, 'askForRegister'])
    ->middleware('guest')
    ->name('register');

Route::post('/verify-email/{id}/{hash}', [VerifyEmailController::class, '__invoke'])
    ->middleware(['signed', 'throttle:6,1'])
    ->name('verification.verify');

Route::post('/login', [AuthenticatedSessionController::class, 'storeMobile'])
    ->middleware('guest')
    ->name('login');

Route::post('/logout', [AuthenticatedSessionController::class, 'destroyMobilr'])
    ->middleware('auth')
    ->name('logout');