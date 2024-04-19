<?php

namespace Vigilant;

class Version
{
    /**
     * @var string $version Vigilant version
     */
    private static string $version = '1.0.1';

    /**
     * Returns version number
     */
    public static function get(): string
    {
        return self::$version;
    }
}
