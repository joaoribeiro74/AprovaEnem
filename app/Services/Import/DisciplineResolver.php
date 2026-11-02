<?php

namespace App\Services\Import;

class DisciplineResolver
{
    /** Uses the configured layout for the year when there is one, otherwise trusts the dataset. */
    public function resolve(int $year, int $number, string $fromDataset): string
    {
        $layout = config("enem.years.{$year}");

        foreach (config("enem.layouts.{$layout}", []) as [$from, $to, $discipline]) {
            if ($number >= $from && $number <= $to) {
                return $discipline;
            }
        }

        return $fromDataset;
    }
}
