<?php

namespace App\DataFixtures;

use App\Entity\Service;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class ServiceFixtures extends Fixture
{
    private $services = [
        'Važiuoklės diagnostika' => 10,
        'Priekinės pakabos balkio keitimas' => 25,
        'Šakės keitimas' => 12,
        'Priekinės pakabos šakės sailenbloko keitimas' => 18,
        'Priekinio balkio sailentbloko keitimas' => 20,
        'Prisukamo šarnyro keitimas' => 18,
        'Presuojamo šarnyro keitimas' => 22,
        'Amortizatorių keitimas' => 20,
        'Amortizatorių aps. gumų keitimas' => 20,
        'Priekinio rato posūkių ašies keitimas' => 15,
        'Varomojo priekinio rato guolio keitimas' => 18,
        'Stabilizatoriaus keitimas' => 10,
        'Stabilizatoriaus vidinių įvorių keitimas' => 10,
        'Priekinio pusašio nuėmimas-pastatymas' => 18,
        'Granatos perrinkimas' => 20,
        'Granatos apsauginės gumos keitimas' => 20,
        'Ratų suvedimas' => 25,
        'Vairo keitimas' => 5,
        'Vairo traukės antgalio keitimas' => 10,
        'Vairo traukės keitimas' => 10,
        'Stiprintuvo dirželio keitimas' => 12,
        'Nevaromojo galinio tilto keitimas' => 55,
        'Galinio tilto reduktoriaus keitimas' => 60,
        'Galinių pusašių apsauginių gumų keitimas' => 25,
        'Pusašio išėmimas-pastymas' => 18,
        'Galinių amortizatorių keitimas' => 15,
        'Galinių traukių keitimas' => 18,
        'Nevaromojo galinio rato guolio keitimas' => 20,
        'Tepalo keitimas galiniame tilte' => 5,
        'Tepalo keitimas reduktoriuje' => 10,
        'Varomojo galinio rato guolio keitimas' => 22,
        'Suporto keitimas' => 10,
        'Stabdžių disko keitimas' => 15,
        'Stabdžių vamzdelio keitimas' => 6,
        'Stabdžių šlangutės keitimas' => 10,
        'Pagrindinio stabdžių cilindro keitimas' => 22,
        'Galinio cilindriuko keitimas' => 12,
        'Galinių stabdžių lėkščių keitimas' => 30,
        'Rankinio stabdžio troso keitimas' => 22,
        'Stabžių stiprintuvo keitimas' => 30,
        'Diskinių stabdžių kaladėlių keitimas' => 20,
        'Stabdžių nuorinimas' => 20,
        'Rankinio troso reguliavimas' => 10,
        'Pavarų dėžės nuėmimas-pastatymas' => 60,
        'Tepalo keitimas mechanineje dėžėje' => 10,
        'Tepalo lygio tikrinimas' => 6,
        'Kardaninio veleno keitimas' => 30,
        'Spidometro troso keitimas' => 20,
        'Sankabos reguliavimas' => 20,
        'Sankabos nuorinimas' => 12,
        'Sankabos troso keitimas' => 15,
        'Generatoriaus nuėmimas-pastatymas' => 20,
        'Starterio nuėmimas-pastatymas' => 30,
        'Valytuvų variklio keitimas' => 25,
        'Degimo spynelės keitimas' => 30,
        'Degimo ritės keitimas' => 10,
        'Generatoriaus dirželio keitimas' => 10,
        'Priekinio žibinto stiklo keitimas' => 30,
        'Priekinio priešrūkinio žibinto keitimas' => 20,
        'Posūkio žibinto keitimas' => 6,
        'Radiatoriaus nuėmimas-pastatymas' => 15,
        'Duslintuvo nuėmimas-pastatymas' => 15,
        'AC sistemons pildymas' => 25,
        'AC sistemos patikra' => 10,
        'Tepalo keitimas' => 10,
        'Kuro filtro keitimas' => 10,
        'Karterio keitimas' => 25,
        'Galvutės remontas' => 75,
        'Vožtuvų remontas' => 25,
        'Variklio keitimas' => 120
    ];// count = 69

	public function load(ObjectManager $objectManager){
	    $index = 0;

		foreach ($this->services as $serviceName => $price) {
			$service = new Service();
			$service->setName($serviceName);
			$service->setPrice($price);
			$service->setDescription('');
            $this->addReference('service' . $index, $service);
			$objectManager->persist($service);
			$index++;
		}

		$objectManager->flush();
	}
}