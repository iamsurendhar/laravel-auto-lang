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

        /*
        |--------------------------------------------------------------------------
        | Load Existing Translations
        |--------------------------------------------------------------------------
        */

        if (File::exists($langPath)) {

            $translations = json_decode(
                File::get($langPath),
                true
            ) ?? [];
        }

        /*
        |--------------------------------------------------------------------------
        | Excluded Folders
        |--------------------------------------------------------------------------
        */

        $excludedFolders = [
            base_path('bootstrap'),
            base_path('config'),
            base_path('database'),
            base_path('routes'),
            base_path('storage'),
            base_path('tests'),
            base_path('vendor'),
        ];

        /*
        |--------------------------------------------------------------------------
        | Allowed File Extensions
        |--------------------------------------------------------------------------
        */

        $extensions = [
            '.php',
            '.blade.php',
            '.js',
            '.ts',
            '.jsx',
            '.tsx',
        ];

        /*
        |--------------------------------------------------------------------------
        | Scan Project Files
        |--------------------------------------------------------------------------
        */

        $files = File::allFiles(base_path());

        foreach ($files as $file) {

            $filePath = $file->getPathname();

            /*
            |--------------------------------------------------------------------------
            | Skip Excluded Folders
            |--------------------------------------------------------------------------
            */

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

            /*
            |--------------------------------------------------------------------------
            | Skip Laravel Root Files
            |--------------------------------------------------------------------------
            */

            if ($file->getPath() === base_path()) {

                $rootExcluded = [
                    'artisan',
                ];

                if (in_array($file->getFilename(), $rootExcluded)) {
                    continue;
                }
            }

            /*
            |--------------------------------------------------------------------------
            | Validate Extension
            |--------------------------------------------------------------------------
            */

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

            /*
            |--------------------------------------------------------------------------
            | PHP / Blade Translation Scan
            |--------------------------------------------------------------------------
            |
            | Scan:
            | __('text')
            | trans('text')
            |
            */

            if (
                str_ends_with($filename, '.blade.php') ||
                str_ends_with($filename, '.php')
            ) {

                preg_match_all(
                    "/__\(['\"](.+?)['\"]\)/",
                    $content,
                    $matches1
                );

                preg_match_all(
                    "/trans\(['\"](.+?)['\"]\)/",
                    $content,
                    $matches2
                );

                $results = array_filter(
                    array_merge(
                        $matches1[1] ?? [],
                        $matches2[1] ?? []
                    )
                );
            } else {

                /*
                |--------------------------------------------------------------------------
                | JS / TS / JSX / TSX Variable Scan
                |--------------------------------------------------------------------------
                |
                | Detect:
                | const title = 'Dashboard'
                |
                */

                preg_match_all(
                    "/const\s+(\w+)\s*=\s*['\"](.+?)['\"]/",
                    $content,
                    $variableMatches,
                    PREG_SET_ORDER
                );

                $variables = [];

                foreach ($variableMatches as $match) {

                    $variables[$match[1]] = $match[2];
                }

                /*
                |--------------------------------------------------------------------------
                | JS / TS / JSX / TSX Translation Scan
                |--------------------------------------------------------------------------
                |
                | Scan:
                | t('text')
                | t("text")
                | t(`text`)
                | t(variable)
                |
                */

                preg_match_all(
                    "/t\(\s*([^)]+)\s*\)/",
                    $content,
                    $matches
                );

                foreach ($matches[1] as $value) {

                    $value = trim($value);

                    /*
                    |--------------------------------------------------------------------------
                    | Direct Strings
                    |--------------------------------------------------------------------------
                    */

                    if (
                        preg_match(
                            "/^['\"](.+?)['\"]$/",
                            $value,
                            $stringMatch
                        )
                    ) {

                        $results[] = $stringMatch[1];
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | Template Literals
                    |--------------------------------------------------------------------------
                    */ elseif (
                        preg_match(
                            "/^`(.+?)`$/",
                            $value,
                            $templateMatch
                        )
                    ) {

                        $results[] = $templateMatch[1];
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | Local Variables
                    |--------------------------------------------------------------------------
                    */ elseif (isset($variables[$value])) {

                        $results[] = $variables[$value];
                    }
                }
            }

            /*
            |--------------------------------------------------------------------------
            | Save Missing Translations
            |--------------------------------------------------------------------------
            */

            foreach ($results as $text) {

                $text = trim($text);

                if (empty($text)) {
                    continue;
                }

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

        /*
        |--------------------------------------------------------------------------
        | Sort Translations
        |--------------------------------------------------------------------------
        */

        ksort($translations);

        /*
        |--------------------------------------------------------------------------
        | Save en.json
        |--------------------------------------------------------------------------
        */

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
