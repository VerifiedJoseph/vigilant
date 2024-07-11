<?php

use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use Vigilant\Helper\Time;
use Vigilant\Exception\AppException;

#[CoversClass(Time::class)]
#[UsesClass(AppException::class)]
class TimeTest extends TestCase
{
    // 'g a', 1 am  - 12-hour without leading zeros and lowercase am/pm
    // 'G a', 01 am - 12-hour with leading zeros and lowercase am/pm
    // 'ga',  1am   - 12-hour without leading zeros, lowercase am/pm and no space
    // 'ha',  01am  - 12-hour with leading zeros, lowercase am/pm and no space
    // 'H',   05    - 24-hour with leading zeros
    // 'G',   15    - 24-hour without leading zeros
    #[TestWith(['1 am', true])]
    #[TestWith(['01 am', true])]
    #[TestWith(['1am', true])]
    #[TestWith(['01am', true])]
    #[TestWith(['05', true])]
	#[TestWith(['15', true])]
    #[TestWith(['15:00', false])]
    public function testIsValid(string $time, bool $expected): void
    {
        $this->assertEquals($expected, Time::isValid($time));
    }
}
