<?php

namespace App\Controller;

use App\Entity\Message;
use App\Entity\MessageMetaData;
use App\Services\MessageManager;
use App\Helpers\PreviousPageExtractor;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class MessageController extends Controller
{
	/**
	 * @Route("user/messages/{id}", name="show_message", requirements={"id"="\d+"}, methods="GET")
	 */
	public function show(Request $request, MessageMetaData $messageMetaData, MessageManager $mm): Response
	{
		$this->savePreviousPaginationPage($request);
		if($messageMetaData !== NULL && $mm->messageMeantForUser($messageMetaData, $this->getUser()))
		{
			$mm->markMessageAsReadIfUnread($messageMetaData);
			return $this->render( 'Profile/Messages/profile_message_show.html.twig',
			                      array('messageData' => $messageMetaData,
				                        'user' => $this->getUser(),
			                      )
			);
		}

		return $this->redirectToRoute('user_messages');
	}

	/**
	 * @Route("user/messages/delete/{id}", name="delete_message", methods="DELETE")
	 */
	public function delete(Request $request, MessageMetaData $messageMetaData, MessageManager $mm): Response
	{
		if($this->isCsrfTokenValid('delete'.$messageMetaData->getId(), $request->request->get('_token')) &&
			$mm->messageMeantForUser($messageMetaData, $this->getUser()))
		{
			$mm->markMessageAsDeletedIfNotDeleted($messageMetaData);
			$this->addFlash('notice', 'Message deleted[PH]');
		}

		return $this->redirectToRoute('user_messages');
	}

	private function savePreviousPaginationPage(Request $request){
		if(!$this->get('session')->has('previous_page')) {
			$previousPageURL = $request->headers->get( 'referer' );
			$previousPage = PreviousPageExtractor::getPreviousPage( $previousPageURL, 'page' );

			$this->get( 'session' )->set( 'previous_page', $previousPage );
		}
	}
}