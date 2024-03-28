<?php

use Vigilant\Config\Base;

class BaseTest extends TestCase
{
    /**
     * Test `getEnv` with no environment variable
     */
    public function testGetEnvEmptyValue(): void
    {
        putenv('VIGILANT_TEST');

        $class = new class () extends Base {
        };
        $this->assertEquals('', $class->getEnv('TEST_1'));
    }
}
