<?php

namespace App\Controller;

use App\Entity\Prediction;
use App\Repository\PredictionRepositoryInterface;
use DateTime;
use Doctrine\ORM\EntityNotFoundException;
use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;
use TypeError;

class PredictionController extends AbstractController
{

    private $predictionRepository;

    private $logger;

    public function __construct(PredictionRepositoryInterface $predictionRepository, LoggerInterface $logger)
    {
        $this->predictionRepository = $predictionRepository;
        $this->logger = $logger;
    }

    /**
     * API : Create prediction
     * @Route("/v1/predictions", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function create(Request $request): JsonResponse
    {
        $this->logger->info("API : POST prediction : ".$request->getClientIp());

        $data = json_decode($request->getContent(),true);
        if(!isset($data["event_id"],$data["market_type"],$data["prediction"])){
            $this->logger->error("API : POST prediction - Missing argument");
            return new JsonResponse(["message"=>"Bad request"],400);
        }

        try {
            $prediction = new Prediction();
            $prediction->setEventId($data["event_id"]);
            $prediction->setMarketType($data["market_type"]);
            $prediction->setPrediction($data["prediction"]);
            $prediction->setStatus(Prediction::UNRESOLVED);
            $prediction->setCreatedAt((new DateTime)->getTimestamp());
            $prediction->setUpdatedAt((new DateTime)->getTimestamp());

            $this->predictionRepository->save($prediction);

            $this->logger->info("API : prediction created successfully - id : ".$prediction->getId());

            return new JsonResponse(["message"=>"Prediction created"],204);
        } catch (InvalidArgumentException | TypeError $exception) {

            $this->logger->debug("API : POST prediction : ".$exception->getMessage());
            return new JsonResponse(["message"=>"Bad request"],400);

        }
    }

    /**
     * API : Return a list of all predictions
     * @Route("/v1/predictions", methods={"GET"})
     * @param SerializerInterface $serializer
     * @param Request $request
     * @return JsonResponse
     */
    public function list(SerializerInterface $serializer, Request $request): JsonResponse
    {
        $this->logger->info("API : GET prediction : ".$request->getClientIp());
        $predictions = $this->predictionRepository->findAll();

        $data = $serializer->serialize($predictions, JsonEncoder::FORMAT);
        return new JsonResponse($data,200, [],true);
    }

    /**
     * API : Update prediction status
     * @Route("/v1/predictions/{id}/status", methods={"PUT"})
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function updateStatus(Request $request, int $id): JsonResponse
    {
        $this->logger->info("API : PUT prediction : ".$request->getClientIp());

        $data = json_decode($request->getContent(),true);
        if(!isset($data["status"])){
            $this->logger->error("API : POST prediction - Missing argument");
            return new JsonResponse(["message"=>"Bad request"],400);
        }

        try {
            $prediction = $this->predictionRepository->findOneById($id);
            $prediction->setStatus($data["status"]);
            $prediction->setUpdatedAt((new DateTime)->getTimestamp());

            $this->predictionRepository->save($prediction);

            $this->logger->info("API : prediction updated successfully - id : ".$prediction->getId().
                " - status : ".$prediction->getStatus());

            return new JsonResponse(["message"=>"Prediction updated"],204);

        } catch(InvalidArgumentException $exception) {

            $this->logger->error("API : PUT prediction : ".$exception->getMessage());
            return new JsonResponse(["message"=>"Bad request"],400);

        } catch(EntityNotFoundException $exception) {

            $this->logger->error("API : PUT prediction : ".$exception->getMessage());
            return new JsonResponse(["message"=>$exception->getMessage()],404);

        }
    }
}