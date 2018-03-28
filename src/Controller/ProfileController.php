<?php

namespace App\Controller;

use App\Entity\Vehicle;
use App\Form\VehicleType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

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
	public function settingsTabAction(){
		return $this->render('Profile/Settings/profile_settings.html.twig', array(
			'user' => $this->getUser()
		));
	}
	/**
	 * @Route("/user/changepassword", name="user_changepassword")
	 */
	public function changepasswordTabAction(){
		return $this->render('Profile/ChangePassword/profile_changepassword.html.twig', array(
			'user' => $this->getUser()
		));
	}
}