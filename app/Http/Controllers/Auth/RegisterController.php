<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Services\Auth\RegisterService;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    public function create()
    {
        return view('auth.register');
    }

    public function store(RegisterRequest $request, RegisterService $register)
    {
        $user = $register->register($request->validated());

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('inicio');
    }
}
