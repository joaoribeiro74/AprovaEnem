<?php

namespace Tests\Feature;

use App\Models\Question;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuestionTest extends TestCase
{
    use RefreshDatabase;

    public function test_alternatives_are_always_returned_in_letter_order(): void
    {
        $question = Question::create([
            'year' => 2023, 'number' => 1, 'language' => null, 'day' => 1,
            'discipline' => 'matematica', 'title' => 'Questão 1',
        ]);

        foreach (['E', 'B', 'A', 'D', 'C'] as $letter) {
            $question->alternatives()->create(['letter' => $letter, 'text' => $letter]);
        }

        $this->assertSame(
            ['A', 'B', 'C', 'D', 'E'],
            $question->fresh()->alternatives->pluck('letter')->all()
        );
    }

    public function test_answerable_scope_excludes_annulled_questions(): void
    {
        $base = ['year' => 2022, 'language' => null, 'day' => 2, 'discipline' => 'matematica'];

        Question::create($base + ['number' => 156, 'title' => 'Q156', 'correct_alternative' => 'A']);
        Question::create($base + ['number' => 157, 'title' => 'Q157', 'correct_alternative' => null]);

        $this->assertSame([156], Question::answerable()->pluck('number')->all());
    }
}
