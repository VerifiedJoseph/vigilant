<?php

namespace tests\Config\Validate;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use MockFileSystem\MockFileSystem as mockfs;
use Vigilant\Config;
use Vigilant\Config\Validator;
use Vigilant\Exception\ConfigException;

#[CoversClass(Validator::class)]
#[UsesClass(Config::class)]
#[UsesClass(ConfigException::class)]
#[UsesClass(\Vigilant\Config\Validate\Ntfy::class)]
#[UsesClass(\Vigilant\Config\Validate\Gotify::class)]
#[UsesClass(\Vigilant\Config\AbstractValidator::class)]
class ValidatorTest extends \TestCase
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
        putenv('VIGILANT_TIMEZONE');
        putenv('VIGILANT_FEEDS_FILE');
        putenv('VIGILANT_NOTIFICATION_SERVICE');

        // Gotify
        putenv('VIGILANT_NOTIFICATION_GOTIFY_URL');
        putenv('VIGILANT_NOTIFICATION_GOTIFY_TOKEN');

        // Ntfy
        putenv('VIGILANT_NOTIFICATION_NTFY_URL');
        putenv('VIGILANT_NOTIFICATION_NTFY_TOPIC');
        putenv('VIGILANT_NOTIFICATION_NTFY_AUTH');
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
        $validate = new Validator(self::$defaults);
        $this->assertEquals(self::$defaults, $validate->getConfig());
    }

    /**
     * Test `version()`
     */
    public function testVersion(): void
    {
        $this->expectException(ConfigException::class);
        $this->expectExceptionMessage('Vigilant requires at least PHP version 8.1.0');

        $validate = new Validator(self::$defaults);
        $validate->version('8.0.0', '8.1.0');
    }

    /**
     * Test `extensions()`
     */
    public function testExtensions(): void
    {
        $this->expectException(ConfigException::class);
        $this->expectExceptionMessage('PHP extension error: pgp extension not loaded');

        $validate = new Validator(self::$defaults);
        $validate->extensions(['pgp']);
    }

    /**
     * Test `timezone()`
     */
    public function testTimezone(): void
    {
        putenv('VIGILANT_TIMEZONE=Europe/London');

        $validate = new Validator(self::$defaults);
        $validate->timezone();
        $config = $validate->getConfig();

        $this->assertEquals('Europe/London', $config['timezone']);
    }

    /**
     * Test `timezone()` with `VIGILANT_TIMEZONE` not set
     */
    public function testNotSettingTimezone(): void
    {
        $validate = new Validator(self::$defaults);
        $validate->timezone();
        $config = $validate->getConfig();

        $this->assertEquals('UTC', $config['timezone']);
    }

    /**
     * Test `timezone()` with invalid timezone
     */
    public function testInvalidTimezone(): void
    {
        $this->expectException(ConfigException::class);
        $this->expectExceptionMessage('Invalid timezone');

        putenv('VIGILANT_TIMEZONE=Europe/Coventry');

        $validate = new Validator(self::$defaults);
        $validate->timezone();
    }

    /**
     * Test `feedsFile()`
     */
    public function testFeedsFile(): void
    {
        putenv('VIGILANT_FEEDS_FILE=feeds.example.yaml');

        $validate = new Validator(self::$defaults);
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

        $validate = new Validator(self::$defaults);
        $validate->feedsFile();
    }

    /**
     * Test with no notification service
     */
    public function testNoNotificationService(): void
    {
        $this->expectException(ConfigException::class);
        $this->expectExceptionMessage('No notification service given');

        $validate = new Validator(self::$defaults);
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

        $validate = new Validator(self::$defaults);
        $validate->notificationService(self::$notificationServices);
    }

    /**
     * Test notification service gotify
     */
    public function testGotify(): void
    {
        putenv('VIGILANT_NOTIFICATION_SERVICE=gotify');
        putenv('VIGILANT_NOTIFICATION_GOTIFY_URL=https://gotify.example.com/');
        putenv('VIGILANT_NOTIFICATION_GOTIFY_TOKEN=qwerty');

        $validate = new Validator(self::$defaults);
        $validate->notificationService(self::$notificationServices);
        $config = $validate->getConfig();

        $this->assertEquals('gotify', $config['notification_service']);
        $this->assertEquals('https://gotify.example.com/', $config['notification_gotify_url']);
        $this->assertEquals('qwerty', $config['notification_gotify_token']);
    }

    /**
     * Test notification service ntfy
     */
    public function testNtfy(): void
    {
        putenv('VIGILANT_NOTIFICATION_SERVICE=ntfy');
        putenv('VIGILANT_NOTIFICATION_NTFY_URL=https://ntfy.example.com/');
        putenv('VIGILANT_NOTIFICATION_NTFY_TOPIC=qwerty');

        $validate = new Validator(self::$defaults);
        $validate->notificationService(self::$notificationServices);
        $config = $validate->getConfig();

        $this->assertEquals('ntfy', $config['notification_service']);
        $this->assertEquals('https://ntfy.example.com/', $config['notification_ntfy_url']);
        $this->assertEquals('qwerty', $config['notification_ntfy_topic']);
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

        $validate = new Validator(self::$defaults);
        $validate->folder($folder);
    }
}
