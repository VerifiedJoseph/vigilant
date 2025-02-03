<?php

declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use Vigilant\Exception\FeedsException;

#[CoversClass(FeedsException::class)]
class FeedsExceptionTest extends TestCase
{
    public function testException(): void
    {
        $this->expectException(FeedsException::class);
        $this->expectExceptionMessage('Feeds error: Testing');

        throw new FeedsException('Testing');
    }
}
