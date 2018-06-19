<?php

namespace Lullabot\Mpx\Tests\Unit\DataService\DateTime;

use Lullabot\Mpx\DataService\DateTime\NullDateTime;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Lullabot\Mpx\DataService\DateTime\NullDateTime
 */
class NullDateTimeTest extends TestCase
{
    public function testFormat()
    {
        $date = new NullDateTime();
        $this->assertEmpty($date->format('Y'));
    }
}
