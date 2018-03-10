<?php

namespace App\Controller;

use App\Form\RegistrationType;
use App\Entity\User;
use App\Services\RegistrationHandler;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegistrationController extends Controller
{
    /**
     * @Route("/register", name="user_registration")
     */
    public function register(Request $request,
                             RegistrationHandler $registrationHandler)
    {

        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);

        if($registrationHandler->handleForm($form, $request))
            return $this->redirectToRoute('homepage');

        return $this->render(
            'registration.html.twig',
            array('form' => $form->createView())
        );
    }
}