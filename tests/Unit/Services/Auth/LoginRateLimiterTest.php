<?php

namespace Tests\Unit\Services\Auth;

use App\Exceptions\TooManyLoginAttemptsException;
use App\Services\Auth\LoginRateLimiter;
use Tests\TestCase;

class LoginRateLimiterTest extends TestCase
{
    private LoginRateLimiter $limiter;

    protected function setUp(): void
    {
        parent::setUp();

        $this->limiter = $this->app->make(LoginRateLimiter::class);
    }

    public function test_allows_up_to_four_failed_attempts(): void
    {
        for ($i = 0; $i < 4; $i++) {
            $this->limiter->hit('joao@example.com', '127.0.0.1');
        }

        $this->limiter->ensureNotLimited('joao@example.com', '127.0.0.1');

        $this->addToAssertionCount(1);
    }

    public function test_blocks_after_five_failed_attempts(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->limiter->hit('joao@example.com', '127.0.0.1');
        }

        try {
            $this->limiter->ensureNotLimited('joao@example.com', '127.0.0.1');
            $this->fail('Expected TooManyLoginAttemptsException.');
        } catch (TooManyLoginAttemptsException $e) {
            $this->assertGreaterThan(0, $e->seconds);
        }
    }

    public function test_clear_resets_the_counter(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->limiter->hit('joao@example.com', '127.0.0.1');
        }

        $this->limiter->clear('joao@example.com', '127.0.0.1');
        $this->limiter->ensureNotLimited('joao@example.com', '127.0.0.1');

        $this->addToAssertionCount(1);
    }

    public function test_email_is_case_insensitive(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->limiter->hit('JOAO@example.com', '127.0.0.1');
        }

        $this->expectException(TooManyLoginAttemptsException::class);

        $this->limiter->ensureNotLimited('joao@example.com', '127.0.0.1');
    }

    public function test_different_ips_have_separate_counters(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->limiter->hit('joao@example.com', '10.0.0.1');
        }

        $this->limiter->ensureNotLimited('joao@example.com', '10.0.0.2');

        $this->addToAssertionCount(1);
    }
}
