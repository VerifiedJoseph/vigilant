<?php

use Vigilant\Output;

class OutputTest extends TestCase
{
    public function setUp(): void
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

	/**
     * Test newline()
     */
    public function testNewline(): void
    {
        $this->expectOutputString(PHP_EOL);

        Output::newline();
    }

    public function tearDown(): void
    {
        Output::disableQuiet();
    }
}
