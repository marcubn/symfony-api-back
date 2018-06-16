<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Offer;
use Doctrine\ORM\EntityRepository;

/**
 * OfferRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class OfferRepository extends EntityRepository
{
    public function getOffers() 
    {
        $results = $this->findAll();

        $offers = [];
        foreach ($results as $result) {
            $data = [
                'id' => $result->getId(),
                'title' => $result->getTitle(),
                'description' => $result->getDescription(),
                'email' => $result->getEmail(),
                'imageUrl' => $result->getImageUrl(),
                'creationDate' => $result->getCreationDate()
            ];
            $offers[] = $data;
        }

        return $offers;
    }

    public function getOffer($id)
    {
        $offer = $this->findOneBy(['id' => $id]);

        $data = [
            'id' => $offer->getId(),
            'title' => $offer->getTitle(),
            'description' => $offer->getDescription(),
            'email' => $offer->getEmail(),
            'imageUrl' => $offer->getImageUrl(),
            'creationDate' => $offer->getCreationDate()
        ];

        return $data;
    }

    public function saveOffer($params)
    {
        $offer = new Offer();
        $offer->setTitle($params['title']);
        $offer->setDescription($params['description']);
        $offer->setEmail($params['email']);
        $offer->setImageUrl($params['imageUrl']);

        // Get the Doctrine service and manager
        $em = $this->getEntityManager();
        $em->persist($offer);
        $em->flush();
    }
}
