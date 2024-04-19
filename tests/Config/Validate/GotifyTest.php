<?php

namespace tests\Config\Validate;

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
class GotifyTest extends \TestCase
{
    /** @var array<string, mixed> $defaults */
    private static array $defaults = [];

    public function setUp(): void
    {
        // Unset environment variables before each test
        putenv('VIGILANT_NOTIFICATION_GOTIFY_URL');
        putenv('VIGILANT_NOTIFICATION_GOTIFY_TOPIC');
        putenv('VIGILANT_NOTIFICATION_GOTIFY_TOKEN');
    }

    /**
     * Test URL
     */
    public function testUrl(): void
    {
        putenv('VIGILANT_NOTIFICATION_GOTIFY_URL=https://ntfy.example.com/');

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
     * Test token
     */
    public function testToken(): void
    {
        putenv('VIGILANT_NOTIFICATION_GOTIFY_TOKEN=qwerty');

        $validate = new Validate(self::$defaults);
        $validate->token();
        $config = $validate->getConfig();

        $this->assertEquals('qwerty', $config['notification_ntfy_token']);
    }

    /**
     * Test no token
     */
    public function testNoToken(): void
    {
        $this->expectException(ConfigException::class);
        $this->expectExceptionMessage('No gotify authentication token given');

        putenv('VIGILANT_NOTIFICATION_GOTIFY_AUTH=token');

        $validate = new Validate(self::$defaults);
        $validate->token();
    }
}
