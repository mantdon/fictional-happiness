<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity("email", message="Pasirinktas elektroninis paštas jau užregistruotas")
 */
class User implements UserInterface, \Serializable
{
    public function __construct(){
    	$this->vehicles = new ArrayCollection();
    	$this->messages = new ArrayCollection();
    	$this->orders = new ArrayCollection();
    }

	/**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=25, unique=true)
     * @Assert\NotBlank(message="Įveskite Email")
     * @Assert\Email(message="Neteisingas Email formatas")
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=64, unique=true)
     */
    private $password;

    /**
     * @Assert\NotBlank(message="Įveskite slaptažodį", groups={"Registration"})
     * @Assert\Length(min="5", minMessage="Slaptažodį turi sudaryti bent 5 simboliai")
     */
    private $plainPassword;

    /**
     * @ORM\Column(type="string", length=64, nullable=true)
     * @Assert\Type(type="alpha", message="Varde gali būti tik raidės")
     */
    private $first_name;

    /**
     * @ORM\Column(type="string", length=64, nullable=true)
     * @Assert\Type(type="alpha", message="Pavardėje gali būti tik raidės")
     */
    private $last_name;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     * @Assert\Regex(pattern="/^\+?[0-9]+$/", message="Blogas numerio formatas")
     */
    private $phone;

    /**
     * @ORM\Column(type="string", length=64, nullable=true)
     */
    private $city;

    /**
     * @ORM\Column(type="string", length=254, nullable=true)
     */
    private $address;

    /**
     * @ORM\Column(type="string", length=64)
     */
    private $role;

	/**
	 * @ORM\Column(type="datetime")
	 */
    private $registrationDate;

	/**
	 * @ORM\OneToMany(targetEntity="App\Entity\Vehicle", mappedBy="user")
	 */
    private $vehicles;

	/**
	 * @ORM\OneToMany(targetEntity="App\Entity\MessageMetaData", mappedBy="recipient");
	 */
    private $messages;

	/**
	 * @ORM\OneToMany(targetEntity="App\Entity\Order", mappedBy="user")
	 */
    private $orders;

    public function getUsername()
    {
        return $this->email;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return User
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    /**
     * @param mixed $plainPassword
     * @return User
     */
    public function setPlainPassword($plainPassword)
    {
        $this->plainPassword = $plainPassword;
        return $this;
    }


    /**
     * @return mixed
     */
    public function getFirstName()
    {
        return $this->first_name;
    }

    /**
     * @param mixed $first_name
     * @return User
     */
    public function setFirstName($first_name)
    {
        $this->first_name = $first_name;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLastName()
    {
        return $this->last_name;
    }

    /**
     * @param mixed $last_name
     * @return User
     */
    public function setLastName($last_name)
    {
        $this->last_name = $last_name;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param mixed $phone
     * @return User
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param mixed $city
     * @return User
     */
    public function setCity($city)
    {
        $this->city = $city;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param mixed $address
     * @return User
     */
    public function setAddress($address)
    {
        $this->address = $address;
        return $this;
    }

	/**
	 * @return \DateTime
	 */
	public function getRegistrationDate()
	{
		return $this->registrationDate;
	}

	public function setRegistrationDate(\DateTime $registrationDate)
	{
		$this->registrationDate = $registrationDate;

		return $this;
	}

    public function getSalt()
    {
        return null;
    }

    public function setRole($role)
    {
        $this->role = $role;
        return $this;
    }

    public function getRoles()
    {
        return array($this->role);
    }

    public function eraseCredentials()
    {
    }

    # Returning email without @handle
    public function getEmailName()
    {
        return preg_filter('/@.*/', '', $this->email);
    }

    public function getVehicles(){
    	return $this->vehicles;
    }

    public function getMessages(){
    	return $this->messages;
    }

	/**
	 * @return ArrayCollection
	 */
    public function getOrders()
    {
    	return $this->orders;
    }

    public function getNumberOfOngoingOrders()
    {
    	$count = 0;

    	foreach($this->orders as $order)
    		if($order->getProgress()->getIsDone() === false)
    			$count++;

    	return $count;
    }

    /** @see \Serializable::serialize() */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->email,
            $this->password,
        ));
    }

    /** @see \Serializable::unserialize() */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->email,
            $this->password,
            ) = unserialize($serialized);
    }
}