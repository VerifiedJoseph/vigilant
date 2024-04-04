<?Php

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
use Vigilant\Notification\Ntfy;
use Vigilant\Exception\NotificationException;

#[CoversClass(Ntfy::class)]
#[CoversClass(Vigilant\Notification\Notification::class)]
#[UsesClass(NotificationException::class)]
#[UsesClass(Vigilant\Output::class)]
class NtfyTest extends TestCase
{
    /** @var array<string, mixed> $config */
    private array $config = [
        'server' => 'https://ntfy.example.invalid',
        'topic' => 'testing',
        'priority' => 0,
        'auth' => [
            'method' => 'none'
        ]
    ];

    /**
     * Test `setup()` with no auth
     */
    #[DoesNotPerformAssertions]
    public function testSetupWithNoAuth(): void
    {
        new Ntfy($this->config);
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
        new Ntfy($config);
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
        new Ntfy($config);
    }

    /**
     * Test `send()`
     */
    public function testSend(): void
    {
        $client = self::createStub(\Ntfy\Client::class);
        $client->method('send')->willReturn(new stdClass());

        $ntfy = new Ntfy($this->config);

        $reflection = new ReflectionClass($ntfy);
        $property = $reflection->getProperty('client');
        $property->setAccessible(true);
        $property->setValue($ntfy, $client);

        $this->expectOutputRegex('/Sent notification using Ntfy/');

        $ntfy->send('Hello World', 'Hello from phpunit');
    }

    public function testSendNotificationException(): void
    {
        $this->expectException(NotificationException::class);
        $this->expectExceptionMessage('[Notification error] [Ntfy]');

        $ntfy = new Ntfy($this->config);
        $ntfy->send('Hello World', 'Hello from phpunit');
    }
}
