<?php

namespace App\Console\Commands;

use App\Services\Import\DisciplineResolver;
use App\Services\Import\LocalDatasetReader;
use Illuminate\Console\Command;

class CheckLayoutsCommand extends Command
{
    protected $signature = 'questions:check-layouts
        {year? : Also list the questions the layout overrides for this year}';

    protected $description = 'Compare each configured layout with the disciplines found in the dataset';

    public function handle(LocalDatasetReader $dataset, DisciplineResolver $resolver): int
    {
        $only = $this->argument('year');
        $rows = [];

        foreach (config('enem.years') as $year => $layout) {
            if ($only && (int) $only !== $year) {
                continue;
            }

            try {
                $questions = collect(iterator_to_array($dataset->questions($year), false));
            } catch (\RuntimeException $e) {
                $rows[] = [$year, $layout, '-', '-', '-', 'no data'];

                continue;
            }

            $overridden = $questions->filter(
                fn (array $q) => $resolver->resolve($year, $q['index'], $q['discipline']) !== $q['discipline']
            );

            $share = $questions->isEmpty() ? 0 : $overridden->count() / $questions->count();

            $rows[] = [
                $year,
                $layout,
                $questions->count(),
                $overridden->count(),
                sprintf('%d%%', $share * 100),
                $share > 0.25 ? 'CHECK' : 'ok',
            ];

            if ($only) {
                $this->table(
                    ['Question', 'Dataset says', 'Layout says'],
                    $overridden->sortBy('index')->map(fn (array $q) => [
                        $q['index'],
                        $q['discipline'],
                        $resolver->resolve($year, $q['index'], $q['discipline']),
                    ])->values()->all()
                );
            }
        }

        $this->table(['Year', 'Layout', 'Questions', 'Overridden', 'Share', 'Status'], $rows);

        return self::SUCCESS;
    }
}
