<?php

declare(strict_types=1);

namespace Tests\Helper;

use Tests\TestCase;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use Vigilant\Helper\Time;
use Vigilant\Exception\AppException;

#[CoversClass(Time::class)]
#[UsesClass(AppException::class)]
class TimeTest extends TestCase
{
    // 'H:i' 05:10 - 24-hour with leading zero and minutes
    // 'G:i' 5:10  - 24-hour without leading zero and minutes
    #[TestWith(['05:10', true])]
    #[TestWith(['5:10', true])]
    #[TestWith(['1am', false])]
    #[TestWith(['25:00', false])]
    public function testIsValid(string $time, bool $expected): void
    {
        $this->assertEquals($expected, Time::isValid($time));
    }
}
