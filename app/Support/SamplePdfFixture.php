<?php

namespace App\Support;

/**
 * Resolves sample PDF paths relative to the repo (never absolute Windows host paths).
 */
class SamplePdfFixture
{
    public static function root(): string
    {
        return database_path('sample_pdfs');
    }

    /**
     * @return list<string> Absolute paths to PDFs under database/sample_pdfs
     */
    public static function all(): array
    {
        $root = self::root();
        if (! is_dir($root)) {
            return [];
        }

        $paths = [];
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($root, \FilesystemIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if ($file->isFile() && strtolower($file->getExtension()) === 'pdf') {
                $paths[] = $file->getPathname();
            }
        }

        sort($paths);

        return $paths;
    }

    public static function firstOrNull(string $relativeUnderSamplePdfs): ?string
    {
        $path = self::root().DIRECTORY_SEPARATOR.str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $relativeUnderSamplePdfs);

        return is_file($path) ? $path : null;
    }
}
