<?php

declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use Vigilant\UserAgent;
use Vigilant\Version;

#[CoversClass(UserAgent::class)]
#[UsesClass(Version::class)]
class UserAgentTest extends TestCase
{
    public function testGetDefault(): void
    {
        $expected = sprintf(
            'Vigilant/%s (https://github.com/VerifiedJoseph/vigilant)',
            Version::get()
        );

        $this->assertEquals($expected, UserAgent::getDefault());
    }
}
