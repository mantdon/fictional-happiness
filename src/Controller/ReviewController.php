<?php

namespace App\Controller;

use App\Entity\Review;
use App\Entity\User;
use App\Form\ReviewType;
use App\Services\PaginationHandler;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ReviewController extends Controller
{
    /**
     * @Route("/reviews/{page}", name="reviews", requirements={"page"="\d+"}, defaults={"page"=1}, methods="GET")
     * @param PaginationHandler $paginationHandler
     * @param                   $page
     * @return Response
     * @throws \BadMethodCallException
     */
    public function reviewsPageAction(PaginationHandler $paginationHandler, $page): Response
    {
        $paginationHandler->setQuery('App:Review', 'getAll')
            ->setPage($page)
            ->setItemLimit(5)
            ->paginate();

        return $this->render('Review/reviews.html.twig',
            ['user' => $this->getUser(),
                'reviews' => $paginationHandler->getResult(),
                'pageCount' => $paginationHandler->getPageCount(),
                'currentPage' => $paginationHandler->getCurrentPage(),
                'pageParameterName' => 'page',
                'route' => 'reviews']);
    }

    /**
     * @Route("/reviews/add", name="review_add")
     * @param Request $request
     * @return RedirectResponse
     */
    public function addReview(Request $request): Response
    {
        $user = $this->getUser();
        $review = new Review();
        $form = $this->createForm(ReviewType::class, $review);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid() && $user != null) {
            $review->setCreationDate(new \DateTime());
            $review->setUser($user);
            $em = $this->getDoctrine()->getManager();
            $em->persist($review);
            $em->flush();
            $this->addFlash('notice', 'Review added[PH]');
            return $this->redirectToRoute('reviews');
        }
        return $this->render('Review/review_write.html.twig',
            array('form' => $form->createView()));
    }

    /**
     * @Route("/reviews/edit/{id}", defaults={"id"=null}, name="review_edit")
     * @param Request $request
     * @param Review|null $review
     * @return RedirectResponse
     */
    public function editReview(Request $request,  Review $review = NULL): Response
    {

        if($review !== NULL && $review->getUser() === $this->getUser()) {
            $newReview = new Review();
            $form = $this->createForm( ReviewType::class, $newReview );
            $form->handleRequest( $request );
            if($form->isSubmitted() && $form->isValid()) {
                $review->setRating($newReview->getRating());
                $review->setContent($newReview->getContent());
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist( $review );
                $entityManager->flush();
                $this->addFlash('notice', 'Review updated[PH]');
                return $this->redirectToRoute( 'reviews' );
            }
            $newReview->setContent($review->getContent());
            $newReview->setRating($review->getRating());
            $form = $this->createForm( ReviewType::class, $newReview );
            return $this->render('Review/review_write.html.twig',
                array('form' => $form->createView()));
        }
        return $this->redirectToRoute( 'reviews' );
    }

    /**
     * @Route("/reviews/delete/{id}", name="review_delete", methods="GET")
     * @param Review $review
     * @return Response
     * @throws \LogicException
     */
    public function reviewDeleteAction(Review $review): Response
    {
        if ($this->getUser() != null && ($review->getUser() === $this->getUser() || $this->checkIfAdmin())){
            return $this->render('Review/review_delete.html.twig',
                array('review' => $review));
        }
        return $this->redirectToRoute('reviews');
    }
    /**
     * @Route("/reviews/delete/{id}", name="review_delete_confirm", methods="DELETE")
     * @param Request $request
     * @param Review    $review
     * @return RedirectResponse
     * @throws \LogicException
     */
    public function reviewDeleteConfirmAction(Request $request, Review $review): RedirectResponse
    {
        if ($review->getUser() === $this->getUser() || $this->checkIfAdmin() &&
            $this->isCsrfTokenValid('delete'.$review->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($review);
            $em->flush();
            $this->addFlash('notice', 'Review deleted[PH]');
        }
        return $this->redirectToRoute('reviews');
    }

    public function checkIfAdmin() {
        foreach($this->getUser()->getRoles() as $role) {
            if($role === "ROLE_ADMIN")
                return true;
        }
        return false;
    }
}