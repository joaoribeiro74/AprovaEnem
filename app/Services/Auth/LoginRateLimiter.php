<?php

namespace App\Services\Auth;

use App\Exceptions\TooManyLoginAttemptsException;
use Illuminate\Cache\RateLimiter;
use Illuminate\Support\Str;

class LoginRateLimiter
{
    private const MAX_ATTEMPTS = 5;

    private const DECAY_SECONDS = 60;

    public function __construct(private RateLimiter $rateLimiter) {}

    public function ensureNotLimited(string $email, string $ip): void
    {
        $key = $this->key($email, $ip);

        if ($this->rateLimiter->tooManyAttempts($key, self::MAX_ATTEMPTS)) {
            throw new TooManyLoginAttemptsException($this->rateLimiter->availableIn($key));
        }
    }

    public function hit(string $email, string $ip): void
    {
        $this->rateLimiter->hit($this->key($email, $ip), self::DECAY_SECONDS);
    }

    public function clear(string $email, string $ip): void
    {
        $this->rateLimiter->clear($this->key($email, $ip));
    }

    private function key(string $email, string $ip): string
    {
        return Str::lower($email).'|'.$ip;
    }
}
