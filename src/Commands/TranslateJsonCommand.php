<?php

namespace NativeCode\AutoLang\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;

class TranslateJsonCommand extends Command
{
    protected $signature = 'auto-lang:translate {locales*}';

    protected $description = 'Translate en.json to another language';

    public function handle(): void
    {
        $locales = $this->argument('locales');

        $sourcePath = lang_path('en.json');

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

        /*
        |--------------------------------------------------------------------------
        | Translate Multiple Locales
        |--------------------------------------------------------------------------
        */

        foreach ($locales as $locale) {

            // Remove commas/spaces
            $locale = trim($locale, ', ');

            if (empty($locale)) {
                continue;
            }

            $targetPath = lang_path("{$locale}.json");

            $translated = [];

            $this->info("Translating locale: {$locale}");

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

                $this->info("{$locale}: {$value}");
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

        $this->info('All translations completed.');
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

        /*
        |--------------------------------------------------------------------------
        | Prevent Empty Values
        |--------------------------------------------------------------------------
        */

        if (empty(trim($text))) {
            return $text;
        }

        /*
        |--------------------------------------------------------------------------
        | Prevent Rate Limits
        |--------------------------------------------------------------------------
        */

        usleep(300000);

        /*
        |--------------------------------------------------------------------------
        | Google Translate
        |--------------------------------------------------------------------------
        */

        try {

            $response = Http::timeout(30)
                ->get(
                    'https://translate.googleapis.com/translate_a/single',
                    [
                        'client' => 'gtx',
                        'sl' => 'en',
                        'tl' => $locale,
                        'dt' => 't',
                        'q' => $text,
                    ]
                );

            if ($response->successful()) {

                $data = $response->json();

                $translated = $data[0][0][0] ?? null;

                if (! empty($translated)) {

                    $this->info(
                        "Google: {$text}"
                    );

                    return $translated;
                }
            }
        } catch (\Throwable $e) {

            $this->warn(
                "Google failed: {$text}"
            );
        }

        /*
        |--------------------------------------------------------------------------
        | LibreTranslate
        |--------------------------------------------------------------------------
        */

        try {

            $response = Http::timeout(30)
                ->post(
                    'https://libretranslate.com/translate',
                    [
                        'q' => $text,
                        'source' => 'en',
                        'target' => $locale,
                        'format' => 'text',
                    ]
                );

            if ($response->successful()) {

                $translated = $response->json()['translatedText']
                    ?? null;

                if (! empty($translated)) {

                    $this->info(
                        "LibreTranslate: {$text}"
                    );

                    return $translated;
                }
            }
        } catch (\Throwable $e) {

            $this->warn(
                "LibreTranslate failed: {$text}"
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Lingva Translate
        |--------------------------------------------------------------------------
        */

        try {

            $url =
                "https://lingva.ml/api/v1/en/" .
                $locale .
                "/" .
                urlencode($text);

            $response = Http::timeout(30)
                ->get($url);

            if ($response->successful()) {

                $translated = $response->json()['translation']
                    ?? null;

                if (! empty($translated)) {

                    $this->info(
                        "Lingva: {$text}"
                    );

                    return $translated;
                }
            }
        } catch (\Throwable $e) {

            $this->warn(
                "Lingva failed: {$text}"
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Fallback
        |--------------------------------------------------------------------------
        */

        return $text;
    }
}
