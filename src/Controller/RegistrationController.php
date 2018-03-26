<?php

namespace App\Controller;

use App\Form\RegistrationType;
use App\Entity\User;
use App\Services\RegistrationHandler;
use App\Services\UserManager;
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

        if($registrationHandler->handleForm($form, $request)) {
	        $token = UserManager::createAuthenticationTokenFor($user);

	        // Logs the user in
	        // Remember me functionality is not present.
	        $this->get('security.token_storage')->setToken($token);
	        $this->get('session')->set('_security_main', serialize($token));

	        return $this->redirectToRoute( 'homepage' );
        }

        return $this->render(
            'registration.html.twig',
            array('form' => $form->createView())
        );
    }
}