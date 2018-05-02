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
	 * @Assert\NotBlank(message="Įveskite automobilio markę.")
	 * @Assert\Type(type="alpha", message="Automobilio markei leidžiamos tik raidės.")
	 * @Assert\Length(
	 *      min = 3,
	 *      max = 12,
	 *      minMessage = "Leistinas markės ilgis 3-12 simbolių.",
	 *      maxMessage = "Leistinas markės ilgis 3-12 simbolių."
	 * )
	 */
    private $make;

	/**
	 * @ORM\Column(type="string", length=30)
	 * @Assert\NotBlank(message="Įveskite automobilio modelį.")
	 * @Assert\Type(type="alnum", message="Modelio pavadinime leidžiamos tik raidės ir skaičiai.")
	 * @Assert\Length(
	 *      min = 3,
	 *      max = 12,
	 *      minMessage = "Leistinas modelio ilgis 3-12 simbolių.",
	 *      maxMessage = "Leistinas modelio ilgis 3-12 simbolių."
	 * )
	 */
    private $model;

	/**
	 * @ORM\Column(type="integer")
	 * @Assert\Range(
	 *     min = 1930,
	 *     max = 2018,
	 *     minMessage="Minimalūs leidžiami metai: 1930",
	 *     maxMessage="Neegzistuoja automobilis pagamintas šiais metais")
	 * @Assert\NotBlank(message="Įveskite automobilio pagaminimo metus.")
	 * @Assert\Type(type="digit", message="Metai susideda tik iš skaičių")
	 */
    private $year_of_production;

	/**
	 * @ORM\Column(type="string", length=7)
	 * @Assert\NotBlank(message="Įveskite automobilio numerius.")
	 * @Assert\Type(type="alnum", message="Numeriai turi susidaryti tik iš raidžių ir skaičių.")
	 * @Assert\Length(
	 *      min = 5,
	 *      max = 8,
	 *      minMessage = "Leistinas numerių ilgis 5-8 simboliai.",
	 *      maxMessage = "Leistinas numerių ilgis 5-8 simboliai."
	 * )
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
