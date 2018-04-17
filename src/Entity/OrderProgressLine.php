<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\OrderProgressLineRepository")
 */
class OrderProgressLine
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="OrderProgress", inversedBy="lines")
     * @ORM\JoinColumn(name="progress_id", referencedColumnName="id")
     */
    private $progress;

    /**
     * @ORM\ManyToOne(targetEntity="Service")
     * @ORM\JoinColumn(name="service_id", referencedColumnName="id")
     */
    private $service;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isDone;

	/**
	 * @ORM\Column(type="datetime", nullable=true)
	 */
    private $completedOn;

    public function getId()
    {
        return $this->id;
    }

	/**
	 * @return OrderProgress
	 */
    public function getProgress()
    {
        return $this->progress;
    }

    public function setProgress($progress): self
    {
        $this->progress = $progress;

        return $this;
    }

	/**
	 * @return Service
	 */
    public function getService()
    {
        return $this->service;
    }

    public function setService($service): self
    {
        $this->service = $service;

        return $this;
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

    public function getCompletedOn()
    {
    	return $this->completedOn;
    }

    public function setCompletedOn($completedOn)
    {
    	$this->completedOn = $completedOn;

    	return $this;
    }
}
