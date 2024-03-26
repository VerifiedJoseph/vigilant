<?php

use Vigilant\Notify;
use Vigilant\Feed\Details;
use Vigilant\Config;
use Vigilant\Notification\Gotify;
use Vigilant\Notification\Ntfy;

class NotifyTest extends TestCase
{
    /** @var array<string, mixed> $feed */
    private array $feed = [
        'name' => 'GitHub status',
        'url' => 'https://www.githubstatus.com/history.rss',
        'interval' => 900
    ];

   /**
    * Test creating Gotify instance
    */
    public function testCreatingGotify(): void
    {
        /** @var PHPUnit\Framework\MockObject\Stub&Config */
        $config = self::createStub(Config::class);
        $config->method('getNotificationService')->willReturn('gotify');
        $config->method('getGotifyUrl')->willReturn('https://gotify.example.com/');
        $config->method('getGotifyPriority')->willReturn(0);
        $config->method('getGotifyToken')->willReturn('fake-token');

        $notify = new notify(new Details($this->feed), $config);

        $this->assertInstanceOf(Gotify::class, $notify->getClass());
    }

   /**
    * Test creating Gotify instance with priority from feed details
    */
    public function testCreatingGotifyWithPriorityFromFeedDetails(): void
    {
        /** @var PHPUnit\Framework\MockObject\Stub&Config */
        $config = self::createStub(Config::class);
        $config->method('getNotificationService')->willReturn('gotify');
        $config->method('getGotifyUrl')->willReturn('https://gotify.example.com/');
        $config->method('getGotifyPriority')->willReturn(0);
        $config->method('getGotifyToken')->willReturn('fake-token');

        $feed = $this->feed;
        $feed['gotify_priority'] = 5;

        $notify = new notify(new Details($feed), $config);

        $this->assertInstanceOf(Gotify::class, $notify->getClass());
    }

   /**
    * Test creating Gotify instance with token from feed details
    */
    public function testCreatingGotifyWithTokenFromFeedDetails(): void
    {
        /** @var PHPUnit\Framework\MockObject\Stub&Config */
        $config = self::createStub(Config::class);
        $config->method('getNotificationService')->willReturn('gotify');
        $config->method('getGotifyUrl')->willReturn('https://gotify.example.com/');
        $config->method('getGotifyPriority')->willReturn(0);
        $config->method('getGotifyToken')->willReturn('fake-token');

        $feed = $this->feed;
        $feed['gotify_token'] = 'qwerty';

        $notify = new notify(new Details($feed), $config);

        $this->assertInstanceOf(Gotify::class, $notify->getClass());
    }

   /**
    * Test creating Ntfy instance without auth
    */
    public function testCreatingNtfy(): void
    {
        /** @var PHPUnit\Framework\MockObject\Stub&Config */
        $config = self::createStub(Config::class);
        $config->method('getNotificationService')->willReturn('ntfy');
        $config->method('getNtfyUrl')->willReturn('https://ntfy.example.com/');
        $config->method('getNtfyPriority')->willReturn(0);

        $notify = new notify(new Details($this->feed), $config);

        $this->assertInstanceOf(Ntfy::class, $notify->getClass());
    }

   /**
    * Test creating Ntfy instance with password auth
    */
    public function testCreatingNtfyWithPasswordAuth(): void
    {
        /** @var PHPUnit\Framework\MockObject\Stub&Config */
        $config = self::createStub(Config::class);
        $config->method('getNotificationService')->willReturn('ntfy');
        $config->method('getNtfyUrl')->willReturn('https://ntfy.example.com/');
        $config->method('getNtfyPriority')->willReturn(0);
        $config->method('getNtfyAuthMethod')->willReturn('password');
        $config->method('getNtfyUsername')->willReturn('bob');
        $config->method('getNtfyPassword')->willReturn('qwerty');

        $notify = new notify(new Details($this->feed), $config);

        $this->assertInstanceOf(Ntfy::class, $notify->getClass());
    }

   /**
    * Test creating Ntfy instance with token auth
    */
    public function testCreatingNtfyWithTokenAuth(): void
    {
        /** @var PHPUnit\Framework\MockObject\Stub&Config */
        $config = self::createStub(Config::class);
        $config->method('getNotificationService')->willReturn('ntfy');
        $config->method('getNtfyUrl')->willReturn('https://ntfy.example.com/');
        $config->method('getNtfyPriority')->willReturn(0);
        $config->method('getNtfyToken')->willReturn('fake-token');
        $config->method('getNtfyAuthMethod')->willReturn('token');

        $notify = new notify(new Details($this->feed), $config);

        $this->assertInstanceOf(Ntfy::class, $notify->getClass());
    }

   /**
    * Test creating Ntfy instance with token from feed details
    */
    public function testCreatingNtfyWithTokenFromFeedDetails(): void
    {
        /** @var PHPUnit\Framework\MockObject\Stub&Config */
        $config = self::createStub(Config::class);
        $config->method('getNotificationService')->willReturn('ntfy');
        $config->method('getNtfyUrl')->willReturn('https://ntfy.example.com/');
        $config->method('getNtfyPriority')->willReturn(0);

        $feed = $this->feed;
        $feed['ntfy_token'] = 'qwerty';

        $notify = new notify(new Details($feed), $config);

        $this->assertInstanceOf(Ntfy::class, $notify->getClass());
    }

   /**
    * Test creating Ntfy instance with topic from feed details
    */
    public function testCreatingNtfyWithTopicFromFeedDetails(): void
    {
        /** @var PHPUnit\Framework\MockObject\Stub&Config */
        $config = self::createStub(Config::class);
        $config->method('getNotificationService')->willReturn('ntfy');
        $config->method('getNtfyUrl')->willReturn('https://ntfy.example.com/');
        $config->method('getNtfyPriority')->willReturn(0);

        $feed = $this->feed;
        $feed['ntfy_topic'] = 'qwerty';

        $notify = new notify(new Details($feed), $config);

        $this->assertInstanceOf(Ntfy::class, $notify->getClass());
    }

   /**
    * Test creating Ntfy instance with priority from feed details
    */
    public function testCreatingNtfyWithPriorityFromFeedDetails(): void
    {
        /** @var PHPUnit\Framework\MockObject\Stub&Config */
        $config = self::createStub(Config::class);
        $config->method('getNotificationService')->willReturn('ntfy');
        $config->method('getNtfyUrl')->willReturn('https://ntfy.example.com/');
        $config->method('getNtfyPriority')->willReturn(0);

        $feed = $this->feed;
        $feed['ntfy_priority'] = 5;

        $notify = new notify(new Details($feed), $config);

        $this->assertInstanceOf(Ntfy::class, $notify->getClass());
    }
}
