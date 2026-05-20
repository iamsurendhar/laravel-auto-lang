<?php

namespace NativeCode\AutoLang\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ScanTranslationsCommand extends Command
{
    protected $signature = 'auto-lang:scan';

    protected $description = 'Scan project translations and add missing keys';

    public function handle(): void
    {
        $translations = [];

        $langPath = lang_path('en.json');

        // Load existing translations
        if (File::exists($langPath)) {

            $translations = json_decode(
                File::get($langPath),
                true
            ) ?? [];
        }

        // Excluded folders
        $excludedFolders = [
            base_path('bootstrap'),
            base_path('config'),
            base_path('database'),
            base_path('routes'),
            base_path('storage'),
            base_path('tests'),
            base_path('vendor'),
        ];

        // Allowed extensions
        $extensions = [
            'php',
            'blade.php',
            'js',
            'ts',
            'jsx',
            'tsx',
        ];

        // Scan all project files
        $files = File::allFiles(base_path());

        foreach ($files as $file) {

            $filePath = $file->getPathname();

            // Skip excluded folders
            $skip = false;

            foreach ($excludedFolders as $excluded) {

                if (str_starts_with($filePath, $excluded)) {
                    $skip = true;
                    break;
                }
            }

            if ($skip) {
                continue;
            }

            // Skip Laravel root files
            if ($file->getPath() === base_path()) {

                $rootExcluded = [
                    'artisan',
                ];

                if (in_array($file->getFilename(), $rootExcluded)) {
                    continue;
                }
            }

            // Validate extension
            $filename = $file->getFilename();

            $valid = false;

            foreach ($extensions as $extension) {

                if (str_ends_with($filename, $extension)) {
                    $valid = true;
                    break;
                }
            }

            if (! $valid) {
                continue;
            }

            $content = File::get($file);

            $results = [];

            // Blade files => scan __()
            if (str_ends_with($filename, '.blade.php')) {

                preg_match_all(
                    "/__\(['\"](.+?)['\"]\)/",
                    $content,
                    $matches
                );

                $results = $matches[1] ?? [];
            } else {

                // PHP/JS/TS/TSX => scan trans() and t()
                preg_match_all(
                    "/trans\(['\"](.+?)['\"]\)|t\(['\"](.+?)['\"]\)/",
                    $content,
                    $matches
                );

                $results = array_filter(
                    array_merge(
                        $matches[1] ?? [],
                        $matches[2] ?? []
                    )
                );
            }

            foreach ($results as $text) {

                // Skip translation keys
                if (str_contains($text, '.')) {
                    continue;
                }

                if (! isset($translations[$text])) {

                    $translations[$text] = $text;

                    $this->info("Added: {$text}");
                }
            }
        }

        // Sort translations
        ksort($translations);

        // Save translations
        File::put(
            $langPath,
            json_encode(
                $translations,
                JSON_PRETTY_PRINT |
                    JSON_UNESCAPED_UNICODE |
                    JSON_UNESCAPED_SLASHES
            )
        );

        $this->info('Translation scan completed.');
    }
}
