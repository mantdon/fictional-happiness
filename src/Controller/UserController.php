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
     * @Route("admin/users/unban/{id}", name="user_unban", methods="GET")
     */
    public function unban(Request $request, User $user){
        $this->savePreviousPaginationPage($request);
        return $this->render('Admin/Users/admin_users_unban_confirm.html.twig', ['user' => $user]);
    }

    /**
     * @Route("admin/users/unban/{id}", name="user_unban_confirm", methods="POST")
     */
    public function unbanConfirm(Request $request, User $user)
    {
        if ($this->isCsrfTokenValid('unban'.$user->getId(), $request->request->get('_token'))) {
            $user->setIsEnabled(true);
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            $this->addFlash('notice', 'User unbanned[PH]');
        }
        return $this->redirectToRoute('admin_users');
    }
    /**
     * @Route("admin/users/ban/{id}", name="user_ban", methods="GET")
     */
    public function ban(Request $request, User $user){
        dump($user->getOrders());
        $this->savePreviousPaginationPage($request);
        return $this->render('Admin/Users/admin_users_ban_confirm.html.twig', ['user' => $user]);
    }

    /**
     * @Route("admin/users/ban/{id}", name="user_ban_confirm", methods="POST")
     */
    public function banConfirm(Request $request, User $user)
    {
        if ($this->isCsrfTokenValid('ban'.$user->getId(), $request->request->get('_token'))) {
            $user->setIsEnabled(false);
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            $this->addFlash('notice', 'User banned[PH]');
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