<?php

declare(strict_types=1);

namespace Vigilant\Config;

abstract class AbstractValidator
{
    protected string $envPrefix = 'VIGILANT_';

    /** @var array<string, mixed> $config Config */
    protected array $config = [];

    /**
     * @param array<string, mixed> $defaults Config defaults
     */
    public function __construct(array $defaults)
    {
        $this->config = $defaults;
    }

    /**
     * Returns config
     *
     * @return array<string, mixed>
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * Check if a environment variable is a boolean
     *
     * @param string $name Variable name excluding prefix
     */
    public function isEnvBoolean(string $name): bool
    {
        return in_array($this->getEnv($name), ['true', 'false']);
    }

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
