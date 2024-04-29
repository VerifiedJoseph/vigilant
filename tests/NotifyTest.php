<?php

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use Vigilant\Notify;
use Vigilant\Feed\Details;
use Vigilant\Config;
use Vigilant\Message;
use Vigilant\Logger;
use Vigilant\Notification\Gotify;
use Vigilant\Notification\Ntfy;

#[CoversClass(Notify::class)]
#[UsesClass(Details::class)]
#[UsesClass(Config::class)]
#[UsesClass(Message::class)]
#[UsesClass(Logger::class)]
#[UsesClass(Gotify::class)]
#[UsesClass(Ntfy::class)]
#[UsesClass(Vigilant\Output::class)]
#[UsesClass(Vigilant\Notification\AbstractNotification::class)]
#[UsesClass(Vigilant\Exception\NotificationException::class)]
class NotifyTest extends TestCase
{
    private static Logger $logger;

    /** @var array<string, mixed> $feed */
    private array $feed = [
        'name' => 'GitHub status',
        'url' => 'https://www.githubstatus.com/history.rss',
        'interval' => 900
    ];

    public static function setUpBeforeClass(): void
    {
        self::$logger = new Logger('UTC');
    }

    public function testSend(): void
    {
        $this->expectOutputRegex('/Sent notification using Ntfy/');

        /** @var PHPUnit\Framework\MockObject\Stub&Config */
        $config = self::createStub(Config::class);
        $config->method('getNotificationService')->willReturn('ntfy');
        $config->method('getNtfyUrl')->willReturn('https://ntfy.sh');
        $config->method('getNtfyTopic')->willReturn('testing');
        $config->method('getNtfyPriority')->willReturn(0);

        $messages = [
            new Message('Hello World', 'Hello??')
        ];

        $notify = new notify(new Details($this->feed), $config, self::$logger);
        $notify->send($messages);
    }

    public function testSendWithNotificationError(): void
    {
        $this->expectOutputRegex('/[Notification error]/');

        /** @var PHPUnit\Framework\MockObject\Stub&Config */
        $config = self::createStub(Config::class);
        $config->method('getNotificationService')->willReturn('gotify');
        $config->method('getGotifyUrl')->willReturn('https://gotify.example.com/');
        $config->method('getGotifyPriority')->willReturn(0);
        $config->method('getGotifyToken')->willReturn('fake-token');

        $messages = [
            new Message('Hello World', 'Hello??')
        ];

        $notify = new notify(new Details($this->feed), $config, self::$logger);
        $notify->send($messages);
    }

   /**
    * Test creating Gotify instance
    */
    public function testCreatingGotify(): void
    {
        $this->expectNotToPerformAssertions();

        /** @var PHPUnit\Framework\MockObject\Stub&Config */
        $config = self::createStub(Config::class);
        $config->method('getNotificationService')->willReturn('gotify');
        $config->method('getGotifyUrl')->willReturn('https://gotify.example.com/');
        $config->method('getGotifyPriority')->willReturn(0);
        $config->method('getGotifyToken')->willReturn('fake-token');

        new notify(new Details($this->feed), $config, self::$logger);
    }

   /**
    * Test creating Gotify instance with priority from feed details
    */
    public function testCreatingGotifyWithPriorityFromFeedDetails(): void
    {
        $this->expectNotToPerformAssertions();

        /** @var PHPUnit\Framework\MockObject\Stub&Config */
        $config = self::createStub(Config::class);
        $config->method('getNotificationService')->willReturn('gotify');
        $config->method('getGotifyUrl')->willReturn('https://gotify.example.com/');
        $config->method('getGotifyPriority')->willReturn(0);
        $config->method('getGotifyToken')->willReturn('fake-token');

        $feed = $this->feed;
        $feed['gotify_priority'] = 5;

        new notify(new Details($feed), $config, self::$logger);
    }

   /**
    * Test creating Gotify instance with token from feed details
    */
    public function testCreatingGotifyWithTokenFromFeedDetails(): void
    {
        $this->expectNotToPerformAssertions();

        /** @var PHPUnit\Framework\MockObject\Stub&Config */
        $config = self::createStub(Config::class);
        $config->method('getNotificationService')->willReturn('gotify');
        $config->method('getGotifyUrl')->willReturn('https://gotify.example.com/');
        $config->method('getGotifyPriority')->willReturn(0);
        $config->method('getGotifyToken')->willReturn('fake-token');

        $feed = $this->feed;
        $feed['gotify_token'] = 'qwerty';

        new notify(new Details($feed), $config, self::$logger);
    }

