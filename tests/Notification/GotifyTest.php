<?Php

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
use Vigilant\Notification\Gotify;
use Vigilant\Exception\NotificationException;

#[CoversClass(Gotify::class)]
#[CoversClass(Vigilant\Notification\Notification::class)]
#[UsesClass(NotificationException::class)]
class GotifyTest extends TestCase
{
    /** @var array<string, mixed> $config */
    private array $config = [
        'server' => 'https://gotify.example.invalid',
        'token' => 'qwerty',
        'priority' => 0,
    ];

    #[DoesNotPerformAssertions]
    public function testSetupWithNoAuth(): void
    {
        new Gotify($this->config);
    }

    public function testSendNotificationException(): void
    {
        $this->expectException(NotificationException::class);
        $this->expectExceptionMessage('[Notification error] [Gotify]');

        $ntfy = new Gotify($this->config);
        $ntfy->send('Hello World', 'Hello from phpunit');
    }
}
