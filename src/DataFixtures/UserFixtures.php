<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    private $passwordEncoder;
    private $usersPasswords = 'password';
    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();
        $password = $this->passwordEncoder->encodePassword(new User(), $this->usersPasswords);

        for($i = 0; $i < 10000; $i++){
            /**
             * @var User $user
             */
            $user = new User();
            $user->setFirstName($faker->firstName);
            $user->setLastName($faker->lastName);
            $user->setPhone('86' . random_int('1200000', '9999999'));
            $user->setCity($faker->city);
            $user->setRole(random_int(0, 1000) > 998 ? 'ROLE_EMPLOYEE' : 'ROLE_USER');
            $user->setRegistrationDate($faker->dateTimeBetween('-1 year'));
            $user->setAddress($faker->address);
            $user->setEmail($faker->unique()->email);
            $this->addReference('user' . $i, $user);
            $user->setPassword($password);
            $manager->persist($user);
        }

        $manager->flush();
    }
}
