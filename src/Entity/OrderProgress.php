<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\OrderProgressRepository")
 */
class OrderProgress
{
	public function __construct()
	{
		$this->lines = new ArrayCollection();
	}

	/**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Order")
     * @ORM\JoinColumn(name="order_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $order;

    /**
     * @ORM\OneToMany(targetEntity="OrderProgressLine", mappedBy="progress")
     */
    private $lines;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isDone;

	/**
	 * @ORM\Column(type="datetime", nullable=true)
	 */
	private $completionDate;

	/**
	 * @ORM\Column(type="integer", nullable=false)
	 */
	private $numberOfServicesCompleted;

    public function getId()
    {
        return $this->id;
    }

	/**
	 * @return Order
	 */
    public function getOrder()
    {
        return $this->order;
    }

    public function setOrder($order): self
    {
        $this->order = $order;

        return $this;
    }

	/**
	 * @return ArrayCollection
	 */
    public function getLines()
    {
        return $this->lines;
    }

    public function getIsDone(): ?bool
    {
        return $this->isDone;
    }

    public function setIsDone(bool $isDone): self
    {
        $this->isDone = $isDone;

        return $this;
    }

	public function getNumberOfServicesCompleted()
	{
		return $this->numberOfServicesCompleted;
	}

	/**
	 * @param mixed $numberOfServicesCompleted
	 * @return OrderProgress
	 */
	public function setNumberOfServicesCompleted($numberOfServicesCompleted)
	{
		$this->numberOfServicesCompleted = $numberOfServicesCompleted;
		return $this;
	}

	public function incrementNumberOfServicesCompleted()
	{
		if($this->numberOfServicesCompleted < $this->order->getServices()->count())
			$this->numberOfServicesCompleted++;
		else
			throw new \OutOfRangeException("Number of services completed cannot exceed the number of services ordered.");
	}

	public function decrementNumberOfServicesCompleted()
	{
		if($this->numberOfServicesCompleted >= 1)
			$this->numberOfServicesCompleted--;
		else
			throw new \OutOfRangeException("Number of services completed cannot be negative.");
	}

	/**
	 * @return mixed
	 */
	public function getCompletionDate()
	{
		return $this->completionDate;
	}

	/**
	 * @param mixed $completionDate
	 * @return OrderProgress
	 */
	public function setCompletionDate($completionDate)
	{
		$this->completionDate = $completionDate;
		return $this;
	}
}
