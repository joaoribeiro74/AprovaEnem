<?php

namespace App\Console\Commands;

use App\Models\Question;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class AuditQuestionsCommand extends Command
{
    protected $signature = 'questions:audit {year : Exam year to check}';

    protected $description = 'Check imported questions for missing alternatives, answer keys and images';

    public function handle(): int
    {
        $year = (int) $this->argument('year');
        $disk = Storage::disk('public');
        $problems = [];
        $total = 0;

        Question::with(['alternatives', 'images'])
            ->where('year', $year)
            ->orderBy('number')
            ->each(function (Question $question) use ($disk, &$problems, &$total) {
                $total++;
                $label = "Q{$question->number}".($question->language ? " ({$question->language})" : '');
                $report = function (string $message) use (&$problems, $label) {
                    $problems[] = [$label, $message];
                };

                $letters = $question->alternatives->pluck('letter')->sort()->values()->all();

                if ($letters !== ['A', 'B', 'C', 'D', 'E']) {
                    $report('alternatives: '.(implode(',', $letters) ?: 'none'));
                }

                if ($question->correct_alternative === null) {
                    if (! in_array($question->number, config("enem.annulled.{$question->year}", []), true)) {
                        $report('no answer key');
                    }
                } elseif (! in_array($question->correct_alternative, $letters, true)) {
                    $report("answer key {$question->correct_alternative} is not among the alternatives");
                }

                if (blank($question->context) && blank($question->statement)) {
                    $report('no context and no statement');
                }

                foreach ($question->alternatives as $alternative) {
                    if (blank($alternative->text) && blank($alternative->image)) {
                        $report("alternative {$alternative->letter} is empty");
                    }

                    if ($alternative->image && ! $disk->exists($alternative->image)) {
                        $report("alternative {$alternative->letter} image missing: {$alternative->image}");
                    }
                }

                $linked = $question->images->pluck('path')->all();
                $texts = array_merge([$question->context, $question->statement], $question->alternatives->pluck('text')->all());

                foreach ($texts as $text) {
                    if ($text === null) {
                        continue;
                    }

                    if (preg_match('/!\[[^\]]*\]\((?!\/storage\/)/', $text)) {
                        $report('image link was not localized');
                    }

                    preg_match_all('/\(\/storage\/([^)\s]+)\)/', $text, $matches);

                    foreach ($matches[1] as $path) {
                        if (! $disk->exists($path)) {
                            $report("file missing on disk: {$path}");
                        }

                        if (! in_array($path, $linked, true)) {
                            $report("image used in text but not in question_images: {$path}");
                        }
                    }
                }

                foreach ($linked as $path) {
                    if (! $disk->exists($path)) {
                        $report("question_images points to a missing file: {$path}");
                    }
                }
            });

        if ($total === 0) {
            $this->error("No questions found for {$year}.");

            return self::FAILURE;
        }

        if ($problems === []) {
            $this->info("{$year}: {$total} questions checked, no problems found.");

            return self::SUCCESS;
        }

        $this->table(['Question', 'Problem'], $problems);
        $this->warn(count($problems)." problem(s) in {$total} questions.");

        return self::FAILURE;
    }
}
