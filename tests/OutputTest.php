<?php

use PHPUnit\Framework\Attributes\CoversClass;
use Vigilant\Output;

#[CoversClass(Output::class)]
class OutputTest extends TestCase
{
    /**
     * @var string $text
     */
    private string $text = 'Hello World';

    /**
     * Test output()
     */
    public function testOutput(): void
    {
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
}
