<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MessageRepository")
 */
class Message
{
	public function __construct()
	{
		$this->metaData = new ArrayCollection();
	}

	/**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

	/**
	 * @ORM\Column(type="string", length=100)
	 */
    private $title;

    /**
     * @ORM\Column(type="text")
     */
    private $content;

	/**
	 * @ORM\OneToMany(targetEntity="App\Entity\MessageMetaData", mappedBy="message")
	 */
    private $metaData;

    public function getId()
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
    	return $this->title;
    }

    public function setTitle(string $title): self
    {
    	$this->title = $title;

    	return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getMetaData()
    {
    	return $this->metaData;
    }
}
