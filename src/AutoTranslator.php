<?php

namespace Vendor\AutoTranslate;

use Illuminate\Translation\Translator;
use Illuminate\Filesystem\Filesystem;

class AutoTranslator extends Translator
{
    protected Filesystem $files;

    public function __construct($loader, $locale)
    {
        parent::__construct($loader, $locale);

        $this->files = new Filesystem();
    }

    public function get($key, array $replace = [], $locale = null, $fallback = true)
    {
        $translation = parent::get($key, $replace, $locale, $fallback);

        // Only track plain JSON strings
        if ($translation === $key) {
            $this->appendToJson($key);
        }

        return $translation;
    }

    protected function appendToJson(string $key): void
    {
        $path = lang_path('en.json');

        if (! $this->files->exists($path)) {
            $this->files->put($path, json_encode([], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        }

        $content = json_decode($this->files->get($path), true);

        if (! isset($content[$key])) {
            $content[$key] = $key;

            ksort($content);

            $this->files->put(
                $path,
                json_encode(
                    $content,
                    JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
                )
            );
        }
    }
}
