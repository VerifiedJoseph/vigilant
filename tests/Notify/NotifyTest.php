<?php

declare(strict_types=1);

namespace Tests\Notify;

use Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\MockObject\Stub;
use Vigilant\Notify\Notify;
use Vigilant\Feed\Details;
use Vigilant\Config;
use Vigilant\Message;
use Vigilant\Request;
use Vigilant\Logger;

#[CoversClass(Notify::class)]
#[UsesClass(Details::class)]
#[UsesClass(Config::class)]
#[UsesClass(Message::class)]
#[UsesClass(Request::class)]
#[UsesClass(Logger::class)]
class NotifyTest extends TestCase
{
    private static Logger $logger;

    /** @var array<string, mixed> $feed */
    private array $feed = [
        'name' => 'GitHub status',
        'url' => 'https://www.githubstatus.com/history.rss',
        'interval' => 900
    ];

    /** @var array<string, mixed> $gotifyConfigValues Config values use by `createConfigStubForGotify()` */
    private array $gotifyConfigValues = [
        'priority' => 0,
        'token' => 'fake-token',
        'server' => 'https://gotify.example.com/',
    ];

    /** @var array<string, mixed> $ntfyConfigValues Config values use by `createConfigStubForNtfy()` */
    private array $ntfyConfigValues = [
        'server' => 'https://ntfy.example.com/',
        'topic' => '',
        'priority' => 0,
        'auth' => [
            'method' => ''
        ]
    ];

    public static function setUpBeforeClass(): void
    {
        self::$logger = new Logger('UTC');
    }

    public function testSend(): void
    {
        /*$this->expectOutputRegex('/Sent notification using ntfy/');

        $config = self::createStub(Config::class);
        $config->method('getNotificationService')->willReturn('ntfy');
        $config->method('getNtfyUrl')->willReturn('https://ntfy.sh');
        $config->method('getNtfyTopic')->willReturn('testing');
        $config->method('getNtfyPriority')->willReturn(0);

        $messages = [
            new Message('Hello World', 'Hello??')
        ];

        $notify = new notify(new Details($this->feed), $config, self::$logger);
        $notify->send($messages);*/
    }
}
