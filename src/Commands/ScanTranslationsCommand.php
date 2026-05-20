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

                $results = [];

                // Blade files => scan __()
                if (str_ends_with($file->getFilename(), '.blade.php')) {

                    preg_match_all(
                        "/__\(['\"](.+?)['\"]\)/",
                        $content,
                        $matches
                    );

                    $results = $matches[1] ?? [];
                } else {

                    // PHP/controllers => scan trans()
                    preg_match_all(
                        "/trans\(['\"](.+?)['\"]\)/",
                        $content,
                        $matches
                    );

                    $results = $matches[1] ?? [];
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
