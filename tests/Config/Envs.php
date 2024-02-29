<?php

use Vigilant\Config\Check\Envs;
use Vigilant\Exception\ConfigException;

class EnvsTest extends TestCase
{
    private array $configDefaults = [
        'QUIET' => false,
        'FEEDS_FILE' => 'feeds.yaml',
        'NOTIFICATION_GOTIFY_PRIORITY' => 4,
        'NOTIFICATION_NTFY_PRIORITY' => 3,
        'NOTIFICATION_NTFY_AUTH' => 'none'
    ];

    private array $notificationServices = ['gotify', 'ntfy'];

    public function SetUp(): void
    {
        // Unset environment variables before each test
        putenv('VIGILANT_FEEDS_FILE');
        putenv('VIGILANT_NOTIFICATION_SERVICE');

        // Gotify
        putenv('VIGILANT_NOTIFICATION_GOTIFY_URL');
        putenv('VIGILANT_NOTIFICATION_GOTIFY_TOKEN');

        // Ntfy
        putenv('VIGILANT_NOTIFICATION_NTFY_URL');
        putenv('VIGILANT_NOTIFICATION_NTFY_TOPIC');
        putenv('VIGILANT_NOTIFICATION_NTFY_AUTH');
        putenv('VIGILANT_NOTIFICATION_NTFY_USERNAME');
        putenv('VIGILANT_NOTIFICATION_NTFY_PASSWORD');
        putenv('VIGILANT_NOTIFICATION_NTFY_TOKEN');
    }

    /**
     * Test valid Gotify vars
     */
    public function testValidGotifyVars(): void
    {
        putenv('VIGILANT_FEEDS_FILE=feeds.example.yaml');
        putenv('VIGILANT_NOTIFICATION_SERVICE=gotify');
        putenv('VIGILANT_NOTIFICATION_GOTIFY_URL=https://gotify.example.com/');
        putenv('VIGILANT_NOTIFICATION_GOTIFY_TOKEN=fake_token');

        $envs = new Envs($this->configDefaults, $this->notificationServices);
        $config = $envs->getConfig();

        $this->assertEquals('feeds.example.yaml', $config['FEEDS_FILE']);
        $this->assertEquals('gotify', $config['NOTIFICATION_SERVICE']);
        $this->assertEquals('https://gotify.example.com/', $config['NOTIFICATION_GOTIFY_URL']);
        $this->assertEquals('fake_token', $config['NOTIFICATION_GOTIFY_TOKEN']);
    }

    /**
     * Test valid Ntfy vars
     */
    public function testValidNtfyVars(): void
    {
        putenv('VIGILANT_FEEDS_FILE=feeds.example.yaml');
        putenv('VIGILANT_NOTIFICATION_SERVICE=ntfy');
        putenv('VIGILANT_NOTIFICATION_NTFY_URL=https://ntfy.example.com/');
        putenv('VIGILANT_NOTIFICATION_NTFY_TOPIC=hello-world');

        $envs = new Envs($this->configDefaults, $this->notificationServices);
        $config = $envs->getConfig();

        $this->assertEquals('ntfy', $config['NOTIFICATION_SERVICE']);
        $this->assertEquals('https://ntfy.example.com/', $config['NOTIFICATION_NTFY_URL']);
        $this->assertEquals('hello-world', $config['NOTIFICATION_NTFY_TOPIC']);
    }

    /**
     * Test valid Ntfy auth password vars
     */
    public function testValidNtfyAuthPasswordVars(): void
    {
        putenv('VIGILANT_FEEDS_FILE=feeds.example.yaml');
        putenv('VIGILANT_NOTIFICATION_SERVICE=ntfy');
        putenv('VIGILANT_NOTIFICATION_NTFY_URL=https://ntfy.example.com/');
        putenv('VIGILANT_NOTIFICATION_NTFY_TOPIC=hello-world');
        putenv('VIGILANT_NOTIFICATION_NTFY_AUTH=password');
        putenv('VIGILANT_NOTIFICATION_NTFY_USERNAME=bob');
        putenv('VIGILANT_NOTIFICATION_NTFY_PASSWORD=qwerty');

        $envs = new Envs($this->configDefaults, $this->notificationServices);
        $config = $envs->getConfig();

        $this->assertEquals('password', $config['NOTIFICATION_NTFY_AUTH']);
        $this->assertEquals('bob', $config['NOTIFICATION_NTFY_USERNAME']);
        $this->assertEquals('qwerty', $config['NOTIFICATION_NTFY_PASSWORD']);
    }

    /**
     * Test valid Ntfy auth token vars
     */
    public function testValidNtfyAuthTokenVars(): void
    {
        putenv('VIGILANT_FEEDS_FILE=feeds.example.yaml');
        putenv('VIGILANT_NOTIFICATION_SERVICE=ntfy');
        putenv('VIGILANT_NOTIFICATION_NTFY_URL=https://ntfy.example.com/');
        putenv('VIGILANT_NOTIFICATION_NTFY_TOPIC=hello-world');
        putenv('VIGILANT_NOTIFICATION_NTFY_AUTH=token');
        putenv('VIGILANT_NOTIFICATION_NTFY_TOKEN=fake-token');
        putenv('VIGILANT_NOTIFICATION_NTFY_PASSWORD=qwerty');

        $envs = new Envs($this->configDefaults, $this->notificationServices);
        $config = $envs->getConfig();

        $this->assertEquals('token', $config['NOTIFICATION_NTFY_AUTH']);
        $this->assertEquals('fake-token', $config['NOTIFICATION_NTFY_TOKEN']);
    }

