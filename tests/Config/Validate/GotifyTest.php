<?php

declare(strict_types=1);

namespace tests\Config\Validate;

use Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use Vigilant\Config;
use Vigilant\Config\Validate\Gotify as Validate;
use Vigilant\Exception\ConfigException;

#[CoversClass(Validate::class)]
#[UsesClass(Config::class)]
#[UsesClass(ConfigException::class)]
#[UsesClass(\Vigilant\Config\Validator::class)]
#[UsesClass(\Vigilant\Config\AbstractValidator::class)]
class GotifyTest extends TestCase
{
    /** @var array<string, mixed> $defaults */
    private static array $defaults = [];

    public function setUp(): void
    {
        $this->resetEnvs();
    }

    /**
     * Test URL
     */
    public function testUrl(): void
    {
        putenv('VIGILANT_NOTIFICATION_GOTIFY_URL=https://gotify.example.com/');

        $validate = new Validate(self::$defaults);
        $validate->url();
        $config = $validate->getConfig();

        $this->assertEquals('https://gotify.example.com/', $config['notification_gotify_url']);
    }

    /**
     * Test no URL
     */
    public function testNoUrl(): void
    {
        $this->expectException(ConfigException::class);
        $this->expectExceptionMessage('No gotify URL given');

        $validate = new Validate(self::$defaults);
        $validate->url();
    }

    /**
     * Test priority
     */
    public function testPriority(): void
    {
        putenv('VIGILANT_NOTIFICATION_GOTIFY_PRIORITY=1');

        $validate = new Validate(self::$defaults);
        $validate->priority();
        $config = $validate->getConfig();

        $this->assertEquals(1, $config['notification_gotify_priority']);
    }

    /**
     * Test priority is not a number
     */
    public function testPriorityNotNumber(): void
    {
        $this->expectException(ConfigException::class);
        $this->expectExceptionMessage('Gotify priority value is not a number');

        putenv('VIGILANT_NOTIFICATION_GOTIFY_PRIORITY=hello');

        $validate = new Validate(self::$defaults);
        $validate->priority();
    }

    /**
     * Test token
     */
    public function testToken(): void
    {
        putenv('VIGILANT_NOTIFICATION_GOTIFY_TOKEN=qwerty');

        $validate = new Validate(self::$defaults);
        $validate->token();
        $config = $validate->getConfig();

        $this->assertEquals('qwerty', $config['notification_gotify_token']);
    }

    /**
     * Test no token
     */
    public function testNoToken(): void
    {
        $this->expectException(ConfigException::class);
        $this->expectExceptionMessage('No gotify app token given');

        $validate = new Validate(self::$defaults);
        $validate->token();
    }
}
