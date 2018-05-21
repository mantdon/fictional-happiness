<?php

namespace App\DataFixtures;

use App\Entity\Order;
use App\Services\OrderCreator;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;

class OrderFixtures extends Fixture implements DependentFixtureInterface
{
    private $orderCreator;
    private $orderTimes = [
        ['15:00'],
        ['9:00', '13:00'],
        ['9:00', '11:00', '13:00', '15:00'],
        ['9:00', '11:00', '13:00'],
        ['9:00', '11:00', '13:00', '15:00'],
        ['13:00', '15:00'],
        ['9:00', '11:00', '13:00'],
        ['9:00', '11:00', '13:00', '15:00'],
        ['9:00', '11:00', '13:00', '15:00'],
        ['9:00', '15:00'],
        ['9:00', '11:00', '13:00', '15:00'],
        ['9:00', '11:00', '13:00'],
        ['9:00', '11:00', '13:00', '15:00'],
        ['9:00', '11:00', '13:00'],
        ['9:00', '11:00'],
        ['9:00', '11:00', '13:00'],
        ['9:00', '11:00', '13:00', '15:00'],
        ['11:00', '13:00'],
        ['9:00', '15:00'],
        ['9:00', '11:00', '13:00'],
        ['9:00', '11:00', '13:00', '15:00'],
        ['9:00', '11:00'],
        ['9:00', '11:00', '13:00', '15:00'],
        ['9:00', '11:00', '13:00', '15:00'],
        ['9:00', '11:00', '13:00', '15:00'],
        ['11:00', '13:00', '15:00'],
        ['9:00', '11:00'],
        ['9:00', '11:00', '13:00', '15:00'],
        ['9:00', '11:00'],
        ['13:00', '15:00'],
        ['9:00', '11:00', '15:00'],
    ];

    public function __construct(OrderCreator $orderCreator)
    {
        $this->orderCreator = $orderCreator;
    }

    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i < 200; $i++) {
            $order = $this->createOrder($i);
            $this->addReference('order' . $i, $order);
        }

        $this->createOrdersThatActuallyOccupyTimes();
    }

    private function createOrder(int $userId): Order
    {
        $faker = Factory::create();
        $user = $this->getReference('user'.$userId);
        $vehicleCount = $user->getVehicles()->count();

        $vehicle = $user->getVehicles()->get(random_int(0, $vehicleCount - 1));
        $serviceIds = $this->getRandomServices();

        $vehicle = ['id' => $vehicle->getId()];
        $visitDate = $faker->dateTimeInInterval('-3 months', '+5 months');

        $order = $this->orderCreator->createOrder($vehicle, $serviceIds, $visitDate->format('Y-m-d H:i'), $user);

        return $order;
    }

    private function getRandomServices(): array
    {
        $serviceIds = [];
        $services = [];
        for ($s = random_int(1, 6); $s > 0; $s--) {
            $service = $this->getReference('service' . random_int(0, 49));
            if (in_array($service->getId(), $serviceIds)) {
                $s--;
                continue;
            }
            $serviceIds[] = $service->getId();
            $services[] = ['id' => $service->getId()];
        }

        return $services;

    }

    private function createOrdersThatActuallyOccupyTimes()
    {
        $count = 0;
        foreach ($this->orderTimes as $daysOffset => $times) {
            foreach ($times as $time) {
                $order = $this->createOrder($count);
                $date = new \DateTime('+' . $daysOffset . ' days');
                $order->setVisitDate(new \DateTime($date->format('Y-m-d') . ' ' . $time));
                $count++;
            }
        }
    }

    public function getDependencies()
    {
        return array(
            UserFixtures::class,
            ServiceFixtures::class
        );
    }
}
