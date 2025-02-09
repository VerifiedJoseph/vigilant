<?php

declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use Vigilant\Exception\NotificationException;

#[CoversClass(NotificationException::class)]
class NotificationExceptionTest extends TestCase
{
    public function testException(): void
    {
        $this->expectException(NotificationException::class);
        $this->expectExceptionMessage('[Notification error] Testing');

        throw new NotificationException('Testing');
    }
}
