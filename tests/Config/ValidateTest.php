<?php

namespace tests\Config\Validate;

use MockFileSystem\MockFileSystem as mockfs;
use Vigilant\Config;
use Vigilant\Config\Validate;
use Vigilant\Exception\ConfigException;

class ValidateTest extends \TestCase
{
    /** @var array<string, mixed> $defaults */
    private static array $defaults = [];

    /** @var array<int, string> $notificationServices */
    private static array $notificationServices = [];

    public static function setupBeforeClass(): void
    {
        $reflection = new \ReflectionClass(new Config());
        self::$defaults = $reflection->getProperty('defaults')->getValue(new Config());
        self::$notificationServices = $reflection->getProperty('notificationServices')->getValue(new Config());
    }

    public function setUp(): void
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

    public function tearDown(): void
    {
        stream_context_set_default(
            [
                'mfs' => [
                    'mkdir_fail' => false,
                ]
            ]
        );
    }

    /**
     * Test `getConfig`
     */
    public function testGetConfig(): void
    {
        $validate = new Validate(self::$defaults);
        $this->assertEquals(self::$defaults, $validate->getConfig());
    }

    /**
     * Test `version()`
     */
    public function testVersion(): void
    {
        $this->expectException(ConfigException::class);
        $this->expectExceptionMessage('Vigilant requires at least PHP version 8.1.0');

        $validate = new Validate(self::$defaults);
        $validate->version('8.0.0', '8.1.0');
    }

    /**
     * Test `extensions()`
     */
    public function testExtensions(): void
    {
        $this->expectException(ConfigException::class);
        $this->expectExceptionMessage('PHP extension error: pgp extension not loaded');

        $validate = new Validate(self::$defaults);
        $validate->extensions(['pgp']);
    }

    /**
     * Test `feedsFile()`
     */
    public function testFeedsFile(): void
    {
        putenv('VIGILANT_FEEDS_FILE=feeds.example.yaml');

        $validate = new Validate(self::$defaults);
        $validate->feedsFile();
        $config = $validate->getConfig();

        $this->assertEquals('feeds.example.yaml', $config['feeds_file']);
    }

    /**
     * Test config with `VIGILANT_FEEDS_FILE` with file that does not exist
     */
    public function testFeedsFileDoesNotExist(): void
    {
        $this->expectException(ConfigException::class);
        $this->expectExceptionMessage('Feeds file does not exist');

        putenv('VIGILANT_FEEDS_FILE=feeds-not-found.yaml');

        $validate = new Validate(self::$defaults);
        $validate->feedsFile();
    }

    /**
     * Test with no notification service
     */
    public function testNoNotificationService(): void
    {
        $this->expectException(ConfigException::class);
        $this->expectExceptionMessage('No notification service given');

        $validate = new Validate(self::$defaults);
        $validate->notificationService(self::$notificationServices);
    }

    /**
     * Test with unknown notification service
     */
    public function testUnknownNotificationService(): void
    {
        $this->expectException(ConfigException::class);
        $this->expectExceptionMessage('Unknown notification service given');

        putenv('VIGILANT_NOTIFICATION_SERVICE=email');

        $validate = new Validate(self::$defaults);
        $validate->notificationService(self::$notificationServices);
    }

    /**
     * Test notification service gotify
     */
    public function testGotifyUrl(): void
    {
        putenv('VIGILANT_NOTIFICATION_SERVICE=gotify');
        putenv('VIGILANT_NOTIFICATION_GOTIFY_URL=https://gotify.example.com/');
        putenv('VIGILANT_NOTIFICATION_GOTIFY_TOKEN=qwerty');

        $validate = new Validate(self::$defaults);
        $validate->notificationService(self::$notificationServices);
        $config = $validate->getConfig();

        $this->assertEquals('gotify', $config['notification_service']);
        $this->assertEquals('https://gotify.example.com/', $config['notification_gotify_url']);
        $this->assertEquals('qwerty', $config['notification_gotify_token']);
    }

    /**
     * Test notification service gotify with no URL
     */
    public function testNoGotifyUrl(): void
    {
        $this->expectException(ConfigException::class);
        $this->expectExceptionMessage('No Gotify URL given');

        putenv('VIGILANT_NOTIFICATION_SERVICE=gotify');

        $validate = new Validate(self::$defaults);
        $validate->notificationService(self::$notificationServices);
    }

    /**
     * Test with no gotify app token
     */
    public function testNoGotifyToken(): void
    {
        $this->expectException(ConfigException::class);
        $this->expectExceptionMessage('No Gotify app token given');

        putenv('VIGILANT_NOTIFICATION_SERVICE=gotify');
        putenv('VIGILANT_NOTIFICATION_GOTIFY_URL=https://gotify.example.com/');

        $validate = new Validate(self::$defaults);
        $validate->notificationService(self::$notificationServices);
    }

    /**
     * Test with no Ntfy URL
     */
    public function testNoNtfyUrl(): void
    {
        $this->expectException(ConfigException::class);
        $this->expectExceptionMessage('No ntfy URL given');

        putenv('VIGILANT_NOTIFICATION_SERVICE=ntfy');

        $validate = new Validate(self::$defaults);
        $validate->notificationService(self::$notificationServices);
    }

