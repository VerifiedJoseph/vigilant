<?php

use PHPUnit\Framework\Attributes\CoversClass;
use Vigilant\Config\Base;

#[CoversClass(Base::class)]
class BaseTest extends TestCase
{
    /**
     * Test `getEnv()`
     */
    public function testGetEnv(): void
    {
        putenv('VIGILANT_TEST=hello');

        $class = new class ([]) extends Base {
        };
        $this->assertEquals('hello', $class->getEnv('TEST'));
    }

    /**
     * Test `getEnv()` with no environment variable
     */
    public function testGetEnvEmptyValue(): void
    {
        putenv('VIGILANT_TEST');

        $class = new class ([]) extends Base {
        };
        $this->assertEquals('', $class->getEnv('TEST_1'));
    }
}
