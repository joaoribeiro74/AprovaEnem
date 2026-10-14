@extends('layouts.app')

@section('content')
    <div class="flex flex-col items-center justify-center min-h-screen p-6">
        <div class="bg-white border border-border p-8 rounded-lg shadow-sm max-w-lg w-full text-center">
            <h1 class="text-3xl font-bold text-main mb-2">
                AprovaEnem
            </h1>
            
            <p class="text-muted mb-6">
                Testando a paleta oficial de cores do projeto.
            </p>

            <div class="flex justify-center gap-4 mb-8 text-sm font-medium">
                <span class="text-success bg-success/10 px-3 py-1 rounded-full">Sistema Online</span>
                <span class="text-alert bg-alert/10 px-3 py-1 rounded-full">Atenção</span>
                <span class="text-error bg-error/10 px-3 py-1 rounded-full">Erro 404</span>
            </div>

            <a href="{{ route('login') }}" class="inline-block bg-active hover:bg-active-hover text-white font-semibold py-2 px-6 rounded transition-colors cursor-pointer text-center uppercase">
                Login
            </a>
        </div>
    </div>
@endsection