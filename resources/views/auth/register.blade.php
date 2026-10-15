@extends('layouts.guest')

@section('content')
    <div class="flex flex-col w-full max-w-125 px-4 gap-8 sm:gap-10">
        
        <div class="flex items-center gap-3 sm:gap-5">
            <a href="{{ route('login') }}" class="flex-1 h-15 flex items-center justify-center gap-2.5 p-3 border-main border-b-3 uppercase font-semibold text-main text-lg sm:text-xl transition-colors">
                ENTRAR
            </a>
            
            <a href="{{ route('register') }}" class="flex-1 h-15 flex items-center justify-center gap-2.5 p-3 border-active border-b-3 uppercase font-semibold text-active text-lg sm:text-xl transition-colors">
                REGISTRAR
            </a>
        </div>

        <div class="flex flex-col gap-5 w-full">
            <form method="POST" action="{{ route('register.store') }}" class="flex flex-col gap-5" novalidate>
                @csrf

                <div class="flex flex-col sm:flex-row gap-5">
                    <div class="flex flex-col gap-1 w-full">
                        <label for="name" class="text-lg sm:text-lg font-semibold text-main uppercase">Nome</label>
                        <input id="name" type="text" name="name" value="{{ old('name') }}" placeholder="Seu nome" required
                            @class([
                                'w-full h-12.5 px-3 py-2 border rounded-[10px] focus:outline-none focus:ring focus:ring-active/50 focus:border-active text-main transition-colors',
                                'border-error' => $errors->has('name'),
                                'border-muted' => !$errors->has('name'),
                            ])>
                        @error('name')
                            <span class="text-xs font-medium text-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="flex flex-col gap-1 w-full">
                        <label for="last_name" class="text-lg sm:text-lg font-semibold text-main uppercase">Sobrenome</label>
                        <input id="last_name" type="text" name="last_name" value="{{ old('last_name') }}" placeholder="Seu sobrenome" required
                            @class([
                                'w-full h-12.5 px-3 py-2 border rounded-[10px] focus:outline-none focus:ring focus:ring-active/50 focus:border-active text-main transition-colors',
                                'border-error' => $errors->has('last_name'),
                                'border-muted' => !$errors->has('last_name'),
                            ])>
                        @error('last_name')
                            <span class="text-xs font-medium text-error">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="flex flex-col gap-1">
                    <label for="nickname" class="text-lg sm:text-lg font-semibold text-main">APELIDO (Opcional)</label>
                    <input id="nickname" type="text" name="nickname" value="{{ old('nickname') }}" placeholder="Como prefere ser chamado?"
                        @class([
                            'w-full h-12.5 px-3 py-2 border rounded-[10px] focus:outline-none focus:ring focus:ring-active/50 focus:border-active text-main transition-colors',
                            'border-error' => $errors->has('nickname'),
                            'border-muted' => !$errors->has('nickname'),
                        ])>
                    @error('nickname')
                        <span class="text-xs font-medium text-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="flex flex-col gap-1">
                    <label for="email" class="text-lg sm:text-lg font-semibold text-main uppercase">E-mail</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="nome@email.com" required
                        @class([
                            'w-full h-12.5 px-3 py-2 border rounded-[10px] focus:outline-none focus:ring focus:ring-active/50 focus:border-active text-main transition-colors',
                            'border-error' => $errors->has('email'),
                            'border-muted' => !$errors->has('email'),
                        ])>
                    @error('email')
                        <span class="text-xs font-medium text-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="flex flex-col gap-1">
                    <label for="phone" class="text-lg sm:text-lg font-semibold text-main uppercase">Telefone</label>
                    <input id="phone" type="tel" name="phone" data-mask="phone" inputmode="tel" autocomplete="tel-national" value="{{ old('phone') }}" placeholder="(11) 99999-9999" required maxlength="15"
                        @class([
                            'w-full h-12.5 px-3 py-2 border rounded-[10px] focus:outline-none focus:ring focus:ring-active/50 focus:border-active text-main transition-colors',
                            'border-error' => $errors->has('phone'),
                            'border-muted' => !$errors->has('phone'),
                        ])>
                    @error('phone')
                        <span class="text-xs font-medium text-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="flex flex-col gap-1">
                    <label for="password" class="text-lg sm:text-lg font-semibold text-main uppercase">Senha</label>
                    <div class="relative w-full">
                        <input id="password" type="password" name="password" placeholder="Crie uma senha forte" required autocomplete="new-password"
                            @class([
                                'w-full h-12.5 px-3 py-2 pr-12 border rounded-[10px] focus:outline-none focus:ring focus:ring-active/50 focus:border-active text-main transition-colors',
                                'border-error' => $errors->has('password'),
                                'border-muted' => !$errors->has('password'),
                            ])>
                        
                        <button type="button" data-password-toggle="password" aria-label="Mostrar senha" class="absolute inset-y-0 right-3 flex items-center text-muted cursor-pointer">
                            <x-gmdi-visibility-off class="w-6 h-6" data-icon-hidden />           
                            <x-gmdi-visibility class="w-6 h-6 hidden" data-icon-visible />
                        </button>
                    </div>
                    <p class="text-[10px] text-muted">Mínimo de 8 caracteres, com maiúscula, minúscula, número e caractere especial.</p>
                    @error('password')
                        <span class="text-xs font-medium text-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="flex flex-col gap-1">
                    <label for="password_confirmation" class="text-lg sm:text-lg font-semibold text-main uppercase">Confirme sua senha</label>
                    <div class="relative w-full">
                        <input id="password_confirmation" type="password" name="password_confirmation" placeholder="Repita a senha" required autocomplete="new-password"
                            @class([
                                'w-full h-12.5 px-3 py-2 pr-12 border rounded-[10px] focus:outline-none focus:ring focus:ring-active/50 focus:border-active text-main transition-colors',
                                'border-error' => $errors->has('password'),
                                'border-muted' => !$errors->has('password'),
                            ])>
                        
                        <button type="button" data-password-toggle="password_confirmation" aria-label="Mostrar senha" class="absolute inset-y-0 right-3 flex items-center text-muted cursor-pointer">
                            <x-gmdi-visibility-off class="w-6 h-6" data-icon-hidden />           
                            <x-gmdi-visibility class="w-6 h-6 hidden" data-icon-visible />
                        </button>
                    </div>
                </div>

                <button type="submit"
                    class="h-12.5 mt-2 mb-4 bg-active hover:bg-active-hover text-white text-[20px] font-semibold py-2 px-6 rounded-[10px] shadow-2xl transition-colors cursor-pointer text-center w-full">
                    Registrar
                </button>
            </form>
        </div>
    </div>
@endsection