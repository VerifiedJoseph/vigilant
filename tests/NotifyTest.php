<?php

declare(strict_types=1);

namespace Tests;

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
#[UsesClass(\Vigilant\Notification\AbstractNotification::class)]
#[UsesClass(\Vigilant\Exception\NotificationException::class)]
class NotifyTest extends TestCase
{
    private static Logger $logger;

    /** @var array<string, mixed> $feed */
    private array $feed = [
        'name' => 'GitHub status',
        'url' => 'https://www.githubstatus.com/history.rss',
        'interval' => 900
    ];

    private array $gotifyConfigValues = [
        'priority' => 0,
        'token' => 'fake-token',
        'server' => 'https://gotify.example.com/',
    ];

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

        $config = $this->createConfigStubForGotify();

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
        $config = $this->createConfigStubForGotify();
        $notify = new notify(new Details($this->feed), $config, self::$logger);

        $notifyReflection = new \ReflectionClass('Vigilant\Notify');
        $service = $notifyReflection->getProperty('service')->getValue($notify);

        $gotifyReflection = new \ReflectionClass('Vigilant\Notification\Gotify');
        $gotifyConfig = $gotifyReflection->getProperty('config')->getValue($service);

        $this->assertEquals($this->gotifyConfigValues, $gotifyConfig);
    }

   /**
    * Test creating Gotify instance with priority from feed details
    */
    public function testCreatingGotifyWithPriorityFromFeedDetails(): void
    {
        $config = $this->createConfigStubForGotify();

        $feed = $this->feed;
        $feed['gotify_priority'] = 5;

        $notify = new notify(new Details($feed), $config, self::$logger);

        $notifyReflection = new \ReflectionClass('Vigilant\Notify');
        $service = $notifyReflection->getProperty('service')->getValue($notify);

        $gotifyReflection = new \ReflectionClass('Vigilant\Notification\Gotify');
        $gotifyConfig = $gotifyReflection->getProperty('config')->getValue($service);

        $this->assertEquals($feed['gotify_priority'],$gotifyConfig['priority']);
    }

   /**
    * Test creating Gotify instance with token from feed details
    */
    public function testCreatingGotifyWithTokenFromFeedDetails(): void
    {
        $config = $this->createConfigStubForGotify();

        $feed = $this->feed;
        $feed['gotify_token'] = 'qwerty';

        $notify = new notify(new Details($feed), $config, self::$logger);

        $notifyReflection = new \ReflectionClass('Vigilant\Notify');
        $service = $notifyReflection->getProperty('service')->getValue($notify);

        $gotifyReflection = new \ReflectionClass('Vigilant\Notification\Gotify');
        $gotifyConfig = $gotifyReflection->getProperty('config')->getValue($service);

        $this->assertEquals($feed['gotify_token'],$gotifyConfig['token']);
    }

   /**
    * Test creating Ntfy instance without auth
    */
    public function testCreatingNtfy(): void
    {
        $config = $this->createConfigStubForNtfy();
        $notify = new notify(new Details($this->feed), $config, self::$logger);

        $notifyReflection = new \ReflectionClass('Vigilant\Notify');
        $service = $notifyReflection->getProperty('service')->getValue($notify);

        $ntfyReflection = new \ReflectionClass('Vigilant\Notification\Ntfy');
        $ntfyConfig = $ntfyReflection->getProperty('config')->getValue($service);

        $this->assertEquals($this->ntfyConfigValues, $ntfyConfig);
    }

   /**
    * Test creating Ntfy instance with password auth
    */
    public function testCreatingNtfyWithPasswordAuth(): void
    {
        $configValues = $this->ntfyConfigValues;
        $configValues['auth']['method'] = 'password';
        $configValues['auth']['username'] = 'bob';
        $configValues['auth']['password'] = 'qwerty';

        $config = $this->createConfigStubForNtfy();
        $config->method('getNtfyAuthMethod')->willReturn($configValues['auth']['method']);
        $config->method('getNtfyUsername')->willReturn($configValues['auth']['username']);
        $config->method('getNtfyPassword')->willReturn( $configValues['auth']['password']);

        $notify = new notify(new Details($this->feed), $config, self::$logger);

        $notifyReflection = new \ReflectionClass('Vigilant\Notify');
        $service = $notifyReflection->getProperty('service')->getValue($notify);

        $ntfyReflection = new \ReflectionClass('Vigilant\Notification\Ntfy');
        $ntfyConfig = $ntfyReflection->getProperty('config')->getValue($service);

        $this->assertEquals($configValues['auth'], $ntfyConfig['auth']);
    }

   /**
    * Test creating Ntfy instance with token auth
    */
    public function testCreatingNtfyWithTokenAuth(): void
    {
        $configValues = $this->ntfyConfigValues;
        $configValues['auth']['method'] = 'token';
        $configValues['auth']['token'] = 'a_ntfy_token';

        $config = $this->createConfigStubForNtfy();
        $config->method('getNtfyAuthMethod')->willReturn($configValues['auth']['method']);
        $config->method('getNtfyToken')->willReturn($configValues['auth']['token']);

        $notify = new notify(new Details($this->feed), $config, self::$logger);

        $notifyReflection = new \ReflectionClass('Vigilant\Notify');
        $service = $notifyReflection->getProperty('service')->getValue($notify);

        $ntfyReflection = new \ReflectionClass('Vigilant\Notification\Ntfy');
        $ntfyConfig = $ntfyReflection->getProperty('config')->getValue($service);

        $this->assertEquals($configValues['auth'], $ntfyConfig['auth']);
    }

   /**
    * Test creating Ntfy instance with token from feed details
    */
    public function testCreatingNtfyWithTokenFromFeedDetails(): void
    {
        $config = $this->createConfigStubForNtfy();

        $feed = $this->feed;
        $feed['ntfy_token'] = 'a_ntfy_token';

        $notify = new notify(new Details($feed), $config, self::$logger);

        $notifyReflection = new \ReflectionClass('Vigilant\Notify');
        $service = $notifyReflection->getProperty('service')->getValue($notify);

        $ntfyReflection = new \ReflectionClass('Vigilant\Notification\Ntfy');
        $ntfyConfig = $ntfyReflection->getProperty('config')->getValue($service);

        $this->assertEquals('token', $ntfyConfig['auth']['method']);
        $this->assertEquals($feed['ntfy_token'], $ntfyConfig['auth']['token']);
    }

   /**
    * Test creating Ntfy instance with topic from feed details
    */
    public function testCreatingNtfyWithTopicFromFeedDetails(): void
    {
        $config = $this->createConfigStubForNtfy();

        $feed = $this->feed;
        $feed['ntfy_topic'] = 'a_ntfy_topic';

        $notify = new notify(new Details($feed), $config, self::$logger);

        $notifyReflection = new \ReflectionClass('Vigilant\Notify');
        $service = $notifyReflection->getProperty('service')->getValue($notify);

        $ntfyReflection = new \ReflectionClass('Vigilant\Notification\Ntfy');
        $ntfyConfig = $ntfyReflection->getProperty('config')->getValue($service);

        $this->assertEquals($feed['ntfy_topic'], $ntfyConfig['topic']);
    }

   /**
    * Test creating Ntfy instance with priority from feed details
    */
    public function testCreatingNtfyWithPriorityFromFeedDetails(): void
    {
        $config = self::createStub(Config::class);
        $config->method('getNotificationService')->willReturn('ntfy');
        $config->method('getNtfyUrl')->willReturn('https://ntfy.example.com/');
        $config->method('getNtfyPriority')->willReturn(0);

        $feed = $this->feed;
        $feed['ntfy_priority'] = 5;

        $notify = new notify(new Details($feed), $config, self::$logger);

        $notifyReflection = new \ReflectionClass('Vigilant\Notify');
        $service = $notifyReflection->getProperty('service')->getValue($notify);

        $ntfyReflection = new \ReflectionClass('Vigilant\Notification\Ntfy');
        $ntfyConfig = $ntfyReflection->getProperty('config')->getValue($service);

        $this->assertEquals($feed['ntfy_priority'], $ntfyConfig['priority']);
    }

    private function createConfigStubForGotify()
    {
        $stub = self::createStub(Config::class);
        $stub->method('getNotificationService')->willReturn('gotify');
        $stub->method('getGotifyUrl')->willReturn($this->gotifyConfigValues['server']);
        $stub->method('getGotifyPriority')->willReturn($this->gotifyConfigValues['priority']);
        $stub->method('getGotifyToken')->willReturn($this->gotifyConfigValues['token']);
        return $stub;
    }

    private function createConfigStubForNtfy()
    {
        $stub = self::createStub(Config::class);
        $stub->method('getNotificationService')->willReturn('ntfy');
        $stub->method('getNtfyUrl')->willReturn($this->ntfyConfigValues['server']);
        $stub->method('getNtfyPriority')->willReturn($this->ntfyConfigValues['priority']);
        return $stub;
    }
}
