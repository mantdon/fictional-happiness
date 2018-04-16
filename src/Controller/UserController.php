<?php

namespace App\Controller;

use App\Entity\User;
use App\Helpers\PreviousPageExtractor;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class UserController extends Controller
{
	/**
	 * @Route("/user", name="user_home")
	 */
    public function showProfile()
    {
	    return $this->redirectToRoute('user_vehicles');
    }

	/**
	 * @Route("admin/users/delete/{id}", name="user_show", methods="GET")
	 */
    public function show(Request $request, User $user){
	    $this->savePreviousPaginationPage($request);
	    return $this->render('Admin/Users/admin_users_delete_confirm.html.twig', ['user' => $user]);
    }

	/**
	 * @Route("admin/users/delete/{id}", name="user_delete", methods="DELETE")
	 */
	public function delete(Request $request, User $user)
	{
		if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
			$em = $this->getDoctrine()->getManager();
			$em->remove($user);
			$em->flush();
			$this->addFlash('notice', 'User removed[PH]');
		}

		return $this->redirectToRoute('admin_users');
	}

	private function savePreviousPaginationPage(Request $request){
		if(!$this->get('session')->has('previous_page')) {
			$previousPageURL = $request->headers->get( 'referer' );
			$previousPage = PreviousPageExtractor::getPreviousPage( $previousPageURL, 'users' );

			$this->get( 'session' )->set( 'previous_page', $previousPage );
		}
	}
}