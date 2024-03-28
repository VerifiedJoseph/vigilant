<?php

namespace Vigilant\Config;

abstract class Base
{
    protected string $envPrefix = 'VIGILANT_';

    /**
     * Check for an environment variable
     *
     * @param string $name Variable name excluding prefix
     */
    public function hasEnv(string $name): bool
    {
        if (getenv($this->envPrefix . $name) === false) {
            return false;
        }

        return true;
    }

    /**
     * Get an environment variable
     *
     * @param string $name Variable name excluding prefix
     */
    public function getEnv(string $name): string
    {
        if ($this->hasEnv($name) === true) {
            return (string) getenv($this->envPrefix . $name);
        }

        return '';
    }
}
