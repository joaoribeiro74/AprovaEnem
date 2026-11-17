<?php

namespace App\Services\Import;

use RuntimeException;
use Symfony\Component\Finder\Finder;

class LocalDatasetReader
{
    private const QUESTIONS_DIR = 'questions';

    private const QUESTION_FILE = 'details.json';

    public function root(): string
    {
        return rtrim(config('enem.dataset_path'), '/');
    }

    /** @return int[] years found in the dataset */
    public function years(): array
    {
        if (! is_dir($this->root())) {
            throw new RuntimeException("Dataset not found at {$this->root()}. Did you init the submodule?");
        }

        return collect(scandir($this->root()))
            ->filter(fn (string $name) => ctype_digit($name) && is_dir("{$this->root()}/{$name}"))
            ->map(fn (string $name) => (int) $name)
            ->sort()
            ->values()
            ->all();
    }

    /** @return \Generator<array> one question per details.json*/
    public function questions(int $year): \Generator
    {
        $dir = "{$this->root()}/{$year}/".self::QUESTIONS_DIR;

        if (! is_dir($dir)) {
            throw new RuntimeException("No questions folder for {$year} at {$dir}");
        }

        $files = Finder::create()->files()->name(self::QUESTION_FILE)->in($dir)->sortByName(true);

        foreach ($files as $file) {
            $data = json_decode($file->getContents(), true, 512, JSON_THROW_ON_ERROR);
            $data['_dir'] = $file->getPath();

            yield $data;
        }
    }
}
