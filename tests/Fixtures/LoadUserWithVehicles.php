<?php

namespace App\Tests\Fixtures;

use App\Entity\Order;
use App\Entity\User;
use App\Entity\Vehicle;
use App\Services\OrderCreator;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\BCryptPasswordEncoder;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;

class LoadUserWithVehicles implements FixtureInterface
{
    private $plateNumbers = ['NNN992', 'MMM123', 'DDD321'];
    private $ordersData = [
        ['vehicle' => ['id' => '1'], 'services' => [['id' => '1'], ['id' => '2']], 'date' => '2018-05-18 13:00'],
        ['vehicle' => ['id' => '1'], 'services' => [['id' => '1'], ['id' => '2']], 'date' => '2018-05-18 09:00'],
        ['vehicle' => ['id' => '1'], 'services' => [['id' => '1'], ['id' => '2']], 'date' => '2018-05-18 11:00'],
        ['vehicle' => ['id' => '1'], 'services' => [['id' => '1'], ['id' => '2']], 'date' => '2018-05-18 15:00'],
        ['vehicle' => ['id' => '1'], 'services' => [['id' => '1'], ['id' => '2']], 'date' => '2018-05-02 15:00']
    ];

    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setFirstName('foo')
            ->setLastName('bar')
            ->setEmail('info@complete.com')
            ->setPlainPassword('pass')
            ->setPhone('86551')
            ->setAddress('add')
            ->setCity('City')
            ->setRole('ROLE_USER')
            ->setRegistrationDate(new \DateTime());

        $passwordEncoders = [ User::class => new BCryptPasswordEncoder(12) ];
        $factory = new EncoderFactory($passwordEncoders);
        $passwordEncoder = new UserPasswordEncoder($factory);

        $user->setPassword($passwordEncoder->encodePassword($user, $user->getPlainPassword()));

        $manager->persist($user);

        $this->createVehicles($manager, $user);
        $this->createOrders($manager, $user);

        $manager->flush();
    }

    private function createVehicles($em, $user)
    {
        $vehicle = new Vehicle();
        foreach ($this->plateNumbers as $plateNumber) {
            $vehicle->setUser($user);
            $vehicle->setMake('make');
            $vehicle->setPlateNumber($plateNumber);
            $vehicle->setYearOfProduction('1999');
            $vehicle->setModel('model');
            $em->persist($vehicle);
        }
    }

    private function createOrders($em, $user)
    {
        $creator = new OrderCreator($em);

        foreach ($this->ordersData as $orderData)
        {
            $creator->createOrder($orderData['vehicle'], $orderData['services'], $orderData['date'], $user);
        }
    }
}