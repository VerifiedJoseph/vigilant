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

    public static function setupBeforeClass(): void
    {
        $reflection = new \ReflectionClass(new Config());
        self::$defaults = $reflection->getProperty('defaults')->getValue(new Config());
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
