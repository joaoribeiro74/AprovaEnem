<?php

namespace Tests\Feature\Import;

use App\Models\Question;
use App\Services\Import\QuestionImporter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class QuestionImporterTest extends TestCase
{
    use RefreshDatabase;

    private string $dataset;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        $this->dataset = storage_path('framework/testing/enem-dataset-'.uniqid());
        File::ensureDirectoryExists($this->dataset);

        config([
            'enem.dataset_path' => $this->dataset,
            'enem.layouts' => [],
            'enem.years' => [],
        ]);
    }

    protected function tearDown(): void
    {
        File::deleteDirectory($this->dataset);

        parent::tearDown();
    }

    private function question(array $overrides = []): array
    {
        return array_merge([
            'index' => 1,
            'discipline' => 'linguagens',
            'language' => null,
            'title' => 'Questão 1',
            'context' => 'Texto da questão',
            'files' => [],
            'correctAlternative' => 'A',
            'alternativesIntroduction' => 'Assinale a alternativa correta.',
            'alternatives' => [
                ['letter' => 'A', 'text' => 'Certa', 'file' => null, 'isCorrect' => true],
                ['letter' => 'B', 'text' => 'Errada', 'file' => null, 'isCorrect' => false],
            ],
        ], $overrides);
    }

    /** Writes one question as the dataset would: <year>/questions/<folder>/details.json plus images. */
    private function writeQuestion(int $year, string $folder, array $question, array $images = []): void
    {
        $dir = "{$this->dataset}/{$year}/questions/{$folder}";

        File::ensureDirectoryExists($dir);
        File::put("{$dir}/details.json", json_encode($question));

        foreach ($images as $name => $bytes) {
            File::put("{$dir}/{$name}", $bytes);
        }
    }

    private function import(int $year = 2023): int
    {
        return app(QuestionImporter::class)->importYear($year);
    }

    // ---------- Basics ----------

    public function test_imports_question_with_alternatives(): void
    {
        $this->writeQuestion(2023, '1', $this->question());

        $this->assertSame(1, $this->import());

        $this->assertDatabaseHas('questions', [
            'year' => 2023, 'number' => 1, 'discipline' => 'linguagens',
            'statement' => 'Assinale a alternativa correta.', 'correct_alternative' => 'A',
        ]);
        $this->assertDatabaseCount('alternatives', 2);
    }

    public function test_running_twice_does_not_duplicate(): void
    {
        $this->writeQuestion(2023, '1', $this->question());

        $this->import();
        $this->import();

        $this->assertDatabaseCount('questions', 1);
        $this->assertDatabaseCount('alternatives', 2);
    }

    public function test_question_without_answer_key_is_imported(): void
    {
        $this->writeQuestion(2023, '1', $this->question(['correctAlternative' => null]));

        $this->import();

        $this->assertDatabaseHas('questions', ['number' => 1, 'correct_alternative' => null]);
    }

    public function test_day_is_derived_from_the_question_number(): void
    {
        $this->writeQuestion(2023, '90', $this->question(['index' => 90, 'title' => 'Questão 90']));
        $this->writeQuestion(2023, '91', $this->question(['index' => 91, 'title' => 'Questão 91']));

        $this->import();

        $this->assertDatabaseHas('questions', ['number' => 90, 'day' => 1]);
        $this->assertDatabaseHas('questions', ['number' => 91, 'day' => 2]);
    }

    // ---------- Foreign language ----------

    public function test_import_fails_loudly_when_two_entries_share_number_and_language(): void
    {
        $this->writeQuestion(2011, '91-a', $this->question(['index' => 91, 'language' => null, 'context' => '1234']));
        $this->writeQuestion(2011, '91-b', $this->question(['index' => 91, 'language' => null, 'context' => '5678']));

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Duplicate questions in 2011');

        $this->import(2011);
    }

    // ---------- Discipline ----------

    public function test_configured_layout_overrides_the_dataset_discipline(): void
    {
        config([
            'enem.layouts' => ['test' => [[1, 45, 'ciencias-natureza']]],
            'enem.years' => [2023 => 'test'],
        ]);

        $this->writeQuestion(2023, '1', $this->question(['index' => 1, 'discipline' => 'ciencias-humanas']));

        $this->import();

        $this->assertDatabaseHas('questions', ['number' => 1, 'discipline' => 'ciencias-natureza']);
    }

    public function test_changed_discipline_updates_the_question_instead_of_duplicating(): void
    {
        Question::create([
            'year' => 2023, 'number' => 1, 'language' => null, 'day' => 1,
            'discipline' => 'ciencias-humanas', 'title' => 'Questão 1',
        ]);

        $this->writeQuestion(2023, '1', $this->question(['discipline' => 'ciencias-natureza']));

        $this->import();

        $this->assertDatabaseCount('questions', 1);
        $this->assertDatabaseHas('questions', ['number' => 1, 'discipline' => 'ciencias-natureza']);
    }

    public function test_images_are_copied_and_links_are_rewritten(): void
    {
        $url = 'https://enem.dev/2023/questions/1/figure.png';

        $this->writeQuestion(2023, '1', $this->question([
            'context' => "Veja a figura: ![figura]({$url})",
            'files' => [$url],
        ]), ['figure.png' => 'fake-image-bytes']);

        $this->import();

        $question = Question::first();
        $image = $question->images()->first();

        Storage::disk('public')->assertExists($image->path);
        $this->assertStringContainsString('/storage/'.$image->path, $question->context);
        $this->assertStringNotContainsString('enem.dev', $question->context);
        $this->assertCount(1, $question->images); // same image in context and files
    }

    public function test_alternative_image_is_copied(): void
    {
        $this->writeQuestion(2023, '1', $this->question([
            'alternatives' => [
                ['letter' => 'A', 'text' => null, 'file' => 'a.png', 'isCorrect' => true],
            ],
        ]), ['a.png' => 'fake-image-bytes']);

        $this->import();

        Storage::disk('public')->assertExists(Question::first()->alternatives()->first()->image);
    }

    public function test_missing_image_keeps_the_original_link(): void
    {
        $url = 'https://enem.dev/2023/questions/1/missing.png';

        $this->writeQuestion(2023, '1', $this->question(['context' => "![x]({$url})"]));

        $this->import();

        $this->assertStringContainsString($url, Question::first()->context);
        $this->assertDatabaseCount('question_images', 0);
    }
}
