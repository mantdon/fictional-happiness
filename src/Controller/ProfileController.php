<?php

namespace App\Controller;

use App\Entity\ChangePassword;
use App\Entity\EditUser;
use App\Entity\User;
use App\Entity\Vehicle;
use App\Form\ChangePasswordType;
use App\Form\EditUserType;
use App\Form\RegistrationType;
use App\Form\VehicleType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ProfileController extends Controller
{
	/**
	 * @Route("/user/orders", name="user_orders")
	 */
	public function ordersTabActions(){
		return $this->render('Profile/Orders/profile_orders.html.twig', array(
			'user' => $this->getUser(),
			//'orders' => $this->getUser()->getOrders()
		));
	}

	/**
	 * @Route("/user/vehicles", name="user_vehicles")
	 */
	public function vehiclesTabAction(){
		return $this->render('Profile/Vehicles/profile_vehicles.html.twig', array(
			'user' => $this->getUser(),
			'vehicles' => $this->getUser()->getVehicles()
		));
	}

	/**
	 * @Route("/user/messages", name="user_messages")
	 */
	public function messagesTabAction(){
		return $this->render('Profile/Messages/profile_messages.html.twig', array(
			'user' => $this->getUser()
		));
	}

	/**
	 * @Route("/user/settings", name="user_settings")
	 */
	public function settingsTabAction(Request $request){
	    $user = $this->getUser();
        $form = $this->createForm(EditUserType::class, $user);
        $form->handleRequest($request);
        if( $form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash('notice', 'User updated[PH]');
        }

		return $this->render('Profile/Settings/profile_settings.html.twig', array(
			'user' => $user,
            'form' => $form->createView()
		));
	}
	/**
	 * @Route("/user/changepassword", name="user_changepassword")
	 */
	public function changepasswordTabAction(Request $request, UserPasswordEncoderInterface $passwordEncoder){
	    $user = $this->getUser();
	    $changePasswordModel = new ChangePassword();
        $form = $this->createForm(ChangePasswordType::class, $changePasswordModel);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $password = $passwordEncoder->encodePassword($this->getUser(), $changePasswordModel->getNewPassword());
            $user->setPassword($password);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash('notice', 'Password changed[PH]');
        }

		return $this->render('Profile/ChangePassword/profile_changepassword.html.twig', array(
			'user' => $this->getUser(),
            'form' => $form->createView()
		));
	}
}