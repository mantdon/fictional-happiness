<?php

namespace App\Tests\Fixtures;

use App\Entity\User;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\BCryptPasswordEncoder;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;

class LoadUserWithoutVehiclesAndOrders implements FixtureInterface
{

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

        $manager->flush();
    }
}