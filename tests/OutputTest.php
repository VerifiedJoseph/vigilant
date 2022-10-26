<?php

use Vigilant\Output;

class OutputTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        Output::disableQuiet();
    }

    /**
     * Test output()
     */
    public function testOutput(): void
    {
        $text = 'hello Word';

        $this->expectOutputString($text . "\n");

        Output::text($text);
    }

    /**
     * Test quiet()
     */
    public function testQuiet(): void
    {
        Output::quiet();

        $text = 'hello Word';

        $this->expectOutputString('');

        Output::text($text);
    }
}
