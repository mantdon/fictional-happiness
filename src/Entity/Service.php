<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ServiceRepository")
 */
class Service
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

	/**
	 * @ORM\Column(type="string", length=100)
	 */
    private $name;

	/**
	 * @ORM\Column(type="text")
	 */
    private $description;

	/**
	 * @ORM\Column(type="decimal", scale=2)
	 * @Assert\Regex(pattern="/^[0-9]*(\.)*[0-9]*$/", message="Kaina susideda tik iš skaitmenų. Liekana atskiriama '.'")
	 */
    private $price;

    public function getId()
    {
        return $this->id;
    }

	/**
	 * @return mixed
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @param mixed $name
	 */
	public function setName( $name ): void
	{
		$this->name = $name;
	}

	/**
	 * @return mixed
	 */
	public function getDescription()
	{
		return $this->description;
	}

	/**
	 * @param mixed $description
	 */
	public function setDescription( $description ): void
	{
		$this->description = $description;
	}

	/**
	 * @return mixed
	 */
	public function getPrice()
	{
		return $this->price;
	}

	/**
	 * @param mixed $price
	 */
	public function setPrice( $price ): void
	{
		$this->price = $price;
	}
}
