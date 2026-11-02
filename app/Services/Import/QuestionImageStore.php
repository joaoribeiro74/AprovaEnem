<?php

namespace App\Services\Import;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class QuestionImageStore
{
    private const DISK = 'public';

    public function __construct(private LocalDatasetReader $dataset) {}

    /** Copies an image from the dataset to the public disk. Returns its path there, or null if not found. */
    public function store(string $reference, string $baseDir, int $year): ?string
    {
        $file = $this->locate($reference, $baseDir);

        if ($file === null) {
            Log::warning("Question image not found: {$reference}", ['dir' => $baseDir]);

            return null;
        }

        $path = "questions/{$year}/".sha1_file($file).'.'.strtolower(pathinfo($file, PATHINFO_EXTENSION));

        if (! Storage::disk(self::DISK)->exists($path)) {
            Storage::disk(self::DISK)->put($path, file_get_contents($file));
        }

        return $path;
    }

    public function publicUrl(string $path): string
    {
        return '/storage/'.$path;
    }

    /**
     * Replaces markdown image links (![alt](ref)) with links to the copied files.
     * Every copied path is appended to $paths.
     */
    public function localizeMarkdown(?string $text, string $baseDir, int $year, array &$paths): ?string
    {
        if ($text === null) {
            return null;
        }

        return preg_replace_callback(
            '/!\[([^\]]*)\]\(([^)\s]+)\)/',
            function (array $match) use ($baseDir, $year, &$paths) {
                if (str_starts_with($match[2], '/storage/')) {
                    return $match[0]; // already localized
                }

                $path = $this->store($match[2], $baseDir, $year);

                if ($path === null) {
                    return $match[0]; // not found: keep the original link
                }

                $paths[] = $path;

                return '!['.$match[1].']('.$this->publicUrl($path).')';
            },
            $text
        );
    }

    private function locate(string $reference, string $baseDir): ?string
    {
        $path = rawurldecode(preg_match('#^https?://#i', $reference)
            ? (string) parse_url($reference, PHP_URL_PATH)
            : $reference);

        // the site serves the dataset's "public" folder, so a URL path mirrors a path inside it;
        // otherwise fall back to the question's own folder
        $candidates = [
            $this->dataset->root().'/'.ltrim($path, '/'),
            $baseDir.'/'.ltrim($path, '/'),
            $baseDir.'/'.basename($path),
        ];

        $root = realpath($this->dataset->root());

        foreach ($candidates as $candidate) {
            $real = realpath($candidate);

            // only files inside the dataset are accepted
            if ($real !== false && is_file($real) && $root !== false && str_starts_with($real, $root)) {
                return $real;
            }
        }

        return null;
    }
}