    /**
     * Test with not found feeds file
     */
    public function testWithNotFoundFeedFile(): void
    {
        $this->expectException(ConfigException::class);
        $this->expectExceptionMessage('Feeds file not found');

        putenv('VIGILANT_FEEDS_FILE=not-found-feeds.yml');

        new Envs($this->configDefaults, $this->notificationServices);
    }

    /**
     * Test with no notification service
     */
    public function testWithNoNotificationService(): void
    {
        $this->expectException(ConfigException::class);
        $this->expectExceptionMessage('No notification service given');

        new Envs($this->configDefaults, $this->notificationServices);
    }

    /**
     * Test with unknown notification service
     */
    public function testWithUnknownNotificationService(): void
    {
        $this->expectException(ConfigException::class);
        $this->expectExceptionMessage('Unknown notification service given');

        putenv('VIGILANT_NOTIFICATION_SERVICE=email');

        new Envs($this->configDefaults, $this->notificationServices);
    }

    /**
     * Test with no gotify URL
     */
    public function testWithNoGotifyUrl(): void
    {
        $this->expectException(ConfigException::class);
        $this->expectExceptionMessage('No Gotify URL given');

        putenv('VIGILANT_NOTIFICATION_SERVICE=gotify');

        new Envs($this->configDefaults, $this->notificationServices);
    }

    /**
     * Test with no gotify app token
     */
    public function testWithNoGotifyToken(): void
    {
        $this->expectException(ConfigException::class);
        $this->expectExceptionMessage('No Gotify app token given');

        putenv('VIGILANT_NOTIFICATION_SERVICE=gotify');
        putenv('VIGILANT_NOTIFICATION_GOTIFY_URL=https://gotify.example.com/');

        new Envs($this->configDefaults, $this->notificationServices);
    }

    /**
     * Test with no Ntfy URL
     */
    public function testWithNoNtfyUrl(): void
    {
        $this->expectException(ConfigException::class);
        $this->expectExceptionMessage('No ntfy URL given');

        putenv('VIGILANT_NOTIFICATION_SERVICE=ntfy');

        new Envs($this->configDefaults, $this->notificationServices);
    }

    /**
     * Test with no Ntfy topic
     */
    public function testWithNoNtfyTopic(): void
    {
        $this->expectException(ConfigException::class);
        $this->expectExceptionMessage('No ntfy topic given');

        putenv('VIGILANT_NOTIFICATION_SERVICE=ntfy');
        putenv('VIGILANT_NOTIFICATION_NTFY_URL=https://ntfy.example.com/');

        new Envs($this->configDefaults, $this->notificationServices);
    }

    /**
     * Test with unknown ntfy auth method
     */
    public function testWithUnknownNtfyAuthMethod(): void
    {
        $this->expectException(ConfigException::class);
        $this->expectExceptionMessage('Unknown ntfy authentication method given');

        putenv('VIGILANT_NOTIFICATION_SERVICE=ntfy');
        putenv('VIGILANT_NOTIFICATION_NTFY_URL=https://ntfy.example.com/');
        putenv('VIGILANT_NOTIFICATION_NTFY_TOPIC=hello-world');
        putenv('VIGILANT_NOTIFICATION_NTFY_AUTH=passkey');

        new Envs($this->configDefaults, $this->notificationServices);
    }

    /**
     * Test with no ntfy auth username given
     */
    public function testWithNoNtfyAuthUsername(): void
    {
        $this->expectException(ConfigException::class);
        $this->expectExceptionMessage('No ntfy authentication username given');

        putenv('VIGILANT_NOTIFICATION_SERVICE=ntfy');
        putenv('VIGILANT_NOTIFICATION_NTFY_URL=https://ntfy.example.com/');
        putenv('VIGILANT_NOTIFICATION_NTFY_TOPIC=hello-world');
        putenv('VIGILANT_NOTIFICATION_NTFY_AUTH=password');

        new Envs($this->configDefaults, $this->notificationServices);
    }

    /**
     * Test with no ntfy auth password given
     */
    public function testWithNoNtfyAuthPassword(): void
    {
        $this->expectException(ConfigException::class);
        $this->expectExceptionMessage('No ntfy authentication password given');

        putenv('VIGILANT_NOTIFICATION_SERVICE=ntfy');
        putenv('VIGILANT_NOTIFICATION_NTFY_URL=https://ntfy.example.com/');
        putenv('VIGILANT_NOTIFICATION_NTFY_TOPIC=hello-world');
        putenv('VIGILANT_NOTIFICATION_NTFY_AUTH=password');
        putenv('VIGILANT_NOTIFICATION_NTFY_USERNAME=bob');

        new Envs($this->configDefaults, $this->notificationServices);
    }

    /**
     * Test with no ntfy auth token given
     */
    public function testWithNoNtfyAuthToken(): void
    {
        $this->expectException(ConfigException::class);
        $this->expectExceptionMessage('No ntfy authentication token given');

        putenv('VIGILANT_NOTIFICATION_SERVICE=ntfy');
        putenv('VIGILANT_NOTIFICATION_NTFY_URL=https://ntfy.example.com/');
        putenv('VIGILANT_NOTIFICATION_NTFY_TOPIC=hello-world');
        putenv('VIGILANT_NOTIFICATION_NTFY_AUTH=token');

        new Envs($this->configDefaults, $this->notificationServices);
    }
}
