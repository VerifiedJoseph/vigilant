<?php

declare(strict_types=1);

namespace Vigilant\Helper;

class Format
{
    /**
     * Format a gotify server URL
     * @param string $url
     * @return string
     */
    public static function gotifyUrl(string $url): string
    {
        if (substr($url, -1) !== '/') {
            $url .= '/';
        }

        return  $url . 'message';
    }
}
