<?php

namespace App\DataFixtures;

use App\Entity\Order;
use App\Entity\User;
use App\Services\OrderCreator;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;

class OrderFixtures extends Fixture implements DependentFixtureInterface
{
    private $orderCreator;

    public function __construct(OrderCreator $orderCreator)
    {
        $this->orderCreator = $orderCreator;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();

        for($i = 0; $i < 100; $i++){
            /**
             * @var User $user
             */
            $user = $this->getReference('user'.$i);
            $vehicleCount = $user->getVehicles()->count();

            $vehicle = $user->getVehicles()->get(random_int(0, $vehicleCount - 1));
            $serviceIds = [];

            for ($s = random_int(1, 6); $s > 0; $s--) {
                $service = $this->getReference('service' . random_int(0, 49));
                if (in_array($service->getId(), $serviceIds)) {
                    $s--;
                    continue;
                }
                $serviceIds[] = ['id' => $service->getId()];
            }

            $vehicle = ['id' => $vehicle->getId()];
            $this->orderCreator->createOrder($vehicle, $serviceIds, $faker->dateTime('-1 year', '+5 months')->format('Y-m-d H:i'), $user);
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
