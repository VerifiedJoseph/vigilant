<?php

use PHPUnit\Framework\Attributes\CoversClass;
use Vigilant\Message;
use Vigilant\Helper\Json;

#[CoversClass(Message::class)]
class MessageTest extends TestCase
{
    /**
     * @var array<string, mixed> $samples Test sample
     */
    private static array $samples = [];

    public static function setUpBeforeClass(): void
    {
        self::$samples = Json::decode(self::loadSample('message-truncation.json'));
    }

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

    /**
     * Test message truncation with the default truncation length
     */
    public function testTruncation(): void
    {
        $message = new Message(
            title: '',
            body: self::$samples['default']['input'],
            truncate: true
        );

        $this->assertEquals(self::$samples['default']['output'], $message->getBody());
    }

    /**
     * Test message truncation with a custom truncation length
     */
    public function testCustomLengthTruncation(): void
    {
        $message = new Message(
            title: '',
            body: self::$samples['custom']['input'],
            truncate: true,
            truncateLength: self::$samples['custom']['length']
        );

        $this->assertEquals(self::$samples['custom']['output'], $message->getBody());
    }

    /**
     * Test message truncation with text that is shorter than given truncation length
     */
    public function testTruncationWithTextShorterThatTruncationLength(): void
    {
        $message = new Message(
            title: '',
            body: self::$samples['short']['input'],
            truncate: true,
            truncateLength: self::$samples['short']['length']
        );

        $this->assertEquals(self::$samples['short']['output'], $message->getBody());
    }
}
