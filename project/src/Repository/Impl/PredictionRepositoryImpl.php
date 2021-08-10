<?php

namespace App\Repository\Impl;

use App\Entity\Prediction;
use App\Repository\PredictionRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;

class PredictionRepositoryImpl implements PredictionRepositoryInterface
{

    private $entityManager;

    private $objectRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->objectRepository = $this->entityManager->getRepository(Prediction::class);
    }

    /**
     * Save prediction object in database
     * @param Prediction $prediction
     */
    public function save(Prediction $prediction) : void
    {
        $this->entityManager->persist($prediction);
        $this->entityManager->flush();
    }

    /**
     * Return prediction object from database
     * @param int $id
     * @return Prediction
     * @throws EntityNotFoundException
     */
    public function findOneById(int $id) : Prediction
    {
        $prediction = $this->objectRepository->findOneBy(['id' => $id]);
        if(empty($prediction)) {
            throw new EntityNotFoundException("This prediction doesn't exist");
        }
        return $prediction;
    }


    /**
     * Return a list of all predictions
     * @return array
     */
    public function findAll(): array
    {
        return $this->objectRepository->findAll();
    }
}