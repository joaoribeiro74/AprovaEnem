<?php

namespace App\Services\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginService
{
    public function __construct(private LoginRateLimiter $rateLimiter) {}

    public function attempt(string $email, string $password, string $ip, bool $remember = false): void
    {
        $this->rateLimiter->ensureNotLimited($email, $ip);

        if (! Auth::attempt(['email' => $email, 'password' => $password], $remember)) {
            $this->rateLimiter->hit($email, $ip);

            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        $this->rateLimiter->clear($email, $ip);
    }
}
