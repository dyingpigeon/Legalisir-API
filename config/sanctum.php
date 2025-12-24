<?php

use Laravel\Sanctum\Sanctum;

return [

    /*
    |--------------------------------------------------------------------------
    | Stateful Domains
    |--------------------------------------------------------------------------
    |
    | Untuk token-based API, kita bisa set ke array kosong atau null
    | karena tidak menggunakan session cookies
    |
    */

    'stateful' => [], // ← KOSONGKAN untuk token-based API

    /*
    |--------------------------------------------------------------------------
    | Sanctum Guards
    |--------------------------------------------------------------------------
    |
    | Untuk token-based, kita bisa tetap pakai web guard atau sesuaikan
    |
    */

    'guard' => ['web'],

    /*
    |--------------------------------------------------------------------------
    | Expiration Minutes
    |--------------------------------------------------------------------------
    |
    | Set expiration untuk token (dalam menit)
    | null = tidak expired (tidak disarankan)
    |
    */

    'expiration' => 15, // ← 15 menit untuk access token

    /*
    |--------------------------------------------------------------------------
    | Token Prefix
    |--------------------------------------------------------------------------
    |
    | Bisa diisi sesuai kebutuhan
    |
    */

    'token_prefix' => env('SANCTUM_TOKEN_PREFIX', ''),

    /*
    |--------------------------------------------------------------------------
    | Sanctum Middleware
    |--------------------------------------------------------------------------
    |
    | Untuk token-based API, hapus middleware yang berhubungan dengan session
    |
    */

    'middleware' => [
        // HAPUS middleware session & CSRF untuk token-based
        // 'authenticate_session' => Laravel\Sanctum\Http\Middleware\AuthenticateSession::class,
        // 'encrypt_cookies' => Illuminate\Cookie\Middleware\EncryptCookies::class,
        // 'validate_csrf_token' => Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class,
    ],

];