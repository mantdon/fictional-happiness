<?php

namespace App\Services;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegistrationHandler
{

    private $passwordEncoder;
    private $em;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder,
                                EntityManagerInterface $em)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->em = $em;
    }

    public function handleForm(FormInterface $form, Request $request)
    {

        $form->handleRequest($request);

        if(!$form->isSubmitted() || !$form->isValid())
            return false;

        $user = $form->getData();
        $this->createUser($user);

        return true;
    }

    private function createUser(User $user)
    {
        $password = $this->passwordEncoder->encodePassword($user, $user->getPlainPassword());

        $user->setPassword($password);

        $this->em->persist($user);
        $this->em->flush();
    }
}