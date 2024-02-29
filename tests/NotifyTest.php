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
    * Test creating Ntfy instance without auth
    */
    public function testCreatingNtfy(): void
    {
        /** @var PHPUnit\Framework\MockObject\Stub&Config */
        $config = self::createStub(Config::class);
        $config->method('getNotificationService')->willReturn('ntfy');
        $config->method('getNtfyUrl')->willReturn('https://ntfy.example.com/');
        $config->method('getNtfyPriority')->willReturn(0);
        $config->method('getNtfyToken')->willReturn('fake-token');

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
        $config->method('getNtfyToken')->willReturn('fake-token');
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
         $config->method('getNtfyToken')->willReturn('bob');

        $notify = new notify(new Details($this->feed), $config);

        $this->assertInstanceOf(Ntfy::class, $notify->getClass());
    }
}
