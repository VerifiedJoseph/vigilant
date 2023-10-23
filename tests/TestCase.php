<?php

use PHPUnit\Framework\TestCase as BaseTestCase;

/**
 * Class TestCase
 */
abstract class TestCase extends BaseTestCase
{
    /**
     * Load sample file data
     */
    protected static function loadSample(string $name): string
    {
        $path = __DIR__ . '/files/' . $name;
        $contents = file_get_contents($path);

        if ($contents === false) {
            throw new RuntimeException(sprintf('Unable to load sample file: %s', $path));
        }

        return $contents;
    }

    /**
     * Get sample file path
     */
    protected static function getSamplePath(string $name): string
    {
        return __DIR__ . '/files/' . $name;
    }
}
