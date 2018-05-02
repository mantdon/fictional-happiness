<?php


namespace App\Tests\Services;

use App\Services\AvailableTimesFetcher;
use App\Services\UnavailableDaysFinder;
use PHPUnit\Framework\TestCase;
use Symfony\Bridge\PhpUnit\ClockMock;

class UnavailableDaysFinderTest extends TestCase
{
    private $data;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->data = [
            ['2018-08-10',
                true
            ],
            ['2018-08-11',
                true
            ],
            ['2018-08-12',
                true
            ],
            ['2018-08-13',
                true
            ],
            ['2018-09-03',
                true
            ],
            ['2018-12-23',
                true
            ]
        ];
    }

    public static function setUpBeforeClass()
    {
        ClockMock::register(UnavailableDaysFinder::class);
    }

    /**
     * @group time-sensitive
     * @dataProvider getData
     */
    public function testItFetchesCorrectUnavailableDays($data)
    {
        $now = new \DateTime($data['now']);
        ClockMock::withClockMock($now->format('U'));

        $availableTimesFetcher = $this->createMock(AvailableTimesFetcher::class);

        $availableTimesFetcher->expects($this->any())
            ->method('isDayUnavailable')
            ->will($this->returnValueMap($this->data));

        $finder = new UnavailableDaysFinder($availableTimesFetcher);
        $result = $finder->findDays();

        $this->assertEquals($data['expected'], $result);
    }

    public static function getData()
    {
        yield [['now' => '2018-08-09', 'expected' => ['2018-08-10', '2018-08-11', '2018-08-12', '2018-08-13', '2018-09-03']]];
        yield [['now' => '2018-08-11', 'expected' => ['2018-08-11', '2018-08-12', '2018-08-13', '2018-09-03']]];
        yield [['now' => '2019-10-23', 'expected' => []]];
    }
}