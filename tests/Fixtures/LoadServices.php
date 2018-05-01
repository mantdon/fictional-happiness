<?php

namespace App\Tests\Fixtures;

use App\Entity\Service;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadServices implements FixtureInterface
{

	/**
	 * Load data fixtures with the passed EntityManager
	 *
	 * @param ObjectManager $manager
	 * @throws \Exception
	 */
	public function load(ObjectManager $manager):void
	{
		for($i = 0; $i < 15; $i++){
			$service = new Service();
			$service->setName('Service' . $i);
			$service->setDescription('Description ' . $i);
			$service->setPrice($i * 1000);
			$manager->persist($service);
		}

		$manager->flush();
	}
}