<?php

declare(strict_types=1);

namespace Tests\Notify;

use Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\MockObject\Stub;
use Vigilant\Notify\ServiceConfig;
use Vigilant\Feed\Details;
use Vigilant\Config;
use Vigilant\Request;

#[CoversClass(ServiceConfig::class)]
#[UsesClass(Details::class)]
#[UsesClass(Config::class)]
#[UsesClass(Request::class)]
class ServiceConfigTest extends TestCase
{
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

   /**
    * Test creating Gotify service config
    */
    public function testCreatingGotifyServiceConfig(): void
    {
        $config = $this->createConfigStubForGotify();
        $serviceConfig = new ServiceConfig($config);

        $data = $serviceConfig->get(new Details($this->feed));

        $this->assertEquals($this->gotifyConfigValues, $data);
    }

   /**
    * Test creating Gotify instance with server url from feed details
    */
    public function testCreatingGotifyWithServerFromFeedDetails(): void
    {
        $config = $this->createConfigStubForGotify();
        $serviceConfig = new ServiceConfig($config);

        $feed = $this->feed;
        $feed['gotify_url'] = 'https://gotify.example.com';

        $data = $serviceConfig->get(new Details($feed));

        $this->assertEquals($feed['gotify_url'], $data['server']);
    }

   /**
    * Test creating Gotify instance with priority from feed details
    */
    public function testCreatingGotifyWithPriorityFromFeedDetails(): void
    {
        $config = $this->createConfigStubForGotify();
        $serviceConfig = new ServiceConfig($config);

        $feed = $this->feed;
        $feed['gotify_priority'] = 5;
        
        $data = $serviceConfig->get(new Details($feed));

        $this->assertEquals($feed['gotify_priority'], $data['priority']);
    }

   /**
    * Test creating Gotify instance with token from feed details
    */
    public function testCreatingGotifyWithTokenFromFeedDetails(): void
    {
        $config = $this->createConfigStubForGotify();
        $serviceConfig = new ServiceConfig($config);

        $feed = $this->feed;
        $feed['gotify_token'] = 'qwerty';

        $data = $serviceConfig->get(new Details($feed));

        $this->assertEquals($feed['gotify_token'], $data['token']);
    }

   /**
    * Test creating Ntfy service config
    */
    public function testCreatingNtfyServiceConfig(): void
    {
        $config = $this->createConfigStubForNtfy();
        $serviceConfig = new ServiceConfig($config);

        $data = $serviceConfig->get(new Details($this->feed));

        $this->assertEquals($this->ntfyConfigValues, $data);
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
        $config->method('getNtfyPassword')->willReturn($configValues['auth']['password']);
        
        $serviceConfig = new ServiceConfig($config);
        $data = $serviceConfig->get(new Details($this->feed));

        $this->assertEquals($configValues['auth'], $data['auth']);
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

        $serviceConfig = new ServiceConfig($config);
        $data = $serviceConfig->get(new Details($this->feed));

        $this->assertEquals($configValues['auth'], $data['auth']);
    }

   /**
    * Test creating Ntfy instance with server URL from feed details
    */
    public function testCreatingNtfyWithUrlFromFeedDetails(): void
    {
        $config = $this->createConfigStubForNtfy();

        $feed = $this->feed;
        $feed['ntfy_url'] = 'https://ntfy.example.com';

        $serviceConfig = new ServiceConfig($config);
        $data = $serviceConfig->get(new Details($feed));

        $this->assertEquals($feed['ntfy_url'], $data['server']);
    }

   /**
    * Test creating Ntfy instance with token from feed details
    */
    public function testCreatingNtfyWithTokenFromFeedDetails(): void
    {
        $config = $this->createConfigStubForNtfy();

        $feed = $this->feed;
        $feed['ntfy_token'] = 'a_ntfy_token';

        $serviceConfig = new ServiceConfig($config);
        $data = $serviceConfig->get(new Details($feed));

        $this->assertEquals('token', $data['auth']['method']);
        $this->assertEquals($feed['ntfy_token'], $data['auth']['token']);
    }

   /**
    * Test creating Ntfy instance with topic from feed details
    */
    public function testCreatingNtfyWithTopicFromFeedDetails(): void
    {
        $config = $this->createConfigStubForNtfy();

        $feed = $this->feed;
        $feed['ntfy_topic'] = 'a_ntfy_topic';

        $serviceConfig = new ServiceConfig($config);
        $data = $serviceConfig->get(new Details($feed));

        $this->assertEquals($feed['ntfy_topic'], $data['topic']);
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

        $serviceConfig = new ServiceConfig($config);
        $data = $serviceConfig->get(new Details($feed));

        $this->assertEquals($feed['ntfy_priority'], $data['priority']);
    }

    private function createConfigStubForGotify(): Config&Stub
    {
        $stub = self::createStub(Config::class);
        $stub->method('getNotificationService')->willReturn('gotify');
        $stub->method('getGotifyUrl')->willReturn($this->gotifyConfigValues['server']);
        $stub->method('getGotifyPriority')->willReturn($this->gotifyConfigValues['priority']);
        $stub->method('getGotifyToken')->willReturn($this->gotifyConfigValues['token']);
        return $stub;
    }

    private function createConfigStubForNtfy(): Config&Stub
    {
        $stub = self::createStub(Config::class);
        $stub->method('getNotificationService')->willReturn('ntfy');
        $stub->method('getNtfyUrl')->willReturn($this->ntfyConfigValues['server']);
        $stub->method('getNtfyPriority')->willReturn($this->ntfyConfigValues['priority']);
        return $stub;
    }
}
