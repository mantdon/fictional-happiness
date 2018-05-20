<?php

namespace App\DataFixtures;

use App\Entity\Vehicle;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;

class VehicleFixtures extends Fixture
{
    private $carData = [
        'Audi' => [
            'A1', 'A2', 'A3', 'A4', 'A5', 'A6', 'Q1', 'Q2', 'Q3'
        ],
        'Citroen' => [
            'Berlingo', 'C1', 'C2', 'C3'
        ],
        'Dacia' => [
            'Duster', 'Sandero'
        ],
        'Ford' => [
            'B-Max', 'C-Max', 'Fiesta', 'Focus', 'Galaxy', 'Mondeo'
        ],
        'Honda' => [
            'Civic', 'CR-V'
        ],
        'Hyundai' => [
            'i10', 'i20', 'i30'
        ],
        'Mitsubishi' => [
            'ASX', 'Eclipse', 'Mirage'
        ],
        'Peugeot' => [
            '108', '208', '308'
        ],
        'Toyota' => [
            'Auris', 'Prius', 'Rav4', 'Yaris'
        ],
        'Volkswagen' => [
            'Golf', 'Passsat', 'Sharan'
        ]
    ];

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();
        $id = 0;

        for ($i = 0; $i < 10000; $i++) {

            for ($x = random_int(1, 2); $x > 0; $x--) {
                /**
                 * @var Vehicle $vehicle
                 */
                $vehicle = new Vehicle();
                $makeIndex = array_rand($this->carData);
                $vehicle->setMake($makeIndex);
                $modelIndex = array_rand($this->carData[$makeIndex]);
                $vehicle->setModel($this->carData[$makeIndex][$modelIndex]);
                $vehicle->setPlateNumber($faker->randomLetters . $faker->randomLetter . $faker->randomLetter . $faker->randomNumber(3, true));
                $vehicle->setYearOfProduction($faker->dateTimeBetween('-20 years')->format('Y'));
                $vehicle->setUser($this->getReference('user'.$i));
                $manager->persist($vehicle);
                $id++;
            }
        }

        $manager->flush();
    }
}
