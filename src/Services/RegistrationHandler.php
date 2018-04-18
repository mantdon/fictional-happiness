<?php

namespace App\Services;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegistrationHandler
{
    private $userManager;

    public function __construct(UserManager $userManager)
    {
        $this->userManager = $userManager;
    }

    public function handleForm(FormInterface $form, Request $request)
    {

        $form->handleRequest($request);

        if(!$form->isSubmitted() || !$form->isValid())
            return false;

        $user = $form->getData();
        $user->setRole('ROLE_USER');
        $user->setRegistrationDate(new \DateTime(date('Y/m/d H:i:s')));
        $this->userManager->createUser($user);

        return true;
    }


}