<?php


namespace App\Tests\Util;

use App\Util\Clock;
use PHPUnit\Framework\TestCase;
use Symfony\Bridge\PhpUnit\ClockMock;

class ClockTest extends TestCase
{
    public static function setUpBeforeClass()
    {
        ClockMock::register(Clock::class);
    }

    /**
     * @dataProvider getTime
     * @param \DateTime $date
     */
    public function testGetCurrentTime($date)
    {
        ClockMock::withClockMock($date->format('U'));

        $this->assertEquals($date->format('Y-m-d H:i'), Clock::now());
    }

    static function getTime()
    {
        yield [ new \DateTime() ];
        yield [ new \DateTime('2023-05-10 20:36') ];
        yield [ new \DateTime('1983-10-23') ];
    }
}