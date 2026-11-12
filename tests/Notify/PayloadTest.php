<?php

declare(strict_types=1);

namespace Tests\Notify;

use Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Vigilant\Notify\Payload;
use Vigilant\Config;
use Vigilant\Message;

#[CoversClass(Payload::class)]
class PayloadTest extends TestCase
{
    /**
     * @var array<string, mixed> $gotifyPayload
     */
    protected array $gotifyPayload = [
        'title' => 'Hello',
        'message' => 'Hello World',
        'priority' => 10,
        'extras' => [
        'client::notification' => [
             'click' => ['url' => 'https://example.com']
            ]
        ]
    ];

    /**
     * @var array<string, mixed> $ntfyPayload
     */
    protected array $ntfyPayload = [
        'topic' => 'testing',
        'title' => 'Hello',
        'message' => 'Hello World',
        'markdown' => false,
        'priority' => 10,
        'click' => 'https://example.com',
    ];

    public function testCreatingGotifyPayload(): void
    {
        $config = self::createStub(Config::class);
        $config->method('getNotificationService')->willReturn('gotify');

        $message = self::createStub(Message::class);
        $message->method('getTitle')->willReturn($this->gotifyPayload['title']);
        $message->method('getBody')->willReturn($this->gotifyPayload['message']);
        $message->method('getUrl')->willReturn('https://example.com');

        $payload =  (new Payload($config))->get(
            $message,
            ['priority' => $this->gotifyPayload['priority']]
        );

        $this->assertEquals($this->gotifyPayload, $payload);
    }

    public function testCreatingNtfyPayload(): void
    {
        $config = self::createStub(Config::class);
        $config->method('getNotificationService')->willReturn('ntfy');

        $message = self::createStub(Message::class);
        $message->method('getTitle')->willReturn($this->ntfyPayload['title']);
        $message->method('getBody')->willReturn($this->ntfyPayload['message']);
        $message->method('getUrl')->willReturn($this->ntfyPayload['click']);

        $payload =  (new Payload($config))->get(
            $message,
            [
                'priority' => $this->ntfyPayload['priority'],
                'topic' => $this->ntfyPayload['topic']
            ]
        );

        $this->assertEquals($this->ntfyPayload, $payload);
    }
}
