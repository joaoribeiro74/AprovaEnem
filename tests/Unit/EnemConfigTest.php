<?php

namespace Tests\Unit;

use Tests\TestCase;

class EnemConfigTest extends TestCase
{
    public function test_every_layout_covers_questions_1_to_180_without_gaps_or_overlap(): void
    {
        foreach (config('enem.layouts') as $name => $ranges) {
            $numbers = collect($ranges)
                ->flatMap(fn (array $range) => range($range[0], $range[1]))
                ->sort()->values()->all();

            $this->assertSame(range(1, 180), $numbers, "Layout {$name} is incomplete or overlapping");
        }
    }

    public function test_every_configured_year_points_to_an_existing_layout(): void
    {
        foreach (config('enem.years') as $year => $layout) {
            $this->assertArrayHasKey($layout, config('enem.layouts'), "Year {$year} uses an unknown layout");
        }
    }
}
