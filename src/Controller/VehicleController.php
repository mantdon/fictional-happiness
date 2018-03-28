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
			$this->addFlash('notice', 'Vehicle added[PH]');
			return $this->redirectToRoute( 'user_home' );
		}

		return $this->render('Profile/Vehicles/profile_vehicles_add.html.twig', array(
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
		if($vehicle !== NULL && $vehicle->getUser() === $this->getUser()) {
			$form = $this->createForm( VehicleType::class, $vehicle );

			$form->handleRequest( $request );

			if( $form->isSubmitted() && $form->isValid() && $vehicle->getUser() === $this->getUser() ) {
				$entityManager = $this->getDoctrine()->getManager();
				$entityManager->persist( $vehicle );
				$entityManager->flush();
				$this->addFlash('notice', 'Vehicle updated[PH]');

				return $this->redirectToRoute( 'user_vehicles' );
			}

			return $this->render( 'Profile/Vehicles/profile_vehicles_edit.html.twig',
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
	 * @Route("user/vehicles/delete/{id}", name="show_vehicle", methods="GET")
	 */
	public function show(Request $request, Vehicle $vehicle): Response
	{
		if($vehicle !== NULL && $vehicle->getUser() === $this->getUser()) {
			return $this->render( 'Profile/Vehicles/profile_vehicles_delete_confirm.html.twig',
			                      array('vehicle' => $vehicle,
					                    'user' => $this->getUser(),
					                    'vehicles' => $this->getUser()->getVehicles())
			);
		}

		return $this->redirectToRoute('user_vehicles');
	}

	/**
	 * @Route("user/vehicles/delete/{id}", name="delete_vehicle", methods="DELETE")
	 */
	public function delete(Request $request, Vehicle $vehicle): Response
	{
		if ($this->isCsrfTokenValid('delete'.$vehicle->getId(), $request->request->get('_token')) &&
			$vehicle->getUser() === $this->getUser()) {
			$em = $this->getDoctrine()->getManager();
			$em->remove($vehicle);
			$em->flush();
			$this->addFlash('notice', 'Vehicle deleted[PH]');
		}

		return $this->redirectToRoute('user_vehicles');
	}
}