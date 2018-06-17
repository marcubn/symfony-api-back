<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Offer;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpFoundation\Response;

/**
 * OfferRepository
 */
class OfferRepository extends EntityRepository
{

    /**
     * Returns a list with all offers from db
     * Filters can be added for further processing
     *
     * @return array
     */
    public function getOffers() 
    {
        $results = $this->findAll();

        $offers = [];
        foreach ($results as $result) {
            /** @var Offer  $result */
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

        return [
            "msg" => json_encode($offers),
            'code' => Response::HTTP_OK
        ];
    }

    /**
     * Get one offer based on an offer id
     *
     * @param $id
     *
     * @return array
     */
    public function getOffer($id)
    {
        $offer = $this->findOneBy(['id' => $id]);
        if(!$offer) {
            return [
                "msg" => 'Id requested was not found',
                'code' => Response::HTTP_NOT_FOUND
            ];
        }

        $data = [
            'id' => $offer->getId(),
            'title' => $offer->getTitle(),
            'description' => $offer->getDescription(),
            'email' => $offer->getEmail(),
            'imageUrl' => $offer->getImageUrl(),
            'creationDate' => $offer->getCreationDate()
        ];

        return [
            "msg" => json_encode($data),
            'code' => Response::HTTP_OK
        ];
    }

    /**
     * Saves a new offed to db
     *
     * @param $params
     *
     * @return int
     */
    public function saveOffer($params)
    {
        $offer = new Offer();

        if (!isset($params['title']) || !isset($params['description']) || !isset($params['email']) || !isset($params['imageUrl'])) {
            return [
                "msg" => 'Data must not be empty',
                'code' => Response::HTTP_BAD_REQUEST
            ];
        }
        $offer->setTitle($params['title']);
        $offer->setDescription($params['description']);
        $offer->setEmail($params['email']);
        $offer->setImageUrl($params['imageUrl']);

        $exists = $this->findOneBy(['email' => $params['email']]);
        if ($exists) {
            return [
                "msg" => 'An offer already exists for this email',
                'code' => Response::HTTP_ALREADY_REPORTED
            ];
        }
        
        $em = $this->getEntityManager();
        $em->persist($offer);
        $em->flush();

        return [
            "msg" => json_encode(["id" => $offer->getId()]),
            'code' => Response::HTTP_OK
        ];
    }

    /**
     * Updates an existing offer with new data
     *
     * @param $params
     *
     * @return mixed
     */
    public function updateOffer($params)
    {
        if (!isset($params['id']) || !isset($params['title']) || !isset($params['description']) || !isset($params['email']) || !isset($params['imageUrl'])) {
            return [
                "msg" => 'Data must not be empty',
                'code' => Response::HTTP_BAD_REQUEST
            ];
        }
        $id = $params['id'];
        $offer = $this->findOneBy(['id' => $id]);

        if(!$offer) {
            return [
                "msg" => 'Id requested was not found',
                'code' => Response::HTTP_NOT_FOUND
            ];
        }
        $offer->setTitle($params['title']);
        $offer->setDescription($params['description']);
        $offer->setEmail($params['email']);
        $offer->setImageUrl($params['imageUrl']);

        $exists = $this->findOneBy(['email' => $params['email']]);
        if ($exists && $exists->getId() != $id) {
            return [
                "msg" => 'An offer already exists for this email',
                'code' => Response::HTTP_ALREADY_REPORTED
            ];
        }

        $em = $this->getEntityManager();
        $em->persist($offer);
        $em->flush();

        return [
            "msg" => json_encode(["id" => $offer->getId()]),
            'code' => Response::HTTP_OK
        ];
    }

    /**
     * Deletes an offer based on an offer id
     *
     * @param $id
     *
     * @return array
     */
    public function deleteOffer($id)
    {
        $offer = $this->findOneBy(['id' => $id]);
        if(!$offer) {
            return [
                "msg" => 'Id requested was not found',
                'code' => Response::HTTP_NOT_FOUND
            ];
        }

        $em = $this->getEntityManager();
        $em->remove($offer);
        $em->flush();

        return [
            "msg" => json_encode(["id" => $id]),
            'code' => Response::HTTP_OK
        ];
    }
}
