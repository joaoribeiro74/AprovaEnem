<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title>Questions {{ $year }}</title>
    <style>
        body { font-family: sans-serif; max-width: 800px; margin: 2rem auto; padding: 0 1rem; }
        article { border-bottom: 2px solid #ccc; padding-bottom: 1.5rem; margin-bottom: 1.5rem; }
        img { max-width: 100%; }
        .correct { font-weight: bold; color: #15803d; }
        .meta { color: #666; font-size: .85rem; }
    </style>
</head>
<body>
    <p>
        @if ($questions->previousPageUrl()) <a href="{{ $questions->previousPageUrl() }}">← previous</a> @endif
        Page {{ $questions->currentPage() }} of {{ $questions->lastPage() }}
        @if ($questions->nextPageUrl()) <a href="{{ $questions->nextPageUrl() }}">next →</a> @endif
    </p>

    @foreach ($questions as $question)
        <article>
            <h2>{{ $question->title }}</h2>
            <p class="meta">
                {{ $question->discipline->value }}
                @if ($question->language) · {{ $question->language }} @endif
                · dia {{ $question->day }} · gabarito: {{ $question->correct_alternative ?? '—' }}
            </p>

            {!! \Illuminate\Support\Str::markdown($question->context ?? '', ['html_input' => 'escape']) !!}
            {!! \Illuminate\Support\Str::markdown($question->statement ?? '', ['html_input' => 'escape']) !!}

            <ul style="list-style: none; padding: 0">
                @foreach ($question->alternatives as $alternative)
                    <li class="{{ $question->correct_alternative === $alternative->letter ? 'correct' : '' }}">
                        {{ $alternative->letter }})
                        {!! \Illuminate\Support\Str::inlineMarkdown($alternative->text ?? '', ['html_input' => 'strip']) !!}
                        @if ($alternative->image)
                            <img src="{{ Storage::disk('public')->url($alternative->image) }}" alt="">
                        @endif
                    </li>
                @endforeach
            </ul>
        </article>
    @endforeach

    <p>
        @if ($questions->previousPageUrl()) <a href="{{ $questions->previousPageUrl() }}">← previous</a> @endif
        Page {{ $questions->currentPage() }} of {{ $questions->lastPage() }}
        @if ($questions->nextPageUrl()) <a href="{{ $questions->nextPageUrl() }}">next →</a> @endif
    </p>
</body>
</html>