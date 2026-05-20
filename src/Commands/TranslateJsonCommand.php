<?php

namespace NativeCode\AutoLang\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;

class TranslateJsonCommand extends Command
{
    protected $signature = 'auto-lang:translate {locale}';

    protected $description = 'Translate en.json to another language';

    public function handle(): void
    {
        $locale = $this->argument('locale');

        $sourcePath = lang_path('en.json');

        $targetPath = lang_path("{$locale}.json");

        /*
        |--------------------------------------------------------------------------
        | Check Source File
        |--------------------------------------------------------------------------
        */

        if (! File::exists($sourcePath)) {

            $this->error('en.json not found.');

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Load English Translations
        |--------------------------------------------------------------------------
        */

        $translations = json_decode(
            File::get($sourcePath),
            true
        ) ?? [];

        $translated = [];

        /*
        |--------------------------------------------------------------------------
        | Translate Keys
        |--------------------------------------------------------------------------
        */

        foreach ($translations as $key => $value) {

            $translated[$key] = $this->translate(
                $value,
                $locale
            );

            $this->info("Translated: {$value}");
        }

        /*
        |--------------------------------------------------------------------------
        | Save Locale JSON
        |--------------------------------------------------------------------------
        */

        File::put(
            $targetPath,
            json_encode(
                $translated,
                JSON_PRETTY_PRINT |
                    JSON_UNESCAPED_UNICODE |
                    JSON_UNESCAPED_SLASHES
            )
        );

        $this->info("{$locale}.json generated successfully.");
    }

    /*
    |--------------------------------------------------------------------------
    | Translate Text
    |--------------------------------------------------------------------------
    */

    protected function translate(
        string $text,
        string $locale
    ): string {

        try {

            $response = Http::get(
                'https://translate.googleapis.com/translate_a/single',
                [
                    'client' => 'gtx',
                    'sl' => 'en',
                    'tl' => $locale,
                    'dt' => 't',
                    'q' => $text,
                ]
            );

            return $response->json()[0][0][0] ?? $text;
        } catch (\Throwable $e) {

            return $text;
        }
    }
}
