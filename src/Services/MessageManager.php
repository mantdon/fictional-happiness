<?php

namespace App\Services;

use App\Entity\Message;
use App\Entity\MessageMetaData;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use \DateTime;

class MessageManager{

	private $em;
	private $mailer;
	// Cannot be changed. Don't.
	private $fromEmailAddress = 'spaminator9001@outlook.com';

	public function __construct(EntityManagerInterface $em, \Swift_Mailer $mailer)
	{
		$this->em       = $em;
		$this->mailer   = $mailer;
	}

	/**
	 * Tries to find and return a message with the specified title and content
	 * form the database. Failing that creates a new one with the specified title
	 * and content.
	 * @param string $title
	 * @param string $content
	 * @return mixed
	 */
	public function fetchOrCreateMessage(string $title, string $content)
	{
		$message =  $this->em->getRepository('App:Message')->getByTitleAndContent($title, $content);

		if($message->getId() === NULL)
		{
			$message->setTitle($title);
			$message->setContent($content);
		}

		return $message;
	}

	/**
	 * Creates data allowing to differentiate instances of the same Message without
	 * creating duplicate entries of the same Message.
	 * @param Message $message
	 * @param User    $recipient
	 * @param string  $sender
	 * @return MessageMetaData
	 */
	private function createMessageMetaData(Message $message, User $recipient, string $sender)
	{
		$messageMetaData = new MessageMetaData();

		$currentDateTime = $this->getCurrentDateTime();

		$messageMetaData->setSender($sender)
			->setRecipient($recipient)
			->setDateSent($currentDateTime)
			->setIsRead(0)
			->setMessage($message)
			->setIsDeletedByUser(0);

		return $messageMetaData;
	}

	private function getCurrentDateTime()
	{
		$dateFormat = 'Y/m/d H:i:s';
		$today = date($dateFormat);
		return $currentDateTime = DateTime::createFromFormat($dateFormat, $today);
	}

	/**
	 * Checks whether the specified MessageMetaData contains the ID of the specified User.
	 * @param MessageMetaData $messageMetaData
	 * @param User            $user
	 * @return bool
	 */
	public function messageMeantForUser(MessageMetaData $messageMetaData, User $user)
	{
		return $messageMetaData->getRecipient()->getId() === $user ->getId();
	}

	/**
	 * Updates the MessageMetaData to signify that the message was read if it
	 * was not read already.
	 * @param MessageMetaData $messageMetaData
	 * @return bool
	 */
	public function markMessageAsReadIfUnread(MessageMetaData $messageMetaData)
	{
		if($messageMetaData->getIsRead() === false) {
			$messageMetaData->setIsRead( 1 );
			$this->em->persist( $messageMetaData );
			$this->em->flush();
			return true;
		}

		return false;
	}

	/**
	 * Updates the MessageMetaData to signify the the User has deleted the message.
	 * @param MessageMetaData $messageMetaData
	 * @return bool
	 */
	public function markMessageAsDeletedIfNotDeleted(MessageMetaData $messageMetaData)
	{
		if($messageMetaData->getIsDeletedByUser() === false) {
			$messageMetaData->setIsDeletedByUser( 1 );
			$this->em->persist( $messageMetaData );
			$this->em->flush();
			return true;
		}

		return false;
	}

	/**
	 * Creates MessageMetaData for the Message and allows the User the view it from their
	 * profile page.
	 * @param Message $message
	 * @param User    $recipient
	 * @param string  $sender String to be displayed as sender for the user.
	 */
	public function sendMessageToProfile(Message $message, User $recipient, string $sender = 'System')
	{
		$messageMetaData = $this->createMessageMetaData($message, $recipient, $sender);

		$recipient->getMessages()->add($messageMetaData);
		$message->getMetaData()->add($messageMetaData);

		$this->em->persist($message);
		$this->em->persist($messageMetaData);
		$this->em->flush();
	}

	/**
	 * Sends the Message to the email assigned to User. MessageMetaData creation
	 * is skipped for emails.
	 * @param Message $message
	 * @param User    $recipient
	 */
	public function sendMessageToEmail(Message $message, User $recipient)
	{
		$message = (new \Swift_Message('Hello Email'))
			->setFrom($this->fromEmailAddress)
			->setTo($recipient->getEmail())
			->setSubject($message->getTitle())
			->setBody($message->getContent(), 'text/html');
		$this->mailer->send($message);
	}

    /**
     * @param User $user
     * @return int count of unread messages in user's inbox.
     */
    public function getUsersUnreadMessagesCount($user)
    {
        return $user->getMessages()->filter(
            function($entry) {
                if (!$entry->getIsRead()) {
                    return true;
                }

                return false;
            }
        )->count();
    }

    /**
     * Sends the Message directly to the email. MessageMetaData creation
     * is skipped for emails.
     * @param Message $message
     * @param         $recipient
     */
    public function sendMessageDirectlyToEmail(Message $message, $recipient)
    {
        $message = (new \Swift_Message('Hello Email'))
            ->setFrom($this->fromEmailAddress)
            ->setTo($recipient)
            ->setSubject($message->getTitle())
            ->setBody($message->getContent(), 'text/html');
        $this->mailer->send($message);
    }
}