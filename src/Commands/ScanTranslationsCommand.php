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

        // Scan paths
        $paths = [
            app_path(),
            resource_path('views'),
        ];

        foreach ($paths as $path) {

            if (! File::exists($path)) {
                continue;
            }

            $files = File::allFiles($path);

            foreach ($files as $file) {

                $content = File::get($file);

                preg_match_all(
                    "/__\(['\"](.+?)['\"]\)|trans\(['\"](.+?)['\"]\)/",
                    $content,
                    $matches
                );

                $results = array_filter(
                    array_merge(
                        $matches[1] ?? [],
                        $matches[2] ?? []
                    )
                );

                foreach ($results as $text) {

                    // Skip translation file keys
                    if (str_contains($text, '.')) {
                        continue;
                    }

                    // Add missing translation
                    if (! isset($translations[$text])) {

                        $translations[$text] = $text;

                        $this->info("Added: {$text}");
                    }
                }
            }
        }

        // Sort translations
        ksort($translations);

        // Save en.json
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
