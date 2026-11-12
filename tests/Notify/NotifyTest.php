<?php

declare(strict_types=1);

namespace Tests\Notify;

use Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use Vigilant\Feed\Details;
use Vigilant\Config;
use Vigilant\Message;
use Vigilant\Request;
use Vigilant\Logger;
use Vigilant\Notify\Notify;
use Vigilant\Notify\Payload;
use Vigilant\Notify\ServiceConfig;

#[CoversClass(Notify::class)]
#[UsesClass(Details::class)]
#[UsesClass(Config::class)]
#[UsesClass(Message::class)]
#[UsesClass(Request::class)]
#[UsesClass(Logger::class)]
#[UsesClass(Payload::class)]
#[UsesClass(ServiceConfig::class)]
class NotifyTest extends TestCase
{
    /** @var array<string, mixed> $feed */
    private array $feed = [
        'name' => 'GitHub status',
        'url' => 'https://www.githubstatus.com/history.rss',
        'interval' => 900
    ];

    public function testSend(): void
    {
        $this->expectOutputRegex('/Sent notification using ntfy/');

        $config = self::createStub(Config::class);
        $config->method('getNotificationService')->willReturn('ntfy');
        $config->method('getNtfyUrl')->willReturn('https://ntfy.sh');
        $config->method('getNtfyTopic')->willReturn('testing');
        $config->method('getNtfyPriority')->willReturn(0);

        $messages = [
            new Message('Hello World', 'Hello??')
        ];

        $notify = new notify(
            new Details($this->feed),
            $config,
            new Logger('UTC')
        );

        $notify->send($messages);
    }
}
