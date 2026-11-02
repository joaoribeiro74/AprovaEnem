<?php

namespace App\Services\Import;

use App\Models\Question;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class QuestionImporter
{
    public function __construct(
        private LocalDatasetReader $dataset,
        private QuestionImageStore $images,
        private DisciplineResolver $disciplines,
    ) {}

    /** @return int number of questions imported or updated */
    public function importYear(int $year, ?callable $onProgress = null): int
    {
        $questions = collect(iterator_to_array($this->dataset->questions($year), false));

        $this->assertUniqueIdentities($year, $questions);

        $count = 0;

        foreach ($questions as $data) {
            $this->storeQuestion($year, $data);

            $count++;
            $onProgress && $onProgress($count);
        }

        return $count;
    }

    /** Two entries with the same number and language would silently collapse into a single row. */
    private function assertUniqueIdentities(int $year, Collection $questions): void
    {
        $duplicates = $questions
            ->groupBy(fn (array $question) => $question['index'].'|'.($question['language'] ?? ''))
            ->filter(fn (Collection $group) => $group->count() > 1);

        if ($duplicates->isEmpty()) {
            return;
        }

        $details = $duplicates
            ->map(fn (Collection $group, string $key) => "{$key} => ".$group->pluck('_dir')->implode(', '))
            ->implode("\n");

        throw new RuntimeException(
            "Duplicate questions in {$year} (same number and language). "
            ."Set the \"language\" field in one of the files of each pair:\n{$details}"
        );
    }

    private function storeQuestion(int $year, array $data): void
    {
        $dir = $data['_dir'];
        $paths = [];

        $context = $this->images->localizeMarkdown($data['context'] ?? null, $dir, $year, $paths);
        $statement = $this->images->localizeMarkdown($data['alternativesIntroduction'] ?? null, $dir, $year, $paths);

        foreach ($data['files'] ?? [] as $file) {
            if ($path = $this->images->store($file, $dir, $year)) {
                $paths[] = $path;
            }
        }

        $alternatives = [];

        foreach ($data['alternatives'] ?? [] as $alternative) {
            $alternatives[] = [
                'letter' => $alternative['letter'],
                'text' => $this->images->localizeMarkdown($alternative['text'] ?? null, $dir, $year, $paths),
                'image' => ! empty($alternative['file'])
                    ? $this->images->store($alternative['file'], $dir, $year)
                    : null,
            ];
        }

        DB::transaction(function () use ($year, $data, $context, $statement, $alternatives, $paths) {
            $question = Question::updateOrCreate(
                [
                    'year' => $year,
                    'number' => $data['index'],
                    'language' => $data['language'] ?? null,
                ],
                [
                    'discipline' => $this->disciplines->resolve($year, $data['index'], $data['discipline']),
                    'day' => $data['index'] <= 90 ? 1 : 2,
                    'title' => $data['title'],
                    'context' => $context,
                    'statement' => $statement,
                    'correct_alternative' => $data['correctAlternative'] ?? null,
                ]
            );

            foreach ($alternatives as $alternative) {
                $question->alternatives()->updateOrCreate(
                    ['letter' => $alternative['letter']],
                    ['text' => $alternative['text'], 'image' => $alternative['image']]
                );
            }

            $question->images()->delete();

            foreach (array_unique($paths) as $path) {
                $question->images()->create(['path' => $path]);
            }
        });
    }
}
