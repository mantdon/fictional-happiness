<?php

namespace App\Services;

use App\Entity\PasswordReset;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class PasswordResetter
{
    private $mailer;
    private $entityManager;
    private $templating;
    private $repository;

    public function __construct(MessageManager $mailer, EntityManagerInterface $entityManager, \Twig_Environment $templating)
    {
        $this->mailer = $mailer;
        $this->entityManager = $entityManager;
        $this->templating = $templating;
        $this->repository = $this->entityManager->getRepository(PasswordReset::class);
    }

    /**
     * Send an user message containing a link to password reset form.
     * @param User $user
     */
    public function resetPassword(User $user)
    {
        $reset = $this->getActiveReset($user);
        $this->sendMessage($user, $reset->getToken());
    }

    private function getActiveReset(User $user): PasswordReset
    {
        $lastReset = $this->repository->findOneBy(['user' => $user, 'isActive' => true]);
        if ($this->isExpired($lastReset)) {
            if ($lastReset !== null) {
                $lastReset->setIsActive(false);
            }
            return $this->setUpReset($user);
        }

        return $lastReset;
    }

    private function isExpired(?PasswordReset $reset): bool
    {
        if ($reset === null) {
            return true;
        }

        $now = new \DateTime(date('Y-m-d H:i', time()));
        $expirationDate = $reset->getExpirationDate();
        if ($now > $expirationDate) {
            return true;
        }
        return false;
    }

    private function setUpReset(User $user)
    {
        $now = new \DateTime(date('Y-m-d H:i', time()));
        $data = random_bytes(10) . $user->getId() . $now->getTimestamp();
        $token = rtrim(strtr(base64_encode($data ), '+/', '-_'), '=');

        $reset = new PasswordReset();
        $reset->setUser($user)
            ->setExpirationDate($now->modify('+1 day'))
            ->setToken($token)
            ->setIsActive(true);

        $this->entityManager->persist($reset);
        $this->entityManager->flush();

        return $reset;
    }

    private function sendMessage(User $user, string $token)
    {
        $messageTitle = 'Paskyros susigrąžinimas';

        $messageContent = $this->templating->render('Email/password_reset_link.html.twig', array(
            'token' => $token
        ));

        $message = $this->mailer->fetchOrCreateMessage($messageTitle, $messageContent);

        $this->mailer->sendMessageToProfile($message, $user);
        $this->mailer->sendMessageToEmail($message, $user);
    }

    /**
     * @param string $token
     * @return PasswordReset
     */
    public function findByToken(string $token): PasswordReset
    {
        return $this->repository->findOneBy(['token' => $token]);
    }
}