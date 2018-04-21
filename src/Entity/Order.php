<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use App\Helpers\EnumOrderStatusType;
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

    /**
     * @ORM\Column(type="decimal", scale=2)
     */
    private $cost;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $visitDate;

	/**
	 * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="orders")
	 */
    private $user;

	/**
	 * @ORM\OneToOne(targetEntity="OrderProgress")
	 * @ORM\JoinColumn(name="progress_id", referencedColumnName="id", onDelete="CASCADE")
	 */
    private $progress;

    /**
     * @ORM\Column(type="orderstatus")
     */
    private $status;

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
     * @return ArrayCollection
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

    /**
     * @return mixed
     */
    public function getCost()
    {
        return $this->cost;
    }

    /**
     * @param mixed $cost
     * @return Order
     */
    public function setCost($cost)
    {
        $this->cost = $cost;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getVisitDate()
    {
        return $this->visitDate;
    }

    /**
     * @param mixed $visitDate
     * @return Order
     */
    public function setVisitDate($visitDate)
    {
        $this->visitDate = $visitDate;
        return $this;
    }

	/**
	 * @return mixed
	 */
	public function getUser()
	{
		return $this->user;
	}

	/**
	 * @param mixed $user
	 * @return Order
	 */
	public function setUser($user)
	{
		$this->user = $user;

		return $this;
	}

	/**
	 * @return OrderProgress
	 */
	public function getProgress()
	{
		return $this->progress;
	}

	/**
	 * @param $progress
	 * @return Order
	 */
	public function setProgress($progress)
	{
		$this->progress = $progress;

		return $this;
	}

	public function getStatus(): string
	{
		return $this->status;
	}

	public function setStatus(string $status)
	{
		$this->status = $status;

		return $this;
	}
}
