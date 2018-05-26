<?php

namespace App\Services;


use App\Entity\Service;
use Doctrine\ORM\EntityManagerInterface;

class ServiceFetcher
{
    private $repositoryName = 'App:Service';
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function findAll(): array
    {
        $services= $this->em->getRepository($this->repositoryName)->findAll();
        return $this->toSerializableArray($services);
    }

    /**
     * Converts array returned by repository to array serializable to JSON.
     * @param $objectArray
     * @return array
     */
    private function toSerializableArray($objectArray)
    {
        $services = [];

        /**
         * @var $service Service
         */
        foreach($objectArray as $service)
        {
            $services[] = [
                'id' => $service->getId(),
                'name' => $service->getName(),
                'description' => $service->getDescription(),
                'price' => $service->getPrice()
            ];
        }

        return $services;
    }
}