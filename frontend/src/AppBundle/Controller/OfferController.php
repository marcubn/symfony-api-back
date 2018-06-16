<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\View\TwitterBootstrap3View;

use AppBundle\Entity\Offer;

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
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $queryBuilder = $em->getRepository('AppBundle:Offer')->createQueryBuilder('e');

        list($filterForm, $queryBuilder) = $this->filter($queryBuilder, $request);
        list($offers, $pagerHtml) = $this->paginator($queryBuilder, $request);
        
        $totalOfRecordsString = $this->getTotalOfRecordsString($queryBuilder, $request);

        $url = 'http://127.0.0.1:8000/api/V1/offer';
        $data = array('key1' => 'value1', 'key2' => 'value2');
// use key 'http' even if you send the request to https://...
        $options = array(
            'http' => array(
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'GET',
                'content' => http_build_query($data),
            ),
        );
        $context  = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        $results = json_decode($result);

        //new \ArrayIterator()
        $offers = [];

        foreach ($results as $result) {
            //dump($result);exit;
            $offer = new Offer();
            $offer->setId($result->id);
            $offer->setTitle($result->title);
            $offer->setDescription($result->description);
            $offer->setEmail($result->email);
            $offer->setImageUrl($result->imageUrl);
            $offer->setCreationDate($result->creationDate);
            $offers[] = $offer;
        }

        dump($offers);exit;

        return $this->render('offer/index.html.twig', array(
            'offers' => $results,
            'pagerHtml' => $pagerHtml,
            'filterForm' => $filterForm->createView(),
            'totalOfRecordsString' => $totalOfRecordsString,

        ));
    }

    /**
    * Create filter form and process filter request.
    *
    */
    protected function filter($queryBuilder, Request $request)
    {
        $session = $request->getSession();
        $filterForm = $this->createForm('AppBundle\Form\OfferFilterType');

        // Reset filter
        if ($request->get('filter_action') == 'reset') {
            $session->remove('OfferControllerFilter');
        }

        // Filter action
        if ($request->get('filter_action') == 'filter') {
            // Bind values from the request
            $filterForm->handleRequest($request);

            if ($filterForm->isValid()) {
                // Build the query from the given form object
                $this->get('lexik_form_filter.query_builder_updater')->addFilterConditions($filterForm, $queryBuilder);
                // Save filter to session
                $filterData = $filterForm->getData();
                $session->set('OfferControllerFilter', $filterData);
            }
        } else {
            // Get filter from session
            if ($session->has('OfferControllerFilter')) {
                $filterData = $session->get('OfferControllerFilter');
                
                foreach ($filterData as $key => $filter) { //fix for entityFilterType that is loaded from session
                    if (is_object($filter)) {
                        $filterData[$key] = $queryBuilder->getEntityManager()->merge($filter);
                    }
                }
                
                $filterForm = $this->createForm('AppBundle\Form\OfferFilterType', $filterData);
                $this->get('lexik_form_filter.query_builder_updater')->addFilterConditions($filterForm, $queryBuilder);
            }
        }

        return array($filterForm, $queryBuilder);
    }


    /**
    * Get results from paginator and get paginator view.
    *
    */
    protected function paginator($queryBuilder, Request $request)
    {
        //sorting
        $sortCol = $queryBuilder->getRootAlias().'.'.$request->get('pcg_sort_col', 'id');
        $queryBuilder->orderBy($sortCol, $request->get('pcg_sort_order', 'desc'));
        // Paginator
        $adapter = new DoctrineORMAdapter($queryBuilder);
        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setMaxPerPage($request->get('pcg_show' , 10));

        try {
            $pagerfanta->setCurrentPage($request->get('pcg_page', 1));
        } catch (\Pagerfanta\Exception\OutOfRangeCurrentPageException $ex) {
            $pagerfanta->setCurrentPage(1);
        }
        
        $entities = $pagerfanta->getCurrentPageResults();

        // Paginator - route generator
        $me = $this;
        $routeGenerator = function($page) use ($me, $request)
        {
            $requestParams = $request->query->all();
            $requestParams['pcg_page'] = $page;
            return $me->generateUrl('offer', $requestParams);
        };

        // Paginator - view
        $view = new TwitterBootstrap3View();
        $pagerHtml = $view->render($pagerfanta, $routeGenerator, array(
            'proximity' => 3,
            'prev_message' => 'previous',
            'next_message' => 'next',
        ));

        return array($entities, $pagerHtml);
    }
    
    
    
    /*
     * Calculates the total of records string
     */
    protected function getTotalOfRecordsString($queryBuilder, $request) {
        $totalOfRecords = $queryBuilder->select('COUNT(e.id)')->getQuery()->getSingleScalarResult();
        $show = $request->get('pcg_show', 10);
        $page = $request->get('pcg_page', 1);

        $startRecord = ($show * ($page - 1)) + 1;
        $endRecord = $show * $page;

        if ($endRecord > $totalOfRecords) {
            $endRecord = $totalOfRecords;
        }
        return "Showing $startRecord - $endRecord of $totalOfRecords Records.";
    }
    
    

    /**
     * Displays a form to create a new Offer entity.
     *
     * @Route("/new", name="offer_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
    
        $offer = new Offer();
        $form   = $this->createForm('AppBundle\Form\OfferType', $offer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($offer);
            $em->flush();
            
            $editLink = $this->generateUrl('offer_edit', array('id' => $offer->getId()));
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
     */
    public function showAction(Offer $offer)
    {
        $deleteForm = $this->createDeleteForm($offer);
        return $this->render('offer/show.html.twig', array(
            'offer' => $offer,
            'delete_form' => $deleteForm->createView(),
        ));
    }
    
    

    /**
     * Displays a form to edit an existing Offer entity.
     *
     * @Route("/{id}/edit", name="offer_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Offer $offer)
    {
        $deleteForm = $this->createDeleteForm($offer);
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
            'delete_form' => $deleteForm->createView(),
        ));
    }
    
    

    /**
     * Deletes a Offer entity.
     *
     * @Route("/{id}", name="offer_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Offer $offer)
    {
    
        $form = $this->createDeleteForm($offer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($offer);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'The Offer was deleted successfully');
        } else {
            $this->get('session')->getFlashBag()->add('error', 'Problem with deletion of the Offer');
        }
        
        return $this->redirectToRoute('offer');
    }
    
    /**
     * Creates a form to delete a Offer entity.
     *
     * @param Offer $offer The Offer entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Offer $offer)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('offer_delete', array('id' => $offer->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
    
    /**
     * Delete Offer by id
     *
     * @Route("/delete/{id}", name="offer_by_id_delete")
     * @Method("GET")
     */
    public function deleteByIdAction(Offer $offer){
        $em = $this->getDoctrine()->getManager();
        
        try {
            $em->remove($offer);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'The Offer was deleted successfully');
        } catch (Exception $ex) {
            $this->get('session')->getFlashBag()->add('error', 'Problem with deletion of the Offer');
        }

        return $this->redirect($this->generateUrl('offer'));

    }
    

    /**
    * Bulk Action
    * @Route("/bulk-action/", name="offer_bulk_action")
    * @Method("POST")
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
