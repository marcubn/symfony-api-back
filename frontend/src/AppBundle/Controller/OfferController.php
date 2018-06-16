<?php

namespace AppBundle\Controller;

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
        $url = 'http://127.0.0.1:8000/api/V1/offer';
        $options = array(
            'http' => array(
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'GET',
                'content' => "",
            ),
        );
        $context  = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        $results = json_decode($result);

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

            $savedData = $request->request->all();
            $data = $savedData['offer'];
            $url = 'http://127.0.0.1:8000/api/V1/offer';

            $options = array(
                'http' => array(
                    'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                    'method'  => 'POST',
                    'content' => json_encode($data),
                ),
            );
            $context  = stream_context_create($options);
            $result = file_get_contents($url, false, $context);
            $results = json_decode($result);
            
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
            $em = $this->getDoctrine()->getManager();
            $em->persist($offer);
            $em->flush();
            
            $this->get('session')->getFlashBag()->add('success', 'Edited Successfully!');
            return $this->redirectToRoute('offer_edit', array('id' => $offer->getId()));
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
        $url = 'http://127.0.0.1:8000/api/V1/offer/delete';

        $options = array(
            'http' => array(
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'DELETE',
                'content' => json_encode(["id" => $offer->getId()]),
            ),
        );
        $context  = stream_context_create($options);
        file_get_contents($url, false, $context);
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
    public function bulkAction(Request $request)
    {
        $ids = $request->get("ids", array());
        $action = $request->get("bulk_action", "delete");

        if ($action == "delete") {
            try {
                $em = $this->getDoctrine()->getManager();
                $repository = $em->getRepository('AppBundle:Offer');

                foreach ($ids as $id) {
                    $offer = $repository->find($id);
                    $em->remove($offer);
                    $em->flush();
                }

                $this->get('session')->getFlashBag()->add('success', 'offers was deleted successfully!');

            } catch (Exception $ex) {
                $this->get('session')->getFlashBag()->add('error', 'Problem with deletion of the offers ');
            }
        }

        return $this->redirect($this->generateUrl('offer'));
    }


}