   /**
    * Test creating Ntfy instance without auth
    */
    public function testCreatingNtfy(): void
    {
        $this->expectNotToPerformAssertions();

        /** @var PHPUnit\Framework\MockObject\Stub&Config */
        $config = self::createStub(Config::class);
        $config->method('getNotificationService')->willReturn('ntfy');
        $config->method('getNtfyUrl')->willReturn('https://ntfy.example.com/');
        $config->method('getNtfyPriority')->willReturn(0);

        new notify(new Details($this->feed), $config, self::$logger);
    }

   /**
    * Test creating Ntfy instance with password auth
    */
    public function testCreatingNtfyWithPasswordAuth(): void
    {
        $this->expectNotToPerformAssertions();

        /** @var PHPUnit\Framework\MockObject\Stub&Config */
        $config = self::createStub(Config::class);
        $config->method('getNotificationService')->willReturn('ntfy');
        $config->method('getNtfyUrl')->willReturn('https://ntfy.example.com/');
        $config->method('getNtfyPriority')->willReturn(0);
        $config->method('getNtfyAuthMethod')->willReturn('password');
        $config->method('getNtfyUsername')->willReturn('bob');
        $config->method('getNtfyPassword')->willReturn('qwerty');

        new notify(new Details($this->feed), $config, self::$logger);
    }

   /**
    * Test creating Ntfy instance with token auth
    */
    public function testCreatingNtfyWithTokenAuth(): void
    {
        $this->expectNotToPerformAssertions();

        /** @var PHPUnit\Framework\MockObject\Stub&Config */
        $config = self::createStub(Config::class);
        $config->method('getNotificationService')->willReturn('ntfy');
        $config->method('getNtfyUrl')->willReturn('https://ntfy.example.com/');
        $config->method('getNtfyPriority')->willReturn(0);
        $config->method('getNtfyToken')->willReturn('fake-token');
        $config->method('getNtfyAuthMethod')->willReturn('token');

        new notify(new Details($this->feed), $config, self::$logger);
    }

   /**
    * Test creating Ntfy instance with token from feed details
    */
    public function testCreatingNtfyWithTokenFromFeedDetails(): void
    {
        $this->expectNotToPerformAssertions();

        /** @var PHPUnit\Framework\MockObject\Stub&Config */
        $config = self::createStub(Config::class);
        $config->method('getNotificationService')->willReturn('ntfy');
        $config->method('getNtfyUrl')->willReturn('https://ntfy.example.com/');
        $config->method('getNtfyPriority')->willReturn(0);

        $feed = $this->feed;
        $feed['ntfy_token'] = 'qwerty';

        new notify(new Details($feed), $config, self::$logger);
    }

   /**
    * Test creating Ntfy instance with topic from feed details
    */
    public function testCreatingNtfyWithTopicFromFeedDetails(): void
    {
        $this->expectNotToPerformAssertions();

        /** @var PHPUnit\Framework\MockObject\Stub&Config */
        $config = self::createStub(Config::class);
        $config->method('getNotificationService')->willReturn('ntfy');
        $config->method('getNtfyUrl')->willReturn('https://ntfy.example.com/');
        $config->method('getNtfyPriority')->willReturn(0);

        $feed = $this->feed;
        $feed['ntfy_topic'] = 'qwerty';

        new notify(new Details($feed), $config, self::$logger);
    }

   /**
    * Test creating Ntfy instance with priority from feed details
    */
    public function testCreatingNtfyWithPriorityFromFeedDetails(): void
    {
        $this->expectNotToPerformAssertions();

        /** @var PHPUnit\Framework\MockObject\Stub&Config */
        $config = self::createStub(Config::class);
        $config->method('getNotificationService')->willReturn('ntfy');
        $config->method('getNtfyUrl')->willReturn('https://ntfy.example.com/');
        $config->method('getNtfyPriority')->willReturn(0);

        $feed = $this->feed;
        $feed['ntfy_priority'] = 5;

        new notify(new Details($feed), $config, self::$logger);
    }
}
