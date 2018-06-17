<?php

namespace AppBundle\Controller;

use AppBundle\Service\ApiService;
use Exception;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use AppBundle\Entity\Offer;
use Symfony\Component\HttpFoundation\Response;

/**
 * Offer controller.
 *
 * @Route("/offer")
 */
class OfferController extends Controller
{
    /**
     * Lists all Offer entities.
     *
     * @Route("/", name="offer")
     * @Method("GET")
     */
    public function indexAction()
    {
        $results = ApiService::call();

        $offers = [];
        foreach ($results as $result) {
            $offer = new Offer();
            $offer->setId($result->id);
            $offer->setTitle($result->title);
            $offer->setDescription($result->description);
            $offer->setEmail($result->email);
            $offer->setImageUrl($result->imageUrl);
            $offer->setCreationDate($result->creationDate);
            $offers[] = $offer;
        }

        return $this->render('offer/index.html.twig', array(
            'offers' => $offers,

        ));
    }

    /**
     * Displays a form to create a new Offer entity.
     *
     * @Route("/new", name="offer_new")
     * @Method({"GET", "POST"})
     * @param Request $request
     *
     * @return RedirectResponse|Response
     */
    public function newAction(Request $request)
    {
    
        $offer = new Offer();
        $form   = $this->createForm('AppBundle\Form\OfferType', $offer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $offer->toArray();
            $results = ApiService::call("POST", $data);
            
            $editLink = $this->generateUrl('offer_edit', array('id' => $results->id));
            $this->get('session')->getFlashBag()->add('success', "<a href='$editLink'>New offer was created successfully.</a>" );
            
            $nextAction=  $request->get('submit') == 'save' ? 'offer' : 'offer_new';
            return $this->redirectToRoute($nextAction);
        }
        return $this->render('offer/new.html.twig', array(
            'offer' => $offer,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Offer entity.
     *
     * @Route("/{id}", name="offer_show")
     * @Method("GET")
     * @param Offer $offer
     *
     * @return Response
     */
    public function showAction(Offer $offer)
    {
        return $this->render('offer/show.html.twig', array(
            'offer' => $offer,
        ));
    }

    /**
     * Displays a form to edit an existing Offer entity.
     *
     * @Route("/{id}/edit", name="offer_edit")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @param Offer   $offer
     *
     * @return RedirectResponse|Response
     */
    public function editAction(Request $request, Offer $offer)
    {
        $editForm = $this->createForm('AppBundle\Form\OfferType', $offer);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $data = $offer->toArray();
            $results = ApiService::call("PUT", $data);
            
            $this->get('session')->getFlashBag()->add('success', 'Edited Successfully!');
            return $this->redirectToRoute('offer_edit', array('id' => $results->id));
        }
        return $this->render('offer/edit.html.twig', array(
            'offer' => $offer,
            'edit_form' => $editForm->createView(),
        ));
    }

    /**
     * Delete Offer by id
     *
     * @Route("/delete/{id}", name="offer_by_id_delete")
     * @Method("GET")
     * @param Offer $offer
     *
     * @return RedirectResponse
     */
    public function deleteByIdAction(Offer $offer)
    {
        $data = ["id" => $offer->getId()];
        $results = ApiService::call('DELETE', $data);
        $this->get('session')->getFlashBag()->add('success', 'The Offer was deleted successfully');

        return $this->redirect($this->generateUrl('offer'));

    }

    /**
     * Bulk Action
     * @Route("/bulk-action/", name="offer_bulk_action")
     * @Method("POST")
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function bulkAction(Request $request) {}


}
