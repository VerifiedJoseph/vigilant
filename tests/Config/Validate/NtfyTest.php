<?php

declare(strict_types=1);

namespace tests\Config\Validate;

use Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use Vigilant\Config;
use Vigilant\Config\Validate\Ntfy as Validate;
use Vigilant\Exception\ConfigException;

#[CoversClass(Validate::class)]
#[UsesClass(Config::class)]
#[UsesClass(ConfigException::class)]
#[UsesClass(\Vigilant\Config\Validator::class)]
#[UsesClass(\Vigilant\Config\AbstractValidator::class)]
class NtfyTest extends TestCase
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
        putenv('VIGILANT_NOTIFICATION_NTFY_URL=https://ntfy.example.com/');

        $validate = new Validate(self::$defaults);
        $validate->url();
        $config = $validate->getConfig();

        $this->assertEquals('https://ntfy.example.com/', $config['notification_ntfy_url']);
    }

    /**
     * Test no URL
     */
    public function testNoUrl(): void
    {
        $this->expectException(ConfigException::class);
        $this->expectExceptionMessage('No ntfy URL given');

        $validate = new Validate(self::$defaults);
        $validate->url();
    }

    /**
     * Test priority
     */
    public function testPriority(): void
    {
        putenv('VIGILANT_NOTIFICATION_NTFY_PRIORITY=1');

        $validate = new Validate(self::$defaults);
        $validate->priority();
        $config = $validate->getConfig();

        $this->assertEquals(1, $config['notification_ntfy_priority']);
    }

    /**
     * Test priority is not a number
     */
    public function testPriorityNotNumber(): void
    {
        $this->expectException(ConfigException::class);
        $this->expectExceptionMessage('Ntfy priority value is not a number');

        putenv('VIGILANT_NOTIFICATION_NTFY_PRIORITY=hello');

        $validate = new Validate(self::$defaults);
        $validate->priority();
    }

    /**
     * Test topic
     */
    public function testTopic(): void
    {
        putenv('VIGILANT_NOTIFICATION_NTFY_TOPIC=testing');

        $validate = new Validate(self::$defaults);
        $validate->topic();
        $config = $validate->getConfig();

        $this->assertEquals('testing', $config['notification_ntfy_topic']);
    }

    /**
     * Test no topic
     */
    public function testNoTopic(): void
    {
        $this->expectException(ConfigException::class);
        $this->expectExceptionMessage('No ntfy topic given');

        $validate = new Validate(self::$defaults);
        $validate->topic();
    }

    /**
     * Test token
     */
    public function testToken(): void
    {
        putenv('VIGILANT_NOTIFICATION_NTFY_TOKEN=qwerty');

        $validate = new Validate(self::$defaults);
        $validate->token();
        $config = $validate->getConfig();

        $this->assertEquals('qwerty', $config['notification_ntfy_token']);
    }

    /**
     * Test empty token
     */
    public function testEmptyToken(): void
    {
        $this->expectException(ConfigException::class);
        $this->expectExceptionMessage('No ntfy authentication token given');

        putenv('VIGILANT_NOTIFICATION_NTFY_TOKEN=');

        $validate = new Validate(self::$defaults);
        $validate->token();
    }
}
