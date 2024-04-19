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

    /**
     * Reset environment variables
     */
    protected function resetEnvs(): void
    {
        // Unset environment variables before each test
        putenv('VIGILANT_TIMEZONE');
        putenv('VIGILANT_FEEDS_FILE');
        putenv('VIGILANT_NOTIFICATION_SERVICE');

        // Gotify
        putenv('VIGILANT_NOTIFICATION_GOTIFY_URL');
        putenv('VIGILANT_NOTIFICATION_GOTIFY_TOKEN');

        // Ntfy
        putenv('VIGILANT_NOTIFICATION_NTFY_URL');
        putenv('VIGILANT_NOTIFICATION_NTFY_TOPIC');
        putenv('VIGILANT_NOTIFICATION_NTFY_AUTH');
        putenv('VIGILANT_NOTIFICATION_NTFY_USERNAME');
        putenv('VIGILANT_NOTIFICATION_NTFY_PASSWORD');
    }
}
