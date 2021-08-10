<?php

namespace App\Tests\Controller;

use App\Entity\Prediction;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PredictionControllerTest extends WebTestCase
{

    protected $client;

    protected function setUp() : void
    {
        parent::setUp();
        $this->client = static::createClient();
    }

    public function testCreateSuccessCorrectScore()
    {
        $data = array(
            'event_id' => 1,
            'market_type' => 'correct_score',
            'prediction' => '3:2'
        );

        $this->client->request('POST', '/v1/predictions',[],[],['CONTENT_TYPE' => 'application/json'],
            json_encode($data));

        $this->assertEquals(204, $this->client->getResponse()->getStatusCode());
    }

    public function testCreateErrorCorrectScore()
    {
        $data = array(
            'event_id' => 1,
            'market_type' => 'correct_score',
            'prediction' => '3:e'
        );

        $this->client->request('POST', '/v1/predictions',[],[],['CONTENT_TYPE' => 'application/json'],
            json_encode($data));

        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());
    }

    public function testCreateSuccess1X2_1()
    {
        $data = array(
            'event_id' => 1,
            'market_type' => '1x2',
            'prediction' => '1'
        );

        $this->client->request('POST', '/v1/predictions',[],[],['CONTENT_TYPE' => 'application/json'],
            json_encode($data));

        $this->assertEquals(204, $this->client->getResponse()->getStatusCode());
    }

    public function testCreateSuccess1X2_X()
    {
        $data = array(
            'event_id' => 1,
            'market_type' => '1x2',
            'prediction' => 'X'
        );

        $this->client->request('POST', '/v1/predictions',[],[],['CONTENT_TYPE' => 'application/json'],
            json_encode($data));

        $this->assertEquals(204, $this->client->getResponse()->getStatusCode());
    }

    public function testCreateSuccess1X2_2()
    {
        $data = array(
            'event_id' => 1,
            'market_type' => '1x2',
            'prediction' => '2'
        );

        $this->client->request('POST', '/v1/predictions',[],[],['CONTENT_TYPE' => 'application/json'],
            json_encode($data));

        $this->assertEquals(204, $this->client->getResponse()->getStatusCode());
    }

    public function testCreateErrorMissingValue()
    {
        $data = array(
            'event_id' => 1,
            'market_type' => 'correct_score'
        );

        $this->client->request('POST', '/v1/predictions',[],[],['CONTENT_TYPE' => 'application/json'],
            json_encode($data));

        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());
    }

    public function testCreateErrorIncorrectEventId()
    {
        $data = array(
            'event_id' => 'e',
            'market_type' => 'correct_score',
            'prediction' => '2'
        );

        $this->client->request('POST', '/v1/predictions',[],[],['CONTENT_TYPE' => 'application/json'],
            json_encode($data));

        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());
    }

    public function testCreateErrorIncorrectMarketType()
    {
        $data = array(
            'event_id' => 1,
            'market_type' => 'incorrect',
            'prediction' => '2'
        );

        $this->client->request('POST', '/v1/predictions',[],[],['CONTENT_TYPE' => 'application/json'],
            json_encode($data));

        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());
    }

    public function testList()
    {
        $prediction = new Prediction();
        $prediction->setEventId(3);
        $prediction->setMarketType("correct_score");
        $prediction->setPrediction("0:2");

        $data = array(
            'event_id' => $prediction->getEventId(),
            'market_type' => $prediction->getMarketType(),
            'prediction' => $prediction->getPrediction()
        );

        $this->client->request('POST', '/v1/predictions',[],[],['CONTENT_TYPE' => 'application/json'],
            json_encode($data));

        $this->client->request('GET', '/v1/predictions');

        $res = json_decode($this->client->getResponse()->getContent(),true);

        $newPrediction = array_pop($res);

        $this->assertEquals($newPrediction["eventId"],$prediction->getEventId());
        $this->assertEquals($newPrediction["marketType"],$prediction->getMarketType());
        $this->assertEquals($newPrediction["prediction"],$prediction->getPrediction());
        $this->assertEquals($newPrediction["status"],$prediction->getStatus());
        $this->assertEquals($newPrediction["createdAt"],$prediction->getCreatedAt());
        $this->assertEquals($newPrediction["updatedAt"],$prediction->getUpdatedAt());
    }

    public function testUpdateSuccessWin()
    {
        $this->client->request('GET', '/v1/predictions');
        $res = json_decode($this->client->getResponse()->getContent(),true);
        $prediction = array_pop($res);

        $data = array(
            'status' => 'win'
        );

        $this->client->request('PUT', '/v1/predictions/'.$prediction['id'].'/status',[],[],['CONTENT_TYPE' => 'application/json'],
            json_encode($data));

        $this->assertEquals(204, $this->client->getResponse()->getStatusCode());
    }

    public function testUpdateSuccessLost()
    {
        $this->client->request('GET', '/v1/predictions');
        $res = json_decode($this->client->getResponse()->getContent(),true);
        $prediction = array_pop($res);

        $data = array(
            'status' => 'lost'
        );

        $this->client->request('PUT', '/v1/predictions/'.$prediction['id'].'/status',[],[],['CONTENT_TYPE' => 'application/json'],
            json_encode($data));

        $this->assertEquals(204, $this->client->getResponse()->getStatusCode());
    }

    public function testUpdateErrorElse()
    {
        $this->client->request('GET', '/v1/predictions');
        $res = json_decode($this->client->getResponse()->getContent(),true);
        $prediction = array_pop($res);

        $data = array(
            'status' => 'else'
        );

        $this->client->request('PUT', '/v1/predictions/'.$prediction['id'].'/status',[],[],['CONTENT_TYPE' => 'application/json'],
            json_encode($data));

        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());
    }

    public function testUpdateErrorNoStatus()
    {
        $this->client->request('GET', '/v1/predictions');
        $res = json_decode($this->client->getResponse()->getContent(),true);
        $prediction = array_pop($res);

        $this->client->request('PUT', '/v1/predictions/'.$prediction['id'].'/status');

        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());
    }

    public function testUpdateErrorDoesNotExist()
    {
        $data = array(
            'status' => 'win'
        );

        $this->client->request('PUT', '/v1/predictions/0/status',[],[],['CONTENT_TYPE' => 'application/json'],
            json_encode($data));

        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());
    }
}
