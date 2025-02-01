<?php

declare(strict_types=1);

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
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
    #[DoesNotPerformAssertions]
    public function testCreatingGotify(): void
    {
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
    #[DoesNotPerformAssertions]
    public function testCreatingGotifyWithPriorityFromFeedDetails(): void
    {
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
    #[DoesNotPerformAssertions]
    public function testCreatingGotifyWithTokenFromFeedDetails(): void
    {
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
    #[DoesNotPerformAssertions]
    public function testCreatingNtfy(): void
    {
        $config = self::createStub(Config::class);
        $config->method('getNotificationService')->willReturn('ntfy');
        $config->method('getNtfyUrl')->willReturn('https://ntfy.example.com/');
        $config->method('getNtfyPriority')->willReturn(0);

        new notify(new Details($this->feed), $config, self::$logger);
    }

   /**
    * Test creating Ntfy instance with password auth
    */
    #[DoesNotPerformAssertions]
    public function testCreatingNtfyWithPasswordAuth(): void
    {
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
    #[DoesNotPerformAssertions]
    public function testCreatingNtfyWithTokenAuth(): void
    {
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
    #[DoesNotPerformAssertions]
    public function testCreatingNtfyWithTokenFromFeedDetails(): void
    {
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
    #[DoesNotPerformAssertions]
    public function testCreatingNtfyWithTopicFromFeedDetails(): void
    {
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
    #[DoesNotPerformAssertions]
    public function testCreatingNtfyWithPriorityFromFeedDetails(): void
    {
        $config = self::createStub(Config::class);
        $config->method('getNotificationService')->willReturn('ntfy');
        $config->method('getNtfyUrl')->willReturn('https://ntfy.example.com/');
        $config->method('getNtfyPriority')->willReturn(0);

        $feed = $this->feed;
        $feed['ntfy_priority'] = 5;

        new notify(new Details($feed), $config, self::$logger);
    }
}
