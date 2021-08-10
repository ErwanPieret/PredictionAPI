<?php

namespace App\Repository;

use App\Entity\Prediction;

interface PredictionRepositoryInterface
{
    public function save(Prediction $prediction) :void;

    public function findOneById(int $id) : Prediction;

    public function findAll() : array;
}