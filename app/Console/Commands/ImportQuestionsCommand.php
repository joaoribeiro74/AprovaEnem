<?php

namespace App\Console\Commands;

use App\Services\Import\LocalDatasetReader;
use App\Services\Import\QuestionImporter;
use Illuminate\Console\Command;

class ImportQuestionsCommand extends Command
{
    protected $signature = 'questions:import
        {years?* : Exam years, e.g. 2022 2023}
        {--all : Import every year found in the dataset}';

    protected $description = 'Import ENEM questions (and their images) from the local enem.dev dataset';

    public function handle(LocalDatasetReader $dataset, QuestionImporter $importer): int
    {
        $years = $this->option('all') ? $dataset->years() : $this->argument('years');

        if (empty($years)) {
            $this->error('Pass at least one year or use --all.');

            return self::FAILURE;
        }

        foreach ($years as $year) {
            $this->info("Importing {$year}...");

            $total = $importer->importYear((int) $year, function (int $count) {
                if ($count % 10 === 0) {
                    $this->output->write('.');
                }
            });

            $this->newLine();
            $this->info("{$year}: {$total} questions imported.");
        }

        return self::SUCCESS;
    }
}
