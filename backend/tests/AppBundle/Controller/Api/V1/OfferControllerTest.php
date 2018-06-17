<?php

namespace tests\Controller\Api\V1;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class OfferControllerTest extends WebTestCase
{
    public function testGetAllAction()
    {
        $client = static::createClient();
        $client->request('GET', 'http://127.0.0.1:8000/api/V1/offer');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        //$this->assertContains('Welcome to Symfony', $crawler->filter('#container h1')->text());
    }

    public function testGetOneActionInvalidId()
    {
        $client = static::createClient();
        $client->request('GET', 'http://127.0.0.1:8000/api/V1/offer/0');
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
        $this->assertEquals("Id requested was not found", $client->getResponse()->getContent());
    }

    public function testDeleteActionIncorrectId()
    {
        $params = [
            "id" => "11111",
            "description" => "offer test",
            "email" => "marcus.bogdannicolae@gmail.com",
            "imageUrl" => "https://r.hswstatic.com/w_907/gif/tesla-cat.jpg"
        ];

        $client = static::createClient();
        $client->request(
            'DELETE',
            'http://127.0.0.1:8000/api/V1/offer',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($params)
        );

        $this->assertEquals(404, $client->getResponse()->getStatusCode());
        $this->assertEquals("Id requested was not found", $client->getResponse()->getContent());

    }

    public function testEditActionIncorrectId()
    {
        $params = [
            "id" => "11111",
            "title" => "test",
            "description" => "offer test",
            "email" => "marcus.bogdannicolae@gmail.com",
            "imageUrl" => "https://r.hswstatic.com/w_907/gif/tesla-cat.jpg"
        ];

        $client = static::createClient();
        $client->request(
            'PUT',
            'http://127.0.0.1:8000/api/V1/offer',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($params)
        );

        $this->assertEquals(404, $client->getResponse()->getStatusCode());
        $this->assertEquals("Id requested was not found", $client->getResponse()->getContent());

    }

    public function testEditActionMissingData()
    {
        $params = [
            "id" => "11111",
            "description" => "offer test",
            "email" => "marcus.bogdannicolae@gmail.com",
            "imageUrl" => "https://r.hswstatic.com/w_907/gif/tesla-cat.jpg"
        ];

        $client = static::createClient();
        $client->request(
            'PUT',
            'http://127.0.0.1:8000/api/V1/offer',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($params)
        );

        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $this->assertEquals("Data must not be empty", $client->getResponse()->getContent());

    }

    public function testAddActionMissingData()
    {
        $params = [
            "description" => "offer test",
            "email" => "marcus.bogdannicolae@gmail.com",
            "imageUrl" => "https://r.hswstatic.com/w_907/gif/tesla-cat.jpg"
        ];

        $client = static::createClient();
        $client->request(
            'POST',
            'http://127.0.0.1:8000/api/V1/offer',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($params)
        );

        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $this->assertEquals("Data must not be empty", $client->getResponse()->getContent());

    }
}