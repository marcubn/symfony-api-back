<?php


namespace AppBundle\Controller\Api\V1;

use AppBundle\Entity\Offer;
use AppBundle\Repository\OfferRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class OfferController extends Controller
{

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
        $offerRepository->saveOffer($params);

        return new Response('Its probably been saved', 201);
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