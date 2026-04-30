<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Translation\TranslationServiceProvider as BaseTranslationServiceProvider;
use Illuminate\Translation\Translator;

class TranslationServiceProvider extends BaseTranslationServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerLoader();
        
        $this->app->singleton('translator', function ($app) {
            $loader = $app['translation.loader'];
            
            $locale = $app['config']['app.locale'];
            
            $trans = new Translator($loader, $locale);
            
            $trans->setFallback($app['config']['app.fallback_locale']);
            
            return $trans;
        });
    }

    /**
     * Register the translation line loader.
     *
     * @return void
     */
    protected function registerLoader()
    {
        $this->app->singleton('translation.loader', function ($app) {
            return new \App\Translation\JsonTranslationLoader($app['files'], $app['path.lang']);
        });
    }
}
