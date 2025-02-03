<?php

declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use Vigilant\Exception\ConfigException;

#[CoversClass(ConfigException::class)]
class ConfigExceptionTest extends TestCase
{
    public function testException(): void
    {
        $this->expectException(ConfigException::class);
        $this->expectExceptionMessage('[Config error] Testing');

        throw new ConfigException('Testing');
    }
}
