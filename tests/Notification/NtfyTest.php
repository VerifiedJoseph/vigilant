<?Php

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
use Vigilant\Notification\Ntfy;
use Vigilant\Exception\NotificationException;

#[CoversClass(Ntfy::class)]
#[CoversClass(Vigilant\Notification\Notification::class)]
#[UsesClass(NotificationException::class)]
class NtfyTest extends TestCase
{
    private array $config = [
        'server' => 'https://ntfy.example.invalid',
        'topic' => 'testing',
        'priority' => 0,
        'auth' => [
            'method' => 'none'
        ]
    ];

    #[DoesNotPerformAssertions]
    public function testSetupWithNoAuth(): void
    {
        new Ntfy($this->config);
    }

    #[DoesNotPerformAssertions]
    public function testSetupWithUserAuth(): void
    {
        $config = $this->config;
        $config['auth']['method'] = 'password';
        $config['auth']['username'] = 'bob';
        $config['auth']['password'] = 'qwerty';
        new Ntfy($config);
    }

    #[DoesNotPerformAssertions]
    public function testSetupWithTokenAuth(): void
    {
        $config = $this->config;
        $config['auth']['method'] = 'token';
        $config['auth']['token'] = 'qwerty';
        new Ntfy($config);
    }

    public function testSendNotificationException(): void
    {
        $this->expectException(NotificationException::class);
        $this->expectExceptionMessage('[Notification error] [Ntfy]');

        $ntfy = new Ntfy($this->config);
        $ntfy->send('Hello World', 'Hello from phpunit');
    }
}
