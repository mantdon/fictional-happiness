<?php

namespace App\DataFixtures;

use App\Entity\Service;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class ServiceFixtures extends Fixture
{
	public function load(ObjectManager $objectManager){
		for($i = 0; $i < 50; $i++){
			$service = new Service();
			$service->setName("Service" . $i);
			if($i % 5 == 0)
				$service->setDescription($this->getVeryLongDescription());
			else
				$service->setDescription("Description" . $i);
			$service->setPrice(mt_rand(10, 10000));
            $this->addReference('service' . $i, $service);
			$objectManager->persist($service);
		}

		$objectManager->flush();
	}

	private function getVeryLongDescription(){
		return "This is a very long... ish description.
				This is a very long... ish description.
				This is a very long... ish description.
				This is a very long... ish description.
				This is a very long... ish description.
				This is a very long... ish description.";
	}
}