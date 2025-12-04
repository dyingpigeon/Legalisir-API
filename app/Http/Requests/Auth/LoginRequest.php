<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Get the login field based on input.
     */
    public function getLoginField(): string
    {
        $login = $this->input('login');
        
        // Cek apakah input adalah email
        if (filter_var($login, FILTER_VALIDATE_EMAIL)) {
            return 'email';
        }
        
        // Cek apakah input adalah NIK (16 digit)
        if (is_numeric($login) && strlen($login) === 16) {
            return 'nik';
        }
        
        // Cek apakah input adalah username (10 digit)
        if (is_numeric($login) && strlen($login) === 10) {
            return 'username';
        }
        
        // Default ke username (untuk kasus non-numeric atau panjang lain)
        return 'username';
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $loginField = $this->getLoginField();
        $loginValue = $this->input('login');

        $credentials = [
            $loginField => $loginValue,
            'password' => $this->input('password')
        ];

        if (!Auth::attempt($credentials, $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'login' => __('auth.failed'),
            ]);
        }

        if (!Auth::user()->hasVerifiedEmail()) {
            Auth::logout();
            throw ValidationException::withMessages([
                'login' => 'Please verify your email before logging in.',
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'login' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->input('login')) . '|' . $this->ip());
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'login' => 'email, username, atau NIK',
        ];
    }
}