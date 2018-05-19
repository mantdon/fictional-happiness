<?php
namespace App\Controller;

use App\Entity\PasswordReset;
use App\Services\PasswordResetter;
use App\Services\UserManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends Controller
{
    /**
     * @Route("/login", name="login")
     * @param Request $request
     * @param AuthenticationUtils $authUtils
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function login(Request $request, AuthenticationUtils $authUtils)
    {
        $error = $authUtils->getLastAuthenticationError();

        $lastUsername = $authUtils->getLastUsername();

        return $this->render('Security/login.html.twig', array(
            'last_username' => $lastUsername,
            'error'         => $error,
        ));
    }

    /**
     * @Route("/reset", name="reset_password")
     */
    public function resetPassword(Request $request, UserManager $userManager, PasswordResetter $passwordResetter)
    {
        $email = $request->get('username');
        $error = '';
        if (isset($email)) {
            $user = $userManager->findUser($request->get('username'));
            if ($user !== null) {
                $passwordResetter->resetPassword($user);
            } else {
                $error = 'Vartotojas nerastas';
            }
        }

        return $this->render('Security/reset_password.html.twig', array(
            'username' => $email,
            'error' => $error
        ));
    }

    /**
     * @Route("reset/set/{token}", name="reset_set_new_password")
     */
    public function newPassword(PasswordReset $reset)
    {

    }
}