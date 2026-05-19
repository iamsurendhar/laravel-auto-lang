<?php

namespace Vendor\AutoTranslate;

use Illuminate\Support\ServiceProvider;
use Illuminate\Translation\FileLoader;

class AutoTranslateServiceProvider extends ServiceProvider
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

            $newTranslator->setFallback($app['config']['app.fallback_locale']);

            return $newTranslator;
        });
    }
}
