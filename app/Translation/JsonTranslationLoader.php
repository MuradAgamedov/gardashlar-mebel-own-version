<?php

namespace App\Translation;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Translation\FileLoader;

class JsonTranslationLoader extends FileLoader
{
    /**
     * Create a new file loader instance.
     *
     * @param  \Illuminate\Filesystem\Filesystem  $files
     * @param  string  $path
     * @return void
     */
    public function __construct(Filesystem $files, $path)
    {
        parent::__construct($files, $path);
    }

    /**
     * Load the messages for the given locale.
     *
     * @param  string  $locale
     * @param  string  $group
     * @param  string  $namespace
     * @return array
     */
    public function load($locale, $group, $namespace = null)
    {
        if ($group === '*' && $namespace === null) {
            // Load all JSON translations for the locale
            return $this->loadJson($locale);
        }

        if ($namespace !== null && $namespace !== '*') {
            return $this->loadNamespaced($locale, $group, $namespace);
        }

        // Fallback to regular PHP files
        return parent::load($locale, $group, $namespace);
    }

    /**
     * Load a JSON translation file.
     *
     * @param  string  $locale
     * @return array|null
     */
    protected function loadJson($locale)
    {
        $path = $this->path.'/'.$locale.'.json';

        if ($this->files->exists($path)) {
            return json_decode($this->files->get($path), true);
        }

        return null;
    }
}
