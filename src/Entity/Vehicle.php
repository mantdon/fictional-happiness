<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\VehicleRepository")
 */
class Vehicle
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

	/**
	 * @ORM\Column(type="string", length=20);
	 * @Assert\NotBlank(message="Needs a make")
	 * @Assert\Type(type="alpha", message="Make from letters only")
	 */
    private $make;

	/**
	 * @ORM\Column(type="string", length=30)
	 * @Assert\NotBlank(message="Needs a model")
	 * @Assert\Type(type="alnum", message="Model from letters and numbers only")
	 */
    private $model;

	/**
	 * @ORM\Column(type="integer")
	 * @Assert\Range(min = 1930, max=2018, minMessage="Too low", maxMessage="too high")
	 * @Assert\NotBlank(message="Needs a year")
	 * @Assert\Type(type="digit", message="only digits in year")
	 */
    private $year_of_production;

	/**
	 * @ORM\Column(type="string", length=7)
	 * @Assert\NotBlank(message="needs plates")
	 * @Assert\Type(type="alnum", message="Only digits and letters for plates")
	 */
    private $plate_number;

	/**
	 * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="vehicles")
	 * @ORM\JoinColumn(nullable=true)
	 */
    private $user;

    public function getId()
    {
        return $this->id;
    }

	/**
	 * @return mixed
	 */
	public function getMake()
	{
		return $this->make;
	}

	/**
	 * @param mixed $make
	 */
	public function setMake( $make ): void
	{
		$this->make = $make;
	}

	/**
	 * @return mixed
	 */
	public function getModel()
	{
		return $this->model;
	}

	/**
	 * @param mixed $model
	 */
	public function setModel( $model ): void
	{
		$this->model = $model;
	}

	/**
	 * @return mixed
	 */
	public function getYearOfProduction()
	{
		return $this->year_of_production;
	}

	/**
	 * @param mixed $year_of_production
	 */
	public function setYearOfProduction( $year_of_production ): void
	{
		$this->year_of_production = $year_of_production;
	}

	/**
	 * @return mixed
	 */
	public function getPlateNumber()
	{
		return $this->plate_number;
	}

	/**
	 * @param mixed $plate_number
	 */
	public function setPlateNumber( $plate_number ): void
	{
		$this->plate_number = $plate_number;
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
	 */
	public function setUser( $user ): void
	{
		$this->user = $user;
	}
}
