<?php


namespace AppBundle\Controller\Api\V1;

use AppBundle\Repository\OfferRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class OfferController extends Controller
{

    /**
     * @Route("/api/V1/offer/delete")
     * @Method("DELETE")
     * @param Request $request
     *
     * @return Response
     * @internal param $id
     *
     */
    public function deleteAction(Request $request)
    {
        $params = json_decode($request->getContent(), true);
        $id = $params['id'];
        /** @var OfferRepository $offerRepository */
        $offerRepository = $this->getDoctrine()->getRepository('AppBundle:Offer');
        $offerRepository->deleteOffer($id);


        return new Response("Offer was deleted", 200);
    }


    /**
     * @Route("/api/V1/offer")
     * @Method("POST")
     * @param Request $request
     * @return Response
     */
    public function addAction(Request $request)
    {
        $params = json_decode($request->getContent(), true);

        /** @var OfferRepository $offerRepository */
        $offerRepository = $this->getDoctrine()->getRepository('AppBundle:Offer');
        $id = $offerRepository->saveOffer($params);

        return new Response(json_encode(["id" => $id]), 201);
    }


    /**
     * @Route("/api/V1/offer")
     * @Method("PUT")
     * @param Request $request
     * @return Response
     */
    public function editAction(Request $request)
    {
        $params = json_decode($request->getContent(), true);

        /** @var OfferRepository $offerRepository */
        $offerRepository = $this->getDoctrine()->getRepository('AppBundle:Offer');
        $id = $offerRepository->updateOffer($params);

        return new Response(json_encode(["id" => $id]), 201);
    }

    /**
     * @Route("/api/V1/offer/{id}")
     * @Method("GET")
     * @param $id
     * @return Response
     */
    public function getOneAction($id)
    {
        /** @var OfferRepository $offerRepository */
        $offerRepository = $this->getDoctrine()->getRepository('AppBundle:Offer');
        $offer = $offerRepository->getOffer($id);

        return new Response(json_encode($offer));
    }

    /**
     * @Route("/api/V1/offer")
     * @Method("GET")
     * @return Response
     */
    public function getAllAction()
    {
        /** @var OfferRepository $offerRepository */
        $offerRepository = $this->getDoctrine()->getRepository('AppBundle:Offer');
        $offers = $offerRepository->getOffers();

        return new Response(json_encode($offers));
    }
}