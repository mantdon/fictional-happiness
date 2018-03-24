<?php

namespace App\Controller;

use App\Entity\Vehicle;
use App\Form\VehicleType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class VehicleController extends Controller
{
	/**
	 * @Route("/user/vehicles/add", name="add_vehicle")
	 */
	public function vehiclesAdd(Request $request){
		$vehicle = new Vehicle();
		$form = $this->createForm( VehicleType::class, $vehicle);

		$form->handleRequest($request);

		if($form->isSubmitted() && $form->isValid()) {
			$entityManager = $this->getDoctrine()->getManager();
			$vehicle->setUser($this->getUser());
			$entityManager->persist( $vehicle );
			$entityManager->flush();

			return $this->redirectToRoute( 'user_home' );
		}

		return $this->render('Profile/profile_vehicles_add.html.twig', array(
			'user' => $this->getUser(),
			'vehicles' => $this->getUser()->getVehicles(),
			'form' => $form->createView()
		));
	}

	/**
	 * @Route("/user/vehicles/edit/{id}", defaults={"id"=null}, name="edit_vehicle")
	 */
	public function vehiclesEdit(Request $request, Vehicle $vehicle = null)
	{
		if($vehicle !== NULL) {
			$form = $this->createForm( VehicleType::class, $vehicle );

			$form->handleRequest( $request );

			if( $form->isSubmitted() && $form->isValid() && $vehicle->getUser() === $this->getUser() ) {
				$entityManager = $this->getDoctrine()->getManager();
				$entityManager->persist( $vehicle );
				$entityManager->flush();

				return $this->redirectToRoute( 'user_vehicles' );
			}

			return $this->render( 'Profile/profile_vehicles_edit.html.twig',
				array(
		           'user' => $this->getUser(),
		           'vehicles' => $this->getUser()->getVehicles(),
		           'form' => $form->createView()
				)
			);
		}
		return $this->redirectToRoute('user_vehicles');
	}

	/**
	 * @Route("/user/vehicles/remove/{id}", defaults={"id"=null}, name="remove_vehicle")
	 */
	public function vehiclesRemove(Request $request, Vehicle $vehicle = NULL){
		// Seems to not allow deletion of other user's vehicles... for now
		if($vehicle !== NULL && $vehicle->getUser() === $this->getUser()) {
			$entityManager = $this->getDoctrine()->getManager();
			$entityManager->remove( $vehicle );
			$entityManager->flush();
		}

		return $this->redirectToRoute('user_vehicles');
	}
}