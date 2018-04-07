<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\OrderRepository")
 * @ORM\Table(name="`order`")
 */
class Order
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Vehicle")
     * @ORM\JoinColumn(name="vehicle_id", referencedColumnName="id")
     */
    private $vehicle;

    /**
     * @var service
     * @ORM\ManyToMany(targetEntity="App\Entity\Service")
     * @ORM\JoinColumn(name="services_ids",referencedColumnName="id")
     */
    private $services;


    public function __construct() {
        $this->services = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getVehicle()
    {
        return $this->vehicle;
    }

    /**
     * @param mixed $vehicle
     * @return Order
     */
    public function setVehicle($vehicle)
    {
        $this->vehicle = $vehicle;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getServices()
    {
        return $this->services;
    }

    /**
     * @param mixed $servicesIds
     * @return Order
     */
    public function setServices($services)
    {
        $this->services = $services;
        return $this;
    }
}
