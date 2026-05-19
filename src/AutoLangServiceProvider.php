<?php

namespace NativeCode\AutoLang;

use Illuminate\Support\ServiceProvider;
use Illuminate\Translation\FileLoader;

class AutoLangServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->extend('translator', function ($translator, $app) {

            $loader = new FileLoader(
                $app['files'],
                lang_path()
            );

            $newTranslator = new AutoTranslator(
                $loader,
                $app->getLocale()
            );

            $newTranslator->setFallback(
                $app['config']['app.fallback_locale']
            );

            return $newTranslator;
        });
    }
}
