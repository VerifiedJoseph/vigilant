<?php

declare(strict_types=1);

namespace Vigilant;

class UserAgent
{
    private static array $presets = [
        'chrome' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36',
        'firefox' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0',
    ];

    /**
     * Returns default user-agent string `Vigilant/VERSION (https://github.com/VerifiedJoseph/vigilant)`
     * @return string
     */
    public static function getDefault(): string
    {
        return sprintf('Vigilant/%s (https://github.com/VerifiedJoseph/vigilant)', Version::get());
    }

    /**
     * Formats user configured user agent into a valid user agent string if value is a preset name
     * @param string $value User configured user agent
     * @return string
     */
    public static function format(string $value): string
    {
        if (array_key_exists($value, self::$presets) === true) {
            return self::$presets[$value];
        }

        return $value;
    }
}
