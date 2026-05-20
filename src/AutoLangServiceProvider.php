<?php

namespace NativeCode\AutoLang;

use Illuminate\Support\ServiceProvider;
use NativeCode\AutoLang\Commands\ScanTranslationsCommand;

class AutoLangServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->commands([
            ScanTranslationsCommand::class,
            TranslateJsonCommand::class,
        ]);
    }
}
