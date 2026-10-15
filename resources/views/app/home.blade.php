@extends('layouts.app')

@section('content')
    <div class="flex flex-col items-center justify-center w-full max-w-3xl mx-auto px-2.5 py-20 gap-8">
        <div class="text-center">
            <h1 class="text-3xl sm:text-4xl font-bold text-main">
                Bem-vindo(a), {{ Auth::user()->display_name }}!
            </h1>
            <p class="text-lg text-muted mt-2">
                Você acessou a área protegida do AprovaEnem.
            </p>
        </div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            
            <button type="submit" 
                class="flex items-center gap-2 bg-error hover:bg-red-700 text-white text-lg font-semibold py-3 px-8 rounded-[10px] transition-colors cursor-pointer drop-shadow-sm">
                <x-gmdi-logout class="w-6 h-6" />
                Sair
            </button>
        </form>

    </div>
@endsection