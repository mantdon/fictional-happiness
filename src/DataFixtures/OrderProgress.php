<?php

namespace App\DataFixtures;

use App\Entity\Order;
use App\Services\OrderCreator;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class OrderProgress extends Fixture implements DependentFixtureInterface
{
    private $orderCreator;

    public function __construct(OrderCreator $orderCreator)
    {
        $this->orderCreator = $orderCreator;
    }

    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i < 200; $i++) {
            $order = $this->getReference('order' . $i);
            $this->setOrderCompletion($order);
        }

        $manager->flush();
    }

    private function setOrderCompletion(Order $order)
    {
        $progress = $order->getProgress();
        $lines = $progress->getLines();
        $visitDate = $order->getVisitDate();

        if ($visitDate < new \DateTime('-1 week')) {
            foreach ($lines as $line) {
                $this->orderCreator->completeLine($line);
            }
        }
        else if ($visitDate < new \DateTime()) {
            foreach ($lines as $line) {
                if (random_int(1, 3) > 1) {
                    $this->orderCreator->completeLine($line);
                }
            }
            $this->orderCreator->approveOrder($order);
        }
        $this->orderCreator->finalizeOrder($order);
    }

    public function getDependencies()
    {
        return array(
            UserFixtures::class,
            ServiceFixtures::class
        );
    }
}
