<?php

namespace Flarum\Testing\integration;

trait UsesTmpDir
{
    protected $DEFAULT_TMP_DIR = __DIR__.'/tmp';

    public function tmpDir() {
        return getenv('FLARUM_TEST_TMP_DIR_LOCAL') ?: getenv('FLARUM_TEST_TMP_DIR') ?: static::$DEFAULT_TMP_DIR;
    }

    public function setupTmpDir() {
        $DIRS_NEEDED = [
            '/',
            '/public',
            '/public/assets',
            '/storage',
            '/storage/formatter',
            '/storage/sessions',
            '/storage/views',
            '/vendor',
            '/vendor/composer'
        ];

        $FILES_NEEDED = [
            '/vendor/composer/installed.json' => '{}'
        ];

        $tmpDir = $this->tmpDir();

        foreach ($DIRS_NEEDED as $path) {
            if (!file_exists($tmpDir.$path)) {
                mkdir($tmpDir.$path);
            }
        }

        foreach ($FILES_NEEDED as $path => $contents) {
            if (!file_exists($tmpDir.$path)) {
                file_put_contents($tmpDir.$path, $contents);
            }
        }
    }
}
