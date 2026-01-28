<?Php

declare(strict_types=1);

namespace Tests\Notification;

use Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
use Vigilant\Logger;
use Vigilant\Notification\Ntfy;
use Vigilant\Notification\AbstractNotification;
use Vigilant\Exception\NotificationException;

#[CoversClass(Ntfy::class)]
#[CoversClass(AbstractNotification::class)]
#[UsesClass(Logger::class)]
#[UsesClass(NotificationException::class)]
class NtfyTest extends TestCase
{
    private static Logger $logger;

    /** @var array<string, mixed> $config */
    private array $config = [
        'server' => 'https://ntfy.example.invalid',
        'topic' => 'testing',
        'priority' => 0,
        'auth' => [
            'method' => 'none'
        ]
    ];

    public static function setUpBeforeClass(): void
    {
        self::$logger = new Logger('UTC');
    }

    /**
     * Test `setup()` with no auth
     */
    #[DoesNotPerformAssertions]
    public function testSetupWithNoAuth(): void
    {
        new Ntfy($this->config, self::$logger);
    }

    /**
     * Test `setup()` with password auth
     */
    #[DoesNotPerformAssertions]
    public function testSetupWithPasswordAuth(): void
    {
        $config = $this->config;
        $config['auth']['method'] = 'password';
        $config['auth']['username'] = 'bob';
        $config['auth']['password'] = 'qwerty';
        new Ntfy($config, self::$logger);
    }

    /**
     * Test `setup()` with token auth
     */
    #[DoesNotPerformAssertions]
    public function testSetupWithTokenAuth(): void
    {
        $config = $this->config;
        $config['auth']['method'] = 'token';
        $config['auth']['token'] = 'qwerty';
        new Ntfy($config, self::$logger);
    }

    /**
     * Test `send()`
     */
    public function testSend(): void
    {
        $client = self::createStub(\Ntfy\Client::class);
        $client->method('send')->willReturn(new \stdClass());

        $ntfy = new Ntfy($this->config, self::$logger);

        $reflection = new \ReflectionClass($ntfy);
        $property = $reflection->getProperty('client');
        $property->setValue($ntfy, $client);

        $this->expectOutputRegex('/Sent notification using Ntfy/');

        $ntfy->send('Hello World', 'Hello from phpunit');
    }

    public function testSendNotificationException(): void
    {
        $this->expectException(NotificationException::class);
        $this->expectExceptionMessage('[Notification error] [Ntfy]');

        $ntfy = new Ntfy($this->config, self::$logger);
        $ntfy->send('Hello World', 'Hello from phpunit');
    }
}
