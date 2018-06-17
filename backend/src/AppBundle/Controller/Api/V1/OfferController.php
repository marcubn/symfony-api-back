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
     * @Route("/api/V1/offer")
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
        $response = $offerRepository->deleteOffer($id);

        return new Response($response['msg'], $response['code']);
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
        $response = $offerRepository->saveOffer($params);

        return new Response($response['msg'], $response['code']);
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
        $response = $offerRepository->updateOffer($params);

        return new Response($response['msg'], $response['code']);
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
        $response = $offerRepository->getOffer($id);

        return new Response($response['msg'], $response['code']);
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
        $response = $offerRepository->getOffers();

        return new Response($response['msg'], $response['code']);
    }
}