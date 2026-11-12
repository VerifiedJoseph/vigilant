<?php

namespace Vigilant\Helper;

class Format
{
    static function gotifyUrl(string $url): string
    {
        if (substr($url, -1) !== '/') {
            $url .= '/';
        }

        return  $url . 'message';
    }
}
