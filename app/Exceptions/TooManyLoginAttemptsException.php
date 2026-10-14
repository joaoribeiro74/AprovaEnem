<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Request;

class TooManyLoginAttemptsException extends Exception
{
    public function __construct(public readonly int $seconds) 
    {
        parent::__construct(__('auth.throttle', ['seconds' => $seconds]));
    }

    public function render(Request $request)
    {
        return back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => $this->getMessage()])
            ->with('retry_after', $this->seconds);
    }
}
