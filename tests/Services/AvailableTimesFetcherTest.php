<?php


namespace App\Tests\Services;

use App\Entity\Order;
use App\Repository\OrderRepository;
use App\Services\AvailableTimesFetcher;
use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\TestCase;
use Symfony\Bridge\PhpUnit\ClockMock;

class AvailableTimesFetcherTest extends TestCase
{
    private $data;
    private $orderRepository;
    private $objectManager;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->data = [
                ['2018-08-10',[
                $this->createOrderWithDate('2018-08-10 9:00'),
                $this->createOrderWithDate('2018-08-10 15:00'),

                    ]
                ],
                ['2018-08-11',[
                    $this->createOrderWithDate('2018-08-11 9:00'),
                    $this->createOrderWithDate('2018-08-11 11:00'),
                    $this->createOrderWithDate('2018-08-11 13:00'),
                    $this->createOrderWithDate('2018-08-11 15:00'),
                ]],
                ['2018-08-12', [

                ]],
                ['2018-08-13',[
                    $this->createOrderWithDate('2018-08-13 9:00'),
                    $this->createOrderWithDate('2018-08-13 11:00'),
                    $this->createOrderWithDate('2018-08-13 13:00'),
                ]]
            ];

        $this->orderRepository = $this->createMock(OrderRepository::class);
        $this->setUpRepositoryResults($this->orderRepository);
        $this->objectManager = $this->createMock(EntityManager::class);

        $this->objectManager->expects($this->any())
            ->method('getRepository')
            ->willReturn($this->orderRepository);
    }

    private function createOrderWithDate($date)
    {
        $order = new Order();
        $order->setVisitDate(new \DateTime($date));
        return $order;
    }

    public static function setUpBeforeClass()
    {
        ClockMock::register(AvailableTimesFetcher::class);
    }

    /**
     * @group time-sensitive
     * @dataProvider getData
     */
    public function testItFetchesCorrectAvailableTimes($data)
    {
        $now = new \DateTime($data['date']);
        ClockMock::withClockMock($now->format('U'));

        $availableTimesFetcher = new AvailableTimesFetcher($this->objectManager);
        $result = $availableTimesFetcher->fetchDay($now->format('Y-m-d'));
        $this->assertEquals($data['expected'], $result);
    }

    private function setUpRepositoryResults($orderRepository)
    {
        $orderRepository->expects($this->any())
            ->method('findAllOnDate')
            ->will($this->returnValueMap($this->data));

    }

    public static function getData()
    {
        yield [['date' => '2018-08-10 10:12', 'expected' => ['11:00', '13:00']]];
        yield [['date' => '2018-08-11 07:20', 'expected' => []]];
        yield [['date' => '2018-08-12 8:30', 'expected' => ['09:00', '11:00', '13:00', '15:00']]];
        yield [['date' => '2018-08-13 12:10', 'expected' => ['15:00']]];
        yield [['date' => '2018-08-12 15:00', 'expected' => []]];
        yield [['date' => '2018-08-12 23:20', 'expected' => []]];
    }
}