<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MessageMetaDataRepository")
 */
class MessageMetaData
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
    private $Sender;

	/**
	 * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="messages")
	 */
    private $recipient;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateSent;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isRead;

	/**
	 * @ORM\Column(type="boolean")
	 */
    private $isDeletedByUser;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Message", inversedBy="metaData")
     */
    private $message;

    public function getId()
    {
        return $this->id;
    }

    public function getSender(): ?string
    {
        return $this->Sender;
    }

    public function setSender(string $Sender): self
    {
        $this->Sender = $Sender;

        return $this;
    }

    public function getRecipient()
    {
        return $this->recipient;
    }

    public function setRecipient(User $recipient)
    {
    	$this->recipient = $recipient;

    	return $this;
    }

    public function getDateSent(): ?\DateTimeInterface
    {
        return $this->dateSent;
    }

    public function setDateSent(\DateTimeInterface $dateSent): self
    {
        $this->dateSent = $dateSent;

        return $this;
    }

    public function getIsRead(): ?bool
    {
        return $this->isRead;
    }

    public function setIsRead(bool $isRead): self
    {
        $this->isRead = $isRead;

        return $this;
    }

    public function getIsDeletedByUser(): ?bool
    {
    	return $this->isDeletedByUser;
    }

    public function setIsDeletedByUser(bool $isDeletedByUser): self
    {
    	$this->isDeletedByUser = $isDeletedByUser;

    	return $this;
    }

    public function getMessage()
    {
    	return $this->message;
    }

    public function setMessage(Message $message)
    {
    	$this->message = $message;

    	return $this;
    }
}
