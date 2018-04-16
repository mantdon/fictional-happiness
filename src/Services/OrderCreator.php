<?php

namespace App\Services;


use App\Entity\Order;
use App\Entity\Service;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;

class OrderCreator
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function createOrder($vehicle, $services, $date)
    {
        $order = new Order();

        $visitDate = new \DateTime($date);
       // $services = $this->getReferenceCollection('App:Service', $this->getIds($services));
        $services = $this->em->getRepository('App:Service')->findBy(['id' => $this->getIds($services)]);
        $order
            ->setVehicle($this->getReference('App:Vehicle', $vehicle['id']))
            ->setServices($services)
            ->setCost($this->calculateCost($services))
            ->setVisitDate($visitDate);

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

    private function calculateCost($array)
    {
        $cost = 0;

        /**
         * @var $item Service
         */
        foreach ($array as $item)
        {
            $cost += $item->getPrice();
        }

        return $cost;
    }

    /**
     * May be used in the future
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