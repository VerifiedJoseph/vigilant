<?php

declare(strict_types=1);

namespace Vigilant;

class UserAgent
{
    /**
     * Returns default user-agent string `Vigilant/VERSION (https://github.com/VerifiedJoseph/vigilant)`
     * @return string
     */
    public static function getDefault(): string
    {
        return sprintf('Vigilant/%s (https://github.com/VerifiedJoseph/vigilant)', Version::get());
    }
}
