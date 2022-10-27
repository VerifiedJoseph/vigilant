<?php

use Vigilant\Output;

class OutputTest extends TestCase
{
    /**
     * @var string $text
     */
    private string $text = 'Hello World';

    public function setUp(): void
    {
        Output::disableQuiet();
    }

    /**
     * Test output()
     */
    public function testOutput(): void
    {
        $this->expectOutputString($this->text . "\n");

        Output::text($this->text);
    }

    /**
     * Test quiet()
     */
    public function testQuiet(): void
    {
        Output::quiet();

        $this->expectOutputString('');

        Output::text($this->text);
    }

    /**
     * Test disableQuiet()
     */
    public function testDisableQuiet(): void
    {
        Output::disableQuiet();

        $this->expectOutputString($this->text . "\n");

        Output::text($this->text);
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
