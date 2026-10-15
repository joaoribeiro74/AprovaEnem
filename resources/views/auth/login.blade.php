@extends('layouts.guest')

@section('content')
    <div class="flex flex-col w-full max-w-125 px-4 gap-8 sm:gap-10">
        
        <div class="flex items-center gap-3 sm:gap-5">
            <a href="{{ route('login') }}" class="flex-1 h-15 flex items-center justify-center gap-2.5 p-3 border-active border-b-3 uppercase font-semibold text-active text-lg sm:text-xl transition-colors">
                ENTRAR
            </a>
            
            <a href="{{ route('register') }}" class="flex-1 h-15 flex items-center justify-center gap-2.5 p-3 border-main border-b-3 uppercase font-semibold text-main text-lg sm:text-xl transition-colors">
                REGISTRAR
            </a>
        </div>

        <div class="flex flex-col gap-5 w-full">
            <x-flash-message />
            <form method="POST" action="{{ route('login') }}" class="flex flex-col gap-5">
                @csrf

                <div class="flex flex-col gap-1">
                    <label for="email" class="text-lg sm:text-lg font-semibold text-main uppercase">E-mail</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="Digite seu e-mail"
                        class="w-full h-12.5 px-3 py-2 border border-muted rounded-[10px] focus:outline-none focus:ring focus:ring-active/50 focus:border-active text-main transition-colors">
                </div>

                <div class="flex flex-col gap-1">
                    <label for="password" class="text-lg sm:text-lg font-semibold text-main uppercase">Senha</label>
                    
                    <div class="relative w-full">
                        <input id="password" type="password" name="password" placeholder="Digite sua senha"
                            class="w-full h-12.5 px-3 py-2 pr-12 border border-muted rounded-[10px] focus:outline-none focus:ring focus:ring-active/50 focus:border-active text-main transition-colors">
                        
                        <button type="button" data-password-toggle="password" aria-label="Mostrar senha" aria-pressed="false" class="absolute inset-y-0 right-3 flex items-center text-muted cursor-pointer">
                            <x-gmdi-visibility-off id="eye-off" class="w-6 h-6" data-icon-hidden />           
                            <x-gmdi-visibility id="eye-on" class="w-6 h-6 hidden" data-icon-visible />
                        </button>
                    </div>

                    <div class="flex justify-end cursor-pointer mt-1">
                        <a href="#" class="text-sm font-normal text-muted hover:text-active transition-colors underline">Esqueceu a senha?</a>
                    </div>
                </div>

                <button type="submit"
                    class="h-12.5 mt-2 bg-active hover:bg-active-hover text-white text-[20px] font-semibold py-2 px-6 rounded-[10px] shadow-2xl transition-colors cursor-pointer text-center w-full">
                    Entrar
                </button>
            </form>
        </div>
    </div>
@endsection