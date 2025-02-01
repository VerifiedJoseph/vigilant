<?php

declare(strict_types=1);

use PHPUnit\Framework\Attributes\CoversClass;
use Vigilant\Logger;

#[CoversClass(Logger::class)]
class LoggerTest extends TestCase
{
    /**
     * Test `info()`
     */
    public function testInfo(): void
    {
        $date = (new DateTime('now', new DateTimeZone('UTC')))->format('Y-m-d H:i:s P');

        $this->expectOutputString(sprintf(
            '[%s] %s %s',
            $date,
            'Hello',
            PHP_EOL
        ));

        $logger = new Logger('UTC', 1);
        $logger->info('Hello');
    }

    /**
     * Test `error()`
     */
    public function testError(): void
    {
        $this->expectOutputRegex('/Hello/');

        $logger = new Logger('UTC', 1);
        $logger->error('Hello');
    }

    /**
     * Test `debug()`
     */
    public function testDebug(): void
    {
        $this->expectOutputRegex('/Hello/');

        $logger = new Logger('UTC', 2);
        $logger->debug('Hello');
    }

    /**
     * Test setting log level too low
     */
    public function testLogLevelTooLow(): void
    {
        $logger = new Logger('UTC', -1);

        $reflection = new \ReflectionClass($logger);
        $actual = $reflection->getProperty('logLevel')->getValue($logger);

        $this->assertEquals(1, $actual);
    }

    /**
     * Test setting log level too hight
     */
    public function testLogLevelTooHigh(): void
    {
        $logger = new Logger('UTC', 3);

        $reflection = new \ReflectionClass($logger);
        $actual = $reflection->getProperty('logLevel')->getValue($logger);

        $this->assertEquals(2, $actual);
    }
}
