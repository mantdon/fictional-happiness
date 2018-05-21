<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ContactController extends Controller
{
    /**
     * @Route("/about", name="about")
     * @param Request $request
     * @return RedirectResponse
     */
    public function about(Request $request): Response
    {
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $contact->setCreationDate(new \DateTime());
            $em = $this->getDoctrine()->getManager();
            $em->persist($contact);
            $em->flush();
            $this->addFlash('notice', 'Form submitted[PH]');
            unset($contact);
            unset($form);
            $contact = new Contact();
            $form = $this->createForm(ContactType::class, $contact);
        }
        return $this->render('about.html.twig',
            array('form' => $form->createView()));
    }
}