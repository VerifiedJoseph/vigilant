<?php

namespace tests\Config\Validate;

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
class NtfyTest extends \TestCase
{
    /** @var array<string, mixed> $defaults */
    private static array $defaults = [];

    public function setUp(): void
    {
        // Unset environment variables before each test
        putenv('VIGILANT_NOTIFICATION_NTFY_URL');
        putenv('VIGILANT_NOTIFICATION_NTFY_TOPIC');
        putenv('VIGILANT_NOTIFICATION_NTFY_AUTH');
        putenv('VIGILANT_NOTIFICATION_NTFY_USERNAME');
        putenv('VIGILANT_NOTIFICATION_NTFY_PASSWORD');
        putenv('VIGILANT_NOTIFICATION_NTFY_TOKEN');
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
        $validate->topic([]);
    }

    /**
     * Test unknown auth method
     */
    public function testUnknownAuthMethod(): void
    {
        $this->expectException(ConfigException::class);
        $this->expectExceptionMessage('Unknown ntfy authentication method given');

        putenv('VIGILANT_NOTIFICATION_NTFY_AUTH=passkey');

        $validate = new Validate(self::$defaults);
        $validate->auth();
    }

    /**
     * Test password auth
     */
    public function testAuthPassword(): void
    {
        putenv('VIGILANT_NOTIFICATION_NTFY_TOPIC=hello-world');
        putenv('VIGILANT_NOTIFICATION_NTFY_AUTH=password');
        putenv('VIGILANT_NOTIFICATION_NTFY_USERNAME=bob');
        putenv('VIGILANT_NOTIFICATION_NTFY_PASSWORD=qwerty');

        $validate = new Validate(self::$defaults);
        $validate->auth();
        $config = $validate->getConfig();

        $this->assertEquals('password', $config['notification_ntfy_auth']);
        $this->assertEquals('bob', $config['notification_ntfy_username']);
        $this->assertEquals('qwerty', $config['notification_ntfy_password']);
    }

    /**
     * Test no username
     */
    public function testNoUsername(): void
    {
        $this->expectException(ConfigException::class);
        $this->expectExceptionMessage('No ntfy authentication username given');

        putenv('VIGILANT_NOTIFICATION_NTFY_AUTH=password');

        $validate = new Validate(self::$defaults);
        $validate->auth();
    }

    /**
     * Test no password
     */
    public function testNoPassword(): void
    {
        $this->expectException(ConfigException::class);
        $this->expectExceptionMessage('No ntfy authentication password given');

        putenv('VIGILANT_NOTIFICATION_NTFY_TOPIC=hello-world');
        putenv('VIGILANT_NOTIFICATION_NTFY_AUTH=password');
        putenv('VIGILANT_NOTIFICATION_NTFY_USERNAME=bob');

        $validate = new Validate(self::$defaults);
        $validate->auth();
    }

    /**
     * Test token
     */
    public function testToken(): void
    {
        putenv('VIGILANT_NOTIFICATION_NTFY_AUTH=token');
        putenv('VIGILANT_NOTIFICATION_NTFY_TOKEN=qwerty');

        $validate = new Validate(self::$defaults);
        $validate->auth();
        $config = $validate->getConfig();

        $this->assertEquals('token', $config['notification_ntfy_auth']);
        $this->assertEquals('qwerty', $config['notification_ntfy_token']);
    }

    /**
     * Test no token
     */
    public function testNoToken(): void
    {
        $this->expectException(ConfigException::class);
        $this->expectExceptionMessage('No ntfy authentication token given');

        putenv('VIGILANT_NOTIFICATION_NTFY_AUTH=token');

        $validate = new Validate(self::$defaults);
        $validate->auth();
    }
}
