<?php

namespace App\Controller;

use App\Entity\Service;
use App\Form\ServiceType;
use App\Repository\ServiceRepository;
use App\Services\PaginatedListFetcher;
use App\Helpers\PreviousPageExtractor;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Services\ServiceFetcher;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ServiceController extends Controller
{
	private $pageParameterName = "page";
	private $limitParameterName = "limit";

    /**
     * @Route("/services/{page}", name="service_index", requirements={"page"="\d+"}, defaults={"page"=1}, methods="GET")
     */
    public function index(Request $request, PaginatedListFetcher $listFetcher, $page): Response
    {
	    $list = $listFetcher->getPaginatedList('App:Service', $page);

	    // Load first page on invalid page entry.
	    // 'totalCount' check is needed to avoid redirection loops on empty list.
	    if($list['totalCount'] !== 0 && $page > $list['pageCount'] || $page < 1)
		    return $this->redirectToRoute('service_index', array($this->pageParameterName => 1));

	    return $this->render(
		    'Service/index.html.twig',
		    array('services' => $list['items'],
			    'pageCount' => $list['pageCount'],
			    'currentPage' => $page,
			    'pageParameterName' => $this->pageParameterName,
			    'route' => "service_index")
	    );
    }

    /**
     * @Route("admin/services/new", name="service_new", methods="GET|POST")
     */
    public function new(Request $request): Response
    {
        $service = new Service();
        $form = $this->createForm(ServiceType::class, $service);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($service);
            $em->flush();
			$this->addFlash('notice', 'Service added[PH]');

            return $this->redirectToRoute('admin_services');
        }

        return $this->render('Admin/Services/admin_services_new.html.twig', [
            'service' => $service,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("admin/services/delete/{id}", name="service_show", methods="GET")
     */
    public function show(Request $request, Service $service): Response
    {
    	$this->savePreviousPaginationPage($request);
        return $this->render('Admin/services/admin_services_delete_confirm.html.twig', ['service' => $service]);
    }

    /**
     * @Route("admin/services/{id}/edit", name="service_edit", methods="GET|POST")
     */
    public function edit(Request $request, Service $service): Response
    {
    	$this->savePreviousPaginationPage($request);

        $form = $this->createForm(ServiceType::class, $service);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('notice', 'Service updated[PH]');
            return $this->redirectToRoute('admin_services');
        }

        return $this->render('Admin/Services/admin_services_edit.html.twig', [
            'service' => $service,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("admin/services/delete/{id}", name="service_delete", methods="DELETE")
     */
    public function delete(Request $request, Service $service): Response
    {
        if ($this->isCsrfTokenValid('delete'.$service->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($service);
            $em->flush();
            $this->addFlash('notice', 'Service removed[PH]');
        }

        return $this->redirectToRoute('admin_services');
    }

    private function savePreviousPaginationPage(Request $request){
	    if(!$this->get('session')->has('previous_page')) {
		    $previousPageURL = $request->headers->get( 'referer' );
		    $previousPage = PreviousPageExtractor::getPreviousPage( $previousPageURL, 'services' );

		    $this->get( 'session' )->set( 'previous_page', $previousPage );
	    }
    }

    /**
     * Testing version
     * @Route("/services/search")
     */
    public function searchServices(ServiceFetcher $serviceFetcher, Request $request)
    {
        $content = json_decode($request->getContent(), true);

        $services = $serviceFetcher->findByPattern($content['pattern']);
        return new JsonResponse($services);
    }
}
