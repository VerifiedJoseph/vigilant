<?php

use PHPUnit\Framework\Attributes\CoversClass;
use Vigilant\Config\AbstractValidator;

#[CoversClass(AbstractValidator::class)]
class AbstractValidatorTest extends TestCase
{
    /**
     * Test `getConfig()`
     */
    public function testFetConfig(): void
    {
        $class = new class (['test' => 'hello']) extends AbstractValidator {
        };
        $this->assertEquals('hello', $class->getConfig('test'));
    }

    /**
     * Test `getEnv()`
     */
    public function testGetEnv(): void
    {
        putenv('VIGILANT_TEST=hello');

        $class = new class ([]) extends AbstractValidator {
        };
        $this->assertEquals('hello', $class->getEnv('TEST'));
    }

    /**
     * Test `getEnv()` with no environment variable
     */
    public function testGetEnvEmptyValue(): void
    {
        putenv('VIGILANT_TEST');

        $class = new class ([]) extends AbstractValidator {
        };
        $this->assertEquals('', $class->getEnv('TEST_1'));
    }
}
