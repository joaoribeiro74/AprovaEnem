@if ($errors->any())
    <div id="flash-error" data-flash class="flex items-center p-4 w-full bg-white border-l-4 border-error rounded-lg shadow-sm transition-opacity duration-500" role="alert">
        <div class="inline-flex items-center justify-center shrink-0 w-8 h-8 text-error bg-error/10 rounded-lg">
            <x-gmdi-error class="w-6 h-6" />
        </div>

        <div class="ml-3 text-sm font-medium text-main"
            @if (session('retry_after'))
                data-countdown="{{ session('retry_after') }}"
                data-template="{{ __('auth.throttle', ['seconds' => ':seconds']) }}"
                data-done="{{ __('auth.throttle_done') }}"
            @endif
        >
            {{ $errors->first() }}
        </div>

        <button type="button" data-flash-close aria-label="Fechar mensagem"
            class="ml-auto -mx-1.5 -my-1.5 bg-white text-muted hover:text-main rounded-lg p-1.5 hover:bg-base inline-flex items-center justify-center h-8 w-8 cursor-pointer transition-colors">
            <x-gmdi-close class="w-5 h-5" />
        </button>
    </div>

    @if (session('retry_after'))
        <template id="flash-success-template">
            <div data-flash class="flex items-center p-4 w-full bg-white border-l-4 border-success rounded-lg shadow-sm transition-opacity duration-500" role="alert">
                <div class="inline-flex items-center justify-center shrink-0 w-8 h-8 text-success bg-success/10 rounded-lg">
                    <x-gmdi-check-circle class="w-6 h-6" />
                </div>

                <div class="ml-3 text-sm font-medium text-main" data-flash-text></div>

                <button type="button" data-flash-close aria-label="Fechar mensagem"
                    class="ml-auto -mx-1.5 -my-1.5 bg-white text-muted hover:text-main rounded-lg p-1.5 hover:bg-base inline-flex items-center justify-center h-8 w-8 cursor-pointer transition-colors">
                    <x-gmdi-close class="w-5 h-5" />
                </button>
            </div>
        </template>
    @endif
@endif

@if (session('success'))
    <div id="flash-success" data-flash class="flex items-center p-4 w-full bg-white border-l-4 border-success rounded-lg shadow-sm transition-opacity duration-500" role="alert">
        <div class="inline-flex items-center justify-center shrink-0 w-8 h-8 text-success bg-success/10 rounded-lg">
            <x-gmdi-check-circle class="w-6 h-6" />
        </div>

        <div class="ml-3 text-sm font-medium text-main">
            {{ session('success') }}
        </div>

        <button type="button" data-flash-close aria-label="Fechar mensagem"
            class="ml-auto -mx-1.5 -my-1.5 bg-white text-muted hover:text-main rounded-lg p-1.5 hover:bg-base inline-flex items-center justify-center h-8 w-8 cursor-pointer transition-colors">
            <x-gmdi-close class="w-5 h-5" />
        </button>
    </div>
@endif

@if (session('warning'))
    <div id="flash-warning" data-flash class="flex items-center p-4 w-full bg-white border-l-4 border-warning rounded-lg shadow-sm transition-opacity duration-500" role="alert">
        <div class="inline-flex items-center justify-center shrink-0 w-8 h-8 text-warning bg-warning/10 rounded-lg">
            <x-gmdi-warning class="w-6 h-6" />
        </div>

        <div class="ml-3 text-sm font-medium text-main">
            {{ session('warning') }}
        </div>

        <button type="button" data-flash-close aria-label="Fechar mensagem"
            class="ml-auto -mx-1.5 -my-1.5 bg-white text-muted hover:text-main rounded-lg p-1.5 hover:bg-base inline-flex items-center justify-center h-8 w-8 cursor-pointer transition-colors">
            <x-gmdi-close class="w-5 h-5" />
        </button>
    </div>
@endif