<?php

use PHPUnit\Framework\Attributes\CoversClass;
use Vigilant\Message;

#[CoversClass(Message::class)]
class MessageTest extends TestCase
{
    public function testClass(): void
    {
        $title = 'Hello World';
        $body = 'Hello World from phpunit';
        $url = 'https://example.com/';

        $message = new Message($title, $body, $url);

        $this->assertEquals($title, $message->getTitle());
        $this->assertEquals($body, $message->getBody());
        $this->assertEquals($url, $message->getUrl());
    }

    /**
     * Test Message class with title prefix
     */
    public function testTitlePrefix(): void
    {
        $title = 'Hello World';
        $body = 'Hello World from phpunit';
        $url = 'https://example.com/';
        $prefix = 'testing!';

        $fullTitle = $prefix . ' ' . $title;

        $message = new Message($title, $body, $url, $prefix);

        $this->assertEquals($fullTitle, $message->getTitle());
    }
}
