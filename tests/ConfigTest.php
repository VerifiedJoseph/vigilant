<?php

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use Vigilant\Config;

#[CoversClass(Config::class)]
#[UsesClass(Vigilant\Config\Validator::class)]
#[UsesClass(Vigilant\Config\Validate\Ntfy::class)]
#[UsesClass(Vigilant\Config\AbstractValidator::class)]
class ConfigTest extends TestCase
{
    /** @var array<string, mixed> $defaults */
    private static array $defaults = [];

    public static function setupBeforeClass(): void
    {
        $config = new Config();
        $reflection = new ReflectionClass($config);
        self::$defaults = $reflection->getProperty('config')->getValue(new Config());
    }

    public function setUp(): void
    {
        // Unset environment variables before each test
        putenv('VIGILANT_NOTIFICATION_SERVICE');
        putenv('VIGILANT_NOTIFICATION_NTFY_URL');
        putenv('VIGILANT_NOTIFICATION_NTFY_TOPIC');
        putenv('VIGILANT_NOTIFICATION_NTFY_AUTH');

        putenv('VIGILANT_NOTIFICATION_GOTIFY_URL');
        putenv('VIGILANT_NOTIFICATION_GOTIFY_TOPIC');
        putenv('VIGILANT_NOTIFICATION_GOTIFY_TOKEN');
    }

    /**
     * Test `validate()`
     */
    public function testValidate(): void
    {
        putenv('VIGILANT_NOTIFICATION_SERVICE=ntfy');
        putenv('VIGILANT_NOTIFICATION_NTFY_URL=https://ntfy.example.com/');
        putenv('VIGILANT_NOTIFICATION_NTFY_TOPIC=hello');

        $config = new Config();
        $config->validate();

        $this->assertEquals('ntfy', $config->getNotificationService());
        $this->assertEquals('https://ntfy.example.com/', $config->getNtfyUrl());
        $this->assertEquals('hello', $config->getNtfyTopic());
    }

    /**
     * Test `getTimezone()`
     */
    public function testGetTimezone(): void
    {
        $config = new Config();
        $this->assertEquals(
            self::$defaults['timezone'],
            $config->getTimezone()
        );
    }

    /**
     * Test `getNotificationService()`
     */
    public function testGetNotificationService(): void
    {
        $config = new Config();

        $reflection = new ReflectionClass($config);
        $property = $reflection->getProperty('config');
        $property->setAccessible(true);
        $property->setValue($config, ['notification_service' => 'ntfy']);

        $this->assertEquals('ntfy', $config->getNotificationService());
    }

    /**
     * Test `getNtfyUrl()`
     */
    public function testGetNtfyUrl(): void
    {
        $config = new Config();

        $reflection = new ReflectionClass($config);
        $property = $reflection->getProperty('config');
        $property->setAccessible(true);
        $property->setValue($config, ['notification_ntfy_url' => 'https://ntfy.example.com/']);

        $this->assertEquals('https://ntfy.example.com/', $config->getNtfyUrl());
    }

    /**
     * Test `getNtfyTopic()`
     */
    public function testGetNtfyTopic(): void
    {
        $config = new Config();

        $reflection = new ReflectionClass($config);
        $property = $reflection->getProperty('config');
        $property->setAccessible(true);
        $property->setValue($config, ['notification_ntfy_topic' => 'hello-world']);

        $this->assertEquals('hello-world', $config->getNtfyTopic());
    }

    /**
     * Test `getNtfyPriority()`
     */
    public function testGetNtfyPriority(): void
    {
        $config = new Config();
        $this->assertEquals(
            self::$defaults['notification_ntfy_priority'],
            $config->getNtfyPriority()
        );
    }

    /**
     * Test `getNtfyAuthMethod()`
     */
    public function testGetNtfyAuthMethod(): void
    {
        $config = new Config();

        $reflection = new ReflectionClass($config);
        $property = $reflection->getProperty('config');
        $property->setAccessible(true);
        $property->setValue($config, ['notification_ntfy_auth' => 'password']);

        $this->assertEquals('password', $config->getNtfyAuthMethod());
    }

    /**
     * Test `getNtfyUsername()`
     */
    public function testGetNtfyUsername(): void
    {
        $config = new Config();

        $reflection = new ReflectionClass($config);
        $property = $reflection->getProperty('config');
        $property->setAccessible(true);
        $property->setValue($config, ['notification_ntfy_username' => 'bob']);

        $this->assertEquals('bob', $config->getNtfyUsername());
    }

    /**
     * Test `getNtfyPassword()`
     */
    public function testGetNtfyPassword(): void
    {
        $config = new Config();

        $reflection = new ReflectionClass($config);
        $property = $reflection->getProperty('config');
        $property->setAccessible(true);
        $property->setValue($config, ['notification_ntfy_password' => 'qwerty']);

        $this->assertEquals('qwerty', $config->getNtfyPassword());
    }

    /**
     * Test `getNtfyToken()`
     */
    public function testGetNtfyToken(): void
    {
        $config = new Config();

        $reflection = new ReflectionClass($config);
        $property = $reflection->getProperty('config');
        $property->setAccessible(true);
        $property->setValue($config, ['notification_ntfy_token' => 'qwerty123456']);

        $this->assertEquals('qwerty123456', $config->getNtfyToken());
    }

    /**
     * Test `getGotifyUrl()`
     */
    public function testGetGotifyUrl(): void
    {
        $config = new Config();

        $reflection = new ReflectionClass($config);
        $property = $reflection->getProperty('config');
        $property->setAccessible(true);
        $property->setValue($config, ['notification_gotify_url' => 'https://gotify.example.com/']);

        $this->assertEquals('https://gotify.example.com/', $config->getGotifyUrl());
    }

    /**
     * Test `getGotifyPriority()`
     */
    public function testGetGotifyPriority(): void
    {
        $config = new Config();
        $this->assertEquals(self::$defaults['notification_gotify_priority'], $config->getGotifyPriority());
    }

    /**
     * Test `getGotifyToken()`
     */
    public function testGetGotifyToken(): void
    {
        $config = new Config();

        $reflection = new ReflectionClass($config);
        $property = $reflection->getProperty('config');
        $property->setAccessible(true);
        $property->setValue($config, ['notification_gotify_token' => 'qwerty']);

        $this->assertEquals('qwerty', $config->getGotifyToken());
    }

    /**
     * Test `getCachePath()`
     */
    public function testGetCachePath(): void
    {
        $expected = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'cache';

        $config = new Config();
        $this->assertEquals($expected, $config->getCachePath());
    }

    /**
     * Test `getMinCheckInterval()`
     */
    public function testGetMinCheckInterval(): void
    {
        $config = new Config();
        $this->assertEquals(300, $config->getMinCheckInterval());
    }

    /**
     * Test `getFeedsPath()`
     */
    public function testGetFeedsPath(): void
    {
        $config = new Config();
        $this->assertEquals(self::$defaults['feeds_file'], $config->getFeedsPath());
    }
}