    /**
     * Test with no Ntfy topic
     */
    public function testNoNtfyTopic(): void
    {
        $this->expectException(ConfigException::class);
        $this->expectExceptionMessage('No ntfy topic given');

        putenv('VIGILANT_NOTIFICATION_SERVICE=ntfy');
        putenv('VIGILANT_NOTIFICATION_NTFY_URL=https://ntfy.example.com/');

        $validate = new Validate(self::$defaults);
        $validate->notificationService(self::$notificationServices);
    }

    /**
     * Test with unknown ntfy auth method
     */
    public function testUnknownNtfyAuthMethod(): void
    {
        $this->expectException(ConfigException::class);
        $this->expectExceptionMessage('Unknown ntfy authentication method given');

        putenv('VIGILANT_NOTIFICATION_SERVICE=ntfy');
        putenv('VIGILANT_NOTIFICATION_NTFY_URL=https://ntfy.example.com/');
        putenv('VIGILANT_NOTIFICATION_NTFY_TOPIC=hello-world');
        putenv('VIGILANT_NOTIFICATION_NTFY_AUTH=passkey');

        $validate = new Validate(self::$defaults);
        $validate->notificationService(self::$notificationServices);
    }

    /**
     * Test ntfy password auth 
     */
    public function testNtfyAuthPassword(): void
    {
        putenv('VIGILANT_NOTIFICATION_SERVICE=ntfy');
        putenv('VIGILANT_NOTIFICATION_NTFY_URL=https://ntfy.example.com/');
        putenv('VIGILANT_NOTIFICATION_NTFY_TOPIC=hello-world');
        putenv('VIGILANT_NOTIFICATION_NTFY_AUTH=password');
        putenv('VIGILANT_NOTIFICATION_NTFY_USERNAME=bob');
        putenv('VIGILANT_NOTIFICATION_NTFY_PASSWORD=qwerty');

        $validate = new Validate(self::$defaults);
        $validate->notificationService(self::$notificationServices);
        $config = $validate->getConfig();

        $this->assertEquals('password', $config['notification_ntfy_auth']);
        $this->assertEquals('bob', $config['notification_ntfy_username']);
        $this->assertEquals('qwerty', $config['notification_ntfy_password']);
    }

    /**
     * Test no ntfy auth username
     */
    public function testNoNtfyAuthUsername(): void
    {
        $this->expectException(ConfigException::class);
        $this->expectExceptionMessage('No ntfy authentication username given');

        putenv('VIGILANT_NOTIFICATION_SERVICE=ntfy');
        putenv('VIGILANT_NOTIFICATION_NTFY_URL=https://ntfy.example.com/');
        putenv('VIGILANT_NOTIFICATION_NTFY_TOPIC=hello-world');
        putenv('VIGILANT_NOTIFICATION_NTFY_AUTH=password');

        $validate = new Validate(self::$defaults);
        $validate->notificationService(self::$notificationServices);
    }

    /**
     * Test no ntfy auth password
     */
    public function testNoNtfyAuthPassword(): void
    {
        $this->expectException(ConfigException::class);
        $this->expectExceptionMessage('No ntfy authentication password given');

        putenv('VIGILANT_NOTIFICATION_SERVICE=ntfy');
        putenv('VIGILANT_NOTIFICATION_NTFY_URL=https://ntfy.example.com/');
        putenv('VIGILANT_NOTIFICATION_NTFY_TOPIC=hello-world');
        putenv('VIGILANT_NOTIFICATION_NTFY_AUTH=password');
        putenv('VIGILANT_NOTIFICATION_NTFY_USERNAME=bob');

        $validate = new Validate(self::$defaults);
        $validate->notificationService(self::$notificationServices);
    }

    /**
     * Test ntfy token auth 
     */
    public function testNtfyAuthToken(): void
    {
        putenv('VIGILANT_NOTIFICATION_SERVICE=ntfy');
        putenv('VIGILANT_NOTIFICATION_NTFY_URL=https://ntfy.example.com/');
        putenv('VIGILANT_NOTIFICATION_NTFY_TOPIC=hello-world');
        putenv('VIGILANT_NOTIFICATION_NTFY_AUTH=token');
        putenv('VIGILANT_NOTIFICATION_NTFY_TOKEN=qwerty');

        $validate = new Validate(self::$defaults);
        $validate->notificationService(self::$notificationServices);
        $config = $validate->getConfig();

        $this->assertEquals('token', $config['notification_ntfy_auth']);
        $this->assertEquals('qwerty', $config['notification_ntfy_token']);
    }

    /**
     * Test no ntfy auth token
     */
    public function testNoNtfyAuthToken(): void
    {
        $this->expectException(ConfigException::class);
        $this->expectExceptionMessage('No ntfy authentication token given');

        putenv('VIGILANT_NOTIFICATION_SERVICE=ntfy');
        putenv('VIGILANT_NOTIFICATION_NTFY_URL=https://ntfy.example.com/');
        putenv('VIGILANT_NOTIFICATION_NTFY_TOPIC=hello-world');
        putenv('VIGILANT_NOTIFICATION_NTFY_AUTH=token');

        $validate = new Validate(self::$defaults);
        $validate->notificationService(self::$notificationServices);
    }

    /**
     * Test `folder()` folder creation failure
     */
    public function testFolderCreationFailure(): void
    {
        $this->expectException(ConfigException::class);
        $this->expectExceptionMessage('Failed to create folder');

        mockfs::create();
        $folder = mockfs::getUrl('/data');

        stream_context_set_default(
            [
                'mfs' => [
                    'mkdir_fail' => true,
                ]
            ]
        );

        $validate = new Validate(self::$defaults);
        $validate->folder($folder);
    }
}
