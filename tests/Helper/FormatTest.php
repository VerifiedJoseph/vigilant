<?php

declare(strict_types=1);

namespace Tests\Helper;

use Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Vigilant\Helper\Format;

#[CoversClass(Format::class)]
class FormatTest extends TestCase
{
    /**
     * Test `gotifyUrl()` with a URL without an ending forward slash
     */
    public function testGotifyUrlWithNoForwardSlash(): void
    {
        $this->assertEquals(
            'https://gotify.example.com/message',
            Format::gotifyUrl('https://gotify.example.com')
        );
    }

    /**
     * Test `gotifyUrl()` with a URL that as a ending forward slash
     */
    public function testGotifyUrlWithForwardSlash(): void
    {
        $this->assertEquals(
            'https://gotify.example.com/message',
            Format::gotifyUrl('https://gotify.example.com/')
        );
    }
}
