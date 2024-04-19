<?Php

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
use Vigilant\Notification\Gotify;
use Vigilant\Exception\NotificationException;

#[CoversClass(Gotify::class)]
#[CoversClass(Vigilant\Notification\AbstractNotification::class)]
#[UsesClass(NotificationException::class)]
#[UsesClass(Vigilant\Output::class)]
class GotifyTest extends TestCase
{
    /** @var array<string, mixed> $config */
    private array $config = [
        'server' => 'https://gotify.example.invalid',
        'token' => 'qwerty',
        'priority' => 0,
    ];

    /**
     * Test `setup()`
     */
    #[DoesNotPerformAssertions]
    public function testSetup(): void
    {
        new Gotify($this->config);
    }

    /**
     * Test `send()`
     */
    public function testSend(): void
    {
        $client = self::createStub(\Gotify\Endpoint\Message::class);
        $client->method('create')->willReturn(new stdClass());

        $gotify = new Gotify($this->config);

        $reflection = new ReflectionClass($gotify);
        $property = $reflection->getProperty('message');
        $property->setAccessible(true);
        $property->setValue($gotify, $client);

        $this->expectOutputRegex('/Sent notification using Gotify/');

        $gotify->send('Hello World', 'Hello from phpunit');
    }

    public function testSendNotificationException(): void
    {
        $this->expectException(NotificationException::class);
        $this->expectExceptionMessage('[Notification error] [Gotify]');

        $gotify = new Gotify($this->config);
        $gotify->send('Hello World', 'Hello from phpunit');
    }
}
