<?php

namespace App\Services;


use App\Entity\Order;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;

class OrderCreator
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function createOrder($vehicle, $services)
    {
        $order = new Order();

        $order
            ->setVehicle($this->getReference('App:Vehicle', $vehicle['id']))
            ->setServices($this->getReferenceCollection('App:Service', $this->getIds($services)));

        $this->em->persist($order);
        $this->em->flush();
    }

    /**
     * @param $entityName String
     * @param $id integer
     * @return null|object used for setting database relationships.
     */
    private function getReference($entityName, $id)
    {
        return $this->em->getReference($entityName,$id);
    }

    /**
     * @param $entityName String
     * @param $ids array
     * @return ArrayCollection used for setting multiple relationships.
     */
    private function getReferenceCollection($entityName, $ids)
    {
        $result = new ArrayCollection();

        foreach ($ids as $id)
        {
            $result->add($this->getReference($entityName, $id));
        }

        return $result;
    }

    /**
     * Picks out id of every element in array.
     * @param $data array
     * @return array
     */
    private function getIds($data)
    {
        $ids = [];

        foreach ($data as $item)
        {
            $ids[] = $item['id'];
        }

        return $ids;
    }
}