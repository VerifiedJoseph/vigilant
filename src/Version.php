<?php

namespace Vigilant;

class Version
{
    /**
     * @var string $version Vigilant version
     */
    private static string $version = '1.2.0';

    /**
     * @var string $cacheFormatVersion Cache format version
     */
    private static int $cacheFormatVersion = 1;

    /**
     * Returns version number
     */
    public static function get(): string
    {
        return self::$version;
    }

    /**
     * Returns cache format version
     */
    public static function getCacheFormatVersion(): int
    {
        return self::$cacheFormatVersion;
    }
}
